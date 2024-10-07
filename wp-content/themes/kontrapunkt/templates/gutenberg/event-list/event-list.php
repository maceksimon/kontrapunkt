<?php
function block_event_list_content($content) {

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
  $posts = new Timber\PostQuery($query);

  $content['items'] = [];
  foreach ($posts->get_posts() as $key=>$item) {
    $date_start = get_field('date_start', $item->ID);
    $date_start_object = DateTime::createFromFormat('Y-m-d', $date_start);
    $date_end = get_field('date_end', $item->ID);
    $date_end_object = DateTime::createFromFormat('Y-m-d', $date_end);
    $time_start = get_field('time_start', $item->ID);
    $time_end = get_field('time_end', $item->ID);
    $time_start_object = DateTime::createFromFormat('H:i', $time_start);
    $time_end_object = DateTime::createFromFormat('H:i', $time_end);

    $content['items'][] = [
      'id' => $item->ID,
      'title' => $item->title,
      'image' => Helpers::formatImage(get_field('image', $item->ID)),
      'perex' => get_field('perex', $item->ID),
      'date_start' => $date_start_object ? $date_start_object->format('j. n. Y') : '',
      'date_end' => $date_end_object ? $date_end_object->format('j. n. Y') : '',
      'time_start' => $time_start_object ? $time_start_object->format('H:i') : '',
      'time_end' => $time_end_object ? $time_end_object->format('H:i') : '',
      'price' => get_field('price', $item->ID),
      'location' => get_field('location', $item->ID),
      'author' => get_field('author', $item->ID),
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
  if(!empty($pagination->next)) {
    $content['pagination']['next'] = $pagination->next['link'];
  }
  if(!empty($pagination->prev)) {
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
