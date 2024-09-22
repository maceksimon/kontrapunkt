<?php
function block_contact_content($content) {
  return $content;
}

add_filter('block_contact_content', 'block_contact_content');

$block = new Block('contact', __('Kontakt', 'starter_theme'));

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
  ->addTextarea('description', [
    'label' => __('Text', 'starter_theme'),
    'instructions' => __('Krátký odstavec textu, který se zobrazí pod nadpisem', 'starter_theme'),
    'required' => 0,
    'placeholder' => __('Zadejte adresu', 'starter_theme'),
    'maxlength' => 280,
    'new_lines' => 'br',
    'wpml_cf_preferences' => 2,
  ])
  ->addTextarea('address', [
    'label' => __('Adresa', 'starter_theme'),
    'instructions' => '',
    'required' => 0,
    'placeholder' => __('Zadejte adresu', 'starter_theme'),
    'maxlength' => 280,
    'new_lines' => 'br',
    'wpml_cf_preferences' => 2,
  ])
  ->addText('email', [
    'label' => __('E-mail', 'starter_theme'),
    'required' => 0,
    'wpml_cf_preferences' => 1,
  ])
  ->addText('phone', [
    'label' => __('Telefon', 'starter_theme'),
    'required' => 0,
    'wpml_cf_preferences' => 1,
  ]);
$block->setValidationFields([]);
$block->register();
