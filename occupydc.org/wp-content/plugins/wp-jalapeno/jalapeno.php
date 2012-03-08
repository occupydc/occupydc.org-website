<?php
/*
Plugin Name: WP Jalapeno
Plugin URI: http://www.wpjalapeno.com
Description: Embed Salsa Actions in your website
Author: New Signature
Version: 1.0.2
Author URI: http://www.newsignature.com
*/

// Make sure the PHP version is up to speed
if(version_compare(PHP_VERSION, '5.0.0') >=  0){

  @include(dirname(__FILE__).'/jalapeno.plugin.php');

// endif PHP version check
} else {
  // Attempting the cleanest and most user friendly manner to deactivate the plugin and give the
  // user an error message. (Would be great if WordPress provided a direct mechanism to handle this).
  // The function deactivate_plugins only appears to exist here when the plugin is being activated
  // which makes the call to deactivate it redundant since the DIE will cause WordPress to
  // deactivate the plugin. But, you never know.
  if(function_exists('deactivate_plugins')){
    deactivate_plugins(substr(__FILE__, strlen(WP_PLUGIN_DIR)+1), true);
    die('<span class="error">You need to use PHP 5.0.0 or higher for your WordPress site in order to use WP Jalapeno. You are currently running PHP '.PHP_VERSION.'</span>');
  }
}
