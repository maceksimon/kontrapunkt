<?php

declare(strict_types=1);

use Timber\Site;
use Timber\Helper;
use Timber\ImageHelper;
use Twig\Environment;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\Extension\StringLoaderExtension;
use Twig\Extra\String\StringExtension;
use Symfony\Bridge\Twig\Extension\DumpExtension;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Parisek\Twig\CommonExtension;
use Parisek\Twig\AttributeExtension;
use Parisek\Twig\TypographyExtension;
use StoutLogic\AcfBuilder\FieldsBuilder;

class Base extends Site
{

  public $theme_name;

  public function __construct()
  {
    add_action('after_setup_theme', array($this, 'theme_supports'));
    add_filter('timber/context', array($this, 'timber_context'));
    add_filter('timber/twig', array($this, 'timber_twig'));
    add_filter('timber/loader/loader', array($this, 'timber_twig_loader'));
    add_action('timber/twig/environment/options', array($this, 'timber_cache_location'), 10, 1);
    add_action('timber/image/new_url', array($this, 'timber_image_new_url'));
    add_action('timber/image/new_path', array($this, 'timber_image_new_path'));
    add_action('init', array($this, 'register_menus'));
    add_action('acf/init', array($this, 'register_post_types'));
    add_action('wp_enqueue_scripts', array($this, 'scripts'));
    add_action('wp_enqueue_scripts', array($this, 'fonts'), 1);
    add_action('enqueue_block_editor_assets', array($this, 'block_editor_assets'));
    add_action('enqueue_block_editor_assets', array($this, 'fonts'));
    add_filter('allowed_block_types_all', array($this, 'allowed_block_types_all'));
    add_action('acf/init', array($this, 'acf_gutenberg_blocks'));
    add_action('acf/init', array($this, 'acf_options_page'));
    add_action('acf/fields/google_map/api', array($this, 'acf_google_map_api'));
    add_action('template_redirect', array($this, 'template_redirect'), 0);
    add_filter('theme_page_templates', array($this, 'theme_page_templates'));
    add_action('restrict_manage_posts', array($this, 'restrict_manage_posts'));
    add_filter('render_block_data', array($this, 'render_block_data'), 10, 3);
    add_filter('render_block', array($this, 'render_block'), 10, 2);
    add_filter('block_categories_all', array($this, 'block_categories_all'));
    add_action('admin_head', array($this, 'hide_core_update_notifications'), 1);
    add_action('acf/input/admin_footer', array($this, 'acf_input_admin_footer'));
    add_filter('tiny_mce_before_init', array($this, 'tiny_mce_before_init'));
    add_filter('wp_get_attachment_image_attributes', array($this, 'wp_get_attachment_image_attributes'), 10, 2);
    add_filter('jpeg_quality', array($this, 'jpeg_quality'));
    add_filter('wp_editor_set_quality', array($this, 'wp_editor_set_quality'));
    add_filter('acf/format_value/type=post_object', array($this, 'fix_wrong_acf_orders_with_ids'), 10, 3);
    add_filter('pre_get_posts', array($this, 'search_post_type_filter'));

    $theme = wp_get_theme();
    $this->theme_name = $theme->get('TextDomain');

    parent::__construct();
  }

  /**
   * Register Menu.
   */
  public function register_menus()
  {
    register_nav_menu('main-menu', __('Main Menu', $this->theme_name));
    register_nav_menu('footer-menu', __('Footer Menu', $this->theme_name));
  }

  /**
   * Register Post Type.
   */
  public function register_post_types()
  {

    $directory = get_template_directory() . '/templates';
    $directory_iterator = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
    $flattened = new RecursiveIteratorIterator($directory_iterator);

    $regex_iterator = new RegexIterator($flattened, '/\.php$/');
    foreach ($regex_iterator as $file) {
      if (strpos($file->getPath(), 'gutenberg') === FALSE) {
        include $file->getPathname();
      }
    }
  }

