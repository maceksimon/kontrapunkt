<?php

function block_accordion_content($content) {
  return $content;
}
add_filter('block_accordion_content', 'block_accordion_content');

$block = new Block('accordion', 'Accordion');
$block->fieldsBuilder()
  ->addText('title', [
    'label' => 'Nadpis',
    'required' => 1,
    'placeholder' => 'Zadejte název',
    'wpml_cf_preferences' => 2,
  ])
  ->addRepeater('items', [
    'label' => 'Položky',
    'instructions' => '',
    'required' => 1,
  ])
    ->addText('title', [
      'label' => 'Nadpis',
      'required' => 1,
      'wpml_cf_preferences' => 2,
    ])
    ->addWysiwyg('perex', [
      'label' => 'Popisek',
      'placeholder' => '',
      'new_lines' => 'wpautop',
      'tabs' => 'all',
      'toolbar' => 'basic',
      'media_upload' => 0,
      'required' => 0,
      'wpml_cf_preferences' => 2,
    ])
    ->addWysiwyg('content', [
      'label' => 'Popisek',
      'placeholder' => '',
      'new_lines' => 'wpautop',
      'tabs' => 'all',
      'toolbar' => 'basic',
      'media_upload' => 0,
      'required' => 1,
      'wpml_cf_preferences' => 2,
    ]);
$block->setValidationFields(['title', 'items']);
$block->register();
