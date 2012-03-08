<?php
    
	function cets_embedRSS_menu() {
	  add_options_page('RSS Embed Options', 'Embed RSS', 8, 'cets_embedRSS_options', 'cets_embedRSS_options');
	}
	
	function cets_embedRSS_options() {
	  echo '<div class="wrap">';
	  echo '<form method="post" action="options.php">';
	 settings_fields( 'cets_embedRSS-group' );

	  ?>
	  <h2>RSS Embedding Options.</h2>
	  <p>These settings can be over-ridden on a feed-by-feed basis when feeds are inserted.</p>
	  <table class="form-table">
		
		<tr valign="top">
		<th scope="row">Default Number of items</th>
		<td><select name="cets_embedRSS_itemcount" id="cets_embedRSS_itemcount">
            <?php for ($i=0; $i < 21; $i++) {
				if ($i > 0){
					echo('<option value="' . $i. '"');
					if (get_option('cets_embedRSS_itemcount')== $i) {
						echo (' selected="selected"');	
					} 
					echo ('>' . $i . "</option>");
				}
				else 
				echo ('<option value="0">Include all</option>');
			}
           ?>
            </select></td>
		</tr>
		<tr valign="top">
		<th scope="row">Display content?</th>
		<td><input type="checkbox" name="cets_embedRSS_itemcontent" value="1" <?php if(get_option('cets_embedRSS_itemcontent')) {echo 'checked="checked"';}?>/>  </td>
		</tr>
		
		<tr valign="top">
		<th scope="row">Display author?</th>
		<td><input type="checkbox" name="cets_embedRSS_itemauthor" value="1" <?php if(get_option('cets_embedRSS_itemauthor')) {echo 'checked="checked"';}?>/>  </td>
		</tr>
		
		<tr valign="top">
		<th scope="row">Display date?</th>
		<td><input type="checkbox" name="cets_embedRSS_itemdate" value="1" <?php if(get_option('cets_embedRSS_itemdate')) {echo 'checked="checked"';}?>/>  </td>
		</tr>
		

	  </table>
	  
	  <input type="hidden" name="action" value="update" />
	  <input type="hidden" name="page_options" value="cets_embedRSS_itemcount,cets_embedRSS_itemcontent,cets_embedRSS_itemauthor,cets_embedRSS_itemdate" />
	  <p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>


	  
	  </form>
	  </div>
	  <?php
	}


function register_cets_embedRSS_settings() { // whitelist options
  	register_setting( 'cets_embedRSS-group', 'cets_embedRSS_itemcontent' );
 	register_setting( 'cets_embedRSS-group', 'cets_embedRSS_itemauthor' );
	register_setting( 'cets_embedRSS-group', 'cets_embedRSS_itemcount' );
 	register_setting( 'cets_embedRSS-group', 'cets_embedRSS_itemdate' );

}

if ( is_admin() ){
	add_action('admin_menu', 'cets_EmbedRSS_menu');
	add_action( 'admin_init', 'register_cets_EmbedRSS_settings' );


}
	
?>