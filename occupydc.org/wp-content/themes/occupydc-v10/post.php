<?php /* Global Post file. */ ?>
            
	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?> >

		<div class="post-content">
				
			<div class="post-title">
				<h2><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php _e('Permanent Link to', 'cp'); ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
			</div>
			
			<div class="post-meta">
				<span class="post-meta-info">Posted on <strong><?php the_time( __('F jS, Y', 'cp') ); ?></strong> in <?php the_category(', ') ?></span>
				<span class="post-meta-comments"><?php edit_post_link(__('Edit', 'cp'), '', ' &middot; '); ?><?php comments_popup_link(__('No comments', 'cp'), __('One comment', 'cp'), '% '.__('comments', 'cp') ); ?>
				</span>
			</div>				
												
			<?php if( (function_exists('has_post_thumbnail')) && (has_post_thumbnail()) ) { ?>
				<div>
					<?php the_post_thumbnail(); ?>
				</div>
			<?php } ?>
					
			<div class="post-entry">
				<?php the_content( '<div class="post-read-more">' . __("Continue reading &raquo;", "cp") . '</div>' ); ?>
			</div>

			<?php if ( is_single() or is_page() ) { wp_link_pages(array('before' => '<p class="post-pages"><strong>' . __('Pages:', 'cp') . '</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); } ?>
				

			<div class="clear"></div>

		</div><!--#END post-content -->

	</div><!-- #END post -->
