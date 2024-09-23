<?php
/**
 * The Template for the sidebar containing the main widget area
 *
 * @package  WordPress
 * @subpackage  Timber
 */

$context = Timber::context();
$templates = ['@wordpress/sidebar/sidebar.twig'];
Timber::render( $templates, $context );
