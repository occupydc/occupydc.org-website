<?php

/**
 * bbPress Admin Settings
 *
 * @package bbPress
 * @subpackage Administration
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/** Start Main Section ********************************************************/

/**
 * Main settings section description for the settings page
 *
 * @since bbPress (r2786)
 */
function bbp_admin_setting_callback_main_section() {
?>

	<p><?php _e( 'Main settings for the bbPress plugin', 'bbpress' ); ?></p>

<?php
}

/**
 * Edit lock setting field
 *
 * @since bbPress (r2737)
 *
 * @uses bbp_form_option() To output the option value
 */
function bbp_admin_setting_callback_editlock() {
?>

	<input name="_bbp_edit_lock" type="text" id="_bbp_edit_lock" value="<?php bbp_form_option( '_bbp_edit_lock', '5' ); ?>" class="small-text" />
	<label for="_bbp_edit_lock"><?php _e( 'minutes', 'bbpress' ); ?></label>

<?php
}

/**
 * Throttle setting field
 *
 * @since bbPress (r2737)
 *
 * @uses bbp_form_option() To output the option value
 */
function bbp_admin_setting_callback_throttle() {
?>

	<input name="_bbp_throttle_time" type="text" id="_bbp_throttle_time" value="<?php bbp_form_option( '_bbp_throttle_time', '10' ); ?>" class="small-text" />
	<label for="_bbp_throttle_time"><?php _e( 'seconds', 'bbpress' ); ?></label>

<?php
}

/**
 * Allow favorites setting field
 *
 * @since bbPress (r2786)
 *
 * @uses checked() To display the checked attribute
 */
function bbp_admin_setting_callback_favorites() {
?>

	<input id="_bbp_enable_favorites" name="_bbp_enable_favorites" type="checkbox" id="_bbp_enable_favorites" value="1" <?php checked( bbp_is_favorites_active( true ) ); ?> />
	<label for="_bbp_enable_favorites"><?php _e( 'Allow users to mark topics as favorites?', 'bbpress' ); ?></label>

<?php
}

/**
 * Allow subscriptions setting field
 *
 * @since bbPress (r2737)
 *
 * @uses checked() To display the checked attribute
 */
function bbp_admin_setting_callback_subscriptions() {
?>

	<input id="_bbp_enable_subscriptions" name="_bbp_enable_subscriptions" type="checkbox" id="_bbp_enable_subscriptions" value="1" <?php checked( bbp_is_subscriptions_active( true ) ); ?> />
	<label for="_bbp_enable_subscriptions"><?php _e( 'Allow users to subscribe to topics', 'bbpress' ); ?></label>

<?php
}

/**
 * Allow topic and reply revisions
 *
 * @since bbPress (r3412)
 *
 * @uses checked() To display the checked attribute
 */
function bbp_admin_setting_callback_revisions() {
?>

	<input id="_bbp_allow_revisions" name="_bbp_allow_revisions" type="checkbox" id="_bbp_allow_revisions" value="1" <?php checked( bbp_allow_revisions( true ) ); ?> />
	<label for="_bbp_allow_revisions"><?php _e( 'Allow topic and reply revision logging', 'bbpress' ); ?></label>

<?php
}

/**
 * Allow anonymous posting setting field
 *
 * @since bbPress (r2737)
 *
 * @uses checked() To display the checked attribute
 */
function bbp_admin_setting_callback_anonymous() {
?>

	<input id="_bbp_allow_anonymous" name="_bbp_allow_anonymous" type="checkbox" id="_bbp_allow_anonymous" value="1" <?php checked( bbp_allow_anonymous( false ) ); ?> />
	<label for="_bbp_allow_anonymous"><?php _e( 'Allow guest users without accounts to create topics and replies', 'bbpress' ); ?></label>

<?php
}

/**
 * Allow global access setting field
 *
 * @since bbPress (r3378)
 *
 * @uses checked() To display the checked attribute
 */
function bbp_admin_setting_callback_global_access() {
?>

	<input id="_bbp_allow_global_access" name="_bbp_allow_global_access" type="checkbox" id="_bbp_allow_global_access" value="1" <?php checked( bbp_allow_global_access( false ) ); ?> />
	<label for="_bbp_allow_global_access"><?php _e( 'Allow all users of your multisite installation to create topics and replies', 'bbpress' ); ?></label>

<?php
}

/** Start Per Page Section ****************************************************/

/**
 * Per page settings section description for the settings page
 *
 * @since bbPress (r2786)
 */
