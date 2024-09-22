<?php

declare(strict_types=1);

use Timber\Term;
use Timber\ImageHelper;

class Helpers {

  public static function formatImage($image) {
    $data = [];

    // if we have multivalue field eg. gallery
    if(is_countable($image) && !Helpers::isAssoc($image)) {
      $items = [];
      foreach ($image as $item) {
        $data = Helpers::formatImage($item);
        if($data) {
          $items[] = $data;
        }
      }
      return $items;
    }

    if(is_object($image)) {
      // fixed weird bug when image/svg+xml is sometimes width 1px / height 1px
      // https://core.trac.wordpress.org/ticket/26256
      $width = (!empty($image->width) && $image->width > 1) ? $image->width : null;
      $height = (!empty($image->height) && $image->height > 1) ? $image->height : null;
      $data[] = [
        'src' => $image->src,
        'type' => $image->post_mime_type,
        'width' => $width,
        'height' => $height,
        'alt' => $image->alt,
        'caption' => $image->caption,
        'description' => $image->description,
      ];
    }elseif(is_array($image)) {
      // fixed weird bug when image/svg+xml is sometimes width 1px / height 1px
      // https://core.trac.wordpress.org/ticket/26256
      $width = (!empty($image['width']) && $image['width'] > 1) ? $image['width'] : null;
      $height = (!empty($image['height']) && $image['height'] >1) ? $image['height'] : null;
      $data[] = [
        'src' => $image['url'],
        'type' => $image['mime_type'],
        'width' => $width,
        'height' => $height,
        'alt' => $image['alt'],
        'caption' => $image['caption'],
        'description' => $image['description'],
      ];
    }elseif(is_numeric($image)) {
      $image = acf_get_attachment($image);
      if($image) {
        $data[] = [
          'src' => $image['url'],
          'type' => $image['mime_type'],
          'width' => $image['width'],
          'height' => $image['height'],
          'alt' => $image['alt'],
          'caption' => $image['caption'],
          'description' => $image['description'],
        ];
      }
    }elseif(filter_var($image, FILTER_VALIDATE_URL)) {
      $image = attachment_url_to_postid($image);
      $image = acf_get_attachment($image);
      if($image) {
        $data[] = [
          'src' => $image['url'],
          'type' => $image['mime_type'],
          'width' => $image['width'],
          'height' => $image['height'],
          'alt' => $image['alt'],
          'caption' => $image['caption'],
          'description' => $image['description'],
        ];
      }
    }

    // generate webp image if needed
    if(count($data)) {
      $image = reset($data);
      if(in_array($image['type'], ['image/png', 'image/jpeg', 'image/pjpeg'])) {
        $webp_src = ImageHelper::img_to_webp($image['src']);
        if(!empty($webp_src)) {
          $data[] = [
            'src' => $webp_src,
            'type' => 'image/webp',
          ];
        }
      }
    }

    // reverse array as browser uses order as priority
    $data = array_reverse($data);

    return $data;
  }

  public static function formatTerms($terms) {
    $items = [];
    if(is_countable($terms)) {
      foreach ($terms as $term) {
        if($term instanceof Term) {
          $link = (strpos($term->link(), '?taxonomy=') === FALSE) ? $term->link() : '';
          $items[] = [
            'id' => $term->ID,
            'title' => $term->title,
            'url' => $link,
          ];
        }
      }
    }

    return $items;
  }

