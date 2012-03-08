<?php

/**
 * bbPress Replies Admin Class
 *
 * @package bbPress
 * @subpackage Administration
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'BBP_Replies_Admin' ) ) :
/**
 * Loads bbPress replies admin area
 *
 * @package bbPress
 * @subpackage Administration
 * @since bbPress (r2464)
 */
class BBP_Replies_Admin {

	/** Variables *************************************************************/

	/**
	 * @var The post type of this admin component
	 */
	var $post_type = '';

	/** Functions *************************************************************/

	/**
	 * The main bbPress admin loader
	 *
	 * @since bbPress (r2515)
	 *
	 * @uses BBP_Replies_Admin::setup_globals() Setup the globals needed
	 * @uses BBP_Replies_Admin::setup_actions() Setup the hooks and actions
	 * @uses BBP_Replies_Admin::setup_actions() Setup the help text
	 */
	function __construct() {
		$this->setup_globals();
		$this->setup_actions();
		$this->setup_help();
	}

	/**
	 * Setup the admin hooks, actions and filters
	 *
	 * @since bbPress (r2646)
	 * @access private
	 *
	 * @uses add_action() To add various actions
	 * @uses add_filter() To add various filters
	 * @uses bbp_get_forum_post_type() To get the forum post type
	 * @uses bbp_get_topic_post_type() To get the topic post type
	 * @uses bbp_get_reply_post_type() To get the reply post type
	 */
	function setup_actions() {

		// Add some general styling to the admin area
		add_action( 'admin_head',            array( $this, 'admin_head'       ) );

		// Messages
		add_filter( 'post_updated_messages', array( $this, 'updated_messages' ) );

		// Reply column headers.
		add_filter( 'manage_' . $this->post_type . '_posts_columns',  array( $this, 'replies_column_headers' ) );

		// Reply columns (in post row)
		add_action( 'manage_' . $this->post_type . '_posts_custom_column',  array( $this, 'replies_column_data' ), 10, 2 );
		add_filter( 'post_row_actions',                                     array( $this, 'replies_row_actions' ), 10, 2 );

		// Reply metabox actions
		add_action( 'add_meta_boxes', array( $this, 'reply_attributes_metabox'      ) );
		add_action( 'save_post',      array( $this, 'reply_attributes_metabox_save' ) );

		// Check if there are any bbp_toggle_reply_* requests on admin_init, also have a message displayed
		add_action( 'bbp_admin_init', array( $this, 'toggle_reply'        ) );
		add_action( 'admin_notices',  array( $this, 'toggle_reply_notice' ) );

		// Anonymous metabox actions
		add_action( 'add_meta_boxes', array( $this, 'author_metabox'      ) );
		add_action( 'save_post',      array( $this, 'author_metabox_save' ) );

		// Add ability to filter topics and replies per forum
		add_filter( 'restrict_manage_posts', array( $this, 'filter_dropdown'  ) );
		add_filter( 'request',               array( $this, 'filter_post_rows' ) );
	}

	/**
	 * Admin globals
	 *
	 * @since bbPress (r2646)
	 * @access private
	 */
	function setup_globals() {

		// Setup the post type for this admin component
		$this->post_type = bbp_get_reply_post_type();
	}

