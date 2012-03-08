<?php

/**
 * bbPress Widgets
 *
 * Contains the forum list, topic list, reply list and login form widgets.
 *
 * @package bbPress
 * @subpackage Widgets
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * bbPress Login Widget
 *
 * Adds a widget which displays the login form
 *
 * @since bbPress (r2827)
 *
 * @uses WP_Widget
 */
class BBP_Login_Widget extends WP_Widget {

	/**
	 * Register the widget
	 *
	 * @since bbPress (r3389)
	 *
	 * @uses register_widget()
	 */
	function register_widget() {
		register_widget( 'BBP_Login_Widget' );
	}

	/**
	 * bbPress Login Widget
	 *
	 * Registers the login widget
	 *
	 * @since bbPress (r2827)
	 *
	 * @uses apply_filters() Calls 'bbp_login_widget_options' with the
	 *                        widget options
	 */
	function BBP_Login_Widget() {
		$widget_ops = apply_filters( 'bbp_login_widget_options', array(
			'classname'   => 'bbp_widget_login',
			'description' => __( 'The login widget.', 'bbpress' )
		) );

		parent::WP_Widget( false, __( 'bbPress Login Widget', 'bbpress' ), $widget_ops );
	}

	/**
	 * Displays the output, the login form
	 *
	 * @since bbPress (r2827)
	 *
	 * @param mixed $args Arguments
	 * @param array $instance Instance
	 * @uses apply_filters() Calls 'bbp_login_widget_title' with the title
	 * @uses get_template_part() To get the login/logged in form
	 */
	function widget( $args, $instance ) {
		extract( $args );

		$title    = apply_filters( 'bbp_login_widget_title',    $instance['title']    );
		$register = apply_filters( 'bbp_login_widget_register', $instance['register'] );
		$lostpass = apply_filters( 'bbp_login_widget_lostpass', $instance['lostpass'] );

		echo $before_widget;

		if ( !empty( $title ) )
			echo $before_title . $title . $after_title;

		if ( !is_user_logged_in() ) : ?>

			<form method="post" action="<?php bbp_wp_login_action( array( 'context' => 'login_post' ) ); ?>" class="bbp-login-form">
				<fieldset>
					<legend><?php _e( 'Log In', 'bbpress' ); ?></legend>

					<div class="bbp-username">
						<label for="user_login"><?php _e( 'Username', 'bbpress' ); ?>: </label>
						<input type="text" name="log" value="<?php bbp_sanitize_val( 'user_login', 'text' ); ?>" size="20" id="user_login" tabindex="<?php bbp_tab_index(); ?>" />
					</div>

					<div class="bbp-password">
						<label for="user_pass"><?php _e( 'Password', 'bbpress' ); ?>: </label>
						<input type="password" name="pwd" value="<?php bbp_sanitize_val( 'user_pass', 'password' ); ?>" size="20" id="user_pass" tabindex="<?php bbp_tab_index(); ?>" />
					</div>

					<div class="bbp-remember-me">
						<input type="checkbox" name="rememberme" value="forever" <?php checked( bbp_get_sanitize_val( 'rememberme', 'checkbox' ), true, true ); ?> id="rememberme" tabindex="<?php bbp_tab_index(); ?>" />
						<label for="rememberme"><?php _e( 'Remember Me', 'bbpress' ); ?></label>
					</div>

					<div class="bbp-submit-wrapper">

						<?php do_action( 'login_form' ); ?>

						<button type="submit" name="user-submit" id="user-submit" tabindex="<?php bbp_tab_index(); ?>" class="button submit user-submit"><?php _e( 'Log In', 'bbpress' ); ?></button>

						<?php bbp_user_login_fields(); ?>

					</div>

					<?php if ( !empty( $register ) || !empty( $lostpass ) ) : ?>

						<div class="bbp-login-links">

							<?php if ( !empty( $register ) ) : ?>

								<a href="<?php echo esc_url( $register ); ?>" title="<?php _e( 'Register', 'bbpress' ); ?>" class="bbp-register-link"><?php _e( 'Register', 'bbpress' ); ?></a>

							<?php endif; ?>

							<?php if ( !empty( $lostpass ) ) : ?>

								<a href="<?php echo esc_url( $lostpass ); ?>" title="<?php _e( 'Lost Password', 'bbpress' ); ?>" class="bbp-lostpass-link"><?php _e( 'Lost Password', 'bbpress' ); ?></a>

							<?php endif; ?>

						</div>

					<?php endif; ?>

				</fieldset>
			</form>

		<?php else : ?>

			<div class="bbp-logged-in">
				<a href="<?php bbp_user_profile_url( bbp_get_current_user_id() ); ?>" class="submit user-submit"><?php echo get_avatar( bbp_get_current_user_id(), '40' ); ?></a>
				<h4><?php bbp_user_profile_link( bbp_get_current_user_id() ); ?></h4>

				<?php bbp_logout_link(); ?>
			</div>

		<?php endif;

		echo $after_widget;
	}

