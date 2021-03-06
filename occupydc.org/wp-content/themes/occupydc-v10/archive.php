<?php get_header(); ?>

<div id="content" class="col-full">

	<div id="main" class="col-left">

		<?php if ( have_posts() ) : ?> <?php $post = $posts[0]; ?>

<?php

$category = "single_cat_title()";
$thumb = get_cat_ID($category);


?>

			<div id="archivetitle">

				<?php if ( is_category() ) { ?>
					<h1><?php _e('Category:', 'cp'); ?> <span><?php single_cat_title(); ?></span></h1>
				<?php } elseif ( is_tag() ) { ?>
					<h1><?php _e('Tag:', 'cp'); ?> <span><?php single_tag_title(); ?></span></h1>
				<?php } elseif ( is_day() ) { ?>
					<h1><?php _e('Archive:', 'cp'); ?> <span><?php the_time( __('F jS, Y', 'cp') ); ?></span></h1>
				<?php } elseif ( is_month() ) { ?>
					<h1><?php _e('Archive:', 'cp'); ?> <span><?php the_time( __('F, Y', 'cp') ); ?></span></h1>
				<?php } elseif ( is_year() ) { ?>
					<h1><?php _e('Archive:', 'cp'); ?> <span><?php the_time( __('Y', 'cp') ); ?></span></h1>
				<?php } elseif ( is_author() ) { ?>	
					<h1><?php _e('Author Archive', 'cp'); ?></h1>
				<?php } elseif ( isset($_GET['paged']) && !empty($_GET['paged']) ) { ?>
					<h1><?php __('Blog Archives', 'cp'); ?></h1>
				<?php } ?>

			</div>

			<?php while ( have_posts() ) : the_post(); ?>

<?php
$category = get_the_category();

if (is_sticky()) {
$thumb = "sticky";
} else {
$thumb = $category[0]->cat_ID;	
}
?>

	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?> >
		
		<div id="post-content">

		<div id="post-thumbnail">
		<a href="<?php the_permalink(); ?>" title="<?php echo $category[0]->cat_name; ?>"><img src="<?php bloginfo('template_directory'); ?>/images/thumbnails/<?php echo $thumb; ?>.png" alt="<?php echo $category[0]->cat_name; ?>" width="48px" height="48px" /></a>
		</div>


			<div class="post-title">
				<h2><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php _e('Permanent Link to', 'cp'); ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
			</div>
								
			<div class="post-entry">
			<?php the_excerpt(); ?>
			</div>

			<div class="post-meta">
				<span class="post-meta-info">Posted on <strong><?php the_time( __('F jS, Y', 'cp') ); ?></strong> in <?php the_category(', ') ?></span>
				<span class="post-meta-comments"><?php edit_post_link(__('Edit', 'cp'), '', ' &middot; '); ?><?php comments_popup_link(__('No comments', 'cp'), __('One comment', 'cp'), '% '.__('comments', 'cp') ); ?> &middot; <a href="<?php echo get_permalink(); ?>"> Read More...</a></span>
			</div>

			<div class="clear"></div>

		</div><!--#END post-content -->

	</div><!-- #END post -->


		<?php endwhile; ?>
      

		<?php if ( function_exists('wp_pagenavi') ) { ?>

			<?php wp_pagenavi(); ?>

			<?php } else { ?>  
		
    	   		<ul class="more-entries">

					<li class="alignleft"><?php next_posts_link( __('&laquo; Older Entries', 'cp') ); ?></li>
					<li class="alignright"><?php previous_posts_link( __('Newer Entries &raquo;', 'cp') ); ?></li>

				</ul>

			<?php } ?>

		<?php else : ?>
					
			<div class="post-title">			
	
				<h1><?php _e('No posts were found', 'cp'); ?></h1>

			</div>

		<?php endif; ?>

	</div><!-- #END main -->

	<?php get_sidebar(); ?>

</div><!-- #END content -->

<?php get_footer(); ?>
