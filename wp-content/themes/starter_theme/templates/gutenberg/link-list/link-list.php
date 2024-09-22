<?php
function block_link_list_content($content) {
  return $content;
}

add_filter('block_link_list_content', 'block_link_list_content');

$block = new Block('link-list', __('Seznam: odkazy', 'starter_theme'));
$block->fieldsBuilder()
  ->addTrueFalse('enable_heading', [
    'label' => __('Zobrazit nadpis', 'starter_theme'),
    'required' => 0,
    'default_value' => 0,
    'ui' => 1,
    'ui_on_text' => __('Aktivní', 'starter_theme'),
    'ui_off_text' => __('Vypnuto', 'starter_theme'),
    'wpml_cf_preferences' => 1,
  ])
  ->addGroup('heading', [
      'label' => __('Nadpis', 'starter_theme'),
      'instructions' => '',
      'required' => 0,
      'layout' => 'block',
  ])->conditional('enable_heading', '==', '1')
  ->addText('heading', [
    'label' => __('Nadpis', 'starter_theme'),
    'instructions' => 'Doporučujeme zadávat nadpis o délce 3-5 slov',
    'required' => 0,
    'placeholder' => 'Zadejte název',
    'maxlength' => 80,
    'wpml_cf_preferences' => 2,
  ]);
$block->fieldsBuilder()
  ->addRepeater('items', [
    'label' => __('Odkazy', 'starter_theme'),
    'required' => 1,
  ])
  ->addLink('link', [
    'label' => __('Odkaz', 'starter_theme'),
    'instructions' => __('Zadejte odkaz', 'starter_theme'),
    'return_format' => 'array',
    'wpml_cf_preferences' => 2,
  ]);
$block->setValidationFields(['items']);
$block->register();
