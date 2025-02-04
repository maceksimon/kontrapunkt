<?php
// Define the constant if it's not already defined
if (!defined('ECOMAIL_SUBSCRIBE_URL')) {
    define('ECOMAIL_SUBSCRIBE_URL', '');
}

function block_newsletter_content($content) {
    $subscribe_url = get_option('newsletter_subscribe_url', '');

    $content['form'] = [
        'action' => $subscribe_url,
        'target' => '_blank',
        'privacy_policy_url' => get_permalink(3),
    ];

    return $content;
}

add_filter('block_newsletter_content', 'block_newsletter_content');

$block = new Block('newsletter', 'Newsletter');

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
$block->setValidationFields([]);
$block->register();