	/**
	 * Update the login widget options
	 *
	 * @since bbPress (r2827)
	 *
	 * @param array $new_instance The new instance options
	 * @param array $old_instance The old instance options
	 */
	function update( $new_instance, $old_instance ) {
		$instance             = $old_instance;
		$instance['title']    = strip_tags( $new_instance['title'] );
		$instance['register'] = esc_url( $new_instance['register'] );
		$instance['lostpass'] = esc_url( $new_instance['lostpass'] );

		return $instance;
	}

	/**
	 * Output the login widget options form
	 *
	 * @since bbPress (r2827)
	 *
	 * @param $instance Instance
	 * @uses BBP_Login_Widget::get_field_id() To output the field id
	 * @uses BBP_Login_Widget::get_field_name() To output the field name
	 */
	function form( $instance ) {

		// Form values
		$title    = !empty( $instance['title'] )    ? esc_attr( $instance['title'] )    : '';
		$register = !empty( $instance['register'] ) ? esc_attr( $instance['register'] ) : '';
		$lostpass = !empty( $instance['lostpass'] ) ? esc_attr( $instance['lostpass'] ) : '';

		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'bbpress' ); ?>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'register' ); ?>"><?php _e( 'Register URI:', 'bbpress' ); ?>
			<input class="widefat" id="<?php echo $this->get_field_id( 'register' ); ?>" name="<?php echo $this->get_field_name( 'register' ); ?>" type="text" value="<?php echo $register; ?>" /></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'lostpass' ); ?>"><?php _e( 'Lost Password URI:', 'bbpress' ); ?>
			<input class="widefat" id="<?php echo $this->get_field_id( 'lostpass' ); ?>" name="<?php echo $this->get_field_name( 'lostpass' ); ?>" type="text" value="<?php echo $lostpass; ?>" /></label>
		</p>

		<?php
	}
}

/**
 * bbPress Views Widget
 *
 * Adds a widget which displays the view list
 *
 * @since bbPress (r3020)
 *
 * @uses WP_Widget
 */
class BBP_Views_Widget extends WP_Widget {

	/**
	 * Register the widget
	 *
	 * @since bbPress (r3389)
	 *
	 * @uses register_widget()
	 */
	function register_widget() {
		register_widget( 'BBP_Views_Widget' );
	}

	/**
	 * bbPress View Widget
	 *
	 * Registers the view widget
	 *
	 * @since bbPress (r3020)
	 *
	 * @uses apply_filters() Calls 'bbp_views_widget_options' with the
	 *                        widget options
	 */
	function BBP_Views_Widget() {
		$widget_ops = apply_filters( 'bbp_views_widget_options', array(
			'classname'   => 'widget_display_views',
			'description' => __( 'A list of views.', 'bbpress' )
		) );

		parent::WP_Widget( false, __( 'bbPress View List', 'bbpress' ), $widget_ops );
	}

