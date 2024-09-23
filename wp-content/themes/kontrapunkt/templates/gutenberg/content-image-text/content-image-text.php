<?php
function block_content_image_text_content($content) {
  return $content;
}

add_filter('block_content_image_text_content', 'block_content_image_text_content');

$block = new Block('content-image-text', __('Obsah: obrázek + text', 'kontrapunkt'));
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
    'label' => __('Zobrazit tlačítko', 'kontrapunkt'),
    'required' => 0,
    'default_value' => 0,
    'ui' => 1,
    'ui_on_text' => __('Aktivní', 'kontrapunkt'),
    'ui_off_text' => __('Vypnuto', 'kontrapunkt'),
    'wpml_cf_preferences' => 1,
  ])
  ->addGroup('button', [
      'label' => __('Tlačítko', 'kontrapunkt'),
      'instructions' => '',
      'required' => 0,
      'layout' => 'block',
  ])->conditional('enable_heading', '==', '1')
  ->addText('text', [
    'label' => __('Text', 'kontrapunkt'),
    'instructions' => 'Doporučujeme zadávat nadpis o délce 3-5 slov',
    'required' => 0,
    'placeholder' => 'Zadejte název',
    'maxlength' => 80,
    'wpml_cf_preferences' => 2,
  ])
  ->addUrl('url', [
    'label' => __('Odkaz', 'kontrapunkt'),
    'instructions' => __('Zadejte odkaz ve formě URL', 'kontrapunkt'),
    'required' => 0,
    'maxlength' => 80,
    'wpml_cf_preferences' => 2,
  ]);
$block->setValidationFields(['text', 'image']);
$block->register();
