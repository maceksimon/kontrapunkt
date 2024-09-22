<?php
/*
Plugin Name: Site plugin
Description: Site specific code changes
Author: PORTADESIGN.cz
Version: 1.0
Author URI: https://portadesign.cz
*/

//odstraní odkaz (link) s parametrem rel = alternate odkazujícím na feed nejnovějších článků a komentářů
remove_action( 'wp_head', 'feed_links', 2 );
//odstraní odkaz (link) s parametrem rel = alternte odkazujícím na speciální feeds (komentáře stránky v tomto případě, ale jde i o feed rubrik, štítků a podobně)
remove_action( 'wp_head', 'feed_links_extra', 3 );
//odstraní odkaz (link) s parametrem rel = EditURI
remove_action( 'wp_head', 'rsd_link' );
//odstraní odkaz (link) na wlwmanifest (Windows Live Writer)
remove_action( 'wp_head', 'wlwmanifest_link' );
//odstraní odkaz (link) s parametrem rel = index
remove_action( 'wp_head', 'index_rel_link' );
//odstraní odkaz (link) s parametrem rel = prev a rel = next (next není ve výpisu nahoře)
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
//odstraní poslední řádek, meta tag generator
remove_action( 'wp_head', 'wp_generator' );
//odstraní odkaz (link) s parametrem rel = up (není ve výše uvedeném výpise)
remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
//odstraní odkaz (link) s parametrem rel = start (není ve výše uvedeném výpisu)
remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
//odstraní smajlíky
remove_action( 'admin_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
add_filter( 'emoji_svg_url', '__return_false' );
//disable XML RPC
add_filter('xmlrpc_enabled', '__return_false');
function portadesign_disable_x_pingback($headers) {
  unset($headers['X-Pingback']);
  return $headers;
}
add_filter('wp_headers', 'portadesign_disable_x_pingback');

function portadesign_disable_feed() {
  global $wp_query;
  $wp_query->set_404();
  status_header(404);
  nocache_headers();
}

// Disable global RSS, RDF & Atom feeds.
add_action( 'do_feed',      'portadesign_disable_feed', -1 );
add_action( 'do_feed_rdf',  'portadesign_disable_feed', -1 );
add_action( 'do_feed_rss',  'portadesign_disable_feed', -1 );
add_action( 'do_feed_rss2', 'portadesign_disable_feed', -1 );
add_action( 'do_feed_atom', 'portadesign_disable_feed', -1 );

// Disable comment feeds.
add_action( 'do_feed_rss2_comments', 'portadesign_disable_feed', -1 );
add_action( 'do_feed_atom_comments', 'portadesign_disable_feed', -1 );

// Prevent feed links from being inserted in the <head> of the page.
add_action( 'feed_links_show_posts_feed',    '__return_false', -1 );
add_action( 'feed_links_show_comments_feed', '__return_false', -1 );
remove_action( 'wp_head', 'feed_links',       2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );

function portadesign_remove_menu() {
  remove_menu_page( 'edit-comments.php' );
  remove_submenu_page( 'options-general.php', 'options-discussion.php' );

  if (!current_user_can('administrator')) {
    remove_menu_page( 'tools.php' );
    remove_menu_page( 'activity_log_page' );
  }
  if(current_user_can('editor')) {
    // They're an editor, so grant the edit_theme_options capability if they don't have it
    if (!current_user_can( 'edit_theme_options' )) {
      $role_object = get_role( 'editor' );
      $role_object->add_cap( 'edit_theme_options' );
    }
    // Hide the Themes page
    remove_submenu_page( 'themes.php', 'themes.php' );

    // Hide the Widgets page
    remove_submenu_page( 'themes.php', 'widgets.php' );

    // Hide the Customize page
    remove_submenu_page( 'themes.php', 'customize.php' );

    // Remove Customize from the Appearance submenu
    global $submenu;
    unset($submenu['themes.php'][6]);

    // AIOSEO add permissions to edit in page overview
    if (!current_user_can( 'aioseo_manage_seo' )) {
      $role_object = get_role( 'editor' );
      $role_object->add_cap( 'aioseo_manage_seo' );
    }

    // remove AIOSEO page
    remove_menu_page('aioseo');
  }
}
add_action( 'admin_menu', 'portadesign_remove_menu', 999);

// Allow editor to edit privacy page
// We don't need capability per user role because this function is working per per post basis
// https://wordpress.stackexchange.com/questions/318666/how-to-allow-editor-to-edit-privacy-page-settings-only
add_action('map_meta_cap', 'portadesign_map_meta_cap', 1, 4);
function portadesign_map_meta_cap($caps, $cap, $user_id, $args) {
  if (!is_user_logged_in()) return $caps;

  $user_meta = get_userdata($user_id);
  if (array_intersect(['editor', 'administrator'], $user_meta->roles)) {
    if ('manage_privacy_options' === $cap) {
      $manage_name = is_multisite() ? 'manage_network' : 'manage_options';
      $caps = array_diff($caps, [ $manage_name ]);
    }
  }
  return $caps;
}

// Removes from post and pages
function portadesign_remove_comment_support() {
  remove_post_type_support( 'post', 'comments' );
  remove_post_type_support( 'page', 'comments' );
}
add_action('init', 'portadesign_remove_comment_support', 100);

// // hide search from web
// function portadesign_filter_query( $query, $error = true ) {
//   if ( ! is_admin() && is_search() ) {
//     $query->is_search = false;
//     $query->query_vars['s'] = false;
//     $query->query['s'] = false;
//     if ( $error == true ) {
//       $query->is_404 = true;
//     }
//   }
// }
// add_action( 'parse_query', 'portadesign_filter_query' );

function portadesign_remove_dashboard_widgets() {

  global $wp_meta_boxes;

  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_drafts']);
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
  unset($wp_meta_boxes['dashboard']['normal']['high']['aioseo-seo-setup']);
  unset($wp_meta_boxes['dashboard']['normal']['core']['aioseo-overview']);
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
  unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
};
add_action('wp_dashboard_setup', 'portadesign_remove_dashboard_widgets', 999);

function portadesign_remove_toolbar_items($wp_adminbar) {
  $wp_adminbar->remove_node('updates');
  $wp_adminbar->remove_node('comments');
  $wp_adminbar->remove_node('aioseo-main');
}
add_action('admin_bar_menu', 'portadesign_remove_toolbar_items', 1200);

// remove automatic <br> from Contact Form 7
add_filter('wpcf7_autop_or_not', '__return_false');

// remove dashboard for editor
function portadesign_remove_dashboard() {
  if (!current_user_can('administrator')) {
    remove_menu_page( 'index.php' );
  }
}
add_action( 'admin_menu', 'portadesign_remove_dashboard', 99 );

// redirect editor to page edit
function portadesign_login_redirect() {
  if (!current_user_can('administrator')) {
    return 'wp-admin/edit.php?post_type=page';
  }
}
add_filter('login_redirect', 'portadesign_login_redirect');