	/**
	 * Contextual help for replies
	 *
	 * @since bbPress (r3119)
	 * @access private
	 */
	function setup_help() {

		// Define local variable(s)
		$contextual_help = array();

		/** New/Edit **********************************************************/

		$bbp_contextual_help[] = __( 'The reply title field and the big reply editing area are fixed in place, but you can reposition all the other boxes using drag and drop, and can minimize or expand them by clicking the title bar of the box. Use the Screen Options tab to unhide more boxes (Reply Attributes, Slug) or to choose a 1- or 2-column layout for this screen.', 'bbpress' );
		$bbp_contextual_help[] = __( '<strong>Title</strong> - Enter a title for your reply. After you enter a title, you will see the permalink below, which you can edit.', 'bbpress' );
		$bbp_contextual_help[] = __( '<strong>Post editor</strong> - Enter the text for your reply. There are two modes of editing: Visual and HTML. Choose the mode by clicking on the appropriate tab. Visual mode gives you a WYSIWYG editor. Click the last icon in the row to get a second row of controls. The screen icon just before that allows you to expand the edit box to full screen. The HTML mode allows you to enter raw HTML along with your forum text. You can insert media files by clicking the icons above the post editor and following the directions.', 'bbpress' );
		$bbp_contextual_help[] = __( '<strong>Reply Attributes</strong> - Select the attributes that your reply should have. The Parent Topic dropdown determines the parent topic that the reply belongs to.', 'bbpress' );
		$bbp_contextual_help[] = __( '<strong>Publish</strong> - The Publish box will allow you to save your reply as Draft or Pending Review. You may Preview your reply before it is published as well. The Visibility will determine whether the reply is Public, Password protected (requiring a password on the site to view) or Private (only the author will have access to it). Replies may be published immediately by clicking the dropdown, or at a specific date and time by clicking the Edit link.', 'bbpress' );
		$bbp_contextual_help[] = __( '<strong>Revisions</strong> - Revisions show past versions of the saved reply. Each revision can be compared to the current version, or another revision. Revisions can also be restored to the current version.', 'bbpress' );
		$bbp_contextual_help[] = __( '<strong>For more information:</strong>', 'bbpress' );
		$bbp_contextual_help[] =
			'<ul>' .
				'<li>' . __( '<a href="http://bbpress.org/documentation/">bbPress Documentation</a>', 'bbpress' ) . '</li>' .
				'<li>' . __( '<a href="http://bbpress.org/forums/">bbPress Support Forums</a>', 'bbpress' ) . '</li>' .
			'</ul>' ;

		// Wrap each help item in paragraph tags
		foreach( $bbp_contextual_help as $paragraph )
			$contextual_help .= '<p>' . $paragraph . '</p>';

		// Add help
		add_contextual_help( bbp_get_reply_post_type(), $contextual_help );

		// Reset
		$contextual_help = $bbp_contextual_help = '';

		/** Post Rows *********************************************************/

		$bbp_contextual_help[] = __( 'This screen displays the replies created on your site.', 'bbpress' );
		$bbp_contextual_help[] = __( 'You can customize the display of this screen in a number of ways:', 'bbpress' );
		$bbp_contextual_help[] =
			'<ul>' .
				'<li>' . __( 'You can hide/display columns based on your needs (Forum, Topic, Author, and Created) and decide how many replies to list per screen using the Screen Options tab.', 'bbpress' ) . '</li>' .
				'<li>' . __( 'You can filter the list of replies by reply status using the text links in the upper left to show All, Published, Pending Review, Draft, or Trashed topics. The default view is to show all replies.', 'bbpress' ) . '</li>' .
				'<li>' . __( 'You can view replies in a simple title list or with an excerpt. Choose the view you prefer by clicking on the icons at the top of the list on the right.', 'bbpress' ) . '</li>' .
				'<li>' . __( 'You can refine the list to show only replies from a specific month by using the dropdown menus above the replies list. Click the Filter button after making your selection.', 'bbpress' ) . '</li>' .
				'<li>' . __( 'You can also show only replies from a specific parent forum by using the parent forum dropdown above the replies list and selecting the parent forum. Click the Filter button after making your selection.', 'bbpress' ) . '</li>' .
			'</ul>';

		$bbp_contextual_help[] = __( 'Hovering over a row in the replies list will display action links that allow you to manage your reply. You can perform the following actions:', 'bbpress' );
		$bbp_contextual_help[] =
			'<ul>' .
				'<li>' . __( 'Edit takes you to the editing screen for that reply. You can also reach that screen by clicking on the reply title.', 'bbpress' ) . '</li>' .
				'<li>' . __( 'Trash removes your reply from this list and places it in the trash, from which you can permanently delete it.', 'bbpress' ) . '</li>' .
				'<li>' . __( 'View will take you to your live reply to view the reply.', 'bbpress' ) . '</li>' .
				'<li>' . __( 'Spam will mark the topic as spam, preventing further replies to it and removing it from the site&rsquo;s public view.', 'bbpress' ) . '</li>' .
			'</ul>';

		$bbp_contextual_help[] = __( 'You can also edit multiple replies at once. Select the replies you want to edit using the checkboxes, select Edit from the Bulk Actions menu and click Apply. You will be able to change the metadata for all selected replies at once. To remove a reply from the grouping, just click the x next to its name in the Bulk Edit area that appears.', 'bbpress' );
		$bbp_contextual_help[] = __( 'The Bulk Actions menu may also be used to delete multiple replies at once. Select Delete from the dropdown after making your selection.', 'bbpress' );
		$bbp_contextual_help[] = __( '<strong>For more information:</strong>', 'bbpress' );
		$bbp_contextual_help[] =
			'<ul>' .
				'<li>' . __( '<a href="http://bbpress.org/documentation/">bbPress Documentation</a>', 'bbpress' ) . '</li>' .
				'<li>' . __( '<a href="http://bbpress.org/forums/">bbPress Support Forums</a>', 'bbpress', 'bbpress' ) . '</li>' .
			'</ul>';

		// Wrap each help item in paragraph tags
		foreach( $bbp_contextual_help as $paragraph )
			$contextual_help .= '<p>' . $paragraph . '</p>';

		// Add help
		add_contextual_help( 'edit-' . bbp_get_reply_post_type(), $contextual_help );
	}

