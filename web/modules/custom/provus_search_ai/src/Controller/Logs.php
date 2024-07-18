<?php

namespace Drupal\provus_search_ai\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for OpenAI Embeddings routes.
 */
class Logs extends ControllerBase {

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
    // Get Node Storage.
    $node_storage = \Drupal::entityTypeManager()->getStorage('node');

    // Get Nids.
    $entity_ids = \Drupal::entityQuery('content_entity_psai_log')
      ->accessCheck(FALSE)
      ->pager(50, 0)
      ->sort('created', 'desc')
      ->execute();

    if (!$entity_ids) {
      $build['#title'] = t("Logs");
      $build['#markup'] = '<p>No logs saved yet.</p>';
      return $build;
    }

    $body = "<table>";
    $body .= "<thead><tr><th>Created</th><th>Question</th><th>Answer</th><th>Session ID</th><th>User ID</th></tr></thead>";

    $entity_type_manager = \Drupal::entityTypeManager();
    foreach ($entity_ids as $entity_id) {
      $entity = $entity_type_manager->getStorage('content_entity_psai_log')->load($entity_id);

      if ($entity) {
        $created = $entity->get('created')->value;
        $question = $entity->get('question')->value;
        $answer = $entity->get('answer')->value;
        $session_id = $entity->get('session_id')->value;
        $user_id = $entity->get('user_id')->target_id;

        $body .= "<tr>";
        $body .= "<td>" . date('M d, Y H:ia', $created) . "</td>";
        $body .= "<td>" . $question . "</td>";
        $body .= "<td>" . $answer . "</td>";
        $body .= "<td>" . $session_id . "</td>";
        $body .= "<td>" . $user_id . "</td>";
        $body .= "</tr>";
      }
    }

    $body .= '</table>';

    $build['#title'] = t("Logs");
    $build['#markup'] = $body;
    $build['pager'] = [
      '#type' => 'pager',
    ];

    return $build;
  }

}
