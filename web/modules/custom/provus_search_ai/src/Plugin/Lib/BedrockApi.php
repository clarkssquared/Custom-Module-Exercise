<?php

namespace Drupal\provus_search_ai\Plugin\Lib;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\State\State;
use Aws\Credentials\Credentials;
use Aws\BedrockAgentRuntime\BedrockAgentRuntimeClient;
use Aws\BedrockAgent\BedrockAgentClient;

/**
 * Main class that generates the metatag using OpenAI.
 */
class BedrockApi {
  use LoggerTrait;

  const MODULE_PREFIX = 'provus_search_ai';

  /**
   * Stores error messages.
   *
   * @var array
   */
  private $errors = [];

  /**
   * Stores Drupal logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  private $logger;

  /**
   * Stores Drupal configuration.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  private $config;

  /**
   * Config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Logger factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * Constructor method.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Config factory.
   * @param \Drupal\Core\State\State $state
   *   The object State.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerFactory
   *   Logger factory.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    State $state,
    LoggerChannelFactoryInterface $loggerFactory
  ) {
    $this->loggerFactory = $loggerFactory;
    $this->configFactory = $config_factory;
    $this->state = $state;

    $this->config = $this->configFactory->get(self::MODULE_PREFIX . '.settings');
  }

  /**
   * Accepts and returns the result in array.
   *
   * @param string $text
   *   The text to be processed.
   * @param string $uuid
   *   Unique session code.
   *
   * @return array
   *   Array with title, description, abstract, and keywords needed by Metatag.
   */
  public function search(string $text, string $uuid = '') {
    if (!$text) {
      $this->logError('No title received!');
      return FALSE;
    }

    $this->logMessage('AWS Bedrock question: ' . $text . ' ~Session: ' . $uuid);

    $region = $this->config->get('aws_region');
    $api_key = $this->config->get('api_key');
    $api_secret = $this->config->get('api_secret');
    $knowledge_base_id = $this->config->get('knowledge_base_id');

    $version = 'latest';
    $credentials = new Credentials($api_key, $api_secret);

    $this->bedrockRuntimeClient = new BedrockAgentRuntimeClient([
      'region' => $region,
      'version' => $version,
      'credentials' => $credentials,
    ]);

    $result = $this->invokeClaude($text, $knowledge_base_id, $region, $uuid);

    $stringResult = '';
    $citeNumber = 0;
    foreach ($result['citations'] as $cite) {
      $stringResult .= '<div class="chat-text-response">';
      $stringResult .= $cite['generatedResponsePart']['textResponsePart']['text'];

      foreach ($cite['retrievedReferences'] as $reference) {
        $uri = $reference['location']['s3Location']['uri'];
        if (preg_match('/node_(\d+)\.json/', $uri, $matches)) {
          $nid = $matches[1];
          $alias = \Drupal::service('path_alias.manager')->getAliasByPath('/node/' . $nid);
          
          $citeNumber++;
          $stringResult .= " <a target='_blank' href='$alias'>[$citeNumber]</a>";
        }
      }

      $stringResult .= '</div>';
    }

    $return[] = [
      'content' => $stringResult,
    ];

    return $return;
  }

  /**
   * Call the LLM.
   */
  private function invokeClaude($prompt, $knowledgeBaseId, $region, $sessionId = '') {
    try {
      $payload = [
        'input' => [
          'text' => $prompt,
        ],
        'retrieveAndGenerateConfiguration' => [
          'knowledgeBaseConfiguration' => [
            'knowledgeBaseId' => $knowledgeBaseId,
            'modelArn' => 'arn:aws:bedrock:' . $region . '::foundation-model/anthropic.claude-v2',
          ],
          'type' => 'KNOWLEDGE_BASE',
        ],
      ];

      if ($sessionId) {
        if (isset($_SESSION['bedrock_api'][$sessionId]) && $_SESSION['bedrock_api'][$sessionId]) {
          $payload["sessionId"] = $_SESSION['bedrock_api'][$sessionId];
        }
      }

      $result = $this->bedrockRuntimeClient->retrieveAndGenerate($payload);

      $data = $result->toArray();

      if ($sessionId) {
        $_SESSION['bedrock_api'][$sessionId] = $data['sessionId'];
      }

      $this->logMessage('AWS Bedrock answer: ' . $prompt . ' ~Answer: ' . json_encode($data, TRUE));

      return $data;
    }
    catch (Exception $e) {
      $this->logError("Error: ({$e->getCode()}) - {$e->getMessage()}\n");
    }

    return FALSE;
  }

  /**
   * Method to invoke sync action in Bedrock.
   */
  public function invokeSync() {
    $region = $this->config->get('aws_region');
    $api_key = $this->config->get('api_key');
    $api_secret = $this->config->get('api_secret');
    $knowledge_base_id = $this->config->get('knowledge_base_id');
    $data_source_id = $this->config->get('data_source_id');

    $version = 'latest';
    $credentials = new Credentials($api_key, $api_secret);

    $this->bedrockRuntimeClient = new BedrockAgentClient([
      'region' => $region,
      'version' => $version,
      'credentials' => $credentials,
    ]);

    $result = $this->bedrockRuntimeClient->startIngestionJob([
      'dataSourceId' => $data_source_id,
      'knowledgeBaseId' => $knowledge_base_id,
    ]);
  }

}
