<?php

global $wp_query;
global $paged;
if (!isset($paged) || !$paged) {
  $paged = 1;
}

$context = Timber::context();
$context['template'] = 'page-full';
$templates = ['@wordpress/layout/page.twig'];

if (is_front_page()) {
  $post = Timber::query_post();
  $context['content'] = [
    'title' => $post->ID,
    'title' => $post->title(),
    'body' => $post->content(),
  ];
  $context['template'] = 'homepage';
}
elseif(is_singular('post')) {
  $post = Timber::query_post();
  $context['content'] = [
    'id' => $post->ID,
    'title' => $post->title(),
    'body' => $post->content(),
  ];
  $context['template'] = 'article-full';
}
elseif(is_singular('event')) {
  $post = Timber::query_post();

  $context['content'] = [
    'id' => $post->ID,
    'title' => $post->title(),
    'body' => $post->content(),
    'location' => get_field('location', $post->ID),
    'perex' => get_field('perex', $post->ID),
    'contact_list' => get_field('contact_list', $post->ID),
    'image' => Helpers::formatImage(get_field('image', $post->ID)),
    'date_start' => get_field('date_start', $post->ID),
    'date_end' => get_field('date_end', $post->ID),
    'time_start' => get_field('time_start', $post->ID),
    'time_end' => get_field('time_end', $post->ID),
  ];
  $context['template'] = 'event-full';
}
elseif(is_category()) {
  $context['title'] = single_tag_title('', false);
  $context['items'] = new Timber\PostQuery();
  array_unshift($templates, '@wordpress/article/article-category.twig');
}
elseif(is_tag()) {
  $context['title'] = single_tag_title('', false);
  $context['items'] = new Timber\PostQuery();
  array_unshift($templates, '@wordpress/article/article-tag.twig');
}
elseif(is_post_type_archive('post')) {
  $context['title'] = post_type_archive_title('', false);
  $context['items'] = new Timber\PostQuery();
  array_unshift($templates, '@wordpress/article/article-list.twig');
}
elseif(is_author()) {
  if (isset($wp_query->query_vars['author'])) {
    $author = new Timber\User( $wp_query->query_vars['author'] );
    $context['author'] = $author;
    $context['title']  = $author->name();
  }
  $context['items'] = new Timber\PostQuery();
  array_unshift($templates, '@wordpress/author/author.twig');
}
elseif(is_singular('page')) {
  $post = Timber::query_post();
  $context['content'] = [
    'id' => $post->ID,
    'title' => $post->title(),
    'body' => $post->content(),
  ];
  $context['template'] = 'page-full';
}
elseif(is_search()) {
  $items = new Timber\PostQuery();
  $context['items'] = [];
  foreach ($items as $item) {
    $context['items'][] = [
      'id' => $item->ID,
      'title' => $item->title(),
      'perex' => $item->post_excerpt,
      'url' => $item->link(),
    ];
  }
  $pagination = $items->pagination(['show_all' => TRUE]);
  $context['pagination'] = Helpers::pagination($pagination);
  array_unshift($templates, '@wordpress/search/search.twig');
}
elseif(is_404()) {
  $context['template'] = '404';
}

Timber::render($templates, $context);