  /**
   * Add variables to global context
   */
  public function timber_context($context)
  {

    // basic
    $context['homeUrl'] = get_home_url();
    $context['templateUrl'] = get_template_directory_uri() . '/static';
    $context['frontPage'] = is_front_page();
    $context['langcode'] = get_bloginfo('language');
    $context['ccnstL'] = get_privacy_policy_url();
    $context['search_query'] = get_search_query();
    $breadcrumbs = new Breadcrumb();
    $context['breadcrumb'] = $breadcrumbs->get();

    // global settings
    $links = get_field('links', 'option');
    $footer = get_field('footer', 'option');

    // global links
    $context['links'] = [];
    if (isset($links) && is_countable($links)) {
      foreach ($links as $key => $item) {
        if (is_string($item)) {
          $post_id = url_to_postid($item);
          if ($post_id) {
            $context['links'][$key] = get_permalink(apply_filters('wpml_object_id', $post_id, 'page'));
          } else {
            $context['links'][$key] = $item;
          }
        } elseif (is_array($item) && isset($item['url'])) {
          $post_id = url_to_postid($item['url']);
          if ($post_id) {
            $url = get_permalink(apply_filters('wpml_object_id', $post_id, 'page'));
          } else {
            $url = $item['url'];
          }
          $context['links'][$key] = [
            'title' => $item['title'],
            'url' => $url,
            'target' => $item['target'],
          ];
        }
      }
    }

    // header
    $language_switcher = '';
    if (is_plugin_active('sitepress-multilingual-cms/sitepress.php')) {
      $language_switcher = $this->get_languages();
    }

    if (function_exists('pll_the_languages')) {
      $language_switcher = $this->get_pll_languages();
    }

    $context['header'] = [
      'main_menu' => $this->get_nav_menu('main-menu') ? $this->get_nav_menu('main-menu') : [],
      'language_switcher' => $language_switcher,
    ];

    // footer
    $context['footer'] = [
      'footer_menu' => $this->get_nav_menu('footer-menu') ? $this->get_nav_menu('footer-menu') : [],
      'social_menu' => (isset($footer['social_menu'])) ? $footer['social_menu'] : '',
      'description' => (isset($footer['description'])) ? $footer['description'] : '',
    ];

    return $context;
  }

  public function theme_supports()
  {
    // Add default posts and comments RSS feed links to head.
    // add_theme_support( 'automatic-feed-links' );

    /*
    * Let WordPress manage the document title.
    * By adding theme support, we declare that this theme does not use a
    * hard-coded <title> tag in the document head, and expect WordPress to
    * provide it for us.
    */
    add_theme_support('title-tag');

    /*
    * Enable support for Post Thumbnails on posts and pages.
    *
    * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
    */
    add_theme_support('post-thumbnails');

    /*
    * Switch default core markup for search form, comment form, and comments
    * to output valid HTML5.
    */
    add_theme_support(
      'html5',
      array(
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
      )
    );

    /*
    * Enable support for Post Formats.
    *
    * See: https://codex.wordpress.org/Post_Formats
    */
    add_theme_support(
      'post-formats',
      array(
        'aside',
        'image',
        'video',
        'quote',
        'link',
        'gallery',
        'audio',
      )
    );

    /*
    * Enable support for Menu.
    *
    * See: https://codex.wordpress.org/WordPress_Menu_User_Guide
    */
    add_theme_support('menus');

    /*
    * Enable support for translations files.
    *
    * See: https://developer.wordpress.org/reference/functions/load_theme_textdomain/
    */
    load_theme_textdomain($this->theme_name, get_template_directory() . '/static/translations');
  }

