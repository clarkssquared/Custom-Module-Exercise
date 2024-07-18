<?php

namespace Drupal\provus_search_ai\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Returns responses for OpenAI Embeddings routes.
 */
class Query extends ControllerBase {

  /**
   * The controller constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->config = $config_factory->get('provus_search_ai.settings');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }

  /**
   * Builds the response.
   */
  public function index() {
    $query = \Drupal::request()->query->get('query');
    $uuid = \Drupal::request()->query->get('uuid');
    if (!$query) {
      $output = '<p>Empty query.</p>';
      return $this->jsonResponse($output);
    }

    $service = $this->config('provus_search_ai.settings')->get('service');
    if ($service == 'kendra') {
      $api = \Drupal::service('provus_search_ai.aws_kendra');
    }
    elseif ($service == 'bedrock') {
      $api = \Drupal::service('provus_search_ai.aws_bedrock');
    }
    else {
      return $this->jsonResponse('No service found. Please configure this module.');
    }

    $result = $api->search($query, $uuid);
    if (!$result) {
      return $this->jsonResponse('No results were found, or results were excluded because they did not meet the relevancy score threshold.');
    }

    $output = '';
    foreach ($result as $resultItem) {
      $output .= "<li><div class='highlight'>{$resultItem['content']}</div></li>";
      $list[] = $output;
    }

    $text = '<ul>' . implode('', $list) . '</ul>';

    $entity_type_manager = \Drupal::entityTypeManager();
    $entity = $entity_type_manager->getStorage('content_entity_psai_log')->create([
      'type' => 'default',
      'question' => $query,
      'answer' => [
        'value' => $text,
        'format' => 'full_html',
      ],
      'session_id' => $uuid,
    ]);
    $entity->save();

    return $this->jsonResponse($text);
  }

  /**
   * Returns Json Response.
   */
  private function jsonResponse($text) {
    return new JsonResponse([
      'data' => $text,
    ], Response::HTTP_OK);
  }

}
