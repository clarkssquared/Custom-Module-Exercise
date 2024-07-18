<?php

namespace Drupal\provus_search_ai\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure AWS Bedrock client settings for this site.
 */
class AwsBedrockSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'provus_search_ai_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['provus_search_ai.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['knowledge_base_id'] = [
      '#required' => TRUE,
      '#type' => 'textfield',
      '#title' => $this->t('Knowledge Base ID'),
      '#default_value' => $this->config('provus_search_ai.settings')->get('knowledge_base_id'),
    ];

    $form['data_source_id'] = [
      '#required' => TRUE,
      '#type' => 'textfield',
      '#title' => $this->t('Data Source ID'),
      '#default_value' => $this->config('provus_search_ai.settings')->get('data_source_id'),
    ];

    $form['s3_bucket_id'] = [
      '#required' => TRUE,
      '#type' => 'textfield',
      '#title' => $this->t('S3 Bucket ID'),
      '#default_value' => $this->config('provus_search_ai.settings')->get('s3_bucket_id'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('provus_search_ai.settings')
      ->set('data_source_id', $form_state->getValue('data_source_id'))
      ->set('knowledge_base_id', $form_state->getValue('knowledge_base_id'))
      ->set('s3_bucket_id', $form_state->getValue('s3_bucket_id'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
