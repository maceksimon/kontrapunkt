<?php

function block_perex_content($content) {
  return $content;
}
add_filter('block_perex_content', 'block_perex_content');

$block = new Block('perex', 'Perex');
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
    'required' => 1,
    'new_lines' => 'wpautop',
    'tabs' => 'all',
    'toolbar' => 'basic',
    'media_upload' => 0,
    'wpml_cf_preferences' => 2,
  ]);
$block->setValidationFields(['title', 'perex']);
$block->register();
