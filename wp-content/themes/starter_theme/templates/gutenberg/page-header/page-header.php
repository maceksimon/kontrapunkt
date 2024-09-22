<?php

function block_page_header_content($content) {
  return $content;
}
add_filter('block_page_header_content', 'block_page_header_content');

$block = new Block('page-header', 'Hlavička stránky');
$block->fieldsBuilder()
  ->addText('title', [
    'label' => 'Nadpis',
    'required' => 1,
    'placeholder' => 'Zadejte název',
    'maxlength' => 80,
    'wpml_cf_preferences' => 2,
  ])
  ->addWysiwyg('perex', [
    'label' => 'Popisek',
    'required' => 0,
    'new_lines' => 'wpautop',
    'tabs' => 'all',
    'toolbar' => 'basic',
    'media_upload' => 0,
    'wpml_cf_preferences' => 2,
  ]);
$block->setValidationFields(['title']);
$block->register();
