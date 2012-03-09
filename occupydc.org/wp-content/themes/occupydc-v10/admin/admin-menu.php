<?php
/**
 * admin-menu.php
 *
 * Theme Settings Admin Panel
 *
 * @package WordPress
 * @subpackage CP-Minimal
 * @since 2.9.0
 */


/* ----------------------------- */
/*  set themename and shortname  */
/* ----------------------------- */

$themename = "Occupy DC";
$shortname = "cp";


/* ---------------------------------------------- */
/*  load the admin panel function into wordpress  */
/* ---------------------------------------------- */

add_action('admin_init', 'cp_theme_add_init');
add_action('admin_menu', 'cp_theme_add_admin');


/* --------------------- */
/*  Admin Panel Options  */
/* --------------------- */

$options = array (

	array( "name" => $themename,
			"type" => "title"),


	/* -------------------------- */
	/*  SECTION: HEADER & FOOTER  */
	/* -------------------------- */
 
	array( "name" => __("General Settings", 'cp'),
			"type" => "section"),

		array( "type" => "open"),
 
			array( "name" => __("Global Announcement", 'cp'),
					"desc" => __("Display announcement on the top of every page of the website.", 'cp'),
					"id" => $shortname."_announcement",
					"type" => "textarea",
					"std" => ""),

	array( "name" => __("About title", 'cp'),
					"desc" => __("The About column title.", 'cp'),
					"id" => $shortname."_abouttitle",
					"type" => "text",
					"std" => ""),

			array( "name" => __("About Snippet", 'cp'),
					"desc" => __("The About Snippet is shown on the front page left column.", 'cp'),
					"id" => $shortname."_about",
					"type" => "textarea",
					"std" => ""),
					
						array( "name" => __("Frontpage, right column title", 'cp'),
					"desc" => __("The right column title.", 'cp'),
					"id" => $shortname."_fpwtitle",
					"type" => "text",
					"std" => ""),

			array( "name" => __("Frontpage, right column Snippet", 'cp'),
					"desc" => __("This Snippet is shown on the front page right column.", 'cp'),
					"id" => $shortname."_fpw",
					"type" => "textarea",
					"std" => ""),

			array( "name" => __("Announcement Category", 'cp'),
					"desc" => __("Enter the category ID of the category you would like to have displayed on the front page under <strong>Recent Announcements by Occupy DC</strong>.", 'cp'),
					"id" => $shortname."_ra_cat",
					"type" => "text",
					"std" => ""),
					
			array( "name" => __("Number of Announcements", 'cp'),
					"desc" => __("Enter the amount of announcements you would like to have displayed on the front page under <strong>Recent Announcements by Occupy DC</strong>.", 'cp'),
					"id" => $shortname."_ra_num",
					"type" => "text",
					"std" => ""),								

			array( "name" => __("Analytics Code", 'cp'),
					"desc" => __("You can paste your Google Analytics or any other tracking code in this box. This will be automatically added to the footer.", 'cp'),
					"id" => $shortname."_analytics",
					"type" => "textarea",
					"std" => ""),	

			array( "name" => __("Feedburner URL", 'cp'),
					"desc" => __("Insert your Feedburner RSS URL here. Leave this blank if you use the blogs RSS.", 'cp'),
					"id" => $shortname."_feedburner",
					"type" => "text",
					"std" => ""),
					
			array( "name" => __("Footer Text", 'cp'),
					"desc" => __("You can change the footer text here. The following shortcodes are allowed: <br />[copyright] displays the date and a link to the blog<br />[wordpress] displays a powered by link to wordpress.org<br />[credit] displays a link to the author of the theme.", 'cp'),
					"id" => $shortname."_footer",
					"type" => "textarea",
					"std" => "[copyright] - [wordpress] - [credit]"),	

 array( "name" => __("Widget Image 1", 'cp'),
					"desc" => __("Enter the full link to your widget image in the left column on the front page.", 'cp'),
					"id" => $shortname."_wimage1",
					"type" => "text",
					"std" => ""),
 array( "name" => __("Widget Image 2", 'cp'),
					"desc" => __("Enter the full link to your widget image in the right column on the front page.", 'cp'),
					"id" => $shortname."_wimage2",
					"type" => "text",
					"std" => ""),
		
	
		array( "type" => "close"),
		
 
		

);


/* -------------------------- */
/*  Add Theme Admin Function  */
/* -------------------------- */