  /**
   * Register Twig Functions.
   */
  public function timber_twig($twig)
  {
    $twig->addExtension(new StringLoaderExtension());
    $twig->addExtension(new CommonExtension());
    $twig->addExtension(new AttributeExtension());
    $typography_settings = get_template_directory() . '/static/typography.yml';
    $twig->addExtension(new TypographyExtension($typography_settings));
    $twig->addExtension(new StringExtension());
    $cloner = new VarCloner();
    $twig->addExtension(new DumpExtension($cloner));
    $twig->addFilter(new TwigFilter('resizer', function ($image, ...$variants) {
      return Helpers::resizeImage($image, $variants);
    }));
    $twig->addFunction(new TwigFunction('component_*', function (Environment $env, $context, $template_name, $content = []) {
      try {
        $template_name = str_replace('_', '-', $template_name);
        $template = $env->load('@component/' . $template_name . '/' . $template_name . '.twig');
        $context = array_merge($context, ['content' => $content]);

        // we user render to allow save output to twig variable
        return $template->render($context);
      } catch (\Throwable $e) {
        try {
          $template = $env->load('@component/alert/alert.twig');
          $content = [
            'type' => 'error',
            'container' => 'container',
            'message' => 'Component template <strong>' . $template_name . '.twig</strong> not found',
          ];
          $context = array_merge($context, ['content' => $content]);

          return $template->render($context);
        } catch (\Throwable $e) {
          return '<div>Component template <strong>' . $template_name . '.twig</strong> not found</div>';
        }
      }
    }, [
      'needs_environment' => true,
      'needs_context' => true,
      'is_safe' => ['html']
    ]));
    $twig->addFunction(new TwigFunction('component', function (Environment $env, $context, $template_name, $content = []) {
      try {
        $template_name = str_replace('_', '-', $template_name);
        $template = $env->load('@component/' . $template_name . '/' . $template_name . '.twig');
        $context = array_merge($context, ['content' => $content]);

        // we user render to allow save output to twig variable
        return $template->render($context);
      } catch (\Throwable $e) {
        try {
          $template = $env->load('@component/alert/alert.twig');
          $content = [
            'type' => 'error',
            'container' => 'container',
            'message' => 'Component template <strong>' . $template_name . '.twig</strong> not found',
          ];
          $context = array_merge($context, ['content' => $content]);

          return $template->render($context);
        } catch (\Throwable $e) {
          return '<div>Component template <strong>' . $template_name . '.twig</strong> not found</div>';
        }
      }
    }, [
      'needs_environment' => true,
      'needs_context' => true,
      'is_safe' => ['html']
    ]));
    $twig->addFunction(new TwigFunction('page_*', function (Environment $env, $context, $template_name, $content = []) {
      try {
        $template_name = str_replace('_', '-', $template_name);
        $template = $env->load('@page/' . $template_name . '/' . $template_name . '.twig');
        $context = array_merge($context, ['content' => $content]);

        // we user render to allow save output to twig variable
        return $template->render($context);
      } catch (\Throwable $e) {
        try {
          $template = $env->load('@component/alert/alert.twig');
          $content = [
            'type' => 'error',
            'container' => 'container',
            'message' => 'Page template <strong>' . $template_name . '.twig</strong> not found',
          ];
          $context = array_merge($context, ['content' => $content]);

          return $template->render($context);
        } catch (\Throwable $e) {
          return '<div>Page template <strong>' . $template_name . '.twig</strong> not found</div>';
        }
      }
    }, [
      'needs_environment' => true,
      'needs_context' => true,
      'is_safe' => ['html']
    ]));
    $twig->addFunction(new TwigFunction('template_exists', function (Environment $env, $context, $template_name) {
      try {
        $env->load($template_name);
        return TRUE;
      } catch (\Throwable $e) {
        return FALSE;
      }
    }, [
      'needs_environment' => true,
      'needs_context' => true,
      'is_safe' => ['html']
    ]));
    $twig->addFunction(new TwigFunction('merge_resizer', function (...$items) {

      $images = [];

      // fix if mobile image is empty
      foreach ($items as $key => $item) {
        if (empty($item)) {
          unset($items[$key]);
        }
      }

      foreach ($items as $key => $item) {
        foreach ($item as $image) {
          if ($key !== array_key_last($items)) {
            if (isset($image['media'])) {
              $images[] = $image;
            }
          } else {
            $images[] = $image;
          }
        }
      }

      return $images;
    }));
    $twig->addFunction(new TwigFunction('gtm4wp_the_gtm_tag', function () {
      if (function_exists('gtm4wp_the_gtm_tag')) {
        gtm4wp_the_gtm_tag();
      }
    }));

    return $twig;
  }

