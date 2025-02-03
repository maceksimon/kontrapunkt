<?php

declare(strict_types=1);

use Timber\Term;
use Timber\ImageHelper;

class Helpers
{

  public static function formatImage($image, $post_id = null, $field = null)
  {
    $data = [];

    // if we have multivalue field eg. gallery
    if (is_countable($image) && ! Helpers::isAssoc($image)) {
      $items = [];
      foreach ($image as $item) {
        $data = Helpers::formatImage($item);
        if ($data) {
          $items[] = $data;
        }
      }
      return $items;
    }

    if (is_object($image)) {
      // fixed weird bug when image/svg+xml is sometimes width 1px / height 1px
      // https://core.trac.wordpress.org/ticket/26256
      $width = (! empty($image->width) && $image->width > 1) ? $image->width : null;
      $height = (! empty($image->height) && $image->height > 1) ? $image->height : null;
      $data[] = [
        'src' => $image->src,
        'type' => $image->post_mime_type,
        'width' => $width,
        'height' => $height,
        'alt' => $image->alt,
        'caption' => $image->caption,
        'description' => $image->description,
      ];
    } elseif (is_array($image)) {
      // fixed weird bug when image/svg+xml is sometimes width 1px / height 1px
      // https://core.trac.wordpress.org/ticket/26256
      $width = (! empty($image['width']) && $image['width'] > 1) ? $image['width'] : null;
      $height = (! empty($image['height']) && $image['height'] > 1) ? $image['height'] : null;
      $data[] = [
        'src' => $image['url'],
        'type' => $image['mime_type'],
        'width' => $width,
        'height' => $height,
        'alt' => $image['alt'],
        'caption' => $image['caption'],
        'description' => $image['description'],
      ];
    } elseif (is_numeric($image)) {
      $image = acf_get_attachment($image);
      if ($image) {
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
    } elseif (filter_var($image, FILTER_VALIDATE_URL)) {
      $image = attachment_url_to_postid($image);
      $image = acf_get_attachment($image);
      if ($image) {
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
    if (count($data)) {
      $image = reset($data);
      if (in_array($image['type'], ['image/png', 'image/jpeg', 'image/pjpeg'])) {
        $webp_src = ImageHelper::img_to_webp($image['src'], 100);
        if (! empty($webp_src)) {
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
  public static function formatTerms($terms)
  {
    $items = [];
    if (is_countable($terms)) {
      foreach ($terms as $term) {
        if ($term instanceof Term) {
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

  public static function resizeImage($image, $variants)
  {

    $theme = wp_get_theme();
    $theme_name = $theme->get('TextDomain');

    $images = [];

    if (is_countable($image)) {
      $image = end($image);
    }

    // if empty src something not working correctly return empty array
    if (! isset($image['src']) || empty($image['src'])) {
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
    if (isset($image['type']) && $image['type'] === 'image/svg+xml') {
      $images[] = $default_image;
      return $images;
    }

    foreach ($variants as $key => $variant) {
      $variants[$key] = [
        'width' => (isset($variant[0]) && ! empty($variant[0])) ? intval($variant[0]) : 0,
        'height' => (isset($variant[1]) && ! empty($variant[1])) ? intval($variant[1]) : 0,
        'media' => (isset($variant[2]) && ! empty($variant[2])) ? intval($variant[2]) : 0,
        'crop' => (isset($variant[3]) && ! empty($variant[3])) ? $variant[3] : 'crop',
      ];
    }

    // sort array by media value
    usort($variants, function ($a, $b) {
      return $b['media'] - $a['media'];
    });

    foreach ($variants as $variant) {

      if (! in_array($variant['crop'], ['center', 'top', 'bottom', 'left', 'right'])) {
        $variant['crop'] = 'center';
      }

      $resize_src_url = ImageHelper::resize($default_image['src'], $variant['width'], $variant['height'], $variant['crop']);
      if (! empty($resize_src_url)) {
        // we need this approach as Timber does not support generate webp images from already resized images
        // https://github.com/timber/timber/issues/1978
        $upload_dir = wp_upload_dir();
        // Resolves issues with wrong relative URLs with WPML
        // Without this we cannot generate unique images from non default languages
        // https://github.com/timber/timber/issues/2117
        if (strpos($upload_dir['relative'], 'http') === 0) {
          $upload_dir['relative'] = str_replace(content_url(), '/wp-content', $upload_dir['relative']);
        }
        // Check if image is in WordPress uploads folder
        // If not we could use images in theme folder
        if (strpos($default_image['src'], $upload_dir['relative']) === FALSE && strpos($default_image['src'], $theme_name) !== FALSE) {
          $resize_src_path = get_template_directory() . str_replace(get_template_directory_uri(), '', $resize_src_url);
        } else {
          $location = str_replace($upload_dir['relative'], '/wp-content/cache/image', $upload_dir['basedir']);
          $resize_src_path = $location . '/' . basename($resize_src_url);
        }

        if (file_exists($resize_src_path) && $default_image['type'] !== 'image/webp') {
          $webp_src = ImageHelper::img_to_webp($resize_src_path, 100);
          if (! empty($webp_src)) {
            $images[] = [
              'src' => $webp_src,
              'type' => 'image/webp',
              'width' => $variant['width'],
              'height' => $variant['height'],
              'media' => (! empty($variant['media'])) ? '(min-width: ' . $variant['media'] . 'px)' : '',
            ];
          }
        }

        $images[] = [
          'src' => $resize_src_url,
          'type' => $default_image['type'],
          'width' => $variant['width'],
          'height' => $variant['height'],
          'media' => (! empty($variant['media'])) ? '(min-width: ' . $variant['media'] . 'px)' : '',
        ];
      }
    }

    // add last as fallback image
    $images[] = $default_image;

    return $images;
  }

  public static function isAssoc(array $array)
  {
    $keys = array_keys($array);
    return array_keys($keys) !== $keys;
  }

  /**
   * Normalize pagination from Timber to Bootstrap
   */
  public static function pagination(object $pagination)
  {
    $content = [];

    if (isset($pagination->current)) {
      $content['current'] = (int) $pagination->current;
    }
    if (isset($pagination->total)) {
      $content['total'] = (int) $pagination->total;
    }

    if (isset($pagination->pages) && count($pagination->pages)) {
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

    if (isset($pagination->next)) {
      $content['next'] = [
        'url' => (isset($pagination->next['link'])) ? $pagination->next['link'] : '',
        'title' => 'Next',
        'disabled' => (isset($pagination->next['link'])) ? false : true,
      ];
    } else {
      $content['next'] = [
        'url' => '',
        'title' => 'Next',
        'disabled' => true,
      ];
    }

    if (isset($pagination->prev)) {
      $content['previous'] = [
        'url' => (isset($pagination->prev['link'])) ? $pagination->prev['link'] : '',
        'title' => 'Previous',
        'disabled' => (isset($pagination->prev['link'])) ? false : true,
      ];
    } else {
      $content['previous'] = [
        'url' => '',
        'title' => 'Previous',
        'disabled' => true,
      ];
    }

    return $content;
  }

  public static function fieldFormatter($field, $post_id = null)
  {

    // we need to allow post_id null when we are using it during preview block without saving
    if (empty($field)) {
      return FALSE;
    }

    if (! isset($field['type']) || ! isset($field['value'])) {
      return $field;
    }

    if ($field['type'] === 'link') {

      $field['value'] = self::formatLink($field['value'], $post_id, $field);
    } elseif (in_array($field['type'], ['wywiwyg', 'textarea'])) {

      // we need to check wysiwyg fields for <br data-mce-bogus="1"> to properly check if empty
      if (is_string($field) && empty(trim(preg_replace('/\s\s+/', ' ', strip_tags($field['value']))))) {
        $field['value'] = '';
      }

      $field['value'] = do_shortcode($field['value']);
    } elseif ($field['type'] === 'image') {

      if ($field['type'] === 'image') {
        $data = self::formatImage($field['value'], $post_id, $field);
        if ($data) {
          $field['value'] = $data;
        }
      }
    } elseif ($field['type'] === 'gallery') {

      if (is_countable($field['value'])) {
        foreach ($field['value'] as &$item) {
          if ($item['type'] === 'image') {
            $data = self::formatImage($item, $post_id, $field);
            if ($data) {
              $item = $data;
            }
          }
        }
      }
    } elseif ($field['type'] === 'post_object') {

      if ($field['value'] instanceof WP_Post) {
        if ($field['value']->post_type === 'wpcf7_contact_form') {
          $field['value'] = do_shortcode('[contact-form-7 id="' . $field['value']->ID . '" title=""]');
        } elseif ($field['value']->post_type === 'wpforms') {
          $field['value'] = do_shortcode('[wpforms id="' . $field['value']->ID . '"]');
        }
      }
    } elseif ($field['type'] === 'oembed') {

      // parse iframe src only
      $field['value'] = preg_match('/src="(.+?)"/', $field['value'], $matches) ? $matches[1] : '';
    } elseif (in_array($field['type'], ['repeater', 'group'])) {

      // create array with sub_fields by name
      $sub_fields = [];
      if (isset($field['sub_fields']) && is_array($field['sub_fields'])) {
        foreach ($field['sub_fields'] as $sub_field) {
          $sub_fields[$sub_field['name']] = $sub_field;
        }
      }

      // we need to combine sub field configuration to sub field value
      if (is_countable($field['value'])) {
        foreach ($field['value'] as $key => &$value) {
          // group field could be associative array
          if (is_countable($field['value']) && ! Helpers::isAssoc($field['value'])) {
            foreach ($value as $sub_key => &$sub_value) {
              if (isset($sub_fields[$sub_key])) {
                $sub_fields[$sub_key]['value'] = $sub_value;
                $sub_value = self::fieldFormatter($sub_fields[$sub_key], $post_id);
              }
            }
          } else {
            if (isset($sub_fields[$key])) {
              $sub_fields[$key]['value'] = $value;
              $value = self::fieldFormatter($sub_fields[$key], $post_id);
            }
          }
        }
      }
    } elseif (in_array($field['type'], ['flexible_content'])) {

      // create array with layouts and sub_fields by name
      $layouts = [];
      if (isset($field['layouts']) && is_array($field['layouts'])) {
        foreach ($field['layouts'] as $layout) {
          $sub_fields = [];
          if (isset($layout['sub_fields']) && is_array($layout['sub_fields'])) {
            foreach ($layout['sub_fields'] as $sub_field) {
              $sub_fields[$sub_field['name']] = $sub_field;
            }
          }
          $layouts[$layout['name']] = $sub_fields;
        }
      }

      // we need to combine layout field configuration to layout field value
      if (is_countable($field['value'])) {
        foreach ($field['value'] as $key => &$value) {
          // group field could be associative array
          if (is_countable($field['value']) && ! Helpers::isAssoc($field['value'])) {
            foreach ($value as $layout_key => &$layout_value) {
              if (isset($layouts[$layout_key])) {
                $layouts[$value['acf_fc_layout']][$layout_key]['value'] = $layout_value;
                $layout_value = self::fieldFormatter($layouts[$value['acf_fc_layout']][$layout_key], $post_id);
              }
            }
          } else {
            if (isset($layouts[$key])) {
              $layouts[$key]['value'] = $value;
              $value = self::fieldFormatter($layouts[$key], $post_id);
            }
          }
        }
      }
    }

    // allow to alter formatter for specific field type
    $field = apply_filters('field_formatter_' . $field['type'], $field, $post_id);

    return $field['value'];
  }

  /**
   * Translate ACF link
   */
  public static function formatLink($value, $post_id, $field)
  {

    // copy target to attributes field
    if (isset($value['target'])) {
      if (! empty($value['target'])) {
        $value['attributes']['target'] = $value['target'];
      } else {
        unset($value['target']);
      }
    }

    $post_type = get_post_type($post_id);

    if (! $post_type) {
      return $value;
    }

    if (! is_array($value)) {
      return $value;
    }

    // apply only on links which are set to translatable
    if (isset($field['wpml_cf_preferences']) && $field['wpml_cf_preferences'] !== 2) {
      return $value;
    }

    if (isset($value['url'])) {

      $parsed_url = parse_url($value['url']);
      $post_id = url_to_postid($value['url']);

      if ($post_id && $post_type) {

        $translated_url = apply_filters('wpml_object_id', $post_id, $post_type);
        $translated_url = get_permalink($translated_url);

        // Add query if it's there
        if (isset($parsed_url['query'])) {
          $translated_url .= '?' . $parsed_url['query'];
        }

        // Add fragment if it's there
        if (isset($parsed_url['fragment'])) {
          $translated_url .= '#' . $parsed_url['fragment'];
        }

        // replace with translated url
        $value['url'] = $translated_url;
      }
    }

    return $value;
  }
}