  public static function resizeImage($image, $variants) {

    $theme = wp_get_theme();
    $theme_name = $theme->get('TextDomain');

    $images = [];

    if(is_countable($image)) {
      $image = end($image);
    }

    // if empty src something not working correctly return empty array
    if(!isset($image['src']) || empty($image['src'])) {
      return $images;
    }

    $default_image = [
      'src' => $image['src'],
      'type' => isset($image['type']) ? $image['type'] : '',
      'width' => isset($image['width']) ? $image['width'] : '',
      'height' => isset($image['height']) ? $image['height'] : '',
      'alt' => isset($image['alt']) ? $image['alt'] : '',
      'caption' => isset($image['caption']) ? $image['caption'] : '',
      'description' => isset($image['description']) ? $image['description'] : '',
    ];

     // if SVG return original image without processing
     if(isset($image['type']) && $image['type'] === 'image/svg+xml') {
      $images[] = $default_image;
      return $images;
    }

    foreach ($variants as $key => $variant) {
      $variants[$key] = [
        'width' => (isset($variant[0]) && !empty($variant[0])) ? intval($variant[0]) : 0,
        'height' => (isset($variant[1]) && !empty($variant[1])) ? intval($variant[1]) : 0,
        'media' => (isset($variant[2]) && !empty($variant[2])) ? intval($variant[2]) : 0,
        'crop' => (isset($variant[3]) && !empty($variant[3])) ? $variant[3] : 'crop',
      ];
    }

    // sort array by media value
    usort($variants, function ($a, $b) {
      return $b['media'] - $a['media'];
    });

    foreach ($variants as $variant) {
      $resize_src_url = ImageHelper::resize($default_image['src'], $variant['width'], $variant['height'], 'center');
      if(!empty($resize_src_url)) {
        // we need this approach as Timber does not support generate webp images from already resized images
        // https://github.com/timber/timber/issues/1978
        $upload_dir = wp_upload_dir();
        // Resolves issues with wrong relative URLs with WPML
        // Without this we cannot generate unique images from non default languages
        // https://github.com/timber/timber/issues/2117
        if(strpos($upload_dir['relative'], 'http') === 0) {
          $upload_dir['relative'] = str_replace(content_url(), '/wp-content', $upload_dir['relative']);
        }
        // Check if image is in WordPress uploads folder
        // If not we could use images in theme folder
        if(strpos($default_image['src'], $upload_dir['relative']) === FALSE && strpos($default_image['src'], $theme_name) !== FALSE) {
          $resize_src_path = get_template_directory() . str_replace(get_template_directory_uri(), '', $resize_src_url);
        }else{
          $location = str_replace($upload_dir['relative'], '/wp-content/cache/image', $upload_dir['basedir']);
          $resize_src_path = $location . '/' . basename($resize_src_url);
        }

        if(file_exists($resize_src_path) && $default_image['type'] !== 'image/webp') {
          $webp_src = ImageHelper::img_to_webp($resize_src_path, 95);
          if(!empty($webp_src)) {
            $images[] = [
              'src' => $webp_src,
              'type' => 'image/webp',
              'width' => $variant['width'],
              'height' => $variant['height'],
              'media' => (!empty($variant['media'])) ? '(min-width: ' . $variant['media'] . 'px)' : '',
            ];
          }
        }

        $images[] = [
          'src' => $resize_src_url,
          'type' => $default_image['type'],
          'width' => $variant['width'],
          'height' => $variant['height'],
          'media' => (!empty($variant['media'])) ? '(min-width: ' . $variant['media'] . 'px)' : '',
        ];
      }
    }

    // add last as fallback image
    $images[] = $default_image;

    return $images;
  }

  public static function isAssoc(array $array) {
    $keys = array_keys($array);
    return array_keys($keys) !== $keys;
  }

  /**
   * Normalize pagination from Timber to Bootstrap
   */
  public static function pagination(object $pagination) {
    $content = [];

    if(isset($pagination->current)) {
      $content['current'] = (int) $pagination->current;
    }
    if(isset($pagination->total)) {
      $content['total'] = (int) $pagination->total;
    }

    if(isset($pagination->pages) && count($pagination->pages)) {
      foreach ($pagination->pages as $page) {
        $content['pages'][] = [
          'url' => (isset($page['link'])) ? $page['link'] : home_url($_SERVER['REQUEST_URI']),
          'title' => $page['title'],
          'current' => $page['current'],
        ];
      }
      $first = reset($content['pages']);
      $content['first'] = [
        'url' => $first['url'],
        'title' => 'First',
        'disabled' => ($first['title'] != $pagination->current) ? false : true,
      ];
      $last = end($content['pages']);
      $content['last'] = [
        'url' => $last['url'],
        'title' => 'Last',
        'disabled' => ($last['title'] != $pagination->current) ? false : true,
      ];
    }

    if(isset($pagination->next)) {
      $content['next'] = [
        'url' => (isset($pagination->next['link'])) ? $pagination->next['link'] : '',
        'title' => 'Next',
        'disabled' => (isset($pagination->next['link'])) ? false : true,
      ];
    }else{
      $content['next'] = [
        'url' => '',
        'title' => 'Next',
        'disabled' => true,
      ];
    }

    if(isset($pagination->prev)) {
      $content['previous'] = [
        'url' => (isset($pagination->prev['link'])) ? $pagination->prev['link'] : '',
        'title' => 'Previous',
        'disabled' => (isset($pagination->prev['link'])) ? false : true,
      ];
    }else{
      $content['previous'] = [
        'url' => '',
        'title' => 'Previous',
        'disabled' => true,
      ];
    }

    return $content;
  }

  public static function getIdBySlug($page_slug) {
    $page = get_page_by_path($page_slug);
    if ($page) {
        return $page->ID;
    } else {
        return null;
    }
  }

}