	/**
	 * Displays the output, the view list
	 *
	 * @since bbPress (r3020)
	 *
	 * @param mixed $args Arguments
	 * @param array $instance Instance
	 * @uses apply_filters() Calls 'bbp_view_widget_title' with the title
	 * @uses bbp_get_views() To get the views
	 * @uses bbp_view_url() To output the view url
	 * @uses bbp_view_title() To output the view title
	 */
	function widget( $args, $instance ) {

		// Only output widget contents if views exist
		if ( bbp_get_views() ) :

			extract( $args );

			$title = apply_filters( 'bbp_view_widget_title', $instance['title'] );

			echo $before_widget;
			echo $before_title . $title . $after_title; ?>

			<ul>

				<?php foreach ( bbp_get_views() as $view => $args ) : ?>

					<li><a class="bbp-view-title" href="<?php bbp_view_url( $view ); ?>" title="<?php bbp_view_title( $view ); ?>"><?php bbp_view_title( $view ); ?></a></li>

				<?php endforeach; ?>

			</ul>

			<?php echo $after_widget;

		endif;
	}

	/**
	 * Update the view widget options
	 *
	 * @since bbPress (r3020)
	 *
	 * @param array $new_instance The new instance options
	 * @param array $old_instance The old instance options
	 */
	function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Output the view widget options form
	 *
	 * @since bbPress (r3020)
	 *
	 * @param $instance Instance
	 * @uses BBP_Views_Widget::get_field_id() To output the field id
	 * @uses BBP_Views_Widget::get_field_name() To output the field name
	 */
	function form( $instance ) {
		$title = !empty( $instance['title'] ) ? esc_attr( $instance['title'] ) : ''; ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'bbpress' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
			</label>
		</p>

		<?php
	}
}

/**
 * bbPress Forum Widget
 *
 * Adds a widget which displays the forum list
 *
 * @since bbPress (r2653)
 *
 * @uses WP_Widget
 */
class BBP_Forums_Widget extends WP_Widget {

	/**
	 * Register the widget
	 *
	 * @since bbPress (r3389)
	 *
	 * @uses register_widget()
	 */
	function register_widget() {
		register_widget( 'BBP_Forums_Widget' );
	}

	/**
	 * bbPress Forum Widget
	 *
	 * Registers the forum widget
	 *
	 * @since bbPress (r2653)
	 *
	 * @uses apply_filters() Calls 'bbp_forums_widget_options' with the
	 *                        widget options
	 */
	function BBP_Forums_Widget() {
		$widget_ops = apply_filters( 'bbp_forums_widget_options', array(
			'classname'   => 'widget_display_forums',
			'description' => __( 'A list of forums.', 'bbpress' )
		) );

		parent::WP_Widget( false, __( 'bbPress Forum List', 'bbpress' ), $widget_ops );
	}

	/**
	 * Displays the output, the forum list
	 *
	 * @since bbPress (r2653)
	 *
	 * @param mixed $args Arguments
	 * @param array $instance Instance
	 * @uses apply_filters() Calls 'bbp_forum_widget_title' with the title
	 * @uses get_option() To get the forums per page option
	 * @uses current_user_can() To check if the current user can read
	 *                           private() To resety name
	 * @uses bbp_set_query_name() To set the query name to 'bbp_widget'
	 * @uses bbp_reset_query_name() To reset the query name
	 * @uses bbp_has_forums() The main forum loop
	 * @uses bbp_forums() To check whether there are more forums available
	 *                     in the loop
	 * @uses bbp_the_forum() Loads up the current forum in the loop
	 * @uses bbp_forum_permalink() To display the forum permalink
	 * @uses bbp_forum_title() To display the forum title
	 */
	function widget( $args, $instance ) {
		extract( $args );

		$title        = apply_filters( 'bbp_forum_widget_title', $instance['title'] );
		$parent_forum = !empty( $instance['parent_forum'] ) ? $instance['parent_forum'] : '0';

		$forums_query = array(
			'post_parent'    => $parent_forum,
			'posts_per_page' => get_option( '_bbp_forums_per_page', 50 ),
			'orderby'        => 'menu_order',
			'order'          => 'ASC'
		);

		bbp_set_query_name( 'bbp_widget' );

		if ( bbp_has_forums( $forums_query ) ) :

			echo $before_widget;
			echo $before_title . $title . $after_title; ?>

			<ul>

				<?php while ( bbp_forums() ) : bbp_the_forum(); ?>

					<li><a class="bbp-forum-title" href="<?php bbp_forum_permalink(); ?>" title="<?php bbp_forum_title(); ?>"><?php bbp_forum_title(); ?></a></li>

				<?php endwhile; ?>

			</ul>

		<?php echo $after_widget;

		endif;

		bbp_reset_query_name();
	}

