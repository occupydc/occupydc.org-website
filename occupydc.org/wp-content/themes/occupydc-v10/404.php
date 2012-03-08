<?php get_header(); ?>

<div id="content" class="col-full">
		
	<div id="main" class="col-left">

		<div class="post">

			<div class="post-content">

				<div class="post-title">
					<h1><?php _e("Uh Oh! Something terrible happened!", 'cp'); ?></h1>
				</div>
			
				<div class="post-entry">
					<p><?php _e("I'm very sorry, but there is nothing here!", 'cp'); ?>
					<?php _e("Either the page you are looking for doesn't exist, or the URL you typed or followed is incorrect or misspelled.", 'cp'); ?></p>
					<p><?php _e("Why don't you try and search for it?", 'cp'); ?></p>
					<p class="alignleft"><?php get_search_form(); ?></p>
				</div>
			
				<div class="clear"></div>
				
			</div><!-- #END post-content -->
			
		</div><!-- #END post -->
	
	</div><!-- #END main -->
	
	<?php get_sidebar(); ?>	

</div><!-- #END content -->

<?php get_footer(); ?>