function bbp_admin_setting_callback_per_page_section() {
?>

	<p><?php _e( 'Per page settings for the bbPress plugin', 'bbpress' ); ?></p>

<?php
}

/**
 * Topics per page setting field
 *
 * @since bbPress (r2786)
 *
 * @uses bbp_form_option() To output the option value
 */
function bbp_admin_setting_callback_topics_per_page() {
?>

	<input name="_bbp_topics_per_page" type="text" id="_bbp_topics_per_page" value="<?php bbp_form_option( '_bbp_topics_per_page', '15' ); ?>" class="small-text" />
	<label for="_bbp_topics_per_page"><?php _e( 'per page', 'bbpress' ); ?></label>

<?php
}

/**
 * Replies per page setting field
 *
 * @since bbPress (r2786)
 *
 * @uses bbp_form_option() To output the option value
 */
function bbp_admin_setting_callback_replies_per_page() {
?>

	<input name="_bbp_replies_per_page" type="text" id="_bbp_replies_per_page" value="<?php bbp_form_option( '_bbp_replies_per_page', '15' ); ?>" class="small-text" />
	<label for="_bbp_replies_per_page"><?php _e( 'per page', 'bbpress' ); ?></label>

<?php
}

/** Start Per RSS Page Section ************************************************/

/**
 * Per page settings section description for the settings page
 *
 * @since bbPress (r2786)
 */
function bbp_admin_setting_callback_per_rss_page_section() {
?>

	<p><?php _e( 'Per RSS page settings for the bbPress plugin', 'bbpress' ); ?></p>

<?php
}

/**
 * Topics per RSS page setting field
 *
 * @since bbPress (r2786)
 *
 * @uses bbp_form_option() To output the option value
 */
function bbp_admin_setting_callback_topics_per_rss_page() {
?>

	<input name="_bbp_topics_per_rss_page" type="text" id="_bbp_topics_per_rss_page" value="<?php bbp_form_option( '_bbp_topics_per_rss_page', '25' ); ?>" class="small-text" />
	<label for="_bbp_topics_per_rss_page"><?php _e( 'per page', 'bbpress' ); ?></label>

<?php
}

/**
 * Replies per RSS page setting field
 *
 * @since bbPress (r2786)
 *
 * @uses bbp_form_option() To output the option value
 */
function bbp_admin_setting_callback_replies_per_rss_page() {
?>

	<input name="_bbp_replies_per_rss_page" type="text" id="_bbp_replies_per_rss_page" value="<?php bbp_form_option( '_bbp_replies_per_rss_page', '25' ); ?>" class="small-text" />
	<label for="_bbp_replies_per_rss_page"><?php _e( 'per page', 'bbpress' ); ?></label>

<?php
}

/** Start Slug Section ********************************************************/

/**
 * Slugs settings section description for the settings page
 *
 * @since bbPress (r2786)
 */
function bbp_admin_setting_callback_root_slug_section() {

	// Flush rewrite rules when this section is saved
	if ( isset( $_GET['settings-updated'] ) && isset( $_GET['page'] ) )
		flush_rewrite_rules(); ?>

	<p><?php printf( __( 'Include custom root slugs to prefix your forums and topics with. These can be partnered with WordPress pages to allow more flexibility.', 'bbpress' ), get_admin_url( null, 'options-permalink.php' ) ); ?></p>

<?php
}

/**
 * Root slug setting field
 *
 * @since bbPress (r2786)
 *
 * @uses bbp_form_option() To output the option value
 */
function bbp_admin_setting_callback_root_slug() {
?>

		<input name="_bbp_root_slug" type="text" id="_bbp_root_slug" class="regular-text code" value="<?php bbp_form_option( '_bbp_root_slug', 'forums', true ); ?>" />

<?php
	// Slug Check
	bbp_form_slug_conflict_check( '_bbp_root_slug', 'forums' );
}

/**
 * Topic archive slug setting field
 *
 * @since bbPress (r2786)
 *
 * @uses bbp_form_option() To output the option value
 */
function bbp_admin_setting_callback_topic_archive_slug() {
?>

	<input name="_bbp_topic_archive_slug" type="text" id="_bbp_topic_archive_slug" class="regular-text code" value="<?php bbp_form_option( '_bbp_topic_archive_slug', 'topics', true ); ?>" />

<?php
	// Slug Check
	bbp_form_slug_conflict_check( '_bbp_topic_archive_slug', 'topics' );
}

