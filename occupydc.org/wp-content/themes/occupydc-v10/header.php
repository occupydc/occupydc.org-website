<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">

<script>var a=new Date,b=a.getHours()+a.getTimezoneOffset()/60;if(18==a.getDate()&&0==a.getMonth()&&2012==a.getFullYear()&&13<=b&&24>=b)window.location="http://sopastrike.com/strike";</script>

	<link rel='shortcut icon' href="<?php bloginfo('template_url'); ?>/favicon.ico" type="image/x-icon" />
	<meta http-equiv="content-type" content="<?php bloginfo('html_type') ?>; charset=<?php bloginfo('charset') ?>" />
	<meta name="description" content="<?php bloginfo('description') ?>" />
	<meta name="language" content="<?php echo get_bloginfo('language'); ?>" />



	<title>
		<?php if ( is_home() ) { ?><?php bloginfo('name'); ?>&nbsp;|&nbsp;<?php bloginfo('description'); ?><?php } ?>
		<?php if ( is_search() ) { ?><?php bloginfo('name'); ?>&nbsp;|&nbsp;<?php _e('Search Results', 'cp'); ?><?php } ?>
		<?php if ( is_404() ) { ?><?php bloginfo('name'); ?>&nbsp;|&nbsp;<?php _e('404 Nothing Found', 'cp');?><?php } ?>
		<?php if ( is_author() ) { ?><?php bloginfo('name'); ?>&nbsp;|&nbsp;<?php _e('Author Archives', 'cp'); ?><?php } ?>
		<?php if ( is_single() ) { ?><?php wp_title(''); ?>&nbsp;|&nbsp;<?php bloginfo('name'); ?><?php } ?>
		<?php if ( is_page() ) { ?><?php bloginfo('name'); ?>&nbsp;|&nbsp;<?php wp_title(''); ?><?php } ?>
		<?php if ( is_category() ) { ?><?php bloginfo('name'); ?>&nbsp;|&nbsp;<?php _e('Archive', 'cp'); ?>&nbsp;|&nbsp;<?php single_cat_title(); ?><?php } ?>
		<?php if ( is_month() ) { ?><?php bloginfo('name'); ?>&nbsp;|&nbsp;<?php _e('Archive', 'cp'); ?>&nbsp;|&nbsp;<?php the_time(__('F', 'cp')); ?><?php } ?>
		<?php if ( is_day() ) { ?><?php bloginfo('name'); ?>&nbsp;|&nbsp;<?php _e('Archive', 'cp'); ?>&nbsp;|&nbsp;<?php the_time(__('F j, Y', 'cp')); ?><?php } ?>
		<?php if (function_exists('is_tag')) { if ( is_tag() ) { ?><?php bloginfo('name'); ?>&nbsp;|&nbsp;<?php _e('Tag Archive', 'cp'); ?>&nbsp;|&nbsp;<?php single_tag_title("", true); } } ?>
	</title>

	<link rel="stylesheet" type="text/css" media="screen, projection" href="<?php bloginfo('stylesheet_url'); ?>" />
	<link rel="stylesheet" type="text/css" media="screen, projection" href="<?php bloginfo('template_url'); ?>/css/custom.css" />
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php if ( get_option('cp_feedburner')<>'') { echo get_option('cp_feedburner'); } else { bloginfo('rss2_url'); } ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<?php if ( is_single() ) wp_enqueue_script('comment-reply'); ?>	
    <?php wp_head(); ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript">
jQuery(document).ready(function() {
  jQuery("#signupalertsbox").hide();
  jQuery("#signupalerts").click(function() {
  jQuery("#signupalertsbox").slideToggle();
  });
});
</script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/twipsy.js"></script>

<!-- Grab Google CDN's jQuery. Fall back to local if necessary -->


<script src="http://www.w3resource.com/twitter-bootstrap/twitter-bootstrap-v2/js/bootstrap-tooltip.js"></script>

<script src="http://www.w3resource.com/twitter-bootstrap/twitter-bootstrap-v2/js/bootstrap-popover.js"></script>


<script>
 $(function() {
   $(".alert-message").alert();
 });
</script>



 <script>
            $(function () {
$('#my-modal').modal({
  keyboard: true
})
})
          </script>
		  
		  <script>

$(function ()

{ $("#example").popover();

});

</script>
<script>$('.carousel').carousel() interval: 2000</script><script>

$(function ()

{ $("#cald").popover();

});

$(function ()

{ $("#evnt").popover();

});

</Script>

</head>

<body data-spy="scroll" data-target=".subnav" data-offset="50" data-twttr-rendered="true"  <?php body_class(); ?> >



<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<?php

				$args = array(
					'theme_location' => 'top-bar',
					'depth'		 => 2,
					'container'	 => false,
					'menu_class'	 => 'nav',
					'walker'	 => new Bootstrap_Walker_Nav_Menu()
				);

				wp_nav_menu($args);

			?>
		</div>
	</div>
</div>
    <div class="container">		<!-- <a href="<?php bloginfo('url'); ?>/" class="brand" title="<?php bloginfo('name'); ?> - <?php bloginfo('description'); ?>"><img src="<?php bloginfo('template_directory'); ?>/images/logo.png" alt="<?php bloginfo('name'); ?> - <?php bloginfo('description'); ?>" /></a> -->

</div>
<div class="wrappd">


