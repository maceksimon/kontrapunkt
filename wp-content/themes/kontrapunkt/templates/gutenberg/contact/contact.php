<?php
function block_contact_content($content) {
  return $content;
}

add_filter('block_contact_content', 'block_contact_content');

$block = new Block('contact', __('Kontakt', 'kontrapunkt'));

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
  ->addTextarea('description', [
    'label' => __('Text', 'kontrapunkt'),
    'instructions' => __('Krátký odstavec textu, který se zobrazí pod nadpisem', 'kontrapunkt'),
    'required' => 0,
    'placeholder' => __('Zadejte adresu', 'kontrapunkt'),
    'maxlength' => 280,
    'new_lines' => 'br',
    'wpml_cf_preferences' => 2,
  ])
  ->addTextarea('address', [
    'label' => __('Adresa', 'kontrapunkt'),
    'instructions' => '',
    'required' => 0,
    'placeholder' => __('Zadejte adresu', 'kontrapunkt'),
    'maxlength' => 280,
    'new_lines' => 'br',
    'wpml_cf_preferences' => 2,
  ])
  ->addText('email', [
    'label' => __('E-mail', 'kontrapunkt'),
    'required' => 0,
    'wpml_cf_preferences' => 1,
  ])
  ->addText('phone', [
    'label' => __('Telefon', 'kontrapunkt'),
    'required' => 0,
    'wpml_cf_preferences' => 1,
  ]);
$block->setValidationFields([]);
$block->register();