/** Single Slugs **************************************************************/

/**
 * Slugs settings section description for the settings page
 *
 * @since bbPress (r2786)
 */
function bbp_admin_setting_callback_single_slug_section() {
?>

	<p><?php printf( __( 'You can enter custom slugs for your single forums, topics, replies, and tags URLs here. If you change these, existing permalinks will also change.', 'bbpress' ), get_admin_url( null, 'options-permalink.php' ) ); ?></p>

<?php
}

/**
 * Include root slug setting field
 *
 * @since bbPress (r2786)
 *
 * @uses checked() To display the checked attribute
 */
function bbp_admin_setting_callback_include_root() {
?>

	<input id="_bbp_include_root" name="_bbp_include_root" type="checkbox" id="_bbp_include_root" value="1" <?php checked( get_option( '_bbp_include_root', true ) ); ?> />
	<label for="_bbp_include_root"><?php _e( 'Prefix your forum area with the Forum Base slug (Recommended)', 'bbpress' ); ?></label>

<?php
}

/**
 * Forum slug setting field
 *
 * @since bbPress (r2786)
 *
 * @uses bbp_form_option() To output the option value
 */
function bbp_admin_setting_callback_forum_slug() {
?>

	<input name="_bbp_forum_slug" type="text" id="_bbp_forum_slug" class="regular-text code" value="<?php bbp_form_option( '_bbp_forum_slug', 'forum', true ); ?>" />

<?php
	// Slug Check
	bbp_form_slug_conflict_check( '_bbp_forum_slug', 'forum' );
}

/**
 * Topic slug setting field
 *
 * @since bbPress (r2786)
 *
 * @uses bbp_form_option() To output the option value
 */
function bbp_admin_setting_callback_topic_slug() {
?>

	<input name="_bbp_topic_slug" type="text" id="_bbp_topic_slug" class="regular-text code" value="<?php bbp_form_option( '_bbp_topic_slug', 'topic', true ); ?>" />

<?php
	// Slug Check
	bbp_form_slug_conflict_check( '_bbp_topic_slug', 'topic' );
}

/**
 * Reply slug setting field
 *
 * @since bbPress (r2786)
 *
 * @uses bbp_form_option() To output the option value
 */
function bbp_admin_setting_callback_reply_slug() {
?>

	<input name="_bbp_reply_slug" type="text" id="_bbp_reply_slug" class="regular-text code" value="<?php bbp_form_option( '_bbp_reply_slug', 'reply', true ); ?>" />

<?php
	// Slug Check
	bbp_form_slug_conflict_check( '_bbp_reply_slug', 'reply' );
}

/**
 * Topic tag slug setting field
 *
 * @since bbPress (r2786)
 *
 * @uses bbp_form_option() To output the option value
 */
function bbp_admin_setting_callback_topic_tag_slug() {
?>

	<input name="_bbp_topic_tag_slug" type="text" id="_bbp_topic_tag_slug" class="regular-text code" value="<?php bbp_form_option( '_bbp_topic_tag_slug', 'topic-tag', true ); ?>" />

<?php

	// Slug Check
	bbp_form_slug_conflict_check( '_bbp_topic_tag_slug', 'topic-tag' );
}

/** Other Slugs ***************************************************************/

/**
 * User slug setting field
 *
 * @since bbPress (r2786)
 *
 * @uses bbp_form_option() To output the option value
 */
function bbp_admin_setting_callback_user_slug() {
?>

	<input name="_bbp_user_slug" type="text" id="_bbp_user_slug" class="regular-text code" value="<?php bbp_form_option( '_bbp_user_slug', 'users', true ); ?>" />

<?php
	// Slug Check
	bbp_form_slug_conflict_check( '_bbp_user_slug', 'users' );
}

/**
 * View slug setting field
 *
 * @since bbPress (r2789)
 *
 * @uses bbp_form_option() To output the option value
 */
function bbp_admin_setting_callback_view_slug() {
?>

	<input name="_bbp_view_slug" type="text" id="_bbp_view_slug" class="regular-text code" value="<?php bbp_form_option( '_bbp_view_slug', 'view', true ); ?>" />

<?php
	// Slug Check
	bbp_form_slug_conflict_check( '_bbp_view_slug', 'view' );
}

/** Settings Page *************************************************************/

/**
 * The main settings page
 *
 * @since bbPress (r2643)
 *
 * @uses screen_icon() To display the screen icon
 * @uses settings_fields() To output the hidden fields for the form
 * @uses do_settings_sections() To output the settings sections
 */
