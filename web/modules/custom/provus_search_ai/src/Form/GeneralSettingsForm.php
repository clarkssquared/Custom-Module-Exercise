<?php

namespace Drupal\provus_search_ai\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Provus Search AI settings for this site.
 */
class GeneralSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'provus_search_ai_general_settings';
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
    $form['service'] = [
      '#required' => TRUE,
      '#type' => 'select',
      '#title' => $this->t('Service'),
      '#options' => [
        // 'kendra' => 'AWS Kendra',
        'bedrock' => 'AWS Bedrock',
      ],
      '#default_value' => $this->config('provus_search_ai.settings')->get('service'),
    ];

    $form['api_key'] = [
      '#required' => TRUE,
      '#type' => 'textfield',
      '#title' => $this->t('Client Key'),
      '#default_value' => $this->config('provus_search_ai.settings')->get('api_key'),
    ];

    $form['api_secret'] = [
      '#required' => TRUE,
      '#type' => 'textfield',
      '#title' => $this->t('Client Secret'),
      '#default_value' => $this->config('provus_search_ai.settings')->get('api_secret'),
    ];

    $form['aws_region'] = [
      '#required' => TRUE,
      '#type' => 'textfield',
      '#title' => $this->t('AWS Region'),
      '#default_value' => $this->config('provus_search_ai.settings')->get('aws_region'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('provus_search_ai.settings')
      ->set('service', $form_state->getValue('service'))
      ->set('api_key', $form_state->getValue('api_key'))
      ->set('api_secret', $form_state->getValue('api_secret'))
      ->set('aws_region', $form_state->getValue('aws_region'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
