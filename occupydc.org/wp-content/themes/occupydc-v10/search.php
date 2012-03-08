<?php get_header(); ?>

<div id="content" class="col-full">

	<div id="main" class="col-left">

		<?php if ( have_posts() ) : ?>

			<div class="post-title"><h4><?php _e('Search results', 'cp'); ?></h4></div>

			<?php while ( have_posts() ) : the_post(); ?>

				<?php include('post.php'); ?>

			<?php endwhile; ?>
        
			<?php if ( function_exists('wp_pagenavi') ) { ?>

				<?php wp_pagenavi(); ?>

			<?php } else { ?>  
		
				<ul class="more-entries">

					<li class="alignleft"><?php next_posts_link(__('&laquo; Older Entries', 'cp')); ?></li>
					<li class="alignright"><?php previous_posts_link(__('Newer Entries &raquo;', 'cp')); ?></li>

				</ul>

			<?php } ?>
		
		<?php else : ?>

			<div class="post">

				<div class="post-content">

			        <p><?php _e('No posts were found. Please try using a different keyword in your search.', 'cp'); ?></p>

				</div>

			</div>

		<?php endif; ?>

	</div><!-- #END main -->

	<?php get_sidebar(); ?>

</div><!-- #END content -->

<?php get_footer(); ?>