	/**
	 * Update the forum widget options
	 *
	 * @since bbPress (r2653)
	 *
	 * @param array $new_instance The new instance options
	 * @param array $old_instance The old instance options
	 */
	function update( $new_instance, $old_instance ) {
		$instance                 = $old_instance;
		$instance['title']        = strip_tags( $new_instance['title'] );
		$instance['parent_forum'] = $new_instance['parent_forum'];

		// Force to any
		if ( !empty( $instance['parent_forum'] ) && !is_numeric( $instance['parent_forum'] ) ) {
			$instance['parent_forum'] = 'any';
		}

		return $instance;
	}

	/**
	 * Output the forum widget options form
	 *
	 * @since bbPress (r2653)
	 *
	 * @param $instance Instance
	 * @uses BBP_Forums_Widget::get_field_id() To output the field id
	 * @uses BBP_Forums_Widget::get_field_name() To output the field name
	 */
	function form( $instance ) {
		$title        = !empty( $instance['title']        ) ? esc_attr( $instance['title']        ) : '';
		$parent_forum = !empty( $instance['parent_forum'] ) ? esc_attr( $instance['parent_forum'] ) : '0'; ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'bbpress' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
			</label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'parent_forum' ); ?>"><?php _e( 'Parent Forum ID:', 'bbpress' ); ?>
				<input class="widefat" id="<?php echo $this->get_field_id( 'parent_forum' ); ?>" name="<?php echo $this->get_field_name( 'parent_forum' ); ?>" type="text" value="<?php echo $parent_forum; ?>" />
			</label>

			<br />

			<small><?php _e( '"0" to show only root - "any" to show all', 'bbpress' ); ?></small>
		</p>

		<?php
	}
}

/**
 * bbPress Topic Widget
 *
 * Adds a widget which displays the topic list
 *
 * @since bbPress (r2653)
 *
 * @uses WP_Widget
 */
class BBP_Topics_Widget extends WP_Widget {

	/**
	 * Register the widget
	 *
	 * @since bbPress (r3389)
	 *
	 * @uses register_widget()
	 */
	function register_widget() {
		register_widget( 'BBP_Topics_Widget' );
	}

	/**
	 * bbPress Topic Widget
	 *
	 * Registers the topic widget
	 *
	 * @since bbPress (r2653)
	 *
	 * @uses apply_filters() Calls 'bbp_topics_widget_options' with the
	 *                        widget options
	 */
	function BBP_Topics_Widget() {
		$widget_ops = apply_filters( 'bbp_topics_widget_options', array(
			'classname'   => 'widget_display_topics',
			'description' => __( 'A list of recent topics, sorted by popularity or freshness.', 'bbpress' )
		) );

		parent::WP_Widget( false, __( 'bbPress Topics List', 'bbpress' ), $widget_ops );
	}

