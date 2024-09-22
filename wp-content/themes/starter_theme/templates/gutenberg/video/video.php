<?php

function block_video_content($content) {
  if(isset($content['video'])) {
    preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $content['video'], $match);
    if(count($match)) {
      $content['video'] = 'https://www.youtube-nocookie.com/embed/' . $match[1];
    }
  }
  return $content;
}
add_filter('block_video_content', 'block_video_content');

$block = new Block('video', 'Video (vlastní náhledový obrázek)');
$block->fieldsBuilder()
  ->addImage('image', [
    'label' => 'Obrázek',
    'required' => 1,
    'wpml_cf_preferences' => 1,
  ])
  ->addUrl('video', [
    'label' => 'Video',
    'required' => 1,
    'wpml_cf_preferences' => 2,
  ]);
$block->setValidationFields(['image', 'video']);
$block->register();
