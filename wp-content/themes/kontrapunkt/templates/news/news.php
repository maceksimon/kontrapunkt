<?php

use StoutLogic\AcfBuilder\FieldsBuilder;

$labels = [
  "name" => __( "Aktuality", "kontrapunkt" ),
  "singular_name" => __( "Aktualita", "kontrapunkt" ),
  "menu_name" => __( "Aktuality", "kontrapunkt" ),
  "all_items" => __( "Všechny aktuality", "kontrapunkt" ),
  "add_new" => __( "Nová aktualita", "kontrapunkt" ),
  "add_new_item" => __( "Přidat novou aktualitu", "kontrapunkt" ),
  "edit_item" => __( "Upravit aktualitu", "kontrapunkt" ),
  "new_item" => __( "Nová aktualita", "kontrapunkt" ),
  "view_item" => __( "Zobrazit aktualitu", "kontrapunkt" ),
  "view_items" => __( "Zobrazit aktuality", "kontrapunkt" ),
  "search_items" => __( "Vyhledat aktualitu", "kontrapunkt" ),
  "not_found" => __( "Žádná aktualita nenalezena", "kontrapunkt" ),
  "not_found_in_trash" => __( "Žádná aktualita nenalezena", "kontrapunkt" ),
  "parent" => __( "Nadřazená aktualita:", "kontrapunkt" ),
  "featured_image" => __( "Hlavní obrázek pro tuto aktualitu", "kontrapunkt" ),
  "set_featured_image" => __( "Nastavit obrázek pro tuto aktualitu", "kontrapunkt" ),
  "remove_featured_image" => __( "Odstranit obrázek pro tuto aktualitu", "kontrapunkt" ),
  "use_featured_image" => __( "Použít jako hlavní obrázek pro tuto aktualitu", "kontrapunkt" ),
  "archives" => __( "Archiv aktualit", "kontrapunkt" ),
  "insert_into_item" => __( "Vložit do aktuality", "kontrapunkt" ),
  "uploaded_to_this_item" => __( "Nahrát do aktuality", "kontrapunkt" ),
  "filter_items_list" => __( "Filtrovat seznam aktualit", "kontrapunkt" ),
  "items_list_navigation" => __( "Navigace seznamu aktualit", "kontrapunkt" ),
  "items_list" => __( "Seznam aktualit", "kontrapunkt" ),
  "attributes" => __( "Vlastnosti aktuality", "kontrapunkt" ),
  "name_admin_bar" => __( "Aktualita", "kontrapunkt" ),
  "item_published" => __( "Aktualita publikována", "kontrapunkt" ),
  "item_published_privately" => __( "Aktualita publikována soukromě", "kontrapunkt" ),
  "item_reverted_to_draft" => __( "Aktualita převedena na koncept", "kontrapunkt" ),
  "item_scheduled" => __( "Aktualita naplánována", "kontrapunkt" ),
  "item_updated" => __( "Aktualita aktualizována", "kontrapunkt" ),
  "parent_item_colon" => __( "Nadřazená aktualita:", "kontrapunkt" ),
];

$args = [
  "label" => __( "Aktuality", "kontrapunkt" ),
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
