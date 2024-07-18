<?php

/**
 * @file
 * Provide views data for rochester_views module.
 */

/**
 * Implements hook_views_data().
 */
function provus_advanced_calendar_views_data() {
  $data = [];

  $data['views']['views_link_area'] = [
    'title' => t('Link area'),
    'help' => t('Insert multiple links in an area.'),
    'area' => [
      'id' => 'views_link_area',
    ],
  ];

  return $data;
}
