<?php
/*
Plugin Name: Embed RSS
Plugin URI: 
Description: This plugin adds an RSS icon to the tinymce editor that allows a user to embed an RSS feed into a post or page.
Author: Deanna Schneider
Version: 1.5
Author URI: http://deannaschneider.wordpress.com/

Copyright 2008-2010 Deanna Schneider

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
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

class cets_EmbedRSS {



	function cets_EmbedRSS() {
		global $wp_version;
		// The current version
		define('cets_EmbedRSS_VERSION', '1.0.0');
		
		// Check for WP2.6 installation
		if (!defined ('IS_WP26'))
			define('IS_WP26', version_compare($wp_version, '2.6', '>=') );
		
		//This works only in WP2.6 or higher
		if ( IS_WP26 == FALSE) {
			add_action('admin_notices', create_function('', 'echo \'<div id="message" class="error fade"><p><strong>' . __('Sorry, cets_EmbedRSS works only under WordPress 2.6 or higher',"cetsEmbedRSS") . '</strong></p></div>\';'));
			return;
		}
		
		
		
		// define URL
		define('cets_EmbedRSS_ABSPATH', WP_PLUGIN_DIR.'/'.plugin_basename( dirname(__FILE__) ).'/' );
		define('cets_EmbedRSS_URLPATH', WP_PLUGIN_URL.'/'.plugin_basename( dirname(__FILE__) ).'/' );
		//define('cets_EmbedRSS_TAXONOMY', 'wt_tag');
		
		
		
		
		include_once (dirname (__FILE__)."/lib/shortcodes.php");
		include_once (dirname (__FILE__)."/tinymce/tinymce.php");
		
		
		// set the default options
		$defaults = array(
			'itemcount' => 0,
			'itemauthor' => 0,
			'itemdate' => 0,
			'itemcontent' => 0,
			'line' => 3
			);
			
		
			
			add_option('cets_embedRSS_itemcount', $defaults['itemcount']);
			add_option('cets_embedRSS_itemauthor', $defaults['itemauthor']);
			add_option('cets_embedRSS_itemdate', $defaults['itemdate']);
			add_option('cets_embedRSS_itemcontent', $defaults['itemcontent']);
			add_option('cets_embedRSS_line', $defaults['line']);
		
		
		//add the quick tags and functions
		add_action( 'edit_form_advanced', array(&$this, 'AddQuicktagsAndFunctions') );
		add_action( 'edit_page_form', array(&$this, 'AddQuicktagsAndFunctions') );
	
		if ( in_array( basename($_SERVER['PHP_SELF']), apply_filters( 'cetsGE_editor_pages', array('post-new.php', 'page-new.php', 'post.php', 'page.php') ) ) ) {
				add_action( 'admin_head', array(&$this, 'EditorCSS') );
				add_action( 'admin_footer', array(&$this, 'OutputjQueryDialogDiv') );
				wp_enqueue_script('jquery');
				wp_enqueue_script( 'jquery-ui-draggable', plugins_url('/embed-rss/lib/jquery-ui/ui.draggable.js'), array('jquery-ui-core'), '1.5.2' );
				wp_enqueue_script( 'jquery-ui-resizable', plugins_url('/embed-rss/lib/jquery-ui/ui.resizable.js'), array('jquery-ui-core'), '1.5.2' );
				wp_enqueue_script( 'jquery-ui-dialog', plugins_url('/embed-rss/lib/jquery-ui/ui.dialog.js'), array('jquery-ui-core'), '1.5.2' );
				wp_enqueue_style( 'cets-jquery-ui', plugins_url('/embed_rss/lib/jquery-ui/cets-jquery-ui.css'), array(), $this->version, 'screen' );		}
		
	}
		
		// all the quick tag stuff is pretty much stolen from Viper. He rocks.
	function addQuicktagsAndFunctions() {		
			
			// Get the default options and make sure they are valid
			$itemcount = get_option('cets_EmbedRSS_itemcount');
			$itemauthor = get_option('cets_EmbedRSS_itemauthor');
			$itemdate = get_option('cets_EmbedRSS_itemdate');
			$itemcontent = get_option('cets_EmbedRSS_itemcontent');
			
			
			$itemcount = (is_numeric($itemcount) == true ? $itemcount : 0);
			$itemauthor = (is_numeric($itemauthor) == true ? $itemauthor : 0);
			$itemdate = (is_numeric($itemauthor) == true ? $itemauthor : 0);
			$itemcontent = (is_numeric($itemcontent) == true ? $itemcontent : 0);
			
			
	// This is the non tiny mce button stuff
		$types = array(
			'cetsEmbedRSS' => array(
			__('Embed RSS', 'cets_EmbedRSS'),
			__('Embed an RSS Feed', 'cets_EmbedRSS'),
			__('Please enter the link for the RSS Feed', 'cets_EmbedRSS'),
			__('http://en.blog.wordpress.com/feed/', 'cets_EmbedRSS')
			)
			
		);
		
				
	$buttonhtml = $datajs = '';
		foreach ( $types as $type => $strings ) {
			// HTML for quicktag button
			$buttonshtml .= '<input type="button" class="ed_button" onclick="cets_RSSButtonClick(\'' . $type . '\')" title="' . $strings[1] . '" value="' . $strings[0] . '" />';

			// Create the data array
			$datajs .= "	cets_RSSData['$type'] = {\n";
			$datajs .= '		title: "' . $this->js_escape( ucwords( $strings[1] ) ) . '",' . "\n";
			$datajs .= '		instructions: "' . $this->js_escape( $strings[2] ) . '",' . "\n";
			$datajs .= '		example: "' . js_escape( $strings[3] ) . '"';
			$datajs .= ",\n		itemcount: " . $itemcount . ",\n";
			$datajs .= "		itemcontent: " . $itemcontent .",\n";
			$datajs .= "		itemauthor: " . $itemauthor . ",\n";
			$datajs .= "		itemdate: " . $itemdate . "\n";
			
			$datajs .= "\n	};\n";
		}	
		
		?>
	<script type="text/javascript">
	// <![CDATA[
		
		var cets_RSSData = {};
	<?php echo $datajs; ?>
		
		// Set default heights (IE sucks)
		if ( jQuery.browser.msie ) {
			var cets_RSSDialogDefaultHeight = 245;
			var cets_RSSDialogDefaultExtraHeight = 275;
		} else {
			var cets_RSSDialogDefaultHeight = 258;
			var cets_RSSDialogDefaultExtraHeight = 258;

		}
		
		// This function is run when a button is clicked. It creates a dialog box for the user to input the data.
		function cets_RSSButtonClick( tag ) {
			
			// Close any existing copies of the dialog
			cets_RSSDialogClose();
	
			// Calculate the height/maxHeight (i.e. add some height for Blip.tv)
			cets_RSSDialogHeight = cets_RSSDialogDefaultHeight;
			cets_RSSDialogMaxHeight = cets_RSSDialogDefaultHeight + cets_RSSDialogDefaultExtraHeight;
			
	
			// Open the dialog while setting the width, height, title, buttons, etc. of it
			var buttons = { "<?php echo js_escape('Okay', 'cets_EmbedRSS'); ?>": cets_RSSButtonOkay, "<?php echo js_escape('Cancel', 'cets_EmbedRSS'); ?>": cets_RSSDialogClose };
			var title = cets_RSSData[tag]["title"];
			
			jQuery("#cets_RSS-dialog").dialog({ autoOpen: false, width: 750, minWidth: 750, height: cets_RSSDialogHeight, minHeight: cets_RSSDialogHeight, maxHeight: cets_RSSDialogMaxHeight, title: title, buttons: buttons, resize: cets_RSSDialogResizing });
			
			// Reset the dialog box incase it's been used before
			jQuery("#cets_RSS-dialog-slide-header").removeClass("selected");
			jQuery("#cets_RSS-dialog-input").val("");
			jQuery("#cets_RSS-dialog-tag").val(tag);
	
			// Set the instructions
			jQuery("#cets_RSS-dialog-message").html("<p>" + cets_RSSData[tag]["instructions"] + "</p><p><strong><?php echo js_escape( __('Example:', 'cets_EmbedRSS') ); ?></strong></p><p><code>" + cets_RSSData[tag]["example"] + "</code></p>");
	
			// Style the jQuery-generated buttons by adding CSS classes and add second CSS class to the "Okay" button
			jQuery(".ui-dialog button").addClass("button").each(function(){
				if ( "<?php echo js_escape('Okay', 'cets_EmbedRSS'); ?>" == jQuery(this).html() ) jQuery(this).addClass("button-highlighted");
			});
	
			// Set up the Additional Settings Box
				jQuery(".cets_RSS-dialog-slide").removeClass("hidden");
				jQuery("#cets_RSS-dialog-itemcount").val(cets_RSSData[tag]["itemcount"]);
				jQuery("#cets_RSS-dialog-itemcontent").val(cets_RSSData[tag]["itemcontent"]);
				jQuery("#cets_RSS-dialog-itemauthor").val(cets_RSSData[tag]["itemauthor"]);
				jQuery("#cets_RSS-dialog-itemdate").val(cets_RSSData[tag]["itemdate"]);

	
			// Do some hackery on any links in the message -- jQuery(this).click() works weird with the dialogs, so we can't use it
			jQuery("#cets_RSS-dialog-message a").each(function(){
				jQuery(this).attr("onclick", 'window.open( "' + jQuery(this).attr("href") + '", "_blank" );return false;' );
			});
	
			// Show the dialog now that it's done being manipulated
			jQuery("#cets_RSS-dialog").dialog("open");
			
			
			// Focus the input field
			jQuery("#cets_RSS-dialog-input").focus();
		}
	
		// Close + reset
		function cets_RSSDialogClose() {
			jQuery(".ui-dialog").height(cets_RSSDialogDefaultHeight);
			jQuery("#cets_RSS-dialog").dialog("close");
		}
	
		// Callback function for the "Okay" button
		function cets_RSSButtonOkay() {
	
			var tag = jQuery("#cets_RSS-dialog-tag").val();
			var text = jQuery("#cets_RSS-dialog-input").val();
			var itemcount = jQuery("#cets_RSS-dialog-itemcount").val();
			var itemdate = jQuery("#cets_RSS-dialog-itemdate").val();
			var itemcontent = jQuery("#cets_RSS-dialog-itemcontent").attr('checked')?1:0;
			var itemauthor = jQuery("#cets_RSS-dialog-itemauthor").attr('checked')?1:0;
			var itemdate = jQuery("#cets_RSS-dialog-itemdate").attr('checked')?1:0;
			
			

			
			if ( !tag || !text ) return cets_RSSDialogClose();
	
			// Create the shortcode here
			var text = "[" + tag + " id=" + text + " itemcount=" + itemcount + " itemauthor=" + itemauthor + " itemdate=" + itemdate + " itemcontent=" + itemcontent + "]";
			
	
			if ( typeof tinyMCE != 'undefined' && ( ed = tinyMCE.activeEditor ) && !ed.isHidden() ) {
				ed.focus();
				if (tinymce.isIE)
					ed.selection.moveToBookmark(tinymce.EditorManager.activeEditor.windowManager.bookmark);
	
				ed.execCommand('mceInsertContent', false, text);
			} else
				edInsertContent(edCanvas, text);
	
			cets_RSSDialogClose();
		}
	
		// This function is called while the dialog box is being resized.
		function cets_RSSDialogResizing( test ) {
			if ( jQuery(".ui-dialog").height() > cets_RSSDialogHeight ) {
				jQuery("#cets_RSS-dialog-slide-header").addClass("selected");
			} else {
				jQuery("#cets_RSS-dialog-slide-header").removeClass("selected");
			}
		}
	
		// On page load...
		jQuery(document).ready(function(){
			// Add the buttons to the HTML view 
			var buttonshtml = '<input type=\"button\" class=\"ed_button\" onclick=\"cets_RSSButtonClick(\'cetsEmbedRSS\')\" title=\"Embed an RSS Feed\" value=\"Embed RSS\" />';
			
			//jQuery("#ed_toolbar").append(<?php echo $this->js_escape( $buttonshtml ); ?>);
			jQuery("#ed_toolbar").append(buttonshtml);
			
			// Make the "Dimensions" bar adjust the dialog box height
			jQuery("#cets_RSS-dialog-slide-header").click(function(){
				if ( jQuery(this).hasClass("selected") ) {
					jQuery(this).removeClass("selected");
					jQuery(this).parents(".ui-dialog").animate({ height: cets_RSSDialogHeight });
				} else {
					jQuery(this).addClass("selected");
					jQuery(this).parents(".ui-dialog").animate({ height: cets_RSSDialogMaxHeight });
				}
			});
	
			// If the Enter key is pressed inside an input in the dialog, do the "Okay" button event
			jQuery("#cets_RSS-dialog :input").keyup(function(event){
				if ( 13 == event.keyCode ) // 13 == Enter
					cets_RSSButtonOkay();
			});
	
			// Make help links open in a new window to avoid loosing the post contents
			jQuery("#cets_RSS-dialog-slide a").each(function(){
				jQuery(this).click(function(){
					window.open( jQuery(this).attr("href"), "_blank" );
					return false;
				});
			});
		});
	// ]]>
	</script>
	<?php
	} //end addquicktags function
	
	// WordPress' js_escape() won't allow <, >, or " -- instead it converts it to an HTML entity. This is a "fixed" function that's used when needed.
	function js_escape($text) {
		$safe_text = addslashes($text);
		$safe_text = preg_replace('/&#(x)?0*(?(1)27|39);?/i', "'", stripslashes($safe_text));
		$safe_text = preg_replace("/\r?\n/", "\\n", addslashes($safe_text));
		$safe_text = str_replace('\\\n', '\n', $safe_text);
		return apply_filters('js_escape', $safe_text, $text);
	}
		
	
	
	function OutputjQueryDialogDiv() { ?>
		<div class="hidden">
			<div id="cets_RSS-dialog">
				<div class="cets_RSS-dialog-content">
					<div id="cets_RSS-dialog-message"></div>
					<p><input type="text" id="cets_RSS-dialog-input" style="width:98%" /></p>
					<input type="hidden" id="cets_RSS-dialog-tag" />
                    
                   
				</div>
                
				<div id="cets_RSS-dialog-slide-header" class="cets_RSS-dialog-slide ui-dialog-titlebar"><?php _e('Additional Settings', 'cets_RSSEmbed'); ?></div>
				<div id="cets_RSS-dialog-slide" class="cets_RSS-dialog-slide cets_RSS-dialog-content">
					<p><?php printf( __("The default information can be set on this plugin's <a href='%s'>settings page</a>. However, you can set custom information for this one particular map here:", 'cets_RSSEmbed'), admin_url('options-general.php?page=cets_embedRSS_options') ); ?></p>
					
					<p>
						How many items? 
			            <select name="cets_RSS-dialog-itemcount" id="cets_RSS-dialog-itemcount">
			            	<?php for ($i=0; $i < 21; $i++) {
							if ($i > 0)
							echo('<option value="' . $i . '">' . $i . "</option>");
							else 
							echo ('<option value="0">Include all</option>');
						}
			           ?>
					   </select>
					</p>
					<p>Include Content: <input type="checkbox" id="cets_RSS-dialog-itemcontent" class="cets_RSS-dialog-dim"  value="1" <?php if (get_option('cets_embedRSS_itemcontent')) {echo ' checked="checked"';}?> /> <br/>
					<p>
					<p>Include Author: <input type="checkbox" id="cets_RSS-dialog-itemauthor" class="cets_RSS-dialog-dim"  value="1" <?php if (get_option('cets_embedRSS_itemauthor')) {echo ' checked="checked"';}?> /> <br/>
					<p>
					<p>Include Date: <input type="checkbox" id="cets_RSS-dialog-itemdate" class="cets_RSS-dialog-dim"  value="1" <?php if (get_option('cets_embedRSS_itemdate')) {echo ' checked="checked"';}?> /> <br/>
					<p>
					
					
				</div>
                
				</div>
			</div>
		</div>
		
		<?php
	}
	
	function EditorCSS() {
		echo "<style type='text/css'>\n	#cets_RSS-precacher { display: none; }\n";

		// Attempt to match the dialog box to the admin colors
		$color = ( 'classic' == get_user_option('admin_color', $user_id) ) ? '#CFEBF7' : '#EAF3FA';
		$color = apply_filters( 'cets_RSS_titlebarcolor', $color ); // Use this hook for custom admin colors
		echo "	.ui-dialog-titlebar { background: $color; }\n";
		echo "</style>\n";
	}
		
	
	

}// end class
// Start this plugin once all other plugins are fully loaded
add_action( 'plugins_loaded', create_function( '', 'global $cets_EmbedRSS; $cets_EmbedRSS = new cets_EmbedRSS();' ) );

// Admin options menu stuff starts here
include_once (dirname (__FILE__)."/options.php");

?>