<?php
/*
Template Name: Front
*/
?>

<?php get_header(); ?>
<style>.popover-title {
display:none !important;
}</style>

<div id="subscribe-wrapper">
  <div id="subscribe" class="row-fluid">
     <div class="span12">
       <!--<div id="qrcode" class="span4"><div class="span2"><a href="<?php bloginfo('template_directory'); ?>/images/odcqr.png"><img src="<?php bloginfo('template_directory'); ?>/images/odcqr.png" id="qr" /></a></div><div class="span1" id="qrtext"><a href="<?php bloginfo('template_directory'); ?>/images/odcqr.png"> <strong><h2 id="qrtext">Download</h2> our QR Code!</strong></a></div></div>-->
        <div id="subscribe-form" class="span4">
          <form id="pre-signup" action="/users/signup" method="get">
            <input type="email" name="email" placeholder="johndoe@occupydc.org" required="true">
            <button type="submit" class="btn btn-primary btn-large"><font><font>Signup For Email Alerts</font></font></button>
           </form>
        </div>
     </div>
 
  </div><div id="subscribe-img" class="span10 offset2"><!-- slideshow -->
         <?php include ('slider.php'); ?>
        </div>
</div>
<?php include ('announcements.php'); ?>


<div id="home-info-wrapper">
		<div class="container">
		<div class="hero-unit">
		 <?php
                  $recentPosts = new WP_Query();
                  $recentPosts->query('showposts='. get_option('cp_ra_num') .'&cat='. get_option('cp_ra_cat') .'');

              ?>








<?php
    $recentPosts = new WP_Query();
    $recentPosts->query('showposts=1');
?>
<?php global $more; $more = 0; ?>
<?php while ($recentPosts->have_posts()) : $recentPosts->the_post(); ?>
    <h1 class="entry-title"><a href="<?php the_permalink(); ?>" data-content="<?php the_title(); ?>" rel="bookmark" id="example"><span style="font-weight: bold;
color: #333;
text-rendering: optimizelegibility;"><?php echo ShortenText(get_the_title()); ?></span></a></h1>
		<div class="entry-meta">
							<?php
								printf( __( '<span class="meta-prep meta-prep-author">Posted on </span><a href="%1$s" rel="bookmark"><time class="entry-date" datetime="%2$s" pubdate>%3$s</time></a> <span class="meta-sep">', 'themename' ),
									get_permalink(),
									get_the_date( 'c' ),
									get_the_date(),
									get_author_posts_url( get_the_author_meta( 'ID' ) ),
									sprintf( esc_attr__( 'View all posts by %s', 'themename' ), get_the_author() ),
									get_the_author()
								);
							?>
						</div><!-- .entry-meta -->
<div class="entry-content">

<?php global $more; $more = false; ?>
	<?php the_excerpt( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'themename' ) ); ?>
<?php $more = true; ?>
			<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'themename' ), 'after' => '</div>' ) ); ?>
		<span class="post-meta-comments">





		
		</span></div><!-- .entry-content -->

<?php endwhile; ?>
<?php
                  $recentPosts = new WP_Query();
                  $recentPosts->query('showposts='. get_option('cp_ra_num') .'&cat='. get_option('cp_ra_cat') .'');

              ?>

				
				  
 		<div class="alert alert-success" style="margin:30px 0 -30px 0;">
  <a class="close" data-dismiss="alert">×</a>
<ul class="nav navalert">
  <li class="dropdown">
    <a href="#"
          class="dropdown-toggle"
          data-toggle="dropdown">
         <strong>View Recent Announcements</strong>
          <b class="caret"></b>
    </a>
    <ul class="dropdown-menu">
			<?php while ($recentPosts->have_posts()) : $recentPosts->the_post(); ?>
              <li><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></li>
    <?php endwhile; ?>
	</ul>
  </li>
</ul>
</div> 
 </div> 

			</div><!-- hero unit -->

<?php
$ilc_settings = get_option( "ilc_theme_settings" );
if( $ilc_settings['ilc_intro'] != '' ) : ?>
<div id="homeBlurb">
<?php echo '<h1><small>' . stripslashes($ilc_settings['ilc_intro']) . '</small><h1>'; ?>
</div>
<?php endif; ?>



<div class="container">
<div class="hero-unit">
<div class="row-fluid">
<div class="span4" id="fpwidget">
<img src="<?php echo do_shortcode( stripslashes( get_option( 'cp_wimage1' ) ) ); ?>" id="fpwidgetimage"/>  
        <h2><strong><?php echo do_shortcode( stripslashes( get_option( 'cp_abouttitle' ) ) ); ?></strong></h2>  
        <p><?php echo do_shortcode( stripslashes( get_option( 'cp_about' ) ) ); ?></p>  
		  
</div>
<div class="span4"> <div id="myCarousel" class="carousel">
  <!-- Carousel items -->
 <h2> <a class="left" id="cald" data-content="View Calendar" href="#myCarousel" data-slide="prev"><strong>Calendar</strong></a></h2>
<h2>  <a class="right" id="evnt" data-content="View Events" href="#myCarousel" data-slide="next"><strong>Events</strong></a></h2>
<div id="calendar-fix">
  <div class="carousel-inner">
    <div class="active item"><?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('center_column')) : else : ?>  
        <p>This center_column is widget ready! Add one in the admin panel.</p>  
    <?php endif; ?>  </div>
    <div class="item"><?php if (function_exists('dynamic_sidebar') && dynamic_sidebar('right_column')) : else : ?>  
        <p>This right_column is widget ready! Add one in the admin panel.</p>  
    <?php endif; ?>  
</div>
  </div></div>
  <!-- Carousel nav -->
</div>

</div>
<div class="span4" id="fpwidget">
<img src="<?php echo do_shortcode( stripslashes( get_option( 'cp_wimage2' ) ) ); ?>" id="fpwidgetimage"/>  
        <h2><strong><?php echo do_shortcode( stripslashes( get_option( 'cp_fpwtitle' ) ) ); ?></strong></h2>  
        <p><?php echo do_shortcode( stripslashes( get_option( 'cp_fpw' ) ) ); ?></p>  
		  
</div>
</div> <!-- row -->
</div> <!-- homeRow -->
		</div><!-- container -->
  	
     </div>
<?php get_footer(); ?>
	