	/**
	 * Add the reply attributes metabox
	 *
	 * @since bbPress (r2746)
	 *
	 * @uses bbp_get_reply_post_type() To get the reply post type
	 * @uses add_meta_box() To add the metabox
	 * @uses do_action() Calls 'bbp_reply_attributes_metabox'
	 */
	function reply_attributes_metabox() {
		add_meta_box (
			'bbp_reply_attributes',
			__( 'Reply Attributes', 'bbpress' ),
			'bbp_reply_metabox',
			$this->post_type,
			'side',
			'high'
		);

		do_action( 'bbp_reply_attributes_metabox' );
	}

	/**
	 * Pass the reply attributes for processing
	 *
	 * @since bbPress (r2746)
	 *
	 * @param int $reply_id Reply id
	 * @uses current_user_can() To check if the current user is capable of
	 *                           editing the reply
	 * @uses do_action() Calls 'bbp_reply_attributes_metabox_save' with the
	 *                    reply id and parent id
	 * @return int Parent id
	 */
	function reply_attributes_metabox_save( $reply_id ) {

		// Bail if doing an autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $reply_id;

		// Bail if not a post request
		if ( 'POST' != strtoupper( $_SERVER['REQUEST_METHOD'] ) )
			return $reply_id;

		// Check action exists
		if ( empty( $_POST['action'] ) )
			return $reply_id;

		// Bail if post_type is not a reply
		if ( get_post_type( $reply_id ) != $this->post_type )
			return;

		// Current user cannot edit this reply
		if ( !current_user_can( 'edit_reply', $reply_id ) )
			return $reply_id;

		// Get the reply meta post values
		$topic_id = !empty( $_POST['parent_id']    ) ? (int) $_POST['parent_id']    : 0;
		$forum_id = !empty( $_POST['bbp_forum_id'] ) ? (int) $_POST['bbp_forum_id'] : bbp_get_topic_forum_id( $topic_id );

		// Formally update the reply
		bbp_update_reply( $reply_id, $topic_id, $forum_id );

		// Allow other fun things to happen
		do_action( 'bbp_reply_attributes_metabox_save', $reply_id, $topic_id, $forum_id );

		return $reply_id;
	}

	/**
	 * Add the author info metabox
	 *
	 * Allows editing of information about an author
	 *
	 * @since bbPress (r2828)
	 *
	 * @uses bbp_get_topic() To get the topic
	 * @uses bbp_get_reply() To get the reply
	 * @uses bbp_get_topic_post_type() To get the topic post type
	 * @uses bbp_get_reply_post_type() To get the reply post type
	 * @uses add_meta_box() To add the metabox
	 * @uses do_action() Calls 'bbp_author_metabox' with the topic/reply
	 *                    id
	 */
	function author_metabox() {
		global $current_screen;

		// Bail if post_type is not a reply
		if ( ( empty( $_GET['action'] ) || ( 'edit' != $_GET['action'] ) ) || ( get_post_type() != $this->post_type ) )
			return;

		// Add the metabox
		add_meta_box(
			'bbp_author_metabox',
			__( 'Author Information', 'bbpress' ),
			'bbp_author_metabox',
			$this->post_type,
			'side',
			'high'
		);

		do_action( 'bbp_author_metabox', get_the_ID() );
	}

