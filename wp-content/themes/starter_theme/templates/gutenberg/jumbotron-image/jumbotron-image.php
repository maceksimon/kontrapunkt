<?php

function block_jumbotron_image_content($content) {
  return $content;
}
add_filter('block_jumbotron_image_content', 'block_jumbotron_image_content');

$block = new Block('jumbotron-image', 'Jumbotron - obrázek');
$block->fieldsBuilder()
  ->addText('title', [
    'label' => 'Nadpis',
    'required' => 1,
    'placeholder' => 'Zadejte název',
    'wpml_cf_preferences' => 2,
  ])
  ->addTextArea('perex', [
    'label' => 'Popis',
    'required' => 1,
    'new_lines' => 'wpautop',
    'wpml_cf_preferences' => 2,
  ])
  ->addImage('image', [
    'label' => 'Obrázek',
    'required' => 1,
    'wpml_cf_preferences' => 1,
  ]);
$block->setValidationFields(['title', 'perex', 'image']);
$block->register();
