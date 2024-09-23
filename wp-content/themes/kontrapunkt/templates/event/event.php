<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

$labels = [
  "name" => __( "Události", "kontrapunkt" ),
  "singular_name" => __( "Událost", "kontrapunkt" ),
  "menu_name" => __( "Události", "kontrapunkt" ),
  "all_items" => __( "Všechny události", "kontrapunkt" ),
  "add_new" => __( "Nová událost", "kontrapunkt" ),
  "add_new_item" => __( "Přidat novou událost", "kontrapunkt" ),
  "edit_item" => __( "Upravit událost", "kontrapunkt" ),
  "new_item" => __( "Nová událost", "kontrapunkt" ),
  "view_item" => __( "Zobrazit události", "kontrapunkt" ),
  "view_items" => __( "Zobrazit události", "kontrapunkt" ),
  "search_items" => __( "Vyhledat událost", "kontrapunkt" ),
  "not_found" => __( "Žádná událost nenalezena", "kontrapunkt" ),
  "not_found_in_trash" => __( "Žádná událost nenalezena", "kontrapunkt" ),
  "parent" => __( "Nadřazená událost:", "kontrapunkt" ),
  "featured_image" => __( "Hlavní obrázek pro tuto událost", "kontrapunkt" ),
  "set_featured_image" => __( "Nastavit obrázek pro tuto událost", "kontrapunkt" ),
  "remove_featured_image" => __( "Odstranit obrázek pro tuto událost", "kontrapunkt" ),
  "use_featured_image" => __( "Použít jako hlavní obrázek pro tuto událost", "kontrapunkt" ),
  "archives" => __( "Archiv událostí", "kontrapunkt" ),
  "insert_into_item" => __( "Vložit do události", "kontrapunkt" ),
  "uploaded_to_this_item" => __( "Nahrát do události", "kontrapunkt" ),
  "filter_items_list" => __( "Filtrovat seznam událostí", "kontrapunkt" ),
  "items_list_navigation" => __( "Navigace seznamu událostí", "kontrapunkt" ),
  "items_list" => __( "Seznam událostí", "kontrapunkt" ),
  "attributes" => __( "Vlastnosti události", "kontrapunkt" ),
  "name_admin_bar" => __( "Událost", "kontrapunkt" ),
  "item_published" => __( "Událost publikována", "kontrapunkt" ),
  "item_published_privately" => __( "Událost publikována soukromě", "kontrapunkt" ),
  "item_reverted_to_draft" => __( "Událost převedena na koncept", "kontrapunkt" ),
  "item_scheduled" => __( "Událost naplánována", "kontrapunkt" ),
  "item_updated" => __( "Událost aktualizována", "kontrapunkt" ),
  "parent_item_colon" => __( "Nadřazená událost:", "kontrapunkt" ),
];

$args = [
  "label" => __( "Události", "kontrapunkt" ),
  "labels" => $labels,
  "description" => "",
  "public" => true,
  "publicly_queryable" => true,
  "show_ui" => true,
  "show_in_rest" => true,
  "rest_base" => "",
  "rest_controller_class" => "WP_REST_Posts_Controller",
  "has_archive" => false,
  "show_in_menu" => true,
  "show_in_nav_menus" => false,
  "delete_with_user" => false,
  "exclude_from_search" => false,
  "capability_type" => "post",
  "map_meta_cap" => true,
  "hierarchical" => false,
  "rewrite" => [ "slug" => "udalosti", "with_front" => true ],
  "query_var" => true,
  "menu_icon" => "dashicons-calendar-alt",
  "supports" => [ "title", "excerpt", "thumbnail" ],
  "show_in_graphql" => false,
];

register_post_type( "event", $args );

$event_fields = new FieldsBuilder('Událost');
$event_fields
  ->addText('type', [
    'label' => 'Druh akce',
    'required' => 0,
    'placeholder' => 'Zadejte krátký název',
    'maxlength' => 40,
    'wpml_cf_preferences' => 2,
  ])
  ->addWysiwyg('description', [
    'label' => 'Popis události',
    'required' => 1,
    'media_upload' => FALSE,
  ])
  ->addText('location', [
    'label' => 'Místo',
    'required' => 1,
    'placeholder' => 'Zadejte název',
    'maxlength' => 40,
    'wpml_cf_preferences' => 2,
  ])
  ->addTrueFalse('has_location_map', [
    'label' => 'Ukázat pozici na mapě',
    'required' => 0,
    'default_value' => 0,
    'ui' => 1,
    'ui_on_text' => 'Ano',
    'ui_off_text' => 'Ne',
    'wpml_cf_preferences' => 1,
  ])
  ->addGoogleMap('location_map', [
    'label' => 'Místo na mapě',
    'instructions' => 'Označte místo kde se akce děje',
    'required' => 0,
    'center_lat' => '50.0755',
    'center_lng' => '14.4378',
    'zoom' => '10',
    'wpml_cf_preferences' => 1,
  ])->conditional('has_location_map', '==', '1')
  ->addTrueFalse('is_date_single', [
    'label' => 'Akce je jednodenní',
    'required' => 0,
    'default_value' => 1,
    'ui' => 1,
    'ui_on_text' => 'Ano',
    'ui_off_text' => 'Ne',
    'wpml_cf_preferences' => 1,
  ])
  ->addDatePicker('date_single', [
    'label' => 'Datum akce',
    'instructions' => 'Vyplňte datum kdy akce začíná.',
    'required' => 1,
    'display_format' => 'd/m/Y',
    'return_format' => 'j. n. Y',
    'first_day' => 1,
    'wpml_cf_preferences' => 1,
  ])->conditional('is_date_single', '==', '1')
  ->addTimePicker('time_start', [
    'label' => 'Začátek akce',
    'instructions' => 'Vyplňte čas kdy akce začíná',
    'required' => 1,
    'display_format' => 'g:i a',
    'return_format' => 'g:i a',
  ])->conditional('is_date_single', '==', '1')
  ->addTimePicker('time_end', [
    'label' => 'Konec akce',
    'instructions' => 'Vyplňte čas kdy akce končí',
    'required' => 1,
    'display_format' => 'g:i a',
    'return_format' => 'g:i a',
  ])->conditional('is_date_single', '==', '1')
  ->addDateTimePicker('date_time_start', [
    'label' => 'Akce začíná',
    'instructions' => 'Vyplňte datum a čas zahájení akce.',
    'required' => 1,
    'display_format' => 'd/m/Y',
    'return_format' => 'j. n. Y',
    'first_day' => 1,
    'wpml_cf_preferences' => 1,
  ])->conditional('is_date_single', '==', '0')
  ->addDateTimePicker('date_time_end', [
    'label' => 'Akce končí',
    'instructions' => 'Vyplňte datum a čas ukončení akce.',
    'required' => 1,
    'display_format' => 'd/m/Y',
    'return_format' => 'j. n. Y',
    'first_day' => 1,
    'wpml_cf_preferences' => 1,
  ])->conditional('is_date_single', '==', '0');

$event_fields->setLocation('post_type', '==', 'event');

acf_add_local_field_group($event_fields->build());