	/**
	 * Save the author information for the topic/reply
	 *
	 * @since bbPress (r2828)
	 *
	 * @param int $post_id Topic or reply id
	 * @uses bbp_get_topic() To get the topic
	 * @uses bbp_get_reply() To get the reply
	 * @uses current_user_can() To check if the current user can edit the
	 *                           topic or reply
	 * @uses bbp_filter_anonymous_post_data() To filter the anonymous user data
	 * @uses update_post_meta() To update the anonymous user data
	 * @uses do_action() Calls 'bbp_author_metabox_save' with the reply id and
	 *                    anonymous data
	 * @return int Topic or reply id
	 */
	function author_metabox_save( $post_id ) {

		// Bail if no post_id
		if ( empty( $post_id ) )
			return $post_id;

		// Bail if not a post request
		if ( 'POST' != strtoupper( $_SERVER['REQUEST_METHOD'] ) )
			return $post_id;

		// Bail if doing an autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		// Bail if post_type is not a topic or reply
		if ( get_post_type( $post_id ) != $this->post_type )
			return;

		// Bail if user cannot edit replies or reply is not anonymous
		if ( !current_user_can( 'edit_reply', $post_id ) )
			return $post_id;

		$anonymous_data = bbp_filter_anonymous_post_data();

		update_post_meta( $post_id, '_bbp_anonymous_name',    $anonymous_data['bbp_anonymous_name']    );
		update_post_meta( $post_id, '_bbp_anonymous_email',   $anonymous_data['bbp_anonymous_email']   );
		update_post_meta( $post_id, '_bbp_anonymous_website', $anonymous_data['bbp_anonymous_website'] );

		do_action( 'bbp_author_metabox_save', $post_id, $anonymous_data );

		return $post_id;
	}

	/**
	 * Add some general styling to the admin area
	 *
	 * @since bbPress (r2464)
	 *
	 * @uses bbp_get_forum_post_type() To get the forum post type
	 * @uses bbp_get_topic_post_type() To get the topic post type
	 * @uses bbp_get_reply_post_type() To get the reply post type
	 * @uses sanitize_html_class() To sanitize the classes
	 * @uses do_action() Calls 'bbp_admin_head'
	 */
	function admin_head() {

		if ( get_post_type() == $this->post_type ) : ?>

			<style type="text/css" media="screen">
			/*<![CDATA[*/

				.column-bbp_forum_topic_count,
				.column-bbp_forum_reply_count,
				.column-bbp_topic_reply_count,
				.column-bbp_topic_voice_count {
					width: 8% !important;
				}

				.column-author,
				.column-bbp_reply_author,
				.column-bbp_topic_author {
					width: 10% !important;
				}

				.column-bbp_topic_forum,
				.column-bbp_reply_forum,
				.column-bbp_reply_topic {
					width: 10% !important;
				}

				.column-bbp_forum_freshness,
				.column-bbp_topic_freshness {
					width: 10% !important;
				}

				.column-bbp_forum_created,
				.column-bbp_topic_created,
				.column-bbp_reply_created {
					width: 15% !important;
				}

				.status-closed {
					background-color: #eaeaea;
				}

				.status-spam {
					background-color: #faeaea;
				}

			/*]]>*/
			</style>

		<?php endif;

	}

	/**
	 * Toggle reply
	 *
	 * Handles the admin-side spamming/unspamming of replies
	 *
	 * @since bbPress (r2740)
	 *
	 * @uses bbp_get_reply() To get the reply
	 * @uses current_user_can() To check if the user is capable of editing
	 *                           the reply
	 * @uses wp_die() To die if the user isn't capable or the post wasn't
	 *                 found
	 * @uses check_admin_referer() To verify the nonce and check referer
	 * @uses bbp_is_reply_spam() To check if the reply is marked as spam
	 * @uses bbp_unspam_reply() To unmark the reply as spam
	 * @uses bbp_spam_reply() To mark the reply as spam
	 * @uses do_action() Calls 'bbp_toggle_reply_admin' with success, post
	 *                    data, action and message
	 * @uses add_query_arg() To add custom args to the url
	 * @uses wp_redirect() Redirect the page to custom url
	 */
	function toggle_reply() {

		// Only proceed if GET is a reply toggle action
		if ( 'GET' == $_SERVER['REQUEST_METHOD'] && !empty( $_GET['action'] ) && in_array( $_GET['action'], array( 'bbp_toggle_reply_spam' ) ) && !empty( $_GET['reply_id'] ) ) {
			$action    = $_GET['action'];            // What action is taking place?
			$reply_id  = (int) $_GET['reply_id'];    // What's the reply id?
			$success   = false;                      // Flag
			$post_data = array( 'ID' => $reply_id ); // Prelim array

			if ( !$reply = bbp_get_reply( $reply_id ) ) // Which reply?
				wp_die( __( 'The reply was not found!', 'bbpress' ) );

			if ( !current_user_can( 'moderate', $reply->ID ) ) // What is the user doing here?
				wp_die( __( 'You do not have the permission to do that!', 'bbpress' ) );

			switch ( $action ) {
				case 'bbp_toggle_reply_spam' :
					check_admin_referer( 'spam-reply_' . $reply_id );

					$is_spam = bbp_is_reply_spam( $reply_id );
					$message = $is_spam ? 'unspammed' : 'spammed';
					$success = $is_spam ? bbp_unspam_reply( $reply_id ) : bbp_spam_reply( $reply_id );

					break;
			}

			$success = wp_update_post( $post_data );
			$message = array( 'bbp_reply_toggle_notice' => $message, 'reply_id' => $reply->ID );

			if ( false == $success || is_wp_error( $success ) )
				$message['failed'] = '1';

			// Do additional reply toggle actions (admin side)
			do_action( 'bbp_toggle_reply_admin', $success, $post_data, $action, $message );

			// Redirect back to the reply
			$redirect = add_query_arg( $message, remove_query_arg( array( 'action', 'reply_id' ) ) );
			wp_redirect( $redirect );

			// For good measure
			exit();
		}
	}

