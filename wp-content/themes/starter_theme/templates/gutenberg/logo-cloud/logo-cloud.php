<?php

function block_logo_cloud_content($content) {
  return $content;
}
add_filter('block_logo_cloud_content', 'block_logo_cloud_content');

$block = new Block('logo-cloud', __('Seznam: loga', 'starter_theme'));
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
    'instructions' => __('Doporučujeme zadávat nadpis o délce 3-5 slov', 'starter_theme'),
    'required' => 0,
    'maxlength' => 80,
    'wpml_cf_preferences' => 2,
  ]);
$block->fieldsBuilder()
  ->addRepeater('items', [
    'label' => __('Loga', 'starter_theme'),
    'instructions' => __('Nahrajte logo, ideálně ve formátu SVG', 'starter_theme'),
    'required' => 1,
  ])
  ->addImage('image', [
    'label' => __('Logo', 'starter_theme'),
    'required' => 1,
    'wpml_cf_preferences' => 1,
  ]);
$block->setValidationFields(['title', 'items']);
$block->register();
