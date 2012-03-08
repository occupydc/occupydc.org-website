<?php get_header(); ?>

<div id="content" class="col-full">
<?php include ('announcements.php'); ?>

	<div id="main" class="col-left">

		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

            
	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?> >

		<div class="post-content">
					
			<div class="post-entry">
				<?php the_content( '<div class="post-read-more">' . __("Continue reading &raquo;", "cp") . '</div>' ); ?>
			</div>

			<?php if ( is_single() or is_page() ) { wp_link_pages(array('before' => '<p class="post-pages"><strong>' . __('Pages:', 'cp') . '</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); } ?>
				

			<div class="clear"></div>

		</div><!--#END post-content -->

	</div><!-- #END post -->

			<?php comments_template(); ?>

		<?php endwhile; ?>

		<?php else : ?>

			<div class="post-title">				

				<h4><?php _e('Sorry, no posts matched your criteria.', 'cp'); ?></h1>

			</div>

		<?php endif; ?>

	</div><!-- #END main -->
	
	<?php get_sidebar(); ?>

</div><!-- #END content -->

<?php get_footer(); ?>
