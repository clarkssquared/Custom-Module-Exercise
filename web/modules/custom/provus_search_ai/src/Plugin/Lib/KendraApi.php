<?php

namespace Drupal\provus_search_ai\Plugin\Lib;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\State\State;

use Aws\KendraV2\Exception\KendraV2Exception;
use Aws\Credentials\Credentials;
use Aws\kendra\kendraClient;

/**
 * Main class that generates the metatag using OpenAI.
 */
class KendraApi {
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

    $region = $this->config->get('kendra_region');
    $api_key = $this->config->get('api_key');
    $api_secret = $this->config->get('api_secret');
    $kendraIndexId = $this->config->get('kendra_index_id');

    // Define your AWS credentials and region.
    $credentials = new Credentials($api_key, $api_secret);

    // Initialize the Kendra client.
    $kendraClient = new kendraClient([
      'version'     => 'latest',
      'credentials' => $credentials,
      'region'      => $region,
    ]);

    // Define the query text.
    $queryText = strip_tags($text);
    $queryText = substr($queryText, 0, 1024);

    $list = [];
    try {
      // Send the query to Kendra.
      $result = $kendraClient->query([
        'IndexId' => $kendraIndexId,
        'QueryText' => $queryText,
      ]);

      // Process and display the search results.
      foreach ($result['ResultItems'] as $resultItem) {
        $list[]['content'] = "<div class='link'><a href='{$resultItem['DocumentURI']}'>{$resultItem['DocumentURI']}</a></div>"
          . "<div class='title'><a href='$link'>{$resultItem['DocumentTitle']['Text']}</a></div>"
          . "<div class='highlight'>{$resultItem['DocumentExcerpt']['Text']}</div>";
      }

      $this->logMessage($result);
    }
    catch (KendraV2Exception $e) {
      $this->logError($e->getMessage());
    }

    return $list;
  }

}
