<?php

/*
+----------------------------------------------------------------+
+	cets_EmbedRSS-tinymce V1.60
+	by Deanna Schneider
+   required for cets_EmbedRSS and WordPress 2.5
+----------------------------------------------------------------+
*/

// look up for the path
require_once( dirname( dirname(__FILE__) ) .'/cets_EmbedRSS-config.php');

global $wpdb;

// check for rights
if ( !is_user_logged_in() || !current_user_can('edit_posts') ) 
	wp_die(__("You are not allowed to be here"));





?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>cets_EmbedRSS</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo get_option('siteurl') ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	<script language="javascript" type="text/javascript">
	function init() {
		tinyMCEPopup.resizeToInnerSize();
	}
	
	function insertcetsEmbedRSSLink() {
		
		var tagtext;
		
		var rss = document.getElementById('rss_panel');
		
		
		// who is active ?
		if (rss.className.indexOf('current') != -1) {
			var rssid = document.getElementById('rsstag').value;
			var itemcount = document.getElementById('itemcount').selectedIndex;
			var itemauthor = document.getElementById('itemauthor').checked;
			var itemdate = document.getElementById('itemdate').checked;
			var itemcontent = document.getElementById('itemcontent').checked;
				
			if (rssid != '' )
			{
				tagtext = "[cetsEmbedRSS id='" + rssid + "'";
				
				tagtext += " itemcount='" + itemcount + "'";
				
				if (itemauthor == 1){
					tagtext += " itemauthor='1'";
				}
				if (itemdate == 1) {
					tagtext += " itemdate='1'";
				}
				if (itemcontent == 1) {
					tagtext += " itemcontent='1'";
				}
				
				tagtext = tagtext  + "]";
			}	
			else
				tinyMCEPopup.close();
		}
	
		
		if(window.tinyMCE) {
			window.tinyMCE.execInstanceCommand('content', 'mceInsertContent', false, tagtext);
			//Peforms a clean up of the current editor HTML. 
			//tinyMCEPopup.editor.execCommand('mceCleanup');
			//Repaints the editor. Sometimes the browser has graphic glitches. 
			tinyMCEPopup.editor.execCommand('mceRepaint');
			tinyMCEPopup.close();
		}
		
		return;
	}
	</script>
	<base target="_self" />
</head>
<body id="link" onload="tinyMCEPopup.executeOnLoad('init();');document.body.style.display='';document.getElementById('rsstag').focus();" style="display: none">
<!-- <form onsubmit="insertLink();return false;" action="#"> -->
	<form name="cets_EmbedRSS" action="#">
	<div class="tabs">
    
    
		<ul>
			<li id="rss_tab" class="current"><span><a href="javascript:mcTabs.displayTab('rss_tab','rss_panel');" onmousedown="return false;"><?php _e("rss", 'cetsEmbedRSS'); ?></a></span></li>
		</ul>
       
	</div> 
	
	<div class="panel_wrapper">
     
		<!-- rss panel -->
		<div id="rss_panel" class="panel current">
        <br/>
		<table border="0" cellpadding="2" cellspacing="0">
         <tr>
            <td nowrap="nowrap"><label for="rsstag"><?php _e("Enter a feed URL:", 'cetsEmbedRSS'); ?></label></td>
            <td><input type="text" id="rsstag" name="rsstag" style="width: 190px" />
            </td>
          </tr>
          <tr>
            <td nowrap="nowrap"><label for="itemcount"><?php _e("How many items?", 'cetsEmbedRSS'); ?></label></td>
            <td><select name="itemcount" id="itemcount">
            <?php 
			$itemcount = get_option('cets_embedRSS_itemcount');
			for ($i=0; $i < 21; $i++) {
				if ($i > 0){
					echo('<option value="' . $i . '"');
					if ($itemcount == $i) { echo (' selected="selected"');}
					echo ('>' . $i . "</option>");
				}
				else 
				echo ('<option value="0">Include all</option>');
			}
           ?>
            </select>
            </td>
          </tr>
          <tr>
            <td nowrap="nowrap"><label for="itemcontent"><?php _e("Display content?", 'cetsEmbedRSS'); ?></label></td>
            <td><input type="checkbox" id="itemcontent" name="itemcontent" value="1" <?php if (get_option('cets_embedRSS_itemcontent')) echo 'checked="checked"';?> />
            </td>
          </tr>
          <tr>
            <td nowrap="nowrap"><label for="itemauthor"><?php _e("Display author?", 'cetsEmbedRSS'); ?></label></td>
            <td><input type="checkbox" id="itemauthor" name="itemauthor" value="1" <?php if (get_option('cets_embedRSS_itemauthor')) echo 'checked="checked"';?>  />
            </td>
          </tr>
          <tr>
            <td nowrap="nowrap"><label for="itemdate"><?php _e("Display date?", 'cetsEmbedRSS'); ?></label></td>
            <td><input type="checkbox" id="itemdate" name="itemdate" value="1" <?php if (get_option('cets_embedRSS_itemdate')) echo 'checked="checked"';?>  />
            </td>
          </tr>
          
        </table>
		</div>
		<!-- end rss panel -->
		
		
	</div>

	<div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="cancel" name="cancel" value="<?php _e("Cancel", 'cetsEmbedRSS'); ?>" onclick="tinyMCEPopup.close();" />
		</div>

		<div style="float: right">
			<input type="submit" id="insert" name="insert" value="<?php _e("Insert", 'cetsEmbedRSS'); ?>" onclick="insertcetsEmbedRSSLink();" />
		</div>
        <div style="clear:both">
        <p>&nbsp;</p>
        This will place a snippet of code in your post or page that will be replaced with the contents of the feed when the page is viewed.</div>
		</div>
</form>
</body>
</html>
<?php

?>