function bbp_admin_settings() {
?>

	<div class="wrap">

		<?php screen_icon(); ?>

		<h2><?php _e( 'bbPress Settings', 'bbpress' ) ?></h2>

		<form action="options.php" method="post">

			<?php settings_fields( 'bbpress' ); ?>

			<?php do_settings_sections( 'bbpress' ); ?>

			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php _e( 'Save Changes', 'bbpress' ); ?>" />
			</p>
		</form>
	</div>

<?php
}

/**
 * Contextual help for bbPress settings page
 *
 * @since bbPress (r3119)
 */
function bbp_admin_settings_help() {

	$bbp_contextual_help[] = __('This screen provides access to basic bbPress settings.', 'bbpress' );
	$bbp_contextual_help[] = __('In the Main Settings you have a number of options:',     'bbpress' );
	$bbp_contextual_help[] =
		'<ul>' .
			'<li>' . __( 'You can choose to lock a post after a certain number of minutes. "Locking post editing" will prevent the author from editing some amount of time after saving a post.',              'bbpress' ) . '</li>' .
			'<li>' . __( '"Throttle time" is the amount of time required between posts from a single author. The higher the throttle time, the longer a user will need to wait between posting to the forum.', 'bbpress' ) . '</li>' .
			'<li>' . __( 'You may choose to allow favorites, which are a way for users to save and later return to topics they favor. This is enabled by default.',                                            'bbpress' ) . '</li>' .
			'<li>' . __( 'You may choose to allow subscriptions, which allows users to subscribe for notifications to topics that interest them. This is enabled by default.',                                 'bbpress' ) . '</li>' .
			'<li>' . __( 'You may choose to allow "Anonymous Posting", which will allow guest users who do not have accounts on your site to both create topics as well as replies.',                          'bbpress' ) . '</li>' .
		'</ul>';

	$bbp_contextual_help[] = __( 'Per Page settings allow you to control the number of topics and replies will appear on each of those pages. This is comparable to the WordPress "Reading Settings" page, where you can set the number of posts that should show on blog pages and in feeds.', 'bbpress' );
	$bbp_contextual_help[] = __( 'The Forums section allows you to control the permalink structure for your forums. Each "base" is what will be displayed after your main URL and right before your permalink slug.', 'bbpress' );
	$bbp_contextual_help[] = __( 'You must click the Save Changes button at the bottom of the screen for new settings to take effect.', 'bbpress' );
	$bbp_contextual_help[] = __( '<strong>For more information:</strong>', 'bbpress' );
	$bbp_contextual_help[] =
		'<ul>' .
			'<li>' . __( '<a href="http://bbpress.org/documentation/">bbPress Documentation</a>', 'bbpress' ) . '</li>' .
			'<li>' . __( '<a href="http://bbpress.org/forums/">bbPress Support Forums</a>',       'bbpress' ) . '</li>' .
		'</ul>' ;

	// Empty the default $contextual_help var
	$contextual_help = '';

	// Wrap each help item in paragraph tags
	foreach( $bbp_contextual_help as $paragraph )
		$contextual_help .= '<p>' . $paragraph . '</p>';

	// Add help
	add_contextual_help( 'settings_page_bbpress', $contextual_help );
}

/**
 * Output settings API option
 *
 * @since bbPress (r3203)
 *
 * @uses bbp_get_bbp_form_option()
 *
 * @param string $option
 * @param string $default
 * @param bool $slug
 */
function bbp_form_option( $option, $default = '' , $slug = false ) {
	echo bbp_get_form_option( $option, $default, $slug );
}
	/**
	 * Return settings API option
	 *
	 * @since bbPress (r3203)
	 *
	 * @uses get_option()
	 * @uses esc_attr()
	 * @uses apply_filters()
	 *
	 * @param string $option
	 * @param string $default
	 * @param bool $slug
	 */
	function bbp_get_form_option( $option, $default = '', $slug = false ) {

		// Get the option and sanitize it
		$value = get_option( $option, $default );

		// Slug?
		if ( true === $slug )
			$value = esc_attr( apply_filters( 'editable_slug', $value ) );

		// Not a slug
		else
			$value = esc_attr( $value );

		// Fallback to default
		if ( empty( $value ) )
			$value = $default;

		// Allow plugins to further filter the output
		return apply_filters( 'bbp_get_form_option', $value, $option );
	}

