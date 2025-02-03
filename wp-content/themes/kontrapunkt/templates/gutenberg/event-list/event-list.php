<?php
function block_event_list_content($content)
{

  global $paged;
  if (!isset($paged) || !$paged) {
    $paged = 1;
  }

  $limit = isset($content['limit']) ? $content['limit'] : 0;
  $today_date = date('Y-m-d');

  $query = [
    'post_type' => 'event',
    'posts_per_page' => $limit ? $limit : -1,
    'paged' => $paged,
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
    'paged' => $paged,
  ];
  $wp_query = new WP_Query($query);
  $posts = new Timber\PostQuery($wp_query);

  $content['items'] = [];
  foreach ($posts as $key => $item) {
    // Build links array for social/web links
    $links = [];

    if ($web_link = get_field('web_link', $item->ID)) {
      $links[] = [
        'type' => 'web',
        'url' => $web_link['url'],
        'title' => $web_link['title'] ?: 'Web',
        'target' => $web_link['target'],
        'icon_before' => 'external-link',
      ];
    }

    if ($facebook_link = get_field('facebook_link', $item->ID)) {
      $links[] = [
        'type' => 'facebook',
        'url' => $facebook_link,
        'title' => 'Facebook',
        'target' => '_blank',
        'icon_before' => 'facebook',
      ];
    }

    if ($instagram_link = get_field('instagram_link', $item->ID)) {
      $links[] = [
        'type' => 'instagram',
        'url' => $instagram_link,
        'title' => 'Instagram',
        'target' => '_blank',
        'icon_before' => 'instagram',
      ];
    }

    $content['items'][] = [
      'id' => $item->ID,
      'title' => $item->title,
      'image' => Helpers::formatImage(get_field('image', $item->ID)),
      'perex' => get_field('perex', $item->ID),
      'date_start' => get_field('date_start', $item->ID),
      'date_end' => get_field('date_end', $item->ID),
      'time_start' => get_field('time_start', $item->ID),
      'time_end' => get_field('time_end', $item->ID),
      'additional_showtimes' => get_field('additional_showtimes', $item->ID),
      'price' => get_field('price', $item->ID),
      'location' => get_field('location', $item->ID),
      'author' => get_field('author', $item->ID),
      'links' => $links,
      'url' => $item->link,
      'created' => $item->date('U'),
    ];
  };

  $pagination = $posts->pagination();
  $content['pagination'] = [];
  // build pagination
  foreach ($pagination->pages as $key => $page) {
    $content['pagination']['pages'][] = [
      'title' => $page['title'],
      'url' => (isset($page['link'])) ? $page['link'] : '',
      'current' => $page['current'],
    ];
  }
  // add next and previous links
  if (!empty($pagination->next)) {
    $content['pagination']['next'] = $pagination->next['link'];
  }
  if (!empty($pagination->prev)) {
    $content['pagination']['previous'] = $pagination->prev['link'];
  }

  return $content;
}

add_filter('block_event_list_content', 'block_event_list_content');

$block = new Block('event-list', 'Seznam - Události');

$block->fieldsBuilder()
  ->addTrueFalse('enable_heading', [
    'label' => 'Zobrazit nadpis',
    'required' => 0,
    'default_value' => 0,
    'ui' => 1,
    'ui_on_text' => 'Ano',
    'ui_off_text' => 'Ne',
  ])
  ->addGroup('heading', [
    'label' => 'Nadpis',
    'instructions' => '',
    'required' => 0,
    'layout' => 'block',
  ])->conditional('enable_heading', '==', '1')
  ->addText('heading', [
    'label' => 'Text nadpisu',
    'instructions' => 'Doporučená délka 3-5 slov',
    'required' => 0,
    'placeholder' => 'Enter title',
    'maxlength' => 80,
  ]);
$block->fieldsBuilder()
  ->addNumber('limit', [
    'label' => 'Počet zobrazených událostí',
    'required' => 1,
    'default_value' => 5,
    'min' => 1,
    'max' => 20,
  ])
  ->addLink('link', [
    'label' => 'Tlačítko',
    'return_format' => 'array',
  ]);
$block->setValidationFields([]);
$block->register();
