<?php

namespace Drupal\provus_search_ai\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure AWS Kendra client settings for this site.
 */
class AwsKendraSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'provus_search_ai_kendra_settings';
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
    $form['kendra_index_id'] = [
      '#required' => TRUE,
      '#type' => 'textfield',
      '#title' => $this->t('Kendra Index ID'),
      '#default_value' => $this->config('provus_search_ai.settings')->get('kendra_index_id'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('provus_search_ai.settings')
      ->set('kendra_index_id', $form_state->getValue('kendra_index_id'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