	/**
	 * Toggle reply notices
	 *
	 * Display the success/error notices from
	 * {@link BBP_Admin::toggle_reply()}
	 *
	 * @since bbPress (r2740)
	 *
	 * @uses bbp_get_reply() To get the reply
	 * @uses bbp_get_reply_title() To get the reply title of the reply
	 * @uses esc_html() To sanitize the reply title
	 * @uses apply_filters() Calls 'bbp_toggle_reply_notice_admin' with
	 *                        message, reply id, notice and is it a failure
	 */
	function toggle_reply_notice() {

		// Only proceed if GET is a reply toggle action
		if ( 'GET' == $_SERVER['REQUEST_METHOD'] && !empty( $_GET['bbp_reply_toggle_notice'] ) && in_array( $_GET['bbp_reply_toggle_notice'], array( 'spammed', 'unspammed' ) ) && !empty( $_GET['reply_id'] ) ) {
			$notice     = $_GET['bbp_reply_toggle_notice'];         // Which notice?
			$reply_id   = (int) $_GET['reply_id'];                  // What's the reply id?
			$is_failure = !empty( $_GET['failed'] ) ? true : false; // Was that a failure?

			// Empty? No reply?
			if ( empty( $notice ) || empty( $reply_id ) || !$reply = bbp_get_reply( $reply_id ) )
				return;

			$reply_title = esc_html( bbp_get_reply_title( $reply->ID ) );

			switch ( $notice ) {
				case 'spammed' :
					$message = $is_failure == true ? sprintf( __( 'There was a problem marking the reply "%1$s" as spam.', 'bbpress' ), $reply_title ) : sprintf( __( 'Reply "%1$s" successfully marked as spam.', 'bbpress' ), $reply_title );
					break;

				case 'unspammed' :
					$message = $is_failure == true ? sprintf( __( 'There was a problem unmarking the reply "%1$s" as spam.', 'bbpress' ), $reply_title ) : sprintf( __( 'Reply "%1$s" successfully unmarked as spam.', 'bbpress' ), $reply_title );
					break;
			}

			// Do additional reply toggle notice filters (admin side)
			$message = apply_filters( 'bbp_toggle_reply_notice_admin', $message, $reply->ID, $notice, $is_failure );

			?>

			<div id="message" class="<?php echo $is_failure == true ? 'error' : 'updated'; ?> fade">
				<p style="line-height: 150%"><?php echo $message; ?></p>
			</div>

			<?php
		}
	}

	/**
	 * Manage the column headers for the replies page
	 *
	 * @since bbPress (r2577)
	 *
	 * @param array $columns The columns
	 * @uses apply_filters() Calls 'bbp_admin_replies_column_headers' with
	 *                        the columns
	 * @return array $columns bbPress reply columns
	 */
	function replies_column_headers( $columns ) {
		$columns = array(
			'cb'                => '<input type="checkbox" />',
			'title'             => __( 'Title',   'bbpress' ),
			'bbp_reply_forum'   => __( 'Forum',   'bbpress' ),
			'bbp_reply_topic'   => __( 'Topic',   'bbpress' ),
			'bbp_reply_author'  => __( 'Author',  'bbpress' ),
			'bbp_reply_created' => __( 'Created', 'bbpress' ),
		);

		return apply_filters( 'bbp_admin_replies_column_headers', $columns );
	}

