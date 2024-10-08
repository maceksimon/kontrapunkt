<?php
function block_download_list_content($content) {
  return $content;
}

add_filter('block_download_list_content', 'block_download_list_content');

$block = new Block('download-list', 'Ke stažení - výpis');
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
    'label' => 'Položky ke stažení',
    'instructions' => '',
    'required' => 1,
  ])
  ->addText('title', [
    'label' => 'Název souboru',
    'required' => 1,
    'placeholder' => 'Zadejte název souboru',
    'wpml_cf_preferences' => 2,
  ])
  ->addUrl('url', [
    'label' => 'Odkaz na soubor',
    'required' => 1,
    'placeholder' => 'Zadejte URL adresu souboru',
    'instructions' => 'Nahrajte soubor do knihovny médií a zkopírujte URL adresu z detailu',
    'wpml_cf_preferences' => 2,
  ]);
$block->setValidationFields([]);
$block->register();