  /**
   * Register Twig Namespace.
   */
  public function timber_twig_loader($loader)
  {
    $loader->addPath(get_template_directory() . '/static/templates/component', 'component');
    $loader->addPath(get_template_directory() . '/static/templates/macro', 'macro');
    $loader->addPath(get_template_directory() . '/static/templates/page', 'page');
    $loader->addPath(get_template_directory() . '/static/images/icons', 'icons');
    $loader->addPath(get_template_directory() . '/static/images', 'images');
    $loader->addPath(get_template_directory() . '/templates', 'wordpress');
    return $loader;
  }

  /**
   * Change Timber's cache folder.
   */
  public function timber_cache_location($options)
  {
    $options['cache'] = WP_CONTENT_DIR . '/cache/timber';

    return $options;
  }

  /**
   * Change Timber's image url.
   */
  public function timber_image_new_url($location)
  {
    $upload_dir = wp_upload_dir();

    $new_dir = str_replace($upload_dir['relative'], '/wp-content/cache/image', $upload_dir['basedir']);
    if (! file_exists($new_dir)) {
      wp_mkdir_p($new_dir);
    }

    $location = str_replace($upload_dir['relative'], '/wp-content/cache/image', $location);
    // Resolves issues with wrong relative URLs with WPML
    // Without this we cannot generate unique images from non default languages
    // https://github.com/timber/timber/issues/2117
    if (strpos($location, '/wp-content/') === 0) {
      $location = str_replace('/wp-content', content_url(), $location);
    }

    return $location;
  }

  /**
   * Change Timber's image path.
   */
  public function timber_image_new_path($location)
  {
    $upload_dir = wp_upload_dir();

    // Resolves issues with wrong relative URLs with WPML
    // Without this we cannot generate unique images from non default languages
    // https://github.com/timber/timber/issues/2117
    if (strpos($upload_dir['relative'], 'http') === 0) {
      $upload_dir['relative'] = str_replace(content_url(), '/wp-content', $upload_dir['relative']);
    }

    $new_dir = str_replace($upload_dir['relative'], '/wp-content/cache/image', $upload_dir['basedir']);
    if (! file_exists($new_dir)) {
      wp_mkdir_p($new_dir);
    }

    $location = str_replace($upload_dir['relative'], '/wp-content/cache/image', $location);

    return $location;
  }

  /**
   * Add fonts.
   */
  public function fonts()
  {
    wp_enqueue_style($this->theme_name . '-fonts', get_template_directory_uri() . '/static/fonts/anonymous-pro/stylesheet.css', [], filemtime(wp_normalize_path(get_template_directory() . '/static/fonts/anonymous-pro/stylesheet.css')));
  }

  /**
   * Enqueue scripts and styles.
   */
  public function scripts($query_args)
  {
    if (defined('WP_DEBUG') && WP_DEBUG) {
      wp_enqueue_style($this->theme_name, get_template_directory_uri() . '/static/dist/css/style.css', [], filemtime(wp_normalize_path(get_template_directory() . '/static/dist/css/style.css')));
    } else {
      wp_enqueue_style($this->theme_name, get_template_directory_uri() . '/static/dist/css/style.min.css', [], filemtime(wp_normalize_path(get_template_directory() . '/static/dist/css/style.min.css')));
    }
    wp_enqueue_script_module($this->theme_name, get_template_directory_uri() . '/static/dist/js/script.js', [], filemtime(wp_normalize_path(get_template_directory() . '/static/dist/js/script.js')));

    wp_dequeue_script('jquery');

    // https://wpml.org/forums/topic/how-to-remove-loading-of-blocks-styling/
    // remove wp-content/plugins/sitepress-multilingual-cms/dist/css/blocks/styles.css
    if (class_exists('WPML\BlockEditor\Loader')) {
      wp_deregister_style(WPML\BlockEditor\Loader::SCRIPT_NAME);
    }
  }

