<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

$labels = [
  "name" => __( "Akce", "kontrapunkt" ),
  "singular_name" => __( "Akce", "kontrapunkt" ),
  "menu_name" => __( "Akce", "kontrapunkt" ),
  "all_items" => __( "Všechny akce", "kontrapunkt" ),
  "add_new" => __( "Nová akce", "kontrapunkt" ),
  "add_new_item" => __( "Přidat novou akci", "kontrapunkt" ),
  "edit_item" => __( "Upravit akci", "kontrapunkt" ),
  "new_item" => __( "Nová akce", "kontrapunkt" ),
  "view_item" => __( "Zobrazit akce", "kontrapunkt" ),
  "view_items" => __( "Zobrazit akce", "kontrapunkt" ),
  "search_items" => __( "Vyhledat akci", "kontrapunkt" ),
  "not_found" => __( "Žádná akce nenalezena", "kontrapunkt" ),
  "not_found_in_trash" => __( "Žádná akce nenalezena", "kontrapunkt" ),
  "parent" => __( "Nadřazená akce:", "kontrapunkt" ),
  "featured_image" => __( "Hlavní obrázek pro tuto akci", "kontrapunkt" ),
  "set_featured_image" => __( "Nastavit obrázek pro tuto akci", "kontrapunkt" ),
  "remove_featured_image" => __( "Odstranit obrázek pro tuto akci", "kontrapunkt" ),
  "use_featured_image" => __( "Použít jako hlavní obrázek pro tuto akci", "kontrapunkt" ),
  "archives" => __( "Archiv akcí", "kontrapunkt" ),
  "insert_into_item" => __( "Vložit do akce", "kontrapunkt" ),
  "uploaded_to_this_item" => __( "Nahrát do akce", "kontrapunkt" ),
  "filter_items_list" => __( "Filtrovat seznam akcí", "kontrapunkt" ),
  "items_list_navigation" => __( "Navigace seznamu akcí", "kontrapunkt" ),
  "items_list" => __( "Seznam akcí", "kontrapunkt" ),
  "attributes" => __( "Vlastnosti akce", "kontrapunkt" ),
  "name_admin_bar" => __( "Akce", "kontrapunkt" ),
  "item_published" => __( "Akce publikována", "kontrapunkt" ),
  "item_published_privately" => __( "Akce publikována soukromě", "kontrapunkt" ),
  "item_reverted_to_draft" => __( "Akce převedena na koncept", "kontrapunkt" ),
  "item_scheduled" => __( "Akce naplánována", "kontrapunkt" ),
  "item_updated" => __( "Akce aktualizována", "kontrapunkt" ),
  "parent_item_colon" => __( "Nadřazená akce:", "kontrapunkt" ),
];

$args = [
  "label" => __( "Akce", "kontrapunkt" ),
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
  "supports" => [ "title", "editor"],
  "show_in_graphql" => false,
];

register_post_type( "event", $args );

$event_fields = new FieldsBuilder('Akce');
$event_fields
->addLink('web_link', [
  'label' => 'Webový odkaz',
  'instructions' => 'Zadejte URL webové stránky akce',
  'required' => 0,
  'wpml_cf_preferences' => 2,
])
->addUrl('facebook_link', [
  'label' => 'Odkaz na Facebook',
  'required' => 0,
  'wpml_cf_preferences' => 2,
])
->addUrl('instagram_link', [
  'label' => 'Odkaz na Instagram',
  'required' => 0,
  'wpml_cf_preferences' => 2,
])
->addImage('image', [
  'label' => 'Obrázek',
  'required' => 1,
  'wpml_cf_preferences' => 1,
])
->addWysiwyg('perex', [
  'label' => 'Popis akce',
  'required' => 1,
  'media_upload' => FALSE,
  'show_in_grpahql' => TRUE,
  'wpml_cf_preferences' => 2,
])
->addText('location', [
  'label' => 'Místo',
  'required' => 0,
  'placeholder' => 'Zadejte název',
  'maxlength' => 40,
  'wpml_cf_preferences' => 2,
])
->addText('author', [
  'label' => 'Autor',
  'required' => 0,
  'placeholder' => 'Zadejte jméno',
  'maxlength' => 80,
  'wpml_cf_preferences' => 2,
])
->addText('price', [
  'label' => 'Cena',
  'required' => 0,
  'placeholder' => 'Zadejte cenu',
  'maxlength' => 40,
  'wpml_cf_preferences' => 2,
])
->addDatePicker('date_start', [
  'label' => 'Datum začátku akce',
  'instructions' => 'Vyplňte datum kdy akce začíná.',
  'required' => 1,
  'display_format' => 'd. m. Y',
  'return_format' => 'Y-m-d',
  'first_day' => 1,
  'wpml_cf_preferences' => 1,
])
->addDatePicker('date_end', [
  'label' => 'Datum konce akce',
  'instructions' => 'Vyplňte datum kdy akce končí.',
  'required' => 0,
  'display_format' => 'd. m. Y',
  'return_format' => 'Y-m-d',
  'first_day' => 1,
  'wpml_cf_preferences' => 1,
])
->addTimePicker('time_start', [
  'label' => 'Začátek akce',
  'instructions' => 'Vyplňte čas kdy akce začíná',
  'required' => 0,
  'display_format' => 'H:i',
  'return_format' => 'H:i',
  'wpml_cf_preferences' => 1,
])
->addTimePicker('time_end', [
  'label' => 'Konec akce',
  'instructions' => 'Vyplňte čas kdy akce končí',
  'required' => 0,
  'display_format' => 'H:i',
  'return_format' => 'H:i',
  'wpml_cf_preferences' => 1,
])
->addText('duration', [
  'label' => 'Doba trvání',
  'instructions' => 'Vyplňte délku trvání akce',
  'required' => 0,
  'wpml_cf_preferences' => 2,
])
->addRepeater('additional_showtimes', [
  'label' => 'Reprízy',
  'required' => 0,
  'min' => 1,
  'max' => 8,
  'layout' => 'table',
])
  ->addTimePicker('time_start', [
    'label' => 'Začátek akce',
    'instructions' => 'Vyplňte čas kdy akce začíná',
    'required' => 0,
    'display_format' => 'H:i',
    'return_format' => 'H:i',
    'wpml_cf_preferences' => 1,
  ])
  ->addTimePicker('time_end', [
    'label' => 'Konec akce',
    'instructions' => 'Vyplňte čas kdy akce končí',
    'required' => 0,
    'display_format' => 'H:i',
    'return_format' => 'H:i',
    'wpml_cf_preferences' => 1,
  ])
  ->addText('duration', [
    'label' => 'Doba trvání',
    'instructions' => 'Vyplňte délku trvání akce',
    'required' => 0,
    'wpml_cf_preferences' => 2,
  ])
->endRepeater();

$event_fields->setLocation('post_type', '==', 'event');

acf_add_local_field_group($event_fields->build());
