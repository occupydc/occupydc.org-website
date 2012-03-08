<?php
/* 
 * Plugin Name:   RSS Poster Free Version
 * Version:       0.7.6
 * Plugin URI:    http://www.wprssposter.com/
 * Description:   RSS Poster
 * Author:        Jesse
 * Author URI:   http://www.wprssposter.com/
 */
if ( ! defined( 'ABSPATH' ) ) exit();	// sanity check

define('RPFDIR', dirname(__FILE__) . '/');               
define('RPFINC', RPFDIR . 'inc/');
define('RPF_URL_ROOT', get_option('siteurl') . '/wp-content/plugins/'.plugin_basename(dirname(__FILE__)).'/');
define('RPF_CACHE', 'cache/');  
define('RPF_PROCESS_FEED_FILE', RPF_URL_ROOT . 'processFeed.php');
define('RPF_VERSION','0.7.6');
require_once(RPFDIR.'RSSPoster.php');
require_once(RPFDIR.'feed.php');
require_once(RPFDIR.'log.php');
require_once(RPFDIR.'misc.php');


register_activation_hook(__FILE__, 'rpf_activate');

//register_deactivation_hook(__FILE__, 'rpf_uninstall');


add_action( 'admin_notices',  'rpf_admin_notices' );
add_action('admin_menu', 'rpf_add_menu_pages');

function rpf_admin_notices(){
	$rpf_options = get_option('rpf_options');
	
	if($rpf_options['version'] !== RPF_VERSION ){
		echo '<div id="notice" class="updated fade"><p>';
				printf( '<b>RSS Poster:</b> <p>Word Limit feature added.</p><p>This version doesn\'t need to Reinstall!</p>');
		echo '</p></div>', "\n";

		$rpf_options['version'] = RPF_VERSION;
		update_option('rpf_options',$rpf_options);
	}

}
function rpf_add_menu_pages(){

		

		add_menu_page('RSS Poster', 'RSS Poster', 8,'rpf_feed_page',  'rpf_feed_page');
			add_submenu_page('rpf_feed_page', 'Settings', 'Settings', 8,'rpf_feed_page',  'rpf_feed_page');
			add_submenu_page('rpf_feed_page', 'Misc', 'Misc', 8, 'rpf_misc_page', 'rpf_misc_page');
			add_submenu_page('rpf_feed_page', 'Log', 'Log', 8, 'rpf_log_page', 'rpf_log_page');
			

		
}

function rpf_activate() {
		
		global $wpdb;
		$db = array( 

			'post' => $wpdb->prefix . "rpf_post", 
			'log' => $wpdb->prefix . "rpf_log"
		);

		$custom_template='Article source: <a href="%SOURCE_URL%">%SOURCE_URL%</a>';

		$rpf_options = get_option('rpf_options');

		if(!$rpf_options){

			$rpf_options['feed'] =array();
			
			$rpf_options['feed'][]=array(
				'id' => '01',
				'name' => '',
				'url' => '',
				'category' => 1,
				'tags' =>'',
				'frequency' => 8,
				'max_items' => 3,
				'lastactive' =>'0',
				'post_status' => 'publish',
				'publish_date' => 'Publish Immediately',
				'author' => 1
			);
			$rpf_options['feed'][]=array(
				'id' => '02',
				'name' => '',
				'url' => '',
				'category' => 1,
				'tags' =>'',
				'frequency' => 8,
				'max_items' => 3,
				'lastactive' =>'0',
				'post_status' => 'publish',
				'publish_date' => 'Publish Immediately',
				'author' => 1
			);
			$rpf_options['feed'][]=array(
				'id' => '03',
				'name' => '',
				'url' => '',
				'category' => 1,
				'tags' =>'',
				'frequency' => 8,
				'max_items' => 3,
				'lastactive' =>'0',
				'post_status' => 'publish',
				'publish_date' => 'Publish Immediately',
				'author' => 1
			);
			$rpf_options['feed'][]=array(
				'id' => '04',
				'name' => '',
				'url' => '',
				'category' => 1,
				'tags' =>'',
				'frequency' => 8,
				'max_items' => 3,
				'lastactive' =>'0',
				'post_status' => 'publish',
				'publish_date' => 'Publish Immediately',
				'author' => 1
			);
			$rpf_options['feed'][]=array(
				'id' => '05',
				'name' => '',
				'url' => '',
				'category' => 1,
				'tags' =>'',
				'frequency' => 8,
				'max_items' => 3,
				'lastactive' =>'0',
				'post_status' => 'publish',
				'publish_date' => 'Publish Immediately',
				'author' => 1
			);
			$rpf_options['word_limit']=0;
			$rpf_options['lastactive']=0;
			$rpf_options['db']=$db;
			$rpf_options['custom_template']=$custom_template;
			add_option('rpf_options',$rpf_options);
		}

		

		
		
   		if(!isset($rpf_options['version'])) {
				
				rpf_install(true);
				$rpf_options['version'] = RPF_VERSION;
				update_option('rpf_options',$rpf_options);

		}
		
		
}
	
	
function rpf_install(){
		
		$rpf_options = get_option('rpf_options');
		

		require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
			

			

		dbDelta(  "CREATE TABLE IF NOT EXISTS {$rpf_options['db']['post']} (
		    				    	  id int(11) unsigned NOT NULL auto_increment,
		    					  hash varchar(255) default '',	    
		    					  PRIMARY KEY  (id)
    		 );" );

		dbDelta(  "CREATE TABLE {$rpf_options['db']['log']} (
  						          id int(11) unsigned NOT NULL auto_increment,
  							  message mediumtext NOT NULL default '',
  							  created_on datetime NOT NULL default '0000-00-00 00:00:00',
  							  PRIMARY KEY  (id)
  		);" );
		
			
						      
       
		
		


}

	
function rpf_uninstall(){
		
	global $wpdb;
	$rpf_options = get_option('rpf_options');
	
	$db=$rpf_options['db'];

	$wpdb->query("DROP TABLE {$db['log']} ");
	//uncomment it
	//$wpdb->query("DROP TABLE {$db['post']} ");
	
    	delete_option('rpf_options');
			
       	
    
}
function rpf_reinstall(){
		
	rpf_uninstall();
	rpf_activate();
       	rpf_install();
	
}     

?>