  /**
   * Enqueue block editor style
   */
  public function block_editor_assets()
  {
    wp_enqueue_style($this->theme_name . '-theme-gutenberg', get_template_directory_uri() . '/static/dist/css/gutenberg.css', [], filemtime(wp_normalize_path(get_template_directory() . '/static/dist/css/gutenberg.css')));
    wp_enqueue_script_module($this->theme_name, get_template_directory_uri() . '/static/dist/js/script.js', [], filemtime(wp_normalize_path(get_template_directory() . '/static/dist/js/script.js')));
  }

  /**
   * Allow only specific blocks in Gutenberg editor
   */
  public function allowed_block_types_all($allowed_blocks)
  {

    // get widget blocks and registered by plugins blocks
    $registered_blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();

    // now $registered_blocks contains only blocks registered by plugins, but we need keys only
    $registered_blocks = array_keys($registered_blocks);

    // // merge the whitelist with plugins blocks
    // $allowed_blocks = array_merge( array(
    //   'core/heading',
    //   'core/shortcode',
    //   'core/spacer',
    //   'core/paragraph',
    //   'core/list',
    //   'core/image',
    //   'core/gallery',
    //   'core/html',
    //   'core/table',
    //   'core/reusableBlock',
    //   'core-embed/youtube',
    //   'core-embed/twitter',
    //   'core-embed/facebook',
    // ), $registered_blocks );

    // $post_type = get_post_type();
    // if( $post_type === 'page' ) {
    //   $allowed_blocks = array_merge([
    //     'core/shortcode'
    //   ], $allowed_blocks);
    // }

    return $allowed_blocks;
  }

  /**
   * Load Dynamicaly Custom Gutenberg Blocks
   */
  public function acf_gutenberg_blocks()
  {

    $directories = [
      get_template_directory() . '/templates/gutenberg',
      get_template_directory() . '/static/templates/component'
    ];

    foreach ($directories as $directory) {
      if (file_exists($directory)) {
        $directory_iterator = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
        $flattened = new RecursiveIteratorIterator($directory_iterator);

        $regex_iterator = new RegexIterator($flattened, '/\.php$/');
        foreach ($regex_iterator as $file) {
          include $file->getPathname();
        }
      }
    }
  }

  /**
   * Identify if gutenberg parent block
   * from https://github.com/WordPress/gutenberg/issues/17358#issuecomment-1698655247
   */
  public function render_block_data($parsed_block, $source_block, $parent_block)
  {

    $parsed_block['parent'] = null;

    if (! empty($parent_block->parsed_block)) {
      $parsed_block['parent'] = array(
        'name' => $parent_block->name,
        'attributes' => $parent_block->attributes,
        'block' => $parent_block->parsed_block,
      );
    }

    return $parsed_block;
  }

  /**
   * Add custom wrapper to all core Gutenberg blocks
   */
  public function render_block($block_content, $block)
  {

    // check if block has parent
    // assigned in render_block_data()
    if (! empty($block['parent'])) {
      return $block_content;
    }

    // Apply filter only on core gutenberg blocks
    // Custom blocks will get filter via Twig
    if (strpos((string) $block['blockName'], 'core/') === FALSE && ! in_array($block['blockName'], ['contact-form-7/contact-form-selector'])) {
      return $block_content;
    }

    // Skip Core columns blocks
    if (in_array($block['blockName'], ['core/column', 'core/columns', 'core/group', 'core/spacer', 'core/block', 'core/list-item'])) {
      return $block_content;
    }

    // Check if we need raw output
    $raw = FALSE;
    if (in_array($block['blockName'], ['core/shortcode', 'contact-form-7/contact-form-selector'])) {
      $raw = TRUE;
    }

    $post_type = get_post_type();
    if (in_array($post_type, ['post', 'case_study'])) {
      return $block_content;
    } else {
      $context = Timber::context();
      $context['content'] = [
        'name' => 'gutenberg-' . str_replace('core/', '', $block['blockName']),
        'wrapper_classes' => 'px-4 mb-6',
        'container' => 'max-w-screen-md mx-auto prose prose-yellow max-w-none',
        'html' => $block_content,
        'raw' => $raw,
      ];
      return Timber::compile('@component/content/content.twig', $context);
    }
  }

