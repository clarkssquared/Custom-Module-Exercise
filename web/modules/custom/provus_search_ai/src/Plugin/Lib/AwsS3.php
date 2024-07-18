<?php

namespace Drupal\provus_search_ai\Plugin\Lib;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

use Aws\Credentials\Credentials;
// Use Aws\AppSync\AppSyncClient.
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

/**
 * Main class that generates the metatag using OpenAI.
 */
class AwsS3 {
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
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerFactory
   *   Logger factory.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    LoggerChannelFactoryInterface $loggerFactory
  ) {
    $this->loggerFactory = $loggerFactory;
    $this->configFactory = $config_factory;

    $this->config = $this->configFactory->get(self::MODULE_PREFIX . '.settings');
  }

  /**
   * Accepts and returns the result in array.
   *
   * @param string $file
   *   The file to be processed.
   *
   * @return array
   *   Array with title, description, abstract, and keywords needed by Metatag.
   */
  public function upload($file) {
    $api_key = $this->config->get('api_key');
    $api_secret = $this->config->get('api_secret');
    $region = $this->config->get('aws_region');
    $bucket = $this->config->get('s3_bucket_id');

    $credentials = new Credentials($api_key, $api_secret);

    $client = S3Client::factory([
      'credentials' => $credentials,
      'region' => $region,
    ]);

    try {
      $filePath = \Drupal::service('file_system')->realpath("public://" . $file);

      $result = $client->putObject([
        'Bucket' => $bucket,
        'Key' => $file,
        'SourceFile' => $filePath,
      ]);

      return $result;

    }
    catch (S3Exception $e) {
      $this->logError($e->getMessage());
    }
  }

}
