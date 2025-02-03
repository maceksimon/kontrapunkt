<?php

use Timber\Timber;

/**
 * If you are installing Timber as a Composer dependency in your theme, you'll need this block
 * to load your dependencies and initialize Timber. If you are using Timber via the WordPress.org
 * plug-in, you can safely delete this block.
 */
$composer_autoload = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $composer_autoload ) ) {
	require_once $composer_autoload;
	Timber::init();
}

/**
 * This ensures that Timber is loaded and available as a PHP class.
 * If not, it gives an error message to help direct developers on where to activate
 */
if ( ! class_exists( 'Timber' ) ) {

	add_action(
		'admin_notices',
		function () {
			echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
		}
	);

	add_filter(
		'template_include',
		function ($template) {
			echo 'Timber not active';
			return;
		}
	);
	return;
}

/**
 * Sets the directories (inside your theme) to find .twig files
 */
Timber::$dirname = [ 'templates' ];

/**
 * By default, Timber does NOT autoescape values.
 */
// add_filter( 'timber/twig/environment/options', function ($options) {
// 	$options['autoescape'] = 'html';

// 	return $options;
// } );

if ( WP_DEBUG ) {
	add_filter( 'timber/twig/environment/options', function ($options) {
		$options['cache'] = true;
		$options['auto_reload'] = true;

		return $options;
	} );
} else {
	add_filter( 'timber/twig/environment/options', function ($options) {
		$options['cache'] = true;

		return $options;
	} );
}

new Base();
