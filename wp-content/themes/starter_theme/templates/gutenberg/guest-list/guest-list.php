<?php
function block_guest_list_content($content) {
  return $content;
}

add_filter('block_guest_list_content', 'block_guest_list_content');

$block = new Block('guest-list', 'Hosté - výpis');
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
    'label' => 'Hosté',
    'instructions' => '',
    'required' => 1,
  ])
  ->addText('title', [
    'label' => 'Jméno',
    'required' => 0,
    'placeholder' => 'Zadejte název',
    'wpml_cf_preferences' => 2,
  ])
  ->addWysiwyg('description', [
    'label' => 'Bio',
    'required' => 1,
    'tabs' => 'all',
    'toolbar' => 'full',
    'media_upload' => 0,
    'wpml_cf_preferences' => 2,
  ])
  ->addImage('image', [
    'label' => 'Obrázek',
    'required' => 0,
    'wpml_cf_preferences' => 1,
  ]);
$block->setValidationFields([]);
$block->register();
