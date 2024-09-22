<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

$labels = [
  "name" => __( "Aktuality", "starter_theme" ),
  "singular_name" => __( "Aktualita", "starter_theme" ),
  "menu_name" => __( "Aktuality", "starter_theme" ),
  "all_items" => __( "Všechny aktuality", "starter_theme" ),
  "add_new" => __( "Nová aktualita", "starter_theme" ),
  "add_new_item" => __( "Přidat novou aktualitu", "starter_theme" ),
  "edit_item" => __( "Upravit aktualitu", "starter_theme" ),
  "new_item" => __( "Nová aktualita", "starter_theme" ),
  "view_item" => __( "Zobrazit aktualitu", "starter_theme" ),
  "view_items" => __( "Zobrazit aktuality", "starter_theme" ),
  "search_items" => __( "Vyhledat aktualitu", "starter_theme" ),
  "not_found" => __( "Žádná aktualita nenalezena", "starter_theme" ),
  "not_found_in_trash" => __( "Žádná aktualita nenalezena", "starter_theme" ),
  "parent" => __( "Nadřazená aktualita:", "starter_theme" ),
  "featured_image" => __( "Hlavní obrázek pro tuto aktualitu", "starter_theme" ),
  "set_featured_image" => __( "Nastavit obrázek pro tuto aktualitu", "starter_theme" ),
  "remove_featured_image" => __( "Odstranit obrázek pro tuto aktualitu", "starter_theme" ),
  "use_featured_image" => __( "Použít jako hlavní obrázek pro tuto aktualitu", "starter_theme" ),
  "archives" => __( "Archiv aktualit", "starter_theme" ),
  "insert_into_item" => __( "Vložit do aktuality", "starter_theme" ),
  "uploaded_to_this_item" => __( "Nahrát do aktuality", "starter_theme" ),
  "filter_items_list" => __( "Filtrovat seznam aktualit", "starter_theme" ),
  "items_list_navigation" => __( "Navigace seznamu aktualit", "starter_theme" ),
  "items_list" => __( "Seznam aktualit", "starter_theme" ),
  "attributes" => __( "Vlastnosti aktuality", "starter_theme" ),
  "name_admin_bar" => __( "Aktualita", "starter_theme" ),
  "item_published" => __( "Aktualita publikována", "starter_theme" ),
  "item_published_privately" => __( "Aktualita publikována soukromě", "starter_theme" ),
  "item_reverted_to_draft" => __( "Aktualita převedena na koncept", "starter_theme" ),
  "item_scheduled" => __( "Aktualita naplánována", "starter_theme" ),
  "item_updated" => __( "Aktualita aktualizována", "starter_theme" ),
  "parent_item_colon" => __( "Nadřazená aktualita:", "starter_theme" ),
];

$args = [
  "label" => __( "Aktuality", "starter_theme" ),
  "labels" => $labels,
  "description" => "Krátká zpráva o aktuálním dění.",
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
  "rewrite" => [ "slug" => "aktuality", "with_front" => true ],
  "query_var" => true,
  "menu_icon" => "dashicons-megaphone",
  "supports" => [ "title", "thumbnail"],
  "show_in_graphql" => false,
];

register_post_type( "news", $args );

$news_fields = new FieldsBuilder('Aktualita');
$news_fields
    ->addTextarea('description', [
      'label' => 'Popis',
      'instructions' => '',
      'required' => 0,
      'default_value' => "",
      'wpml_cf_preferences' => 2,
    ])
    ->addLink('link', [
      'label' => 'Odkaz',
      'instructions' => 'Nastavením textu odkazu přepíšete nadpis aktuality na stránce.',
      'required' => 0,
      'conditional_logic' => [],
      'return_format' => 'array',
  ]);
$news_fields->setLocation('post_type', '==', 'news');

acf_add_local_field_group($news_fields->build());
