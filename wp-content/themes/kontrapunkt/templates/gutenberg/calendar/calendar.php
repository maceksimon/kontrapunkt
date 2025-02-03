<?php
function block_calendar_content($content) {
  $today_date = date('Y-m-d');

  $query = [
    'post_type' => 'event',
    'posts_per_page' => -1, // Fetch all events
    'meta_key' => 'date_start',
    'orderby' => 'meta_value',
    'order' => 'ASC',
    'meta_query' => [
      'relation' => 'OR',
      [
        'key' => 'date_start',
        'value' => $today_date,
        'compare' => '>=',
        'type' => 'DATE',
      ],
      [
        'key' => 'date_end',
        'value' => $today_date,
        'compare' => '>=',
        'type' => 'DATE',
      ],
    ],
  ];

  $wp_query = new WP_Query($query);
  $posts = new Timber\PostQuery($wp_query);

  $content['items'] = [];
  foreach ($posts as $key => $item) {
    $date_start = get_field('date_start', $item->ID);
    $date_end = get_field('date_end', $item->ID);
    $time_start = get_field('time_start', $item->ID);
    $time_end = get_field('time_end', $item->ID);
    $time_start_object = DateTime::createFromFormat('H:i', $time_start);
    $time_end_object = DateTime::createFromFormat('H:i', $time_end);

    if (empty($date_end)) {
      $date_end = $date_start;
    }

    // Merge date and time into datetime object
    if (empty($time_start_object)) {
      $start_datetime = $date_start;
    } else {
      $start_datetime = $date_start . 'T' . $time_start_object->format('H:i') . ':00';
    }
    if (empty($time_end_object)) {
      $end_date = new DateTime($date_end);
      $end_date->modify('+1 day');
      $end_datetime = $end_date->format('Y-m-d');
    } else {
      $end_datetime = $date_end . 'T' . $time_end_object->format('H:i') . ':00';
    }

    $content['items'][] = [
      'id' => $item->ID,
      'title' => $item->title,
      'start' => $start_datetime,
      'end' => $end_datetime,
      'location' => get_field('location', $item->ID),
      'author' => get_field('author', $item->ID),
      'url' => get_the_permalink($item->ID),
    ];
  }

  return $content;
}

add_filter('block_calendar_content', 'block_calendar_content');

$block = new Block('calendar', 'Kalendář');

$block->setValidationFields([]);
$block->register();
