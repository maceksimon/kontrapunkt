<?php
function block_news_list_content($content) {

  // global $paged;
  // if (!isset($paged) || !$paged) {
  //   $paged = 1;
  // }

  $paged = false;
  $limit = isset($content['limit']) ? $content['limit'] : 0;

  $query = [
    'post_type' => 'news',
    'orderby' => 'post published',
    'order' => 'DESC',
    'posts_per_page' => $limit ? $limit : -1,
    'paged' => $paged,
  ];
  $posts = new Timber\PostQuery($query);

  $content['items'] = [];
  foreach ($posts->get_posts() as $key=>$item) {
    $content['items'][] = [
      'id' => $item->ID,
      'title' => $item->title,
      'description' => get_field('description', $item->ID),
      'image' => Helpers::formatImage($item->thumbnail),
      'link' => $item->link,
      'created' => $item->date('U'),
    ];
  };

  return $content;
}

add_filter('block_news_list_content', 'block_news_list_content');

$block = new Block('news-list', 'Aktuality - výpis');
$block->fieldsBuilder()
  ->addTrueFalse('enable_heading', [
    'label' => 'Nadpis bloku',
    'required' => 0,
    'default_value' => 0,
    'ui' => 1,
    'ui_on_text' => 'Aktivní',
    'ui_off_text' => 'Vypnuto',
  ])
  ->addGroup('heading', [
      'label' => 'Nadpis',
      'instructions' => '',
      'required' => 0,
      'layout' => 'block',
  ])->conditional('enable_heading', '==', '1')
  ->addText('heading', [
    'label' => 'Nadpis',
    'instructions' => 'Doporučujeme zadávat nadpis o délce 3-5 slov',
    'required' => 0,
    'placeholder' => 'Zadejte název',
    'maxlength' => 80,
  ]);
$block->fieldsBuilder()
  ->addNumber('limit', [
    'label' => 'Počet aktualit',
    'instructions' => 'Vyberte maximální počet aktualit, které chcete zobrazit. Pro zobrazení všech aktualit ponechte pole prázdné.',
    'required' => 0,
  ]);
$block->setValidationFields([]);
$block->register();
