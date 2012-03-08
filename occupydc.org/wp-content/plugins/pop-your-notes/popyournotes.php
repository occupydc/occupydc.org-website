<?php

/*
Plugin Name: Pop Your Notes
Plugin URI: 
Description: Showing modal window as you want, setting with color, style, and wp's conditional branches.
Version: 1.0.2
Author: Studio Switch
Author URI: 
*/

/*  Copyright 2010-2011 Studio Switch.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define("pyn_domain", "pyn");
load_plugin_textdomain(pyn,'wp-content/plugins/pop-your-notes/lang/', 'pop-your-notes/lang/');

// Some Defaults
$var1				= __("This is sample contents.", "pyn");
$var2				= '#FF0000';
$var3				= '#FFFFFF';
$var4				= '';
$var5				= '1';

$availpynvars = get_option("popyournotes-var1");
if(! $availoynvars){
	add_option("popyournotes-var1", $var1);
	add_option("popyournotes-var2", $var2);
	add_option("popyournotes-var3", $var3);
	add_option("popyournotes-var4", $var4);
	add_option("popyournotes-type", $var5);
	for($i = 1; $i < 21; $i++) {
	add_option("popyournotes-option$i", $var4);
	}
}

add_action('admin_menu', 'popyournotes');
add_action('admin_head', 'adminhead_inc');

function popyournotes() {
	global $wpdb;
	$pyn_menutitle = __("Pop Your Notes Settings", "pyn");
	add_submenu_page('options-general.php', $pyn_menutitle, $pyn_menutitle, 8, __FILE__, 'popyournotes_menu');
}

function popyournotes_menu() {
include('admin-page.php');
}

function adminhead_inc() {
include('adminhead.php');
}

$plug_path = str_replace("\\", "/", plugin_dir_url( __FILE__ ));
$plug_path = substr($plug_path, 0, -1);

wp_enqueue_script('jquery');
wp_enqueue_script('simplemodal', $plug_path.'/jquery.simplemodal-1.4.js', array('jquery'), '1.4');

include('purpose.php');

?>