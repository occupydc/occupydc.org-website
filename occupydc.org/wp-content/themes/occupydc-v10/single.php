<?php get_header(); ?>

<div id="content" class="col-full">

	<div id="main" class="col-left">

		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	
			<?php include('post.php'); ?>

			<?php comments_template(); ?>
	
		<?php endwhile; else : ?>

			<div class="post-title">			

				<h4><?php _e('Sorry, no posts matched your criteria.', 'cp'); ?></h4>

			</div>

		<?php endif; ?>

	</div><!-- #END main -->

	<?php get_sidebar(); ?>

</div><!-- #END content -->

<?php get_footer(); ?>
