<?php
/**
 * @file
 * Contains \Drupal\career_jobs\Form\JobSearch.
 */
namespace Drupal\career_jobs\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;


class JobSearch extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'career_jobs_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['country'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Country'),
      '#required' => TRUE,
    ];

    $form['job'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Job'),
      '#required' => TRUE,
    ];

    $form['city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('City'),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $query = [
      'country' => $form_state->getValue('country'),
      'job' => $form_state->getValue('job'),
      'city' => $form_state->getValue('city'),
    ];

    $url = \Drupal\Core\Url::fromRoute('career_jobs.jobResults', [], ['query' => $query]);
    $form_state->setRedirectUrl($url);
  }
}