  /**
   * Custom categories for Gutenberg Blocks
   */
  public function block_categories_all($categories)
  {
    return array_merge(
      $categories,
      array(
        array(
          'slug'  => 'custom',
          'title' => 'Vlastní',
        ),
      )
    );
  }

  /**
   * Hide WordPress core update notifications from all users except administrators
   * From https://www.cssigniter.com/hide-the-wordpress-update-notifications-from-all-users-except-administrators/
   */
  public function hide_core_update_notifications()
  {
    if (!current_user_can('update_core')) {
      remove_action('admin_notices', 'update_nag', 3);
    }
  }

  /**
   * ACF Wysiwyg set height to lower value then default
   * From https://gist.github.com/courtneymyers/eb51f918181746181871f7ae516b428b
   */
  public function acf_input_admin_footer()
  {

    $str = <<<EOF
      <style>
        .acf-editor-wrap iframe {
          min-height: 0;
        }
      </style>
      <script>
        (function($) {
          // reduce placeholder textarea height to match tinymce settings (when using delay-setting)
          $('.acf-editor-wrap.delay textarea').css('height', '60px');
          // (filter called before the tinyMCE instance is created)
          acf.add_filter('wysiwyg_tinymce_settings', function(mceInit, id, field) {
            // enable autoresizing of the WYSIWYG editor
            mceInit.wp_autoresize_on = true;
            return mceInit;
          });
          // (action called when a WYSIWYG tinymce element has been initialized)
          acf.add_action('wysiwyg_tinymce_init', function(ed, id, mceInit, field) {
            // reduce tinymce's min-height settings
            ed.settings.autoresize_min_height = 60;
            // reduce iframe's 'height' style to match tinymce settings
            $('.acf-editor-wrap iframe').css('height', '60px');
          });
        })(jQuery)
      </script>
    EOF;
    print $str;
  }

  /**
   * ACF Wysiwyg set height to lower value then default
   * From https://gist.github.com/courtneymyers/eb51f918181746181871f7ae516b428b
   */
  public function tiny_mce_before_init($mceInit)
  {
    $styles = 'body.mce-content-body { margin-top:0;margin-bottom:0 }';
    if (isset($mceInit['content_style'])) {
      $mceInit['content_style'] .= ' ' . $styles . ' ';
    } else {
      $mceInit['content_style'] = $styles . ' ';
    }
    return $mceInit;
  }

  /**
   * Create custom admin pages
   */
  public function acf_options_page()
  {
    if (function_exists('acf_add_options_page')) {
      acf_add_options_page([
        'page_title'    => 'Nastavení',
        'menu_title'    => 'Nastavení',
        'menu_slug'     => 'settings',
        'capability'    => 'edit_posts',
        'icon_url'      => 'dashicons-admin-generic',
        'redirect'      => false,
        'graphql_field_name' => 'settings',
        'show_in_graphql' => false
      ]);
      $fields_builder = new FieldsBuilder('footer_fields', [
        'title' => 'Patička',
        'position' => 'acf_after_title',
      ]);
      $fields_builder
        ->addGroup('footer', [
          'label' => '',
          'instructions' => '',
          'required' => 0,
          'layout' => 'block',
        ])
        ->addTextarea('description', [
          'label' => 'Dovětek',
          'instructions' => '',
          'required' => 0,
          'placeholder' => 'Přidejte volitelnou informaci',
          'rows' => '6',
          'new_lines' => 'br',
        ])
        ->addRepeater('social_menu', [
          'label' => 'Ikony sociálních sítí',
          'max' => 15,
        ])
        ->addText('title', [
          'label' => 'Název',
          'required' => TRUE,
        ])
        ->addUrl('url', [
          'label' => 'URL',
          'required' => TRUE,
          'placeholder' => 'Zadejte URL',
        ])
        ->addSelect('icon', [
          'label' => 'Ikona',
          'required' => TRUE,
          'choices' => [
            'apple' => 'Apple',
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'spotify' => 'Spotify',
            'youtube' => 'Youtube',
            'linkedin' => 'LinkedIn',
            'twitter' => 'Twitter/X',
          ],
        ]);
      $fields_builder
        ->addGroup( 'links', [
          'label' => '',
          'instructions' => '',
          'required' => 0,
          'layout' => 'block',
          'wpml_cf_preferences' => 3,
        ] )
        ->addLink( 'article_list', [
          'label' => 'Blog',
          'required' => TRUE,
          'placeholder' => 'Vyberte odkaz',
          'instructions' => 'Odkaz na výpis článků - používá se např. pro drobečkovou navigaci',
          'wpml_cf_preferences' => 2,
        ] );
      $fields_builder->setLocation('options_page', '==', 'settings');

      acf_add_local_field_group($fields_builder->build());
    }
  }