/**
 * Used to check if a bbPress slug conflicts with an existing known slug.
 *
 * @since bbPress (r3306)
 *
 * @param string $slug
 * @param string $default 
 *
 * @uses bbp_get_form_option() To get a sanitized slug string
 */
function bbp_form_slug_conflict_check( $slug, $default ) {

	// Only set the slugs once ver page load
	static $the_core_slugs = array();

	// Get the form value
	$this_slug = bbp_get_form_option( $slug, $default, true );

	if ( empty( $the_core_slugs ) ) {

		// Slugs to check
		$core_slugs = apply_filters( 'bbp_slug_conflict_check', array(

			/** WordPress Core ****************************************************/

			// Core Post Types
			'post_base'       => array( 'name' => __( 'Posts'         ), 'default' => 'post',          'context' => 'WordPress' ),
			'page_base'       => array( 'name' => __( 'Pages'         ), 'default' => 'page',          'context' => 'WordPress' ),
			'revision_base'   => array( 'name' => __( 'Revisions'     ), 'default' => 'revision',      'context' => 'WordPress' ),
			'attachment_base' => array( 'name' => __( 'Attachments'   ), 'default' => 'attachment',    'context' => 'WordPress' ),
			'nav_menu_base'   => array( 'name' => __( 'Menus'         ), 'default' => 'nav_menu_item', 'context' => 'WordPress' ),

			// Post Tags
			'tag_base'        => array( 'name' => __( 'Tag base'      ), 'default' => 'tag',           'context' => 'WordPress' ),

			// Post Categories
			'category_base'   => array( 'name' => __( 'Category base' ), 'default' => 'category',      'context' => 'WordPress' ),

			/** bbPress Core ******************************************************/

			// Forum archive slug
			'_bbp_root_slug'          => array( 'name' => __( 'Forums base', 'bbpress' ), 'default' => 'forums', 'context' => 'bbPress' ),

			// Topic archive slug
			'_bbp_topic_archive_slug' => array( 'name' => __( 'Topics base', 'bbpress' ), 'default' => 'topics', 'context' => 'bbPress' ),

			// Forum slug
			'_bbp_forum_slug'         => array( 'name' => __( 'Forum slug',  'bbpress' ), 'default' => 'forum',  'context' => 'bbPress' ),

			// Topic slug
			'_bbp_topic_slug'         => array( 'name' => __( 'Topic slug',  'bbpress' ), 'default' => 'topic',  'context' => 'bbPress' ),

			// Reply slug
			'_bbp_reply_slug'         => array( 'name' => __( 'Reply slug',  'bbpress' ), 'default' => 'reply',  'context' => 'bbPress' ),

			// User profile slug
			'_bbp_user_slug'          => array( 'name' => __( 'User base',   'bbpress' ), 'default' => 'users',  'context' => 'bbPress' ),

			// View slug
			'_bbp_view_slug'          => array( 'name' => __( 'View base',   'bbpress' ), 'default' => 'view',   'context' => 'bbPress' ),

			// Topic tag slug
			'_bbp_topic_tag_slug'     => array( 'name' => __( 'Topic tag slug', 'bbpress' ), 'default' => 'topic-tag', 'context' => 'bbPress' ),
		) );

		/** BuddyPress Core *******************************************************/

		if ( defined( 'BP_VERSION' ) ) {
			global $bp;

			// Loop through root slugs and check for conflict
			if ( !empty( $bp->pages ) ) {
				foreach ( $bp->pages as $page => $page_data ) {
					$page_base    = $page . '_base';
					$page_title   = sprintf( __( '%s page', 'bbpress' ), $page_data->title );
					$core_slugs[$page_base] = array( 'name' => $page_title, 'default' => $page_data->slug, 'context' => 'BuddyPress' );
				}
			}			
		}

		// Set the static
		$the_core_slugs = apply_filters( 'bbp_slug_conflict', $core_slugs );
	}

	// Loop through slugs to check
	foreach( $the_core_slugs as $key => $value ) {
		
		// Get the slug
		$slug_check = bbp_get_form_option( $key, $value['default'], true );

		// Compare
		if ( ( $slug != $key ) && ( $slug_check == $this_slug ) ) : ?>
	
			<span class="attention"><?php printf( __( 'Possible %1$s conflict: <strong>%2$s</strong>', 'bbpress' ), $value['context'], $value['name'] ); ?></span>
			
		<?php endif; 
	}
}

?>
