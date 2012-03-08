<?php

// Do not delete these lines
	if (isset($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
			die ('Please do not load this page directly. Thanks!');
	
	if ( post_password_required() ) { ?>
		<p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.', 'kubrick'); ?></p> 
	<?php
		return;
	}
?>

<!-- You can start editing here. -->

<?php if ( have_comments() ) : ?>

	<div class="commentlist">	

		<h4><?php comments_number( __('No Responses', 'cp'), __('One Response', 'cp'), __('% Responses', 'cp') ); ?> <?php _e('to', 'cp'); ?> &#8220;<?php the_title(); ?>&#8221;</h4>
        <a href="#respond"></a>

		<ol><?php wp_list_comments('callback=cp_comments'); ?></ol>

		<div class="comment-navigation">
			<div class="alignleft"><?php previous_comments_link() ?></div>
			<div class="alignright"><?php next_comments_link() ?></div>
		</div>
   
	</div>

<?php else : // this is displayed if there are no comments so far ?>

	<?php if ( comments_open() ) : ?>

	<?php else : // comments are closed ?>
		<p class="nocomments"><h3><?php _e('Comments are closed.', 'cp'); ?></h3></p>
	<?php endif; ?>

<?php endif; ?>


<?php if ( comments_open() ) : ?>

	<div id="respond" class="comment-form">

		<h4><?php _e('Leave Your Response', 'cp'); ?></h4>

		<div id="cancel-comment-reply"> 
			<small><?php cancel_comment_reply_link() ?></small>
		</div> 

		<?php if ( get_option('comment_registration') && !is_user_logged_in() ) : ?>

			<p><?php _e('You must be', 'cp'); ?> <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>"><?php _e('logged in', 'cp'); ?></a> <?php _e('to post a comment', 'cp'); ?>.</p>
		
		<?php else : ?>

			<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">

				<fieldset>

					<?php if ( is_user_logged_in() ) : ?>

						<p><?php _e('Logged in as', 'cp'); ?> <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="<?php _e('Log out of this account', 'cp'); ?>"><?php _e('Log out &raquo;', 'cp'); ?></a></p>

					<?php else : ?>

						<div><input type="text" tabindex="1" size="25" name="author" id="author" value="<?php echo esc_attr($comment_author); ?>" />
						<label for="author"><?php _e('Name', 'cp'); ?> <?php if ($req) _e("(required)", "cp"); ?></label></div>
						<div><input type="text" tabindex="2" size="25" name="email" id="email" value="<?php echo esc_attr($comment_author_email); ?>" />
						<label for="email"><?php _e('Mail (will not be published)', 'cp'); ?> <?php if ($req) _e("(required)", "cp"); ?></label></div>
						<div><input type="text" tabindex="3" size="25" name="url" id="url" value="<?php echo  esc_attr($comment_author_url); ?>" />
						<label for="url"><?php _e('Website', 'cp'); ?></label></div>

					<?php endif; ?>

					<div class="textarea"><textarea name="comment" id="comment" cols="" rows="" tabindex="4"></textarea></div>
					
					<p><input name="submit" type="submit" id="submit" tabindex="5" value="<?php _e('Submit Comment', 'cp'); ?>" /><?php comment_id_fields(); ?></p>
					<?php do_action('comment_form', $post->ID); ?>

				</fieldset>

			</form>

		<?php endif; // If registration required and not logged in ?>

	</div><!-- #END comment-form -->

<?php endif; // if you delete this the sky will fall on your head ?>

