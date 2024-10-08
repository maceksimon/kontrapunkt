<?php
function block_contact_list_content($content) {
  return $content;
}

add_filter('block_contact_list_content', 'block_contact_list_content');

$block = new Block('contact-list', 'Kontakty - výpis');
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
    'label' => 'Kontakty',
    'instructions' => '',
    'required' => 1,
  ])
  ->addSelect('type', [
    'label' => 'Typ kontaktu',
    'required' => 1,
    'choices' => [
      'address' => 'Adresa',
      'phone' => 'Telefon',
      'email' => 'E-mail',
      'bank' => 'Bankovní údaje',
    ],
    'default_value' => 'address',
    'return_format' => 'value',
    'wpml_cf_preferences' => 2,
  ])
  ->addText('value_simple', [
    'label' => 'Hodnota',
    'required' => 1,
    'wpml_cf_preferences' => 2,
  ])
  ->conditional('type', '==', 'phone')
  ->or('type', '==', 'email')
  ->addWysiwyg('value_wysiwyg', [
    'label' => 'Hodnota',
    'required' => 1,
    'tabs' => 'all',
    'toolbar' => 'full',
    'media_upload' => 0,
    'wpml_cf_preferences' => 2,
  ])
  ->conditional('type', '!=', 'phone')
  ->and('type', '!=', 'email');
$block->setValidationFields([]);
$block->register();
