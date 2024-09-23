<?php

function block_logo_cloud_content($content) {
  return $content;
}
add_filter('block_logo_cloud_content', 'block_logo_cloud_content');

$block = new Block('logo-cloud', __('Seznam: loga', 'kontrapunkt'));
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
    'instructions' => __('Doporučujeme zadávat nadpis o délce 3-5 slov', 'kontrapunkt'),
    'required' => 0,
    'maxlength' => 80,
    'wpml_cf_preferences' => 2,
  ]);
$block->fieldsBuilder()
  ->addRepeater('items', [
    'label' => __('Loga', 'kontrapunkt'),
    'instructions' => __('Nahrajte logo, ideálně ve formátu SVG', 'kontrapunkt'),
    'required' => 1,
  ])
  ->addImage('image', [
    'label' => __('Logo', 'kontrapunkt'),
    'required' => 1,
    'wpml_cf_preferences' => 1,
  ]);
$block->setValidationFields(['title', 'items']);
$block->register();
