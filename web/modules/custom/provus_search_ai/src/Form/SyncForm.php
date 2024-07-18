<?php

namespace Drupal\provus_search_ai\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Sync nodes to AWS S3.
 */
class SyncForm extends FormBase {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'provus_search_ai_sync';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['provus_search_ai.settings'];
  }

  /**
   * Constructs a MetatagAIConfigForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['all'] = [
      '#type' => 'details',
      '#title' => t('All Nodes'),
      '#open' => TRUE,
    ];

    $content_types = $this->entityTypeManager->getStorage('node_type')->loadMultiple();

    $options = [];
    foreach ($content_types as $content_type) {
      $options[$content_type->id()] = $content_type->label();
    }

    $form['all']['content_types'] = [
      '#type' => 'radios',
      '#title' => $this->t('Content Types'),
      '#options' => $options,
      '#default_value' => [],
    ];

    $form['all']['all_nodes'] = [
      '#type' => 'submit',
      '#value' => $this->t('Sync Now'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $all = $form_state->getValue('all_nodes');
    $contentType = $form_state->getValue('content_types');
    $this->sync($contentType);
  }

  /**
   * Responsible for generating JSON file and uploading to AWS S3.
   */
  private function sync($contentType) {
    $nodes = \Drupal::entityTypeManager()->getStorage('node')
      ->loadByProperties(['type' => $contentType, 'status' => 1]);

    if (!$nodes) {
      return;
    }

    $file_system = \Drupal::service('file_system');
    $dir = 'exports/' . $contentType;
    $dirPath = 'public://' . $dir;
    $file_system->prepareDirectory($dirPath, FileSystemInterface::CREATE_DIRECTORY);

    $s3 = \Drupal::service('provus_search_ai.aws_s3');

    $content = [];
    $syncTotal = 0;
    foreach ($nodes as $node) {
      $bodyField = '';
      switch ($contentType) {
        case 'case':
          if (isset($node->field_case_project_overview)) {
            $bodyField .= $node->field_case_project_overview->value . '<br>';
          }

          if (isset($node->field_case_company_background)) {
            $bodyField .= $node->field_case_company_background->value;
          }

          break;

        default:
          if (isset($ndoe->field_body)) {
            $bodyField = $ndoe->field_body->value;
          }
          else {
            $field = 'field_' . $contentType . '_body';
            if (isset($node->{$field})) {
              $bodyField = $node->{$field}->value;
            }
          }
      }

      $nid = $node->nid->value;
      if (!$bodyField) {
        $this->messenger()->addStatus('No content found for Node ID: ' . $nid);
      }
      else {
        $this->messenger()->addStatus('Syncing Node ID: ' . $nid);
      }

      $content = json_encode([
        'id' => $nid,
        'title' => $node->title->value,
        'content' => $bodyField,
      ], TRUE);

      $path = $dirPath . '/node_' . $nid . '.json';
      $file_system = \Drupal::service('file.repository');
      $file_system->writeData($content, $path, FileSystemInterface::EXISTS_REPLACE);

      // Step 2: Upload the same file to AWS.
      $s3->upload($dir . '/node_' . $nid . '.json');
      $syncTotal++;
    }

    // Step 3: Synce Knowledge Base.
    if ($syncTotal) {
      $api = \Drupal::service('provus_search_ai.aws_bedrock');
      $api->invokeSync();
    }

    $this->messenger()->addStatus('Finished syncing a total of ' . $syncTotal . ' ' . $contentType);
  }

}
