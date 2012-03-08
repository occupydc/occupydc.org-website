<?php /* Template Name: Archives */ ?>

<?php get_header(); ?>

<div id="content" class="col-full">

	<div id="main" class="col-left">

		<div class="post">

			<div class="post-content">

				<div class="post-title">
					<h2><?php _e("Search", cp); ?></h2>
				</div>

				<div class="post-entry">
					<?php include ( TEMPLATEPATH . "/includes/theme-searchform.php"); ?>
				</div>

				<div class="alignleft">

					<div class="post-title">
						<h2><?php _e("Archives by Month", cp); ?></h2>
					</div>

					<div class="post-entry">
						<ul><?php wp_get_archives('type=monthly'); ?></ul>
					</div>

				</div>

				<div class="alignright">

					<div class="post-title">
						<h2><?php _e("Archives by Subject", cp); ?></h2>
					</div>

					<div class="post-entry">
						<ul><?php wp_list_categories(); ?></ul>
					</div>
				</div>

				<div class="clear"></div>

			</div><!-- #END post-content -->

		</div><!-- #END post -->

	</div><!-- #END main -->

	<?php get_sidebar(); ?>

</div><!-- #END content -->

<?php get_footer(); ?>