	/**
	 * Print extra columns for the replies page
	 *
	 * @since bbPress (r2577)
	 *
	 * @param string $column Column
	 * @param int $reply_id reply id
	 * @uses bbp_get_reply_topic_id() To get the topic id of the reply
	 * @uses bbp_topic_title() To output the reply's topic title
	 * @uses apply_filters() Calls 'reply_topic_row_actions' with an array
	 *                        of reply topic actions
	 * @uses bbp_get_topic_permalink() To get the topic permalink
	 * @uses bbp_get_topic_forum_id() To get the forum id of the topic of
	 *                                 the reply
	 * @uses bbp_get_forum_permalink() To get the forum permalink
	 * @uses admin_url() To get the admin url of post.php
	 * @uses add_query_arg() To add custom args to the url
	 * @uses apply_filters() Calls 'reply_topic_forum_row_actions' with an
	 *                        array of reply topic forum actions
	 * @uses bbp_reply_author_display_name() To output the reply author name
	 * @uses get_the_date() Get the reply creation date
	 * @uses get_the_time() Get the reply creation time
	 * @uses esc_attr() To sanitize the reply creation time
	 * @uses bbp_get_reply_last_active_time() To get the time when the reply was
	 *                                    last active
	 * @uses do_action() Calls 'bbp_admin_replies_column_data' with the
	 *                    column and reply id
	 */
	function replies_column_data( $column, $reply_id ) {

		// Get topic ID
		$topic_id = bbp_get_reply_topic_id( $reply_id );

		// Populate Column Data
		switch ( $column ) {

			// Topic
			case 'bbp_reply_topic' :

				// Output forum name
				if ( !empty( $topic_id ) ) {

					// Topic Title
					if ( !$topic_title = bbp_get_topic_title( $topic_id ) )
						$topic_title = __( 'No Topic', 'bbpress' );

					// Output the title
					echo $topic_title;

				// Reply has no topic
				} else {
					_e( 'No Topic', 'bbpress' );
				}

				break;

			// Forum
			case 'bbp_reply_forum' :

				// Get Forum ID's
				$reply_forum_id = bbp_get_reply_forum_id( $reply_id );
				$topic_forum_id = bbp_get_topic_forum_id( $topic_id );

				// Output forum name
				if ( !empty( $reply_forum_id ) ) {

					// Forum Title
					if ( !$forum_title = bbp_get_forum_title( $reply_forum_id ) )
						$forum_title = __( 'No Forum', 'bbpress' );

					// Alert capable users of reply forum mismatch
					if ( $reply_forum_id != $topic_forum_id ) {
						if ( current_user_can( 'edit_others_replies' ) || current_user_can( 'moderate' ) ) {
							$forum_title .= '<div class="attention">' . __( '(Mismatch)', 'bbpress' ) . '</div>';
						}
					}

					// Output the title
					echo $forum_title;

				// Reply has no forum
				} else {
					_e( 'No Forum', 'bbpress' );
				}

				break;

			// Author
			case 'bbp_reply_author' :
				bbp_reply_author_display_name ( $reply_id );
				break;

			// Freshness
			case 'bbp_reply_created':

				// Output last activity time and date
				printf( __( '%1$s <br /> %2$s', 'bbpress' ),
					get_the_date(),
					esc_attr( get_the_time() )
				);

				break;

			// Do action for anything else
			default :
				do_action( 'bbp_admin_replies_column_data', $column, $reply_id );
				break;
		}
	}

