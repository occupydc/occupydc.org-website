<div id="sidebar" class="col-right">

	<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(1) ) : ?>

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

</div>

<div class="clear"></div>
