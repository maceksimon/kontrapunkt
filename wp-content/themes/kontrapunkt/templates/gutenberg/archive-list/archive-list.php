<?php
function block_archive_list_content($content) {
  $postIds = [];
  foreach ($content['items'] as $key=>$value) {
    $item = new Timber\Post($value['postId']);
    $items[] = [
      'title' => $item->post_title,
      'excerpt' => $item->get_preview(50, false, ''),
      'image' => Helpers::formatImage($item->thumbnail),
      'url' => $item->link,
    ];
  }
  $content['items'] = $items;

  return $content;
}

add_filter('block_archive_list_content', 'block_archive_list_content');

$block = new Block('archive-list', 'Archiv - výpis');
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
  ->addRepeater('items', [
    'label' => 'Stránky',
    'instructions' => '',
    'required' => 1,
  ])
  ->addPostObject('postId', [
    'label' => 'Stránka',
    'instructions' => 'Vyberte stránku z nabídky',
    'required' => 0,
    'post_type' => ['page', 'post'],
    'allow_null' => 0,
    'multiple' => 0,
    'return_format' => 'id',
    'ui' => 1,
]);
$block->setValidationFields([]);
$block->register();
