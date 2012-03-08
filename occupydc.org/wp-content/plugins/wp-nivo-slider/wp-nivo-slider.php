<?php
/*
Plugin Name: WP Nivo Slider
Plugin URI: http://www.nerdhead.com.br/en/wp-nivo-slider-en/
Description: Creates a slider using js created by Gilbert Pellegrom. WordPress plugin develop by Rafael Cirolini
Version: 2.0
Author: Rafael Cirolini
Author URI: http://www.nerdhead.com.br/
License: GPL2
*/

/*  Copyright 2010  WP Nivo Slider - Rafael Cirolini  (email : rafael@nerdhead.com.br)
 
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.
 
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action('admin_menu', 'wpns_add_menu');
add_action('admin_init', 'wpns_reg_function' );

register_activation_hook( __FILE__, 'wpns_activate' );

add_theme_support('post-thumbnails');

function wpns_add_menu() {
    $page = add_options_page('WP Nivo Slider', 'WP Nivo Slider', 'administrator', 'wpns_menu', 'wpns_menu_function');
}

function wpns_reg_function() {
	register_setting( 'wpns-settings-group', 'wpns_category' );
	register_setting( 'wpns-settings-group', 'wpns_effect' );
	register_setting( 'wpns-settings-group', 'wpns_slices' );
	register_setting( 'wpns-settings-group', 'wpns_width' );
	register_setting( 'wpns-settings-group', 'wpns_height' );
}

function wpns_activate() {
	add_option('wpns_category','1');
	add_option('wpns_effect','random');
	add_option('wpns_slices','5');	
}

wp_enqueue_script('nivo_slider', WP_PLUGIN_URL . '/wp-nivo-slider/js/jquery.nivo.slider.pack.js', array('jquery'), '2.3' );

function show_nivo_slider() {
?>

<style type="text/css">
#slider {
	-moz-box-shadow:0 0 10px #333333;
	background:url("<?php echo WP_PLUGIN_URL . "/wp-nivo-slider/"; ?>images/loading.gif") no-repeat scroll 50% 50% #202834;
	width:<?php echo get_option('wpns_width'); ?>px; /* Change this to your images width */
    height:<?php echo get_option('wpns_height'); ?>px; /* Change this to your images height */
}
#slider img {
	position:absolute;
	top:0px;
	left:0px;
	display:none;
}
#slider a {
	border:0 none;
	display:block;
}
/* The Nivo Slider styles */
.nivoSlider {
	position:relative;
}
.nivoSlider img {
	position:absolute;
	top:0px;
	left:0px;
}
/* If an image is wrapped in a link */
.nivoSlider a.nivo-imageLink {
	position:absolute;
	top:0px;
	left:0px;
	width:100%;
	height:100%;
	border:0;
	padding:0;
	margin:0;
	z-index:60;
	display:none;
}
/* The slices in the Slider */
.nivo-slice {
	display:block;
	position:absolute;
	z-index:50;
	height:100%;
}
/* Caption styles */
.nivo-caption {
	position:absolute;
	left:0px;
	bottom:0px;
	background:#000;
	color:#fff;
	opacity:0.8; /* Overridden by captionOpacity setting */
	width:100%;
	z-index:89;
}
.nivo-caption p {
	padding:5px;
	margin:0;
}
.nivo-caption a {
	display:inline !important;
}
.nivo-html-caption {
    display:none;
}
/* Direction nav styles (e.g. Next & Prev) */
.nivo-directionNav a {
	position:absolute;
	top:45%;
	z-index:99;
	cursor:pointer;
}
.nivo-prevNav {
	left:0px;
}
.nivo-nextNav {
	right:0px;
}
.nivo-controlNav {
	bottom:-30px;	
	left:47%;
	position:absolute;
}
.nivo-controlNav a {
	background:url("<?php echo WP_PLUGIN_URL . "/wp-nivo-slider/"; ?>images/bullets.png") no-repeat scroll 0 0 transparent;
	border:0 none;
	display:block;
	float:left;
	height:10px;
	margin-right:3px;
	text-indent:-9999px;
	width:10px;
}
.nivo-controlNav a.active {
	background-position:-10px 0;
}
.nivo-controlNav a {
	cursor:pointer;
	position:relative;
	z-index:99;
}
.nivo-controlNav a.active {
	font-weight:bold;
}
.nivo-directionNav a {
	background:url("<?php echo WP_PLUGIN_URL . "/wp-nivo-slider/"; ?>images/arrows.png") no-repeat scroll 0 0 transparent;
	border:0 none;
	display:block;
	height:34px;
	text-indent:-9999px;
	width:32px;
}
a.nivo-nextNav {
	background-position:-32px 0;
	right:10px;
}
a.nivo-prevNav {
	left:10px;
}
</style>

