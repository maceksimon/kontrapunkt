<?php
function block_project_list_content($content) {
  return $content;
}

add_filter('block_project_list_content', 'block_project_list_content');

$block = new Block('project-list', 'Projekty - výpis');
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
    'label' => 'Projekty',
    'instructions' => '',
    'required' => 1,
  ])
  ->addText('title', [
    'label' => 'Název projektu',
    'required' => 1,
    'placeholder' => 'Zadejte název projektu',
    'wpml_cf_preferences' => 2,
  ])
  ->addTextarea('perex', [
    'label' => 'Perex',
    'required' => 1,
    'rows' => 4,
    'placeholder' => 'Zadejte krátký popis projektu',
    'wpml_cf_preferences' => 2,
  ])
  ->addImage('image', [
    'label' => 'Obrázek',
    'required' => 1,
    'return_format' => 'array',
    'preview_size' => 'medium',
    'library' => 'all',
    'wpml_cf_preferences' => 1,
  ]);
$block->setValidationFields([]);
$block->register();
