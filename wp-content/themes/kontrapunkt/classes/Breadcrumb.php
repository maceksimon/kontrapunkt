<?php

declare(strict_types=1);

class Breadcrumb
{

  public $items;

  public function __construct()
  {
    $this->items[] = [
      'url' => get_home_url(),
      'title' => __('Home', 'kontrapunkt'),
    ];
  }

  /**
   * Get breadcrumbs array
   */
  public function get()
  {

    if (is_front_page()) {
      return;
    }

    $links = $this->get_global_links();
    $post_type = get_post_type();
    switch ($post_type) {
      case 'page':
        $items = $this->by_menu_trail();
        if (count($items)) {
          $this->items = array_merge($this->items, $items);
        } else {
          $parent = get_post_parent();
          if ($parent) {
            $this->items[] = [
              'url' => get_permalink($parent),
              'title' => get_the_title($parent)
            ];
          }
        }
        break;
      case 'post':
        if (isset($links['article_list'])) {
          $this->items[] = [
            'url' => $links['article_list']['url'],
            'title' => $links['article_list']['title'],
          ];
        }
        break;
    }

    $object = get_queried_object();
    $object_url = '';
    $object_title = '';
    if ($object instanceof \WP_Post) {
      $object_url = get_permalink($object->ID);
      $object_title = $object->post_title;
    } elseif ($object instanceof \WP_Term) {
      $object_url = get_term_link($object->term_id);
      $object_title = $object->name;
    } elseif ($object instanceof \WP_User) {
      $object_url = get_author_posts_url($object->ID);
      $object_title = $object->data->display_name;
    } elseif (is_search()) {
      $object_url = get_search_link();
      $object_title = __('Search', 'kontrapunkt');
    }

    if (! empty($object_url) && ! empty($object_title)) {
      $this->items[] = [
        'url' => $object_url,
        'title' => $object_title
      ];
    }

    return $this->items;
  }

  private function by_menu_trail($menu_name = 'main-menu')
  {

    $breadcrumb_items = [];

    // load menu from menu position
    $locations = get_nav_menu_locations();
    if (isset($locations[$menu_name])) {
      $menu_name = $locations[$menu_name];
    }

    $items = wp_get_nav_menu_items($menu_name);
    if (false === $items) {
      return $breadcrumb_items;
    }

    // get the menu item for the current page
    $item = $this->get_menu_item('object_id', get_queried_object_id(), $items);
    if (false === $item) {
      return $breadcrumb_items;
    }

    // start an array of objects for the crumbs
    $menu_item_objects = [$item];
    // loop over menu items to get the menu item parents
    while (0 != $item->menu_item_parent) {
      $item = $this->get_menu_item('ID', $item->menu_item_parent, $items);
      array_unshift($menu_item_objects, $item);
    }

    foreach ($menu_item_objects as $menu_item) {
      if (! isset($menu_item->url)) {
        continue;
      }
      // skip current we add it later
      if ($menu_item->url === get_permalink()) {
        continue;
      }
      $breadcrumb_items[] = [
        'url' => $menu_item->url,
        'title' => $menu_item->title
      ];
    }
    return $breadcrumb_items;
  }

  /**
   * Helper function to find a menu item in an array of items
   */
  private function get_menu_item($field, $object_id, $items)
  {
    foreach ($items as $item) {
      if ($item->$field == $object_id)
        return $item;
    }
    return false;
  }

  /**
   * Helper function to get global links
   * TODO: Update for Polylang
   */
  private function get_global_links()
  {

    $data = [];
    $links = get_field('links', 'option');

    if (isset($links) && is_countable($links)) {
      foreach ($links as $key => $item) {
        if (is_string($item) && ! empty($item)) {
          $post_id = url_to_postid($item);
          if ($post_id) {
            $data[$key] = get_permalink(apply_filters('wpml_object_id', $post_id, 'page'));
          } else {
            $data[$key] = $item;
          }
        } elseif (is_array($item) && isset($item['url'])) {
          $post_id = url_to_postid($item['url']);
          if ($post_id) {
            $url = get_permalink(apply_filters('wpml_object_id', $post_id, 'page'));
          } else {
            $url = $item['url'];
          }
          $data[$key] = [
            'id' => $post_id,
            'title' => $item['title'],
            'url' => $url,
          ];
        }
      }
    }

    return $data;
  }
}
