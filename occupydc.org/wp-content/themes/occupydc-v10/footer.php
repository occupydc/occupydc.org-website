		<div id="footer" class="col-full">
		<table><tbody><tr><td style="
    width: 15%;
"> </td><td style="
    width: 500px;
">
			<p>To all who value democracy, we encourage you to collaborate and share available resources.

Join your voice with ours and let it amplify until the heart of the movement booms with our chorus of solidarity.
*These grievances are not all inclusive.</p></td><td style="
    width: 560px;
"><a href="https://www.facebook.com/OccupyDC"><img src="http://localhost:8080/occupydc.org/wp-content/themes/occupydc-v10/images/social/facebook.jpg" class="social" alt="Facebook"></a>
<a href="https://www.twitter.com/Occupy_DC"><img src="http://localhost:8080/occupydc.org/wp-content/themes/occupydc-v10/images/social/twitter.png" class="social" alt="Twitter"></a>
<a href="https://plus.google.com/112483678724979264653/posts"><img src="http://localhost:8080/occupydc.org/wp-content/themes/occupydc-v10/images/social/google.jpg" class="social" alt="Google"></a>
<a href="http://occupywashdc.tumblr.com"><img src="http://localhost:8080/occupydc.org/wp-content/themes/occupydc-v10/images/social/tumblr.png" class="social" alt="Tumblr"></a>
<a href="http://www.reddit.com/r/occupyDC"><img src="http://localhost:8080/occupydc.org/wp-content/themes/occupydc-v10/images/social/reddit.png" class="social" alt="Reddit"></a>
<a href="https://www.twitter.com/OccupyKst"><img src="http://localhost:8080/occupydc.org/wp-content/themes/occupydc-v10/images/social/twitter.png" class="social" alt="Twitter"></a>
<a href="http://www.flickr.com/groups/occupydc/"><img src="http://localhost:8080/occupydc.org/wp-content/themes/occupydc-v10/images/social/flickr.png" class="social" alt="Flickr"></a>
<a href="http://www.youtube.com/user/occupytv"><img src="http://localhost:8080/occupydc.org/wp-content/themes/occupydc-v10/images/social/youtube.png" class="social" alt="Youtube"></a>
<a href="http://vimeo.com/channels/occupydc"><img src="http://localhost:8080/occupydc.org/wp-content/themes/occupydc-v10/images/social/vimeo.png" class="social" alt="Vimeo"></a>
<a href="http://occupydc.org/feed"><img src="http://localhost:8080/occupydc.org/wp-content/themes/occupydc-v10/images/social/feed.png" class="social" alt="Feed"></a></td></tr></tbody></table>
		</div>

<?php
/**
 * @package WordPress
 * @subpackage themename
 */
?>

	</div><!-- #main  -->
</div><!-- #page -->
<div id="home-info-wrapper">
<div class="row-fluid footerr">

<div class="span2" style="text-align: center;">
<?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('f_left_column')) : else : ?>  
        <h2>Widget Ready</strong></h2>  
        <p>This left_column is widget ready! Add one in the admin panel.</p>  
    <?php endif; ?>  
</div>
<div class="span6">  
<?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('f_center_column')) : else : ?>  
        <h2>Widget Ready</strong></h2> 
        <p>This center_column is widget ready! Add one in the admin panel.</p>  
    <?php endif; ?>  
</div>
<div class="span4">  
<?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('f_right_column')) : else : ?>  
        <h2>Widget Ready</strong></h2>  
        <p>This right_column is widget ready! Add one in the admin panel.</p>  
    <?php endif; ?>  
</div><!-- .row -->
		
			<div style="text-align="center" class="span12"><?php echo do_shortcode( stripslashes( get_option( 'cp_footer' ) ) ); ?></div></div></div>
	</footer><!-- #colophon -->


 

<?php
$ilc_settings = get_option( "ilc_theme_settings" );
if( $ilc_settings['ilc_ga'] != '' ) : ?>
<?php echo stripslashes($ilc_settings['ilc_ga']); ?>
<?php endif; ?>

<?php wp_footer(); ?>

</body>
</html>