	/**
	 * Displays the output, the topic list
	 *
	 * @since bbPress (r2653)
	 *
	 * @param mixed $args
	 * @param array $instance
	 * @uses apply_filters() Calls 'bbp_topic_widget_title' with the title
	 * @uses bbp_set_query_name() To set the query name to 'bbp_widget'
	 * @uses bbp_reset_query_name() To reset the query name
	 * @uses bbp_has_topics() The main topic loop
	 * @uses bbp_topics() To check whether there are more topics available
	 *                     in the loop
	 * @uses bbp_the_topic() Loads up the current topic in the loop
	 * @uses bbp_topic_permalink() To display the topic permalink
	 * @uses bbp_topic_title() To display the topic title
	 * @uses bbp_get_topic_last_active_time() To get the topic last active
	 *                                         time
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic_reply_count() To get the topic reply count
	 */
	function widget( $args, $instance ) {

		extract( $args );

		$title        = apply_filters( 'bbp_topic_widget_title', $instance['title'] );
		$max_shown    = !empty( $instance['max_shown']    ) ? (int) $instance['max_shown'] : 5;
		$show_date    = !empty( $instance['show_date']    ) ? 'on'                         : false;
		$parent_forum = !empty( $instance['parent_forum'] ) ? $instance['parent_forum']    : 'any';
		$pop_check    = ( $instance['pop_check'] < $max_shown || empty( $instance['pop_check'] ) ) ? -1 : $instance['pop_check'];

		// Query defaults
		$topics_query = array(
			'author'         => 0,
			'post_parent'    => $parent_forum,
			'posts_per_page' => $max_shown > $pop_check ? $max_shown : $pop_check,
			'posts_per_page' => $max_shown,
			'show_stickies'  => false,
			'order'          => 'DESC',
		);

		bbp_set_query_name( 'bbp_widget' );

		// Topics exist
		if ( bbp_has_topics( $topics_query ) ) : 
			
			// Sort by time
			if ( $pop_check < $max_shown ) :

				echo $before_widget;
				echo $before_title . $title . $after_title; ?>

				<ul>

					<?php while ( bbp_topics() ) : bbp_the_topic(); ?>

						<li>
							<a class="bbp-forum-title" href="<?php bbp_topic_permalink(); ?>" title="<?php bbp_topic_title(); ?>"><?php bbp_topic_title(); ?></a><?php if ( $show_date == 'on' ) _e( ', ' . bbp_get_topic_last_active_time() . ' ago' ); ?>
						</li>

					<?php endwhile; ?>

				</ul>

				<?php echo $after_widget;

			// Sort by popularity
			elseif ( $pop_check >= $max_shown ) :

				echo $before_widget;
				echo $before_title . $title . $after_title;

				while ( bbp_topics() ) {
					bbp_the_topic();
					$topics[bbp_get_topic_id()] = bbp_get_topic_reply_count();
				}

				arsort( $topics );
				$topic_count = 1;

				?>

				<ul>

					<?php foreach ( $topics as $topic_id => $topic_reply_count ) : ?>

						<li><a class="bbp-topic-title" href="<?php bbp_topic_permalink( $topic_id ); ?>" title="<?php bbp_topic_title( $topic_id ); ?>"><?php bbp_topic_title( $topic_id ); ?></a><?php if ( $show_date == 'on' ) _e( ', ' . bbp_get_topic_last_active_time( $topic_id ) . ' ago' ); ?></li>

					<?php

						$topic_count++;

						if ( $topic_count > $max_shown )
							break;

					endforeach; ?>

				</ul>

				<?php echo $after_widget;

			endif;
		endif;

		bbp_reset_query_name();

	}

	/**
	 * Update the forum widget options
	 *
	 * @since bbPress (r2653)
	 *
	 * @param array $new_instance The new instance options
	 * @param array $old_instance The old instance options
	 */
	function update( $new_instance, $old_instance ) {
		$instance              = $old_instance;
		$instance['title']     = strip_tags( $new_instance['title']     );
		$instance['max_shown'] = strip_tags( $new_instance['max_shown'] );
		$instance['show_date'] = strip_tags( $new_instance['show_date'] );
		$instance['pop_check'] = strip_tags( $new_instance['pop_check'] );

		return $instance;
	}

