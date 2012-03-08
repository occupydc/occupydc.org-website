<?php
/**
 * @package Sopastrike
 * @version 1.4
 */
/*
Plugin Name: SOPA Strike
Plugin URI: http://extrafuture.com/sopa-strike-wordpress-plugin/
Description: On Wednesday, January 18th 2012 this plugin will redirect all users of your blog to the SOPA Strike page. It logs your website name and URL to be included on a roll call of supporters.
Author: Phil Nelson
Version: 1.4
Author URI: http://extrafuture.com
*/

// Add our JS
function sopastrike()
{
	if(!is_admin())
	{
		if(time() > 1326862801 && time() < 1326934800)
		{
			header("HTTP/1.1 503 Service Unavailable");
			header("Location: http://sopastrike.com/strike");
			
			exit;
		}
	}
}

function phone_home()
{
	$url = get_bloginfo('siteurl');
	$name = get_bloginfo('name');
	
	$context = stream_context_create(array( 
	  'http' => array( 
	      'timeout' => 1 
	      ) 
	  ) 
	); 
	$content = file_get_contents('http://extrafuture.com/code/sopastrike/track.php?url='.urlencode($url).'&name='.urlencode($name), false,  $context); 
}

register_activation_hook( __FILE__, 'phone_home' );
add_action( 'init', 'sopastrike' );

?>