  /**
   * Google Maps API key
   */
  public function acf_google_map_api($api)
  {
    // Place CONSTANT definition to wp-config.php
    // define('GOOGLE_MAPS_API_KEY', 'XXX');
    if (defined('GOOGLE_MAPS_API_KEY')) {
      $api['key'] = GOOGLE_MAPS_API_KEY;
    }
    return $api;
  }

  /**
   * Template redirect
   * Allow paging on custom post types
   */
  public function template_redirect()
  {

    global $wp_query;

    if (is_singular('post')) {
      $page = (int) $wp_query->get('page');
      if ($page > 1) {
        // convert 'page' to 'paged'
        $wp_query->set('page', 1);
        $wp_query->set('paged', $page);
      }
      // prevent redirect
      remove_action('template_redirect', 'redirect_canonical');
    }
  }

  /**
   * Define custom page templates in code
   */
  public function theme_page_templates($templates)
  {

    $register_templates = [
      //'article-list.php' => 'Články - výpis',
    ];

    $templates = array_merge($templates, $register_templates);
    return $templates;
  }

  /**
   * Allow to filter by custom taxonomies in administration
   * https://wordpress.stackexchange.com/a/387502
   */
  public function restrict_manage_posts()
  {

    $screen = get_current_screen();

    // Single out WordPress default posts types
    $restricted_post_types = array(
      'post',
      'page',
      'attachment',
      'revision',
      'nav_menu_item',
    );

    if ('edit' === $screen->base && ! in_array($screen->post_type, $restricted_post_types)) {
      $taxonomies = get_object_taxonomies($screen->post_type, 'objects');

      // Loop through each taxonomy
      foreach ($taxonomies as $taxonomy) {
        if ($taxonomy->show_admin_column) {
          wp_dropdown_categories(
            array(
              'show_option_all' => $taxonomy->labels->all_items,
              'pad_counts' => true,
              'show_count' => true,
              'hierarchical' => true,
              'name' => $taxonomy->query_var,
              'id' => 'filter-by-' . $taxonomy->query_var,
              'class' => '',
              'value_field' => 'slug',
              'taxonomy' => $taxonomy->query_var,
              'hide_if_empty' => true,
            )
          );
        };
      };
    };
  }

  /**
   * Generate main menu array
   */
  public function get_nav_menu($menu_name)
  {

    $menu = Timber::get_menu($menu_name);

    $items = [];
    if (isset($menu->items)) {
      foreach ($menu->items as $item) {

        $attributes = [];
        $attributes['target'] = $item->target ?: null;
        if (isset($item->classes) && is_array($item->classes)) {
          $attributes['class'] = reset($item->classes);
        }

        // we cannot directly translate description, so we register it here manually
        $description = $item->description;
        if (is_plugin_active('sitepress-multilingual-cms/sitepress.php') && ! empty($description)) {
          $default_language = apply_filters('wpml_default_language', null);
          icl_register_string($menu->name . ' menu', 'Menu Item Description ' . $item->ID, $description, false, $default_language);
          $description = icl_t($menu->name . ' menu', 'Menu Item Description ' . $item->ID, $description);
        }

        $items[] = [
          'title' => $item->name,
          'url' => $item->url,
          'description' => $description,
          'attributes' => $attributes,
          'in_active_trail' => $item->current_item_ancestor,
          'is_active' => $item->current,
          'below' => $this->get_nav_menu_child($item, $menu),
        ];
      }
    }

    return $items;
  }

