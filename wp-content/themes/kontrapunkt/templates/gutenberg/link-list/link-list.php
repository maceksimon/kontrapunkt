<?php
function block_link_list_content($content) {
  return $content;
}

add_filter('block_link_list_content', 'block_link_list_content');

$block = new Block('link-list', __('Seznam: odkazy', 'kontrapunkt'));
$block->fieldsBuilder()
  ->addTrueFalse('enable_heading', [
    'label' => __('Zobrazit nadpis', 'kontrapunkt'),
    'required' => 0,
    'default_value' => 0,
    'ui' => 1,
    'ui_on_text' => __('Aktivní', 'kontrapunkt'),
    'ui_off_text' => __('Vypnuto', 'kontrapunkt'),
    'wpml_cf_preferences' => 1,
  ])
  ->addGroup('heading', [
      'label' => __('Nadpis', 'kontrapunkt'),
      'instructions' => '',
      'required' => 0,
      'layout' => 'block',
  ])->conditional('enable_heading', '==', '1')
  ->addText('heading', [
    'label' => __('Nadpis', 'kontrapunkt'),
    'instructions' => 'Doporučujeme zadávat nadpis o délce 3-5 slov',
    'required' => 0,
    'placeholder' => 'Zadejte název',
    'maxlength' => 80,
    'wpml_cf_preferences' => 2,
  ]);
$block->fieldsBuilder()
  ->addRepeater('items', [
    'label' => __('Odkazy', 'kontrapunkt'),
    'required' => 1,
  ])
  ->addLink('link', [
    'label' => __('Odkaz', 'kontrapunkt'),
    'instructions' => __('Zadejte odkaz', 'kontrapunkt'),
    'return_format' => 'array',
    'wpml_cf_preferences' => 2,
  ]);
$block->setValidationFields(['items']);
$block->register();
