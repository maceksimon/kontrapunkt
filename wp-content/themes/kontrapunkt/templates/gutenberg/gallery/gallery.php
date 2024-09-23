<?php
function block_gallery_content($content) {
  return $content;
}

add_filter('block_gallery_content', 'block_gallery_content');

$block = new Block('gallery', __('Galerie', 'kontrapunkt'));
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
    ->addWysiwyg('description', [
      'label' => __('Popis galerie', 'kontrapunkt'),
      'instructions' => 'Text popisu se zobrazí nad galerií',
      'required' => 0,
      'tabs' => 'all',
      'toolbar' => 'full',
      'media_upload' => 0,
      'wpml_cf_preferences' => 2,
    ])
    ->addGallery('items', [
      'label' => __('Galerie', 'kontrapunkt'),
      'instructions' => 'Nahrajte obrázky z knihovny médií',
      'required' => 1,
      'return_format' => 'array',
      'wpml_cf_preferences' => 1,
    ]);
$block->setValidationFields(['items']);
$block->register();