function cp_theme_add_admin() 
{
 
	global $themename, $shortname, $options;
 
	if ( $_GET['page'] == basename(__FILE__) ) 
	{
 
		if ( 'save' == $_REQUEST['action'] ) 
		{
 
			foreach ($options as $value) 
			{
				update_option( $value['id'], $_REQUEST[ $value['id'] ] ); 
			}
 
			foreach ($options as $value) 
			{
				if( isset( $_REQUEST[ $value['id'] ] ) ) 
				{ 
					update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); 
				} 
				else 
				{ 
					delete_option( $value['id'] ); 
				} 
			}
 
			$location = $_SERVER['REQUEST_URI'] . "&saved=true";
			header("Location: $location");
			die;
 
		} 
		else if( 'reset' == $_REQUEST['action'] ) 
		{
 
			foreach ($options as $value) 
			{
				delete_option( $value['id'] ); 
			}
 
			$location = $_SERVER['REQUEST_URI'] . "&reset=true";
			header("Location: $location");
			die;
 
		}
	}

	add_theme_page($themename." Options", "$themename Options", 'edit_themes', basename(__FILE__), 'cp_theme_admin');
}





/* ------------------------------------------------- */
/*  Load the CSS and JavaScript for the admin panel  */
/* ------------------------------------------------- */

function cp_theme_add_init() 
{
	$template_dir = get_bloginfo('template_directory');
	wp_enqueue_style('admin-menu', $template_dir . '/admin/admin-menu.css', false, '1.0', 'all');
}


/* ------------------------------- */
/*  Display the theme admin panel  */
/* ------------------------------- */

function cp_theme_admin() 
{
 
	global $themename, $shortname, $options;
	$i=0;
 
	if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.__(" settings saved.", "cp").'</strong></p></div>';
	if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.__(" settings reset.", "cp").'</strong></p></div>';
 
	?>
	<div class="wrap admin_wrap">

		<h2><?php echo $themename; ?> <?php _e('Settings', 'cp'); ?></h2>
 
		<div class="admin_opts">
		
			<form method="post">

				<?php foreach ($options as $value) {

					switch ( $value['type'] ) {
 
						case "open":
							?>
							<?php break;
 
						case "close":
							?>
							</div>
							</div>
							<br />
							<?php break;
 
						case "title":
							?>
							<?php break;
 
						case 'text':
							?>
							<div class="admin_input admin_text">
								<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
								<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id'])  ); } else { echo $value['std']; } ?>" />
								<small><?php echo $value['desc']; ?></small><div class="clear"></div>
							</div>
							<?php break;
 
						case 'password':
							?>
							<div class="admin_input admin_text">
								<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
								<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id'])  ); } else { echo $value['std']; } ?>" />
								<small><?php echo $value['desc']; ?></small><div class="clear"></div>
							</div>
							<?php break;

						case 'textarea':
							?>
							<div class="admin_input admin_textarea">
								<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
							 	<textarea name="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" cols="" rows=""><?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id']) ); } else { echo $value['std']; } ?></textarea>
								<small><?php echo $value['desc']; ?></small><div class="clear"></div>
							</div>
							<?php break;
 
						case 'select':
							?>
							<div class="admin_input admin_select">
								<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
								<select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
									<?php foreach ($value['options'] as $option) { ?>
									<option <?php if (get_option( $value['id'] ) == $option) { echo 'selected="selected"'; } ?>><?php echo $option; ?></option><?php } ?>
								</select>
								<small><?php echo $value['desc']; ?></small><div class="clear"></div>
							</div>
							<?php break;
 
						case "checkbox":
							?>
							<div class="admin_input admin_checkbox">
								<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
								<?php if(get_option($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = "";} ?>
								<input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />
								<small><?php echo $value['desc']; ?></small><div class="clear"></div>
							</div>
							<?php break; 

						case "section":
							$i++;
							?>
							<div class="admin_section">
								<div class="admin_title"><h3><img src="<?php bloginfo('template_directory')?>/functions/images/trans.gif" class="inactive" alt="""><?php echo $value['name']; ?></h3><span class="submit"><input name="save<?php echo $i; ?>" type="submit" value="<?php _e('Save changes', 'cp'); ?>" /></span>
								<div class="clear"></div>
							</div>
							<div class="admin_options">
							<?php break;

						case "sub_open":
							?>
							<div class="admin_subsection">
							<?php break;
 
						case "sub_close":
							?>
							</div>
							<br />
							<?php break;

 
					}
				}
				?>
 
				<input type="hidden" name="action" value="save" />
			</form>

			<form method="post">
				<p class="submit">
					<input name="reset" type="submit" value="<?php _e('Reset', 'cp'); ?>" />
					<input type="hidden" name="action" value="reset" />
				</p>
			</form>

		</div> 

<?php
}

?>