	/**
	 * Reply Row actions
	 *
	 * Remove the quick-edit action link under the reply title and add the
	 * content and spam link
	 *
	 * @since bbPress (r2577)
	 *
	 * @param array $actions Actions
	 * @param array $reply Reply object
	 * @uses bbp_get_reply_post_type() To get the reply post type
	 * @uses bbp_reply_content() To output reply content
	 * @uses bbp_get_reply_permalink() To get the reply link
	 * @uses bbp_get_reply_title() To get the reply title
	 * @uses current_user_can() To check if the current user can edit or
	 *                           delete the reply
	 * @uses bbp_is_reply_spam() To check if the reply is marked as spam
	 * @uses get_post_type_object() To get the reply post type object
	 * @uses add_query_arg() To add custom args to the url
	 * @uses remove_query_arg() To remove custom args from the url
	 * @uses wp_nonce_url() To nonce the url
	 * @uses get_delete_post_link() To get the delete post link of the reply
	 * @return array $actions Actions
	 */
	function replies_row_actions( $actions, $reply ) {

		if ( bbp_get_reply_post_type() == $reply->post_type ) {
			unset( $actions['inline hide-if-no-js'] );

			// Reply view links to topic
			$actions['view'] = '<a href="' . bbp_get_reply_url( $reply->ID ) . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;', 'bbpress' ), bbp_get_reply_title( $reply->ID ) ) ) . '" rel="permalink">' . __( 'View', 'bbpress' ) . '</a>';

			// User cannot view replies in trash
			if ( ( bbp_get_trash_status_id() == $reply->post_status ) && !current_user_can( 'view_trash' ) )
				unset( $actions['view'] );

			// Only show the actions if the user is capable of viewing them
			if ( current_user_can( 'moderate', $reply->ID ) ) {
				if ( in_array( $reply->post_status, array( bbp_get_public_status_id(), bbp_get_spam_status_id() ) ) ) {
					$spam_uri  = esc_url( wp_nonce_url( add_query_arg( array( 'reply_id' => $reply->ID, 'action' => 'bbp_toggle_reply_spam' ), remove_query_arg( array( 'bbp_reply_toggle_notice', 'reply_id', 'failed', 'super' ) ) ), 'spam-reply_'  . $reply->ID ) );
					if ( bbp_is_reply_spam( $reply->ID ) ) {
						$actions['spam'] = '<a href="' . $spam_uri . '" title="' . esc_attr__( 'Mark the reply as not spam', 'bbpress' ) . '">' . __( 'Not spam', 'bbpress' ) . '</a>';
					} else {
						$actions['spam'] = '<a href="' . $spam_uri . '" title="' . esc_attr__( 'Mark this reply as spam',    'bbpress' ) . '">' . __( 'Spam',     'bbpress' ) . '</a>';
					}
				}
			}

			// Trash
			if ( current_user_can( 'delete_reply', $reply->ID ) ) {
				if ( bbp_get_trash_status_id() == $reply->post_status ) {
					$post_type_object = get_post_type_object( bbp_get_reply_post_type() );
					$actions['untrash'] = "<a title='" . esc_attr( __( 'Restore this item from the Trash', 'bbpress' ) ) . "' href='" . add_query_arg( array( '_wp_http_referer' => add_query_arg( array( 'post_type' => bbp_get_reply_post_type() ), admin_url( 'edit.php' ) ) ), wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $reply->ID ) ), 'untrash-' . $reply->post_type . '_' . $reply->ID ) ) . "'>" . __( 'Restore', 'bbpress' ) . "</a>";
				} elseif ( EMPTY_TRASH_DAYS ) {
					$actions['trash'] = "<a class='submitdelete' title='" . esc_attr( __( 'Move this item to the Trash', 'bbpress' ) ) . "' href='" . add_query_arg( array( '_wp_http_referer' => add_query_arg( array( 'post_type' => bbp_get_reply_post_type() ), admin_url( 'edit.php' ) ) ), get_delete_post_link( $reply->ID ) ) . "'>" . __( 'Trash', 'bbpress' ) . "</a>";
				}

