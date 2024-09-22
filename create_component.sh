#!/bin/bash

# Check if component name is provided
if [ -z "$1" ]; then
  echo "You must provide a component name."
  exit 1
fi

# Function to ensure the directory exists
ensure_dir() {
  if [ ! -d "$1" ]; then
    mkdir -p "$1"
  fi
}

component_name_kebab=$(echo "$1")
component_name_snake=$(echo "$1" | tr '-' '_')
component_name_human=$(echo "$1" | tr '-' ' ' | awk '{print toupper(substr($0, 1, 1)) substr($0, 2)}')

# Boilerplate for .twig file
TWIG_TEMPLATE="{#
name: "$component_name_human"
#}
{% import '@macro/icons/icons.twig' as icons %}
{% extends "@component/component/component.twig" %}
{% set component = {
  'name': '"$component_name_kebab"',
  'id': content.wrapper_id,
  'classes': [
    'p-component',
    content.wrapper_classes,
  ],
  'container': content.inner ? '' : 'container',
  'heading': content.heading,
} %}

{% block content %}

{% endblock %}
"

STYLEGUIDE_TEMPLATE="{{ component_"$component_name_snake"({

}) }}
"

# Boilerplate for .cy.js file
PHP_TEMPLATE="<?php
function block_"$component_name_snake"_content($content) {
  return $content;
}

add_filter('block_"$component_name_snake"_content', 'block_"$component_name_snake"_content');

\$block = new Block('"$component_name_kebab"', '"$component_name_human"');

\$block->fieldsBuilder()
\$block->fieldsBuilder()
  ->addTrueFalse('enable_heading', [
    'label' => 'Zobrazit nadpis',
    'required' => 0,
    'default_value' => 0,
    'ui' => 1,
    'ui_on_text' => 'Ano',
    'ui_off_text' => 'Ne',
  ])
  ->addGroup('heading', [
    'label' => 'Nadpis',
    'instructions' => '',
    'required' => 0,
    'layout' => 'block',
  ])->conditional('enable_heading', '==', '1')
  ->addText('heading', [
    'label' => 'Text nadpisu',
    'instructions' => 'Doporučená délka 3-5 slov',
    'required' => 0,
    'placeholder' => 'Zadejte nadpis',
    'maxlength' => 160,
  ]);
\$block->setValidationFields([]);
\$block->register();
"

# Create directories
ensure_dir "./wp-content/themes/starter_theme/static/templates/component/$component_name_kebab"
ensure_dir "./wp-content/themes/starter_theme/templates/gutenberg/$component_name_kebab"

# Create .twig file
echo "$TWIG_TEMPLATE" > "./wp-content/themes/starter_theme/static/templates/component/$component_name_kebab/$component_name_kebab.twig"
# Create styleguide file
echo "$STYLEGUIDE_TEMPLATE" > "./wp-content/themes/starter_theme/static/templates/component/$component_name_kebab/styleguide.twig"
# Create .php file
echo "$PHP_TEMPLATE" > "./wp-content/themes/starter_theme/templates/gutenberg/$component_name_kebab/$component_name_kebab.php"

echo "Component $1 created successfully."