	/**
	 * Output the topic widget options form
	 *
	 * @since bbPress (r2653)
	 *
	 * @param $instance Instance
	 * @uses BBP_Topics_Widget::get_field_id() To output the field id
	 * @uses BBP_Topics_Widget::get_field_name() To output the field name
	 */
	function form( $instance ) {
		$title     = !empty( $instance['title']     ) ? esc_attr( $instance['title']     ) : '';
		$max_shown = !empty( $instance['max_shown'] ) ? esc_attr( $instance['max_shown'] ) : '';
		$show_date = !empty( $instance['show_date'] ) ? esc_attr( $instance['show_date'] ) : '';
		$pop_check = !empty( $instance['pop_check'] ) ? esc_attr( $instance['pop_check'] ) : ''; ?>

		<p><label for="<?php echo $this->get_field_id( 'title'     ); ?>"><?php _e( 'Title:',                  'bbpress' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title'     ); ?>" name="<?php echo $this->get_field_name( 'title'     ); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'max_shown' ); ?>"><?php _e( 'Maximum topics to show:', 'bbpress' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'max_shown' ); ?>" name="<?php echo $this->get_field_name( 'max_shown' ); ?>" type="text" value="<?php echo $max_shown; ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Show post date:',         'bbpress' ); ?> <input type="checkbox" id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" <?php checked( 'on', $show_date ); ?> /></label></p>
		<p>
			<label for="<?php echo $this->get_field_id( 'pop_check' ); ?>"><?php _e( 'Popularity check:',  'bbpress' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'pop_check' ); ?>" name="<?php echo $this->get_field_name( 'pop_check' ); ?>" type="text" value="<?php echo $pop_check; ?>" /></label>
			<br /><small><?php _e( 'Number of topics back to check reply count to determine popularity. A number less than the maximum number of topics to show disables the check.', 'bbpress' ); ?></small>
		</p>

		<?php
	}
}

/**
 * bbPress Replies Widget
 *
 * Adds a widget which displays the replies list
 *
 * @since bbPress (r2653)
 *
 * @uses WP_Widget
 */
class BBP_Replies_Widget extends WP_Widget {

	/**
	 * Register the widget
	 *
	 * @since bbPress (r3389)
	 *
	 * @uses register_widget()
	 */
	function register_widget() {
		register_widget( 'BBP_Replies_Widget' );
	}

	/**
	 * bbPress Replies Widget
	 *
	 * Registers the replies widget
	 *
	 * @since bbPress (r2653)
	 *
	 * @uses apply_filters() Calls 'bbp_replies_widget_options' with the
	 *                        widget options
	 */
	function BBP_Replies_Widget() {
		$widget_ops = apply_filters( 'bbp_replies_widget_options', array(
			'classname'   => 'widget_display_replies',
			'description' => __( 'A list of bbPress recent replies.', 'bbpress' )
		) );

		parent::WP_Widget( false, 'bbPress Reply List', $widget_ops );
	}

