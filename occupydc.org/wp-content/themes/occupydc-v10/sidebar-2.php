<?php do_action( 'bp_before_sidebar' ) ?>

<div id="sidebar" class="col-right"><div id="login-form">

	<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar( 'sidebar-2') ) : ?></div>

		<!-- Display recent posts -->
		<div class="widget">

			<h2 class="widgettitle"><?php _e('Recent Posts', 'cp'); ?></h2>

			<div class="widgetcontent">

				<ul><?php wp_get_archives('type=postbypost&limit=10'); ?></ul>

			</div>
		
		</div>

		<!-- Meta -->
		<div class="widget">

			<h2 class="widgettitle"><?php _e('Meta', 'cp'); ?></h2>

			<div class="widgetcontent">

				<ul>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
				</ul>

			</div>
		
		</div>

	<?php endif; ?>
<div class="clear"></div>

</div>



	


	


<?php do_action( 'bp_after_sidebar' ) ?>
