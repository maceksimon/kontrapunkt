<?php

function block_video_list_content($content) {
  if(isset($content['items'])) {
    foreach ($content['items'] as &$item) {
      if(isset($item['video'])) {
        if(isset($content['video'])) {
          preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $content['video'], $match);
          if(count($match)) {
            $item['video'] = 'https://www.youtube-nocookie.com/embed/' . $match[1];
          }
        }
      }
    }
  }

  return $content;
}
add_filter('block_video_list_content', 'block_video_list_content');

$block = new Block('video-list', 'Video - list');
$block->fieldsBuilder()
  ->addTrueFalse('enable_heading', [
    'label' => 'Zobrazit nadpis',
    'required' => 0,
    'default_value' => 0,
    'ui' => 1,
    'ui_on_text' => 'Ano',
    'ui_off_text' => 'Ne',
  ])
  ->addGroup('heading', [
    'label' => 'Nadpis',
    'instructions' => '',
    'required' => 0,
    'layout' => 'block',
  ])->conditional('enable_heading', '==', '1')
  ->addText('heading', [
    'label' => 'Text nadpisu',
    'instructions' => 'Doporučená délka 3-5 slov',
    'required' => 0,
    'placeholder' => 'Zadejte nadpis',
    'maxlength' => 160,
  ]);
$block->fieldsBuilder()
  ->addRepeater('items', [
    'label' => 'Položky',
    'layout' => 'block',
    'button_label' => 'Přidat položku'
  ])
  ->addOembed('video', [
    'label' => 'Video',
    'required' => TRUE,
  ])
  ->addWysiwyg('perex', [
    'label' => 'Perex',
    'required' => FALSE,
    'toolbar' => 'basic',
    'media_upload' => 0,
    'placeholder' => 'Zadejte perex',
  ]);
$block->fieldsBuilder()
  ->addWysiwyg('text_before', [
    'label' => 'Text nad',
    'required' => FALSE,
    'toolbar' => 'basic',
    'media_upload' => 0,
    'placeholder' => 'Zadejte text',
  ])
  ->addWysiwyg('text_after', [
    'label' => 'Text pod',
    'required' => FALSE,
    'toolbar' => 'basic',
    'media_upload' => 0,
  ]);
$block->setValidationFields(['items']);
$block->register();
