<?php

namespace Drupal\career_jobs\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\career_jobs\Form;
use Symfony\Component\HttpFoundation\Request;
use Drupal\career_jobs\Library\Careerjet_API;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Returns responses for Career Jobs Search routes.
 */
class CareerJobsController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

  /**
   * Displays Career Jet searh form.
   */
  public function displayForm() {

    $form =  \Drupal::formBuilder()->getForm('Drupal\career_jobs\Form\JobSearch');

    return $form;
  }

  /**
   * Displays Job Results from Careerjet API
   */
  public function getResult(Request $request) {
    $country = $request->query->get('country');
    $job =  $request->query->get('job');
    $city = $request->query->get('city');
    $page = $request->query->get('page', 1); // Default to page 1 if not set

    // Initialize Careerjet API
    $cjapi = new Careerjet_API('en_PH');

    $since_date = date('Y-m-d', strtotime('-7 days'));
    $result = $cjapi->search([
      'keywords' => $job,
      'location' => $city,
      'affid'    => '678bdee048',
      'page'     => $page,
      'since' => $since_date
    ]);

    $build = [
      '#theme' => 'job_search_results',
      '#jobs' => [],
      '#pager' => [],
    ];

    if ($result->type == 'JOBS') {
      $jobs = [];
      foreach ($result->jobs as $job) {
        // Strip HTML tags from job description
        $job->description = strip_tags($job->description);
        $jobs[] = $job;
      }

      $build['#jobs'] = $jobs;

      // Add pagination
      $total_pages = $result->pages;
      dump($total_pages);
      if ($total_pages > 1) {
        $pager = [];
        for ($i = 1; $i <= $total_pages; $i++) {
          $pager[] = Link::fromTextAndUrl($i, Url::fromRoute('career_jobs.jobResults',[
            'country' => $country,
            'job' => $job,
            'city' => $city,
            'page' => $i,
          ]))->toRenderable();
        }
          // $build['#pager'] = [
          //   '#theme' => 'job-search-results',
          //   '#type' => 'pager',
          //   '#element' => 0,
          //   '#pager' => (string) $pager,
          // ];
      }
    }
    return $build;
  }
}