	/**
	 * Displays the output, the replies list
	 *
	 * @since bbPress (r2653)
	 *
	 * @param mixed $args
	 * @param array $instance
	 * @uses apply_filters() Calls 'bbp_reply_widget_title' with the title
	 * @uses bbp_set_query_name() To set the query name to 'bbp_widget'
	 * @uses bbp_reset_query_name() To reset the query name
	 * @uses bbp_has_replies() The main reply loop
	 * @uses bbp_replies() To check whether there are more replies available
	 *                     in the loop
	 * @uses bbp_the_reply() Loads up the current reply in the loop
	 * @uses bbp_get_reply_author_link() To get the reply author link
	 * @uses bbp_get_reply_author() To get the reply author name
	 * @uses bbp_get_reply_id() To get the reply id
	 * @uses bbp_get_reply_url() To get the reply url
	 * @uses bbp_get_reply_excerpt() To get the reply excerpt
	 * @uses bbp_get_reply_topic_title() To get the reply topic title
	 * @uses get_the_date() To get the date of the reply
	 * @uses get_the_time() To get the time of the reply
	 */
	function widget( $args, $instance ) {

		extract( $args );

		$title     = apply_filters( 'bbp_replies_widget_title', $instance['title'] );
		$max_shown = !empty( $instance['max_shown'] ) ? $instance['max_shown'] : '5';
		$show_date = !empty( $instance['show_date'] ) ? 'on'                   : false;

		// Query defaults
		$replies_query = array(
			'post_status'    => join( ',', array( bbp_get_public_status_id(), bbp_get_closed_status_id() ) ),
			'posts_per_page' => $max_shown,
			'order'          => 'DESC'
		);

		// Set the query name
		bbp_set_query_name( 'bbp_widget' );

		// Get replies and display them
		if ( bbp_has_replies( $replies_query ) ) :

			echo $before_widget;
			echo $before_title . $title . $after_title; ?>

			<ul>

				<?php while ( bbp_replies() ) : bbp_the_reply(); ?>

					<li>

						<?php
						$author_link = bbp_get_reply_author_link( array( 'type' => 'both', 'size' => 14 ) );
						$reply_link  = '<a class="bbp-reply-topic-title" href="' . esc_url( bbp_get_reply_url() ) . '" title="' . bbp_get_reply_excerpt( bbp_get_reply_id(), 50 ) . '">' . bbp_get_reply_topic_title() . '</a>';

						/* translators: bbpress replies widget: 1: reply author, 2: reply link, 3: reply date, 4: reply time */
						printf( _x( $show_date == 'on' ? '%1$s on %2$s, %3$s, %4$s' : '%1$s on %2$s', 'widgets', 'bbpress' ), $author_link, $reply_link, get_the_date(), get_the_time() );
						?>

					</li>

				<?php endwhile; ?>

			</ul>

			<?php echo $after_widget;

		endif;

		bbp_reset_query_name();
	}

	/**
	 * Update the forum widget options
	 *
	 * @since bbPress (r2653)
	 *
	 * @param array $new_instance The new instance options
	 * @param array $old_instance The old instance options
	 */
	function update( $new_instance, $old_instance ) {
		$instance              = $old_instance;
		$instance['title']     = strip_tags( $new_instance['title']     );
		$instance['max_shown'] = strip_tags( $new_instance['max_shown'] );
		$instance['show_date'] = strip_tags( $new_instance['show_date'] );

		return $instance;
	}

	/**
	 * Output the reply widget options form
	 *
	 * @since bbPress (r2653)
	 *
	 * @param $instance Instance
	 * @uses BBP_Replies_Widget::get_field_id() To output the field id
	 * @uses BBP_Replies_Widget::get_field_name() To output the field name
	 */
	function form( $instance ) {
		$title     = !empty( $instance['title']     ) ? esc_attr( $instance['title']     ) : '';
		$max_shown = !empty( $instance['max_shown'] ) ? esc_attr( $instance['max_shown'] ) : '';
		$show_date = !empty( $instance['show_date'] ) ? esc_attr( $instance['show_date'] ) : ''; ?>

		<p><label for="<?php echo $this->get_field_id( 'title'     ); ?>"><?php _e( 'Title:',                   'bbpress' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title'     ); ?>" name="<?php echo $this->get_field_name( 'title'     ); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'max_shown' ); ?>"><?php _e( 'Maximum replies to show:', 'bbpress' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'max_shown' ); ?>" name="<?php echo $this->get_field_name( 'max_shown' ); ?>" type="text" value="<?php echo $max_shown; ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Show post date:',          'bbpress' ); ?> <input type="checkbox" id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" <?php checked( 'on', $show_date ); ?> /></label></p>

		<?php
	}
}

?>