<script type="text/javascript">
jQuery(window).load(function() {
	jQuery('#slider').nivoSlider({
		effect:'<?php echo get_option('wpns_effect'); ?>',
		slices:<?php echo get_option('wpns_slices'); ?>,
		animSpeed:500, //Slide transition speed
        pauseTime:3000,
        startSlide:0, //Set starting Slide (0 index)
        directionNav:true, //Next & Prev
        directionNavHide:true, //Only show on hover
        controlNav:true, //1,2,3...
        controlNavThumbs:false, //Use thumbnails for Control Nav
        controlNavThumbsFromRel:false, //Use image rel for thumbs
        controlNavThumbsSearch: '.jpg', //Replace this with...
        controlNavThumbsReplace: '_thumb.jpg', //...this in thumb Image src
        keyboardNav:true, //Use left & right arrows
        pauseOnHover:true, //Stop animation while hovering
        manualAdvance:false, //Force manual transitions
        captionOpacity:0.8, //Universal caption opacity
        beforeChange: function(){},
        afterChange: function(){},
        slideshowEnd: function(){}, //Triggers after all slides have been shown
        lastSlide: function(){}, //Triggers when last slide is shown
        afterLoad: function(){} //Triggers when slider has loaded
	});
});
</script>
		
<div id="slider">
<?php 
	$category = get_option('wpns_category');
	$n_slices = get_option('wpns_slices');
?>
<?php query_posts( 'cat='.$category.'&posts_per_page=$n_slices' ); if( have_posts() ) : while( have_posts() ) : the_post(); ?>
	<?php if(has_post_thumbnail()) : ?>
	<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"> 
		<?php the_post_thumbnail(); ?>
	</a>
	<?php endif ?>
	<?php endwhile; endif;?>
	<?php wp_reset_query();?>
</div>

<?php } 

function wpns_menu_function() {

?>

<div class="wrap">
<h2>WP Nive Slider</h2>
 
<form method="post" action="options.php">
    <?php settings_fields( 'wpns-settings-group' ); ?>
    <table class="form-table">
      
        <tr valign="top">
        <th scope="row">Category</th>
        <td>
        <select name="wpns_category" id="wpns_category"> 
			 <option value="">Select a Category</option> 
 			<?php 
 				$category = get_option('wpns_category');
  				$categories=  get_categories(); 
  				foreach ($categories as $cat) {
  					$option = '<option value="'.$cat->term_id.'"';
  					if ($category == $cat->term_id) $option .= ' selected="selected">';
  					else { $option .= '>'; }
					$option .= $cat->cat_name;
					$option .= ' ('.$cat->category_count.')';
					$option .= '</option>';
					echo $option;
  				}
 			?>
		</select>

        </tr>
    	
    	<tr valign="top">
        <th scope="row">Number of slices</th>
        <td>
        <label>
        <input type="text" name="wpns_slices" id="wpns_slices" size="7" value="<?php echo get_option('wpns_slices'); ?>" />
        </label>
        </tr>
        
        <tr valign="top">
        <th scope="row">Type of Animation</th>
        <td>
        <label>
        <?php $effect = get_option('wpns_effect'); ?>
        <select name="wpns_effect" id="wpns_effect">
        	<option value="random" <?php if($effect == 'random') echo 'selected="selected"'; ?>>Random</option>
        	<option value="sliceDown" <?php if($effect == 'sliceDown') echo 'selected="selected"'; ?> >sliceDown</option>
        	<option value="sliceDownLeft" <?php if($effect == 'sliceDownLeft') echo 'selected="selected"'; ?> >sliceDownLeft</option>
        	<option value="sliceUp" <?php if($effect == 'sliceUp') echo 'selected="selected"'; ?> >sliceUp</option>
        	<option value="sliceUpLeft" <?php if($effect == 'sliceUpLeft') echo 'selected="selected"'; ?> >sliceUpLeft</option>
        	<option value="sliceUpDown" <?php if($effect == 'sliceUpDown') echo 'selected="selected"'; ?> >sliceUpDown</option>
        	<option value="sliceUpDownLeft" <?php if($effect == 'sliceUpDownLeft') echo 'selected="selected"'; ?> >sliceUpDownLeft</option>
        	<option value="fold" <?php if($effect == 'fold') echo 'selected="selected"'; ?> >fold</option>
        	<option value="fade" <?php if($effect == 'fade') echo 'selected="selected"'; ?> >fade</option>
        </select>
        </label>
        </tr>
		
		<tr valign="top">
			<td>This is size of yours images. This plugin do not resize images.</td>
        </tr>
		
		<tr valign="top">
        <th scope="row">Width</th>
        <td>
        <label>
        <input type="text" name="wpns_width" id="wpns_width" size="7" value="<?php echo get_option('wpns_width'); ?>" />px
        </label>
        </tr>
		
		<tr valign="top">
        <th scope="row">Height</th>
        <td>
        <label>
        <input type="text" name="wpns_height" id="wpns_height" size="7" value="<?php echo get_option('wpns_height'); ?>" />px
        </label>
        </tr>
    
    </table>
 
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>
 
</form>
</div>

<?php } ?>
