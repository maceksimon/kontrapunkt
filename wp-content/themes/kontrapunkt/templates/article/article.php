<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

$get_post_type = get_post_type_object('post');
$labels = $get_post_type->labels;
$labels->name = 'Články';
$labels->singular_name = 'Článek';
$labels->add_new = 'Přidat článek';
$labels->add_new_item = 'Přidat článek';
$labels->edit_item = 'Upravit článek';
$labels->new_item = 'Články';
$labels->view_item = 'Zobrazit článek';
$labels->search_items = 'Vyhledat článek';
$labels->not_found = 'Žádné články nebyly nalezeny';
$labels->not_found_in_trash = 'Žádné články nebyly v koši nalezeny';
$labels->all_items = 'Všechny články';
$labels->menu_name = 'Články';
$labels->name_admin_bar = 'Články';

$fields_builder = new FieldsBuilder('post', [
  'title' => 'Více informací o článku',
  'position' => 'acf_after_title',
  'show_in_graphql' => true,
  'graphql_field_name' => 'acfPost',
]);
$fields_builder
  ->addImage('image', [
    'label' => 'Obrázek',
    'required' => 1,
    'wpml_cf_preferences' => 1,
  ])
  ->addWysiwyg('perex', [
    'label' => 'Popisek',
    'required' => 1,
    'wpml_cf_preferences' => 2,
  ])
  ->setLocation('post_type', '==', 'post');
acf_add_local_field_group($fields_builder->build());

function kontrapunkt_modify_taxonomy() {
  $category = get_taxonomy( 'category' );
  $category->public = FALSE;
  $category->publicly_queryable = FALSE;
  $category->rewrite = FALSE;
  register_taxonomy( 'category', 'post', (array) $category );
}
add_action( 'init', 'kontrapunkt_modify_taxonomy', 999 );

add_filter('manage_edit-category_columns', function ( $columns ) {
  if( isset( $columns['description'] ) ) {
    unset( $columns['description'] );
  }
  if( isset( $columns['slug'] ) ) {
    unset( $columns['slug'] );
  }
  return $columns;
} );

add_action( 'category_edit_form', function () {
  echo "<style> .term-description-wrap, .term-slug-wrap, .term-parent-wrap { display:none; } </style>";
} );
add_action( 'category_add_form', function () {
  echo "<style> .term-description-wrap, .term-slug-wrap, .term-parent-wrap { display:none; } </style>";
} );

function kontrapunkt_article_admin_menu() {
 //remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=post_tag');
}
add_action( 'admin_menu', 'kontrapunkt_article_admin_menu', 999);
