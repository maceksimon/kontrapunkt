<?php
function block_content_image_text_content($content) {
  return $content;
}

add_filter('block_content_image_text_content', 'block_content_image_text_content');

$block = new Block('content-image-text', __('Obsah: obrázek + text', 'starter_theme'));
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
  ->addWysiwyg('text', [
    'label' => 'Text',
    'required' => 1,
    'toolbar' => 'basic',
    'media_upload' => 0,
  ])
  ->addImage('image', [
    'label' => 'Obrázek',
    'instructions' => '',
    'required' => 1,
  ])
  ->addTrueFalse('reversed', [
    'label' => 'Obrázek nalevo',
    'required' => 0,
    'default_value' => 0,
    'ui' => 1,
    'ui_on_text' => 'Ano',
    'ui_off_text' => 'Ne',
  ]);
$block->fieldsBuilder()
  ->addTrueFalse('enable_button', [
    'label' => __('Zobrazit tlačítko', 'starter_theme'),
    'required' => 0,
    'default_value' => 0,
    'ui' => 1,
    'ui_on_text' => __('Aktivní', 'starter_theme'),
    'ui_off_text' => __('Vypnuto', 'starter_theme'),
    'wpml_cf_preferences' => 1,
  ])
  ->addGroup('button', [
      'label' => __('Tlačítko', 'starter_theme'),
      'instructions' => '',
      'required' => 0,
      'layout' => 'block',
  ])->conditional('enable_heading', '==', '1')
  ->addText('text', [
    'label' => __('Text', 'starter_theme'),
    'instructions' => 'Doporučujeme zadávat nadpis o délce 3-5 slov',
    'required' => 0,
    'placeholder' => 'Zadejte název',
    'maxlength' => 80,
    'wpml_cf_preferences' => 2,
  ])
  ->addUrl('url', [
    'label' => __('Odkaz', 'starter_theme'),
    'instructions' => __('Zadejte odkaz ve formě URL', 'starter_theme'),
    'required' => 0,
    'maxlength' => 80,
    'wpml_cf_preferences' => 2,
  ]);
$block->setValidationFields(['text', 'image']);
$block->register();
