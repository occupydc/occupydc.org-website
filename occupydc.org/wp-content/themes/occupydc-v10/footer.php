		<div id="footer" class="col-full">
			<p><?php echo do_shortcode( stripslashes( get_option( 'cp_footer' ) ) ); ?></p>
		</div>

	</div><!-- #END container -->

	<?php wp_footer(); ?>

	<?php if ( get_option('cp_analytics') <> '') { echo stripslashes(get_option('cp_analytics')); }	?></div>

</body>
</html>