				if ( bbp_get_trash_status_id() == $reply->post_status || !EMPTY_TRASH_DAYS ) {
					$actions['delete'] = "<a class='submitdelete' title='" . esc_attr( __( 'Delete this item permanently', 'bbpress' ) ) . "' href='" . add_query_arg( array( '_wp_http_referer' => add_query_arg( array( 'post_type' => bbp_get_reply_post_type() ), admin_url( 'edit.php' ) ) ), get_delete_post_link( $reply->ID, '', true ) ) . "'>" . __( 'Delete Permanently', 'bbpress' ) . "</a>";
				} elseif ( bbp_get_spam_status_id() == $reply->post_status ) {
					unset( $actions['trash'] );
				}
			}
		}

		return $actions;
	}

	/**
	 * Add forum dropdown to topic and reply list table filters
	 *
	 * @since bbPress (r2991)
	 *
	 * @uses bbp_get_reply_post_type() To get the reply post type
	 * @uses bbp_get_topic_post_type() To get the topic post type
	 * @uses bbp_dropdown() To generate a forum dropdown
	 * @return bool False. If post type is not topic or reply
	 */
	function filter_dropdown() {

		// Bail if not viewing the topics list
		if (
				// post_type exists in _GET
				empty( $_GET['post_type'] ) ||

				// post_type is reply or topic type
				( $_GET['post_type'] != $this->post_type )
			)
			return;

		// Add Empty Spam button
		if ( !empty( $_GET['post_status'] ) && ( bbp_get_spam_status_id() == $_GET['post_status'] ) && current_user_can( 'moderate' ) ) {
			wp_nonce_field( 'bulk-destroy', '_destroy_nonce' );
			$title = esc_attr__( 'Empty Spam', 'bbpress' );
			submit_button( $title, 'button-secondary apply', 'delete_all', false );
		}

		// Get which forum is selected
		$selected = !empty( $_GET['bbp_forum_id'] ) ? $_GET['bbp_forum_id'] : '';

		// Show the forums dropdown
		bbp_dropdown( array(
			'selected'  => $selected,
			'show_none' => __( 'In all forums', 'bbpress' )
		) );
	}

	/**
	 * Adjust the request query and include the forum id
	 *
	 * @since bbPress (r2991)
	 *
	 * @param array $query_vars Query variables from {@link WP_Query}
	 * @uses is_admin() To check if it's the admin section
	 * @uses bbp_get_topic_post_type() To get the topic post type
	 * @uses bbp_get_reply_post_type() To get the reply post type
	 * @return array Processed Query Vars
	 */
	function filter_post_rows( $query_vars ) {
		global $pagenow;

		// Avoid poisoning other requests
		if (
				// Only look in admin
				!is_admin()                 ||

				// Make sure the current page is for post rows
				( 'edit.php' != $pagenow  ) ||

				// Make sure we're looking for a post_type
				empty( $_GET['post_type'] ) ||

				// Make sure we're looking at bbPress topics
				( $_GET['post_type'] != $this->post_type )
			)

			// We're in no shape to filter anything, so return
			return $query_vars;

		// Add post_parent query_var if one is present
		if ( !empty( $_GET['bbp_forum_id'] ) ) {
			$query_vars['meta_key']   = '_bbp_forum_id';
			$query_vars['meta_value'] = $_GET['bbp_forum_id'];
		}

		// Return manipulated query_vars
		return $query_vars;
	}

	/**
	 * Custom user feedback messages for reply post type
	 *
	 * @since bbPress (r3080)
	 *
	 * @global WP_Query $post
	 * @global int $post_ID
	 * @uses get_post_type()
	 * @uses bbp_get_topic_permalink()
	 * @uses wp_post_revision_title()
	 * @uses esc_url()
	 * @uses add_query_arg()
	 *
	 * @param array $messages
	 *
	 * @return array
	 */
	function updated_messages( $messages ) {
		global $post, $post_ID;

		if ( get_post_type( $post_ID ) != $this->post_type )
			return $messages;

		// URL for the current topic
		$topic_url = bbp_get_topic_permalink( bbp_get_reply_topic_id( $post_ID ) );

		// Messages array
		$messages[$this->post_type] = array(
			0 =>  '', // Left empty on purpose

			// Updated
			1 =>  sprintf( __( 'Reply updated. <a href="%s">View topic</a>' ), $topic_url ),

			// Custom field updated
			2 => __( 'Custom field updated.', 'bbpress' ),

			// Custom field deleted
			3 => __( 'Custom field deleted.', 'bbpress' ),

			// Reply updated
			4 => __( 'Reply updated.', 'bbpress' ),

			// Restored from revision
			// translators: %s: date and time of the revision
			5 => isset( $_GET['revision'] )
					? sprintf( __( 'Reply restored to revision from %s', 'bbpress' ), wp_post_revision_title( (int) $_GET['revision'], false ) )
					: false,

			// Reply created
			6 => sprintf( __( 'Reply created. <a href="%s">View topic</a>', 'bbpress' ), $topic_url ),

			// Reply saved
			7 => __( 'Reply saved.', 'bbpress' ),

			// Reply submitted
			8 => sprintf( __( 'Reply submitted. <a target="_blank" href="%s">Preview topic</a>', 'bbpress' ), esc_url( add_query_arg( 'preview', 'true', $topic_url ) ) ),

			// Reply scheduled
			9 => sprintf( __( 'Reply scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview topic</a>', 'bbpress' ),
					// translators: Publish box date format, see http://php.net/date
					date_i18n( __( 'M j, Y @ G:i' ),
					strtotime( $post->post_date ) ),
					$topic_url ),

			// Reply draft updated
			10 => sprintf( __( 'Reply draft updated. <a target="_blank" href="%s">Preview topic</a>', 'bbpress' ), esc_url( add_query_arg( 'preview', 'true', $topic_url ) ) ),
		);

		return $messages;
	}
}
endif; // class_exists check

/**
 * Setup bbPress Replies Admin
 *
 * @since bbPress (r2596)
 *
 * @uses BBP_Replies_Admin
 */
function bbp_admin_replies() {
	global $bbp;

	// Bail if bbPress is not loaded
	if ( 'bbPress' !== get_class( $bbp ) ) return;

	$bbp->admin->replies = new BBP_Replies_Admin();
}

?>
