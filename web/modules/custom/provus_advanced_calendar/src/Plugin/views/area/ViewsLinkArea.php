<?php

/**
 * @file
 * Contains \Drupal\block\Plugin\views\area\Block.
 */

namespace Drupal\provus_advanced_calendar\Plugin\views\area;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\views\Plugin\views\area\AreaPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides an area handler which renders links.
 *
 * @ingroup views_area_handlers
 *
 * @ViewsArea("views_link_area")
 */
class ViewsLinkArea extends AreaPluginBase {

  /**
   * The route match interface.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $request;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition,
      $container->get('current_route_match'),
      $container->get('request_stack')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $route_match, RequestStack $request_stack) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->routeMatch = $route_match;
    $this->request = $request_stack;
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['links'] = ['default' => []];
    $options['retain_query'] = ['default' => FALSE];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    // Prepare path format value and convert to string.
    foreach ($this->options['links'] as $delta => $values) {
      if (!empty($values['path'])) {
        $url = Url::fromRoute($this->options['links'][$delta]['path']['route_name'], $this->options['links'][$delta]['path']['route_parameters']);
        $path = $url->toString();
        $this->options['links'][$delta]['path'] = $path;
      }
    }
    $form['links'] = [
      '#type' => 'multivalue',
      '#title' => $this->t('Links'),
      '#default_value' => $this->options['links'],
      '#required' => TRUE,
      'title' => [
        '#type' => 'textfield',
        '#title' => $this->t('Title'),
        '#required' => TRUE,
      ],
      'path' => [
        '#type' => 'path',
        '#title' => $this->t('Path'),
        '#required' => TRUE,
      ],
    ];

    $form['retain_query'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Retain query values from the URL?'),
      '#default_value' => $this->options['retain_query'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function render($empty = FALSE) {
    $element = [];

    if (!$empty || !empty($this->options['empty'])) {
      // Prepare nav container.
      $element['tabs'] = [
        '#type' => 'html_tag',
        '#tag' => 'nav',
        '#attributes' => [
          'class' => [
            'view-link-area-toggle',
          ],
          'role' => 'navigation',
          'aria-label' => $this->t('Views toggle'),
          'data-drupal-nav-tabs' => '',
          'data-once' => 'nav-tabs',
        ],
        'list' => [
          '#theme' => 'item_list',
          '#list_type' => 'ul',
          '#attributes' => [
            'class' => [
              'list-group-horizontal',
            ],
          ],
          '#items' => [],
        ],
      ];

      // Populate list items with links.
      $route_name = $this->routeMatch->getRouteName();
      foreach ($this->options['links'] as $delta => $link) {
        $url = Url::fromRoute($link['path']['route_name'], $link['path']['route_parameters']);

        // Add query values if the retention setting is enabled.
        $query = $this->request->getCurrentRequest()->query->all();
        if ($this->options['retain_query'] &&
          !empty($query)) {
          if (!empty($query['ajax_page_state'])) {
            unset($query['ajax_page_state']);
          }
          $url->setOptions(['query' => $query]);
        }

        $element['tabs']['list']['#items'][$delta] = [
          '#title' => $this
            ->t('@title', ['@title' => $this->options['links'][$delta]['title']]),
          '#type' => 'link',
          '#url' => $url,
          '#attributes' => [
            'class' => ['nav-link'],
          ],
        ];

        // Add active class if current route.
        if ($route_name == $link['path']['route_name']) {
          $element['tabs']['list']['#items'][$delta]['#attributes']['class'][] = 'active';
        }
      }
    }

    return $element;
  }

}