  /**
   * Generate menu child items
   */
  public function get_nav_menu_child($item, $menu)
  {

    $below = [];
    if (isset($item->children)) {
      foreach ($item->children as $child) {

        $attributes = [];
        $attributes['target'] = $child->target ?: null;
        if (isset($child->classes) && is_array($child->classes)) {
          $attributes['class'] = reset($child->classes);
        }

        // we cannot directly translate description, so we register it here manually
        $description = $child->description;
        if (is_plugin_active('sitepress-multilingual-cms/sitepress.php') && ! empty($description)) {
          $default_language = apply_filters('wpml_default_language', null);
          icl_register_string($menu->name . ' menu', 'Menu Item Description ' . $child->ID, $description, false, $default_language);
          $description = icl_t($menu->name . ' menu', 'Menu Item Description ' . $child->ID, $description);
        }

        $below[] = [
          'title' => $child->name,
          'url' => $child->url,
          'description' => $description,
          'attributes' => $attributes,
          'in_active_trail' => $child->current_item_ancestor,
          'is_active' => $child->current,
          'below' => $this->get_nav_menu_child($child, $menu),
        ];
      }
    }

    return $below;
  }

  /**
   * Generate language switcher array
   */
  public function get_languages()
  {

    global $sitepress;

    $languages = apply_filters('wpml_active_languages', null, ['skip_missing' => FALSE]);

    $items = [];
    if (!empty($languages) && is_countable($languages)) {
      foreach ($languages as $language) {
        $url = esc_url($language['url']);
        if (isset($language['missing']) && $language['missing']) {
          $url = '';
        }
        $home_url = esc_url($sitepress->language_url($language['language_code']));
        $items[] = [
          'id' => esc_html($language['language_code']),
          'title' => esc_html($language['native_name']),
          'url' => $url,
          'home_url' => $home_url,
          'is_active' => (bool)$language['active'],
        ];
      }
    }

    return $items;
  }

  /**
   * Add default CSS classes to image
   */
  public function wp_get_attachment_image_attributes($attr, $attachment)
  {

    if (strpos($attr['class'], 'img-fluid') === FALSE) {
      $attr['class'] .= ' img-fluid';
    }

    return $attr;
  }

  /**
   * Set maximum quality, use resiter for optimization.
   */
  public function jpeg_quality($quality)
  {
    return 100;
  }

  /**
   * Set maximum quality, use resiter for optimization.
   */
  public function wp_editor_set_quality($quality)
  {
    return 100;
  }

  /**
   * Fix wrong order in ACF gallery, relationship, post_object fields with WPML
   * from https://www.pixelbar.be/blog/fix-wrong-order-in-acf-gallery-and-relationship-fields-with-wpml/
   */
  public function fix_wrong_acf_orders_with_ids($value, $field_id, $field)
  {

    if (! is_plugin_active('sitepress-multilingual-cms/sitepress.php')) {
      return $value;
    }

    if (! is_array($value)) {
      return $value;
    }

    $wpml_value = array();
    foreach ($value as $key => $v) {
      $id = apply_filters('wpml_object_id', $v, 'post', true);
      if (is_int($id)) {
        $wpml_value[$key] = $id;
      }
    }

    return $wpml_value;
  }

  public function search_post_type_filter($query)
  {

    if ($query->is_search && ! is_admin()) {
      $query->set('post_type', array('post'));
    }

    return $query;
  }

  /**
   * Generate language switcher array if using Polylang plugin
   */
  public function get_pll_languages()
  {

    $languages = pll_the_languages(array(
      'dropdown' => 0,
      'display_names_as' => 'slug',
      'post_id' => get_the_ID(),
      'echo' => 0,
      'raw' => 1,
    ));

    $items = [];
    foreach ($languages as $language) {
      $items[] = [
        'id' => $language['slug'],
        'title' => $language['name'],
        'url' => $language['url'],
        'is_active' => (bool)$language['current_lang'],
      ];
    }

    return $items;
  }
}
