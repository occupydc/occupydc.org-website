<?php
/*
Plugin Name: BP Disable Activation
Description:Disables the requirement for users to activate by email on a standalone WP install with BuddyPress
Version: 0.4
Requires at least: BuddyPress 1.2.3
Tested up to: BuddyPress 1.2.3+wp 2.9.2
Site Wide Only: true
License: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html
Author: John Lynn
Author URI: http://crashutah.com/
Plugin URI:http://crashutah.com/
Last updated:7th june 2010
*/
/***
    Copyright (C) 2009 John Lynn(crashutah.com)

    This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or  any later version.

    This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along with this program; if not, see <http://www.gnu.org/licenses>.

    */
	
/***
    Credit goes to AndyPeatling for most of the initial code
    */
	
/***
    Word of Caution: Use this Plugin at your own risk.  The email activation can be one way to keep spammers from registering on your site.  Make sure you're looking at other options to prevent spammers if you use this plugin to remove the email activation.

    */

/* Load the plugin once BuddyPress is loaded*/
function my_plugin_init() {
	require( dirname( __FILE__ ) . '/bp-disable-activation.php' );
}
add_action( 'bp_init', 'my_plugin_init' );

?>