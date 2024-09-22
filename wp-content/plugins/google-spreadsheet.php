<?php
/**
 * @package Google Spreadsheet Data Feed
 * @version 1.0
 */
/*
Plugin Name: Google Spreadsheet Data Feed
Plugin URI: https://proficio.cz/
Description: This is Google Spreadsheet Data Feed for remote statistics
Author: PROFICIO
Version: 1.0
Author URI: https://proficio.cz/
*/

include_once('../../wp-load.php');
include_once('../../wp-admin/includes/plugin.php');

if( isset($_GET['type']) ) {
  $type = $_GET['type'];
} else {
  echo "Missing parameter";
}

if($type == 'version') {

  global $wp_version;

  $all_plugins = get_plugins();
  $upgrade_plugins = array();
  $current = get_site_transient( 'update_plugins' );

  foreach ( (array)$all_plugins as $plugin_file => $plugin_data) {
    if ( isset( $current->response[ $plugin_file ] ) ) {
      $upgrade_plugins[ $plugin_file ] = (object) $plugin_data;
      $upgrade_plugins[ $plugin_file ]->update = $current->response[ $plugin_file ];
    }
  }

  $update_count = count($upgrade_plugins);

  if($update_count) {
    echo $wp_version . " (" . $update_count . ")";
  }else{
    echo $wp_version;
  }

}
else {
  echo "Wrong type";
}
