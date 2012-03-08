<?php

/**
 * bbPress Topic Template Tags
 *
 * @package bbPress
 * @subpackage TemplateTags
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/** Post Type *****************************************************************/

/**
 * Output the unique id of the custom post type for topics
 *
 * @since bbPress (r2857)
 *
 * @uses bbp_get_topic_post_type() To get the topic post type
 */
function bbp_topic_post_type() {
	echo bbp_get_topic_post_type();
}
	/**
	 * Return the unique id of the custom post type for topics
	 *
	 * @since bbPress (r2857)
	 *
	 * @uses apply_filters() Calls 'bbp_get_topic_post_type' with the topic
	 *                        post type id
	 * @return string The unique topic post type id
	 */
	function bbp_get_topic_post_type() {
		global $bbp;

		return apply_filters( 'bbp_get_topic_post_type', $bbp->topic_post_type );
	}

/** Topic Loop ****************************************************************/

/**
 * The main topic loop. WordPress makes this easy for us
 *
 * @since bbPress (r2485)
 *
 * @param mixed $args All the arguments supported by {@link WP_Query}
 * @uses current_user_can() To check if the current user can edit other's topics
 * @uses bbp_get_topic_post_type() To get the topic post type
 * @uses WP_Query To make query and get the topics
 * @uses is_page() To check if it's a page
 * @uses bbp_is_single_forum() To check if it's a forum
 * @uses bbp_get_forum_id() To get the forum id
 * @uses bbp_get_paged() To get the current page value
 * @uses bbp_get_super_stickies() To get the super stickies
 * @uses bbp_get_stickies() To get the forum stickies
 * @uses wpdb::get_results() To execute our query and get the results
 * @uses WP_Rewrite::using_permalinks() To check if the blog is using permalinks
 * @uses get_permalink() To get the permalink
 * @uses add_query_arg() To add custom args to the url
 * @uses apply_filters() Calls 'bbp_topics_pagination' with the pagination args
 * @uses paginate_links() To paginate the links
 * @uses apply_filters() Calls 'bbp_has_topics' with
 *                        bbPres::topic_query::have_posts()
 *                        and bbPres::topic_query
 * @return object Multidimensional array of topic information
 */
function bbp_has_topics( $args = '' ) {
	global $wp_rewrite, $wp_query, $bbp, $wpdb;

	// What are the default allowed statuses (based on user caps)
	if ( !bbp_is_query_name( 'bbp_widget' ) && bbp_get_view_all() )
		$default_status = join( ',', array( bbp_get_public_status_id(), bbp_get_closed_status_id(), bbp_get_spam_status_id(), bbp_get_trash_status_id() ) );
	else
		$default_status = join( ',', array( bbp_get_public_status_id(), bbp_get_closed_status_id() ) );

	// Default arguments
	$default = array (

		// Narrow query down to bbPress topics
		'post_type'      => bbp_get_topic_post_type(),

		// Forum ID
		'post_parent'    => bbp_is_single_forum() ? bbp_get_forum_id() : 'any',

		// Make sure topic has some last activity time
		'meta_key'       => '_bbp_last_active_time',

		// 'meta_value', 'author', 'date', 'title', 'modified', 'parent', rand',
		'orderby'        => 'meta_value',

		// 'ASC', 'DESC'
		'order'          => 'DESC',

		// Topics per page
		'posts_per_page' => bbp_get_topics_per_page(),

		// Page Number
		'paged'          => bbp_get_paged(),

		// Topic Search
		's'              => !empty( $_REQUEST['ts'] ) ? $_REQUEST['ts'] : '',

		// Ignore sticky topics?
		'show_stickies'  => bbp_is_single_forum(),

		// Maximum number of pages to show
		'max_num_pages'  => false,

		// Post Status
		'post_status'    => $default_status,
	);

	// Set up topic variables
	$bbp_t = wp_parse_args( $args, $default );

	// Filter the topics query to allow just-in-time modifications
	$bbp_t = apply_filters( 'bbp_has_topics_query', $bbp_t );

	// Extract the query variables
	extract( $bbp_t );

	// Call the query
	$bbp->topic_query = new WP_Query( $bbp_t );

	// Set post_parent back to 0 if originally set to 'any'
	if ( 'any' == $bbp_t['post_parent'] )
		$bbp_t['post_parent'] = $post_parent = 0;

	// Limited the number of pages shown
	if ( !empty( $max_num_pages ) )
		$bbp->topic_query->max_num_pages = $max_num_pages;

	// Put sticky posts at the top of the posts array
	if ( !empty( $show_stickies ) && $paged <= 1 ) {

		// Get super stickies and stickies in this forum
		$stickies = bbp_get_super_stickies();
		$stickies = !empty( $post_parent ) ? array_merge( $stickies, bbp_get_stickies( $post_parent ) ) : $stickies;
		$stickies = array_unique( $stickies );

		// We have stickies
		if ( is_array( $stickies ) && !empty( $stickies ) ) {

			// Setup the number of stickies and reset offset to 0
			$num_topics    = count( $bbp->topic_query->posts );
			$sticky_offset = 0;

			// Loop over topics and relocate stickies to the front.
			for ( $i = 0; $i < $num_topics; $i++ ) {
				if ( in_array( $bbp->topic_query->posts[$i]->ID, $stickies ) ) {
					$sticky = $bbp->topic_query->posts[$i];

					// Remove sticky from current position
					array_splice( $bbp->topic_query->posts, $i, 1 );

					// Move to front, after other stickies
					array_splice( $bbp->topic_query->posts, $sticky_offset, 0, array( $sticky ) );

					// Increment the sticky offset.  The next sticky will be placed at this offset.
					$sticky_offset++;

					// Remove post from sticky posts array
					$offset = array_search( $sticky->ID, $stickies );

					// Cleanup
					unset( $stickies[$offset] );
					unset( $sticky            );
				}
			}

			// If any posts have been excluded specifically, Ignore those that are sticky.
			if ( !empty( $stickies ) && !empty( $post__not_in ) )
				$stickies = array_diff( $stickies, $post__not_in );

			// Fetch sticky posts that weren't in the query results
			if ( !empty( $stickies ) ) {

				// Query to use in get_posts to get sticky posts
				$sticky_query = array(
					'post_type'   => bbp_get_topic_post_type(),
					'post_parent' => 'any',
					'include'     => $stickies
				);

				// Get all stickies
				if ( $sticky_posts = get_posts( $sticky_query ) ) {

					// Get a count of the visible stickies
					$sticky_count = count( $sticky_posts );

					// Loop through stickies and add them to beginning of array
					foreach ( $sticky_posts as $sticky )
						$topics[] = $sticky;

					// Loop through topics and add them to end of array
					foreach ( $bbp->topic_query->posts as $topic )
						$topics[] = $topic;

					// Adjust loop and counts for new sticky positions
					$bbp->topic_query->posts       = $topics;
					$bbp->topic_query->found_posts = (int) $bbp->topic_query->found_posts + (int) $sticky_count;
					$bbp->topic_query->post_count  = (int) $bbp->topic_query->post_count  + (int) $sticky_count;

					// Cleanup
					unset( $topics       );
					unset( $stickies     );
					unset( $sticky_posts );
				}
			}
		}
	}

	// If no limit to posts per page, set it to the current post_count
	if ( -1 == $posts_per_page )
		$posts_per_page = $bbp->topic_query->post_count;

	// Add pagination values to query object
	$bbp->topic_query->posts_per_page = $posts_per_page;
	$bbp->topic_query->paged          = $paged;

	// Only add pagination if query returned results
	if ( !bbp_is_query_name( 'bbp_widget' ) && ( (int) $bbp->topic_query->post_count || (int) $bbp->topic_query->found_posts ) && (int) $bbp->topic_query->posts_per_page ) {

		// Limit the number of topics shown based on maximum allowed pages
		if ( ( !empty( $max_num_pages ) ) && $bbp->topic_query->found_posts > $bbp->topic_query->max_num_pages * $bbp->topic_query->post_count )
			$bbp->topic_query->found_posts = $bbp->topic_query->max_num_pages * $bbp->topic_query->post_count;

		// If pretty permalinks are enabled, make our pagination pretty
		if ( $wp_rewrite->using_permalinks() ) {

			// Profile page
			if ( bbp_is_single_user() )
				$base = bbp_get_user_profile_url( bbp_get_displayed_user_id() );

			// View
			elseif ( bbp_is_single_view() )
				$base = bbp_get_view_url();

			// Topic tag
			elseif ( bbp_is_topic_tag() )
				$base = bbp_get_topic_tag_link();

			// Page or single post
			elseif ( is_page() || is_single() )
				$base = get_permalink();

			// Topic archive
			elseif ( bbp_is_topic_archive() )
				$base = home_url( $bbp->topic_archive_slug );

			// Default
			else
				$base = get_permalink( $post_parent );

			// Use pagination base
			$base = trailingslashit( $base ) . user_trailingslashit( $wp_rewrite->pagination_base . '/%#%/' );

		// Unpretty pagination
		} else {
			$base = add_query_arg( 'paged', '%#%' );
		}

		// Pagination settings with filter
		$bbp_topic_pagination = apply_filters( 'bbp_topic_pagination', array (
			'base'      => $base,
			'format'    => '',
			'total'     => $posts_per_page == $bbp->topic_query->found_posts ? 1 : ceil( (int) $bbp->topic_query->found_posts / (int) $posts_per_page ),
			'current'   => (int) $bbp->topic_query->paged,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size'  => 1
		) );

		// Add pagination to query object
		$bbp->topic_query->pagination_links = paginate_links ( $bbp_topic_pagination );

		// Remove first page from pagination
		$bbp->topic_query->pagination_links = str_replace( $wp_rewrite->pagination_base . "/1/'", "'", $bbp->topic_query->pagination_links );
	}

	// Return object
	return apply_filters( 'bbp_has_topics', $bbp->topic_query->have_posts(), $bbp->topic_query );
}

/**
 * Whether there are more topics available in the loop
 *
 * @since bbPress (r2485)
 *
 * @uses WP_Query bbPress::topic_query::have_posts()
 * @return object Topic information
 */
function bbp_topics() {
	global $bbp;

	// Put into variable to check against next
	$have_posts = $bbp->topic_query->have_posts();

	// Reset the post data when finished
	if ( empty( $have_posts ) )
		wp_reset_postdata();

	return $have_posts;
}

/**
 * Loads up the current topic in the loop
 *
 * @since bbPress (r2485)
 *
 * @uses WP_Query bbPress::topic_query::the_post()
 * @return object Topic information
 */
function bbp_the_topic() {
	global $bbp;
	return $bbp->topic_query->the_post();
}

/**
 * Output the topic id
 *
 * @since bbPress (r2485)
 *
 * @uses bbp_get_topic_id() To get the topic id
 */
function bbp_topic_id( $topic_id = 0) {
	echo bbp_get_topic_id( $topic_id );
}
	/**
	 * Return the topic id
	 *
	 * @since bbPress (r2485)
	 *
	 * @param $topic_id Optional. Used to check emptiness
	 * @uses bbPress::topic_query::post::ID To get the topic id
	 * @uses bbp_is_single_topic() To check if it's a topic page
	 * @uses bbp_is_topic_edit() To check if it's a topic edit page
	 * @uses bbp_is_single_reply() To check if it it's a reply page
	 * @uses bbp_is_reply_edit() To check if it's a reply edit page
	 * @uses bbp_get_reply_topic_edit() To get the reply topic id
	 * @uses get_post_field() To get the post's post type
	 * @uses WP_Query::post::ID To get the topic id
	 * @uses bbp_get_topic_post_type() To get the topic post type
	 * @uses apply_filters() Calls 'bbp_get_topic_id' with the topic id and
	 *                        supplied topic id
	 * @return int The topic id
	 */
	function bbp_get_topic_id( $topic_id = 0 ) {
		global $bbp, $wp_query;

		// Easy empty checking
		if ( !empty( $topic_id ) && is_numeric( $topic_id ) )
			$bbp_topic_id = $topic_id;

		// Currently inside a topic loop
		elseif ( !empty( $bbp->topic_query->in_the_loop ) && isset( $bbp->topic_query->post->ID ) )
			$bbp_topic_id = $bbp->topic_query->post->ID;

		// Currently viewing a topic
		elseif ( ( bbp_is_single_topic() || bbp_is_topic_edit() ) && isset( $wp_query->post->ID ) )
			$bbp_topic_id = $bbp->current_topic_id = $wp_query->post->ID;

		// Currently viewing a topic
		elseif ( bbp_is_single_reply() )
			$bbp_topic_id = $bbp->current_topic_id = bbp_get_reply_topic_id();

		// Fallback
		else
			$bbp_topic_id = 0;

		// Check if current_reply_id is set, and check post_type if so
		if ( !empty( $bbp->current_topic_id ) && ( bbp_get_topic_post_type() != get_post_field( 'post_type', $bbp_topic_id ) ) )
			$bbp->current_topic_id = null;

		return apply_filters( 'bbp_get_topic_id', (int) $bbp_topic_id, $topic_id );
	}

/**
 * Gets a topic
 *
 * @since bbPress (r2787)
 *
 * @param int|object $topic Topic id or topic object
 * @param string $output Optional. OBJECT, ARRAY_A, or ARRAY_N. Default = OBJECT
 * @param string $filter Optional Sanitation filter. See {@link sanitize_post()}
 * @uses get_post() To get the topic
 * @uses apply_filters() Calls 'bbp_get_topic' with the topic, output type and
 *                        sanitation filter
 * @return mixed Null if error or topic (in specified form) if success
 */
function bbp_get_topic( $topic, $output = OBJECT, $filter = 'raw' ) {

	if ( empty( $topic ) || is_numeric( $topic ) )
		$topic = bbp_get_topic_id( $topic );

	if ( !$topic = get_post( $topic, OBJECT, $filter ) )
		return $topic;

	if ( $topic->post_type !== bbp_get_topic_post_type() )
		return null;

	if ( $output == OBJECT ) {
		return $topic;

	} elseif ( $output == ARRAY_A ) {
		$_topic = get_object_vars( $topic );
		return $_topic;

	} elseif ( $output == ARRAY_N ) {
		$_topic = array_values( get_object_vars( $topic ) );
		return $_topic;

	}

	return apply_filters( 'bbp_get_topic', $topic, $output, $filter );
}

/**
 * Output the link to the topic in the topic loop
 *
 * @since bbPress (r2485)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_permalink() To get the topic permalink
 */
function bbp_topic_permalink( $topic_id = 0 ) {
	echo bbp_get_topic_permalink( $topic_id );
}
	/**
	 * Return the link to the topic
	 *
	 * @since bbPress (r2485)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @param $string $redirect_to Optional. Pass a redirect value for use with
	 *                              shortcodes and other fun things.
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses get_permalink() To get the topic permalink
	 * @uses esc_url_raw() To clean the redirect_to url
	 * @uses apply_filters() Calls 'bbp_get_topic_permalink' with the link
	 *                        and topic id
	 * @return string Permanent link to topic
	 */
	function bbp_get_topic_permalink( $topic_id = 0, $redirect_to = '' ) {
		$topic_id = bbp_get_topic_id( $topic_id );

		// Use the redirect address
		if ( !empty( $redirect_to ) )
			$topic_permalink = esc_url_raw( $redirect_to );

		// Use the topic permalink
		else
			$topic_permalink = get_permalink( $topic_id );

		return apply_filters( 'bbp_get_topic_permalink', $topic_permalink, $topic_id );
	}

/**
 * Output the title of the topic
 *
 * @since bbPress (r2485)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_title() To get the topic title
 */
function bbp_topic_title( $topic_id = 0 ) {
	echo bbp_get_topic_title( $topic_id );
}
	/**
	 * Return the title of the topic
	 *
	 * @since bbPress (r2485)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses get_the_title() To get the title
	 * @uses apply_filters() Calls 'bbp_get_topic_title' with the title and
	 *                        topic id
	 * @return string Title of topic
	 */
	function bbp_get_topic_title( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );

		return apply_filters( 'bbp_get_topic_title', get_the_title( $topic_id ), $topic_id );
	}

/**
 * Output the topic archive title
 *
 * @since bbPress (r3249)
 *
 * @param string $title Default text to use as title
 */
function bbp_topic_archive_title( $title = '' ) {
	echo bbp_get_topic_archive_title( $title );
}
	/**
	 * Return the topic archive title
	 *
	 * @since bbPress (r3249)
	 *
	 * @global bbPress $bbp The main bbPress class
	 * @param string $title Default text to use as title
	 *
	 * @uses bbp_get_page_by_path() Check if page exists at root path
	 * @uses get_the_title() Use the page title at the root path
	 * @uses get_post_type_object() Load the post type object
	 * @uses bbp_get_topic_post_type() Get the topic post type ID
	 * @uses get_post_type_labels() Get labels for topic post type
	 * @uses apply_filters() Allow output to be manipulated
	 *
	 * @return string The topic archive title
	 */
	function bbp_get_topic_archive_title( $title = '' ) {
		global $bbp;

		// If no title was passed
		if ( empty( $title ) ) {

			// Set root text to page title
			if ( $page = bbp_get_page_by_path( $bbp->topic_archive_slug ) ) {
				$title = get_the_title( $page->ID );

			// Default to topic post type name label
			} else {
				$tto    = get_post_type_object( bbp_get_topic_post_type() );
				$title  = $tto->labels->name;
			}
		}

		return apply_filters( 'bbp_get_topic_archive_title', $title );
	}

/**
 * Output the content of the topic
 *
 * @since bbPress (r2780)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_content() To get the topic content
 */
function bbp_topic_content( $topic_id = 0 ) {
	echo bbp_get_topic_content( $topic_id );
}
	/**
	 * Return the content of the topic
	 *
	 * @since bbPress (r2780)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses post_password_required() To check if the topic requires pass
	 * @uses get_the_password_form() To get the password form
	 * @uses get_post_field() To get the content post field
	 * @uses apply_filters() Calls 'bbp_get_topic_content' with the content
	 *                        and topic id
	 * @return string Content of the topic
	 */
	function bbp_get_topic_content( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );

		// Check if password is required
		if ( post_password_required( $topic_id ) )
			return get_the_password_form();

		$content = get_post_field( 'post_content', $topic_id );

		return apply_filters( 'bbp_get_topic_content', $content, $topic_id );
	}

/**
 * Output the excerpt of the topic
 *
 * @since bbPress (r2780)
 *
 * @param int $topic_id Optional. Topic id
 * @param int $length Optional. Length of the excerpt. Defaults to 100 letters
 * @uses bbp_get_topic_excerpt() To get the topic excerpt
 */
function bbp_topic_excerpt( $topic_id = 0, $length = 100 ) {
	echo bbp_get_topic_excerpt( $topic_id, $length );
}
	/**
	 * Return the excerpt of the topic
	 *
	 * @since bbPress (r2780)
	 *
	 * @param int $topic_id Optional. topic id
	 * @param int $length Optional. Length of the excerpt. Defaults to 100
	 *                     letters
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses get_post_field() To get the excerpt
	 * @uses bbp_get_topic_content() To get the topic content
	 * @uses apply_filters() Calls 'bbp_get_topic_excerpt' with the excerpt,
	 *                        topic id and length
	 * @return string topic Excerpt
	 */
	function bbp_get_topic_excerpt( $topic_id = 0, $length = 100 ) {
		$topic_id = bbp_get_topic_id( $topic_id );
		$length   = (int) $length;
		$excerpt  = get_post_field( $topic_id, 'post_excerpt' );

		if ( empty( $excerpt ) )
			$excerpt = bbp_get_topic_content( $topic_id );

		$excerpt = trim( strip_tags( $excerpt ) );

		if ( !empty( $length ) && strlen( $excerpt ) > $length ) {
			$excerpt  = substr( $excerpt, 0, $length - 1 );
			$excerpt .= '&hellip;';
		}

		return apply_filters( 'bbp_get_topic_excerpt', $excerpt, $topic_id, $length );
	}

/**
 * Output pagination links of a topic within the topic loop
 *
 * @since bbPress (r2966)
 *
 * @param mixed $args See {@link bbp_get_topic_pagination()}
 * @uses bbp_get_topic_pagination() To get the topic pagination links
 */
function bbp_topic_pagination( $args = '' ) {
	echo bbp_get_topic_pagination( $args );
}
	/**
	 * Returns pagination links of a topic within the topic loop
	 *
	 * @since bbPress (r2966)
	 *
	 * @param mixed $args This function supports these arguments:
	 *  - topic_id: Topic id
	 *  - before: Before the links
	 *  - after: After the links
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses WP_Rewrite::using_permalinks() To check if the blog is using
	 *                                       permalinks
	 * @uses user_trailingslashit() To add a trailing slash
	 * @uses trailingslashit() To add a trailing slash
	 * @uses get_permalink() To get the permalink of the topic
	 * @uses add_query_arg() To add query args
	 * @uses bbp_get_topic_reply_count() To get topic reply count
	 * @uses bbp_show_topic_lead() Are we showing the topic as a lead?
	 * @uses get_option() To get replies per page option
	 * @uses paginate_links() To paginate the links
	 * @uses apply_filters() Calls 'bbp_get_topic_pagination' with the links
	 *                        and arguments
	 * @return string Pagination links
	 */
	function bbp_get_topic_pagination( $args = '' ) {
		global $wp_rewrite;

		$defaults = array(
			'topic_id' => bbp_get_topic_id(),
			'before'   => '<span class="bbp-topic-pagination">',
			'after'    => '</span>',
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r );

		// If pretty permalinks are enabled, make our pagination pretty
		if ( $wp_rewrite->using_permalinks() )
			$base = trailingslashit( get_permalink( $topic_id ) ) . user_trailingslashit( $wp_rewrite->pagination_base . '/%#%/' );
		else
			$base = add_query_arg( 'paged', '%#%', get_permalink( $topic_id ) );

		// Get total and add 1 if topic is included in the reply loop
		$total = bbp_get_topic_reply_count( $topic_id );

		// Bump if topic is in loop
		if ( !bbp_show_lead_topic() )
			$total++;

		// Pagination settings
		$pagination = array(
			'base'      => $base,
			'format'    => '',
			'total'     => ceil( (int) $total / (int) bbp_get_replies_per_page() ),
			'current'   => 0,
			'prev_next' => false,
			'mid_size'  => 2,
			'end_size'  => 3,
			'add_args'  => ( bbp_get_view_all() ) ? array( 'view' => 'all' ) : false
		);

		// Add pagination to query object
		if ( $pagination_links = paginate_links( $pagination ) ) {

			// Remove first page from pagination
			if ( $wp_rewrite->using_permalinks() )
				$pagination_links = str_replace( $wp_rewrite->pagination_base . '/1/', '', $pagination_links );
			else
				$pagination_links = str_replace( '&#038;paged=1', '', $pagination_links );

			// Add before and after to pagination links
			$pagination_links = $before . $pagination_links . $after;
		}

		return apply_filters( 'bbp_get_topic_pagination', $pagination_links, $args );
	}

/**
 * Append revisions to the topic content
 *
 * @since bbPress (r2782)
 *
 * @param string $content Optional. Content to which we need to append the revisions to
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_revision_log() To get the topic revision log
 * @uses apply_filters() Calls 'bbp_topic_append_revisions' with the processed
 *                        content, original content and topic id
 * @return string Content with the revisions appended
 */
function bbp_topic_content_append_revisions( $content = '', $topic_id = 0 ) {

	// Bail if in admin
	if ( is_admin() )
		return;

	// Validate the ID
	$topic_id = bbp_get_topic_id( $topic_id );

	return apply_filters( 'bbp_topic_append_revisions', $content . bbp_get_topic_revision_log( $topic_id ), $content, $topic_id );
}

/**
 * Output the revision log of the topic
 *
 * @since bbPress (r2782)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_revision_log() To get the topic revision log
 */
function bbp_topic_revision_log( $topic_id = 0 ) {
	echo bbp_get_topic_revision_log( $topic_id );
}
	/**
	 * Return the formatted revision log of the topic
	 *
	 * @since bbPress (r2782)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic_revisions() To get the topic revisions
	 * @uses bbp_get_topic_raw_revision_log() To get the raw revision log
	 * @uses bbp_get_topic_author_display_name() To get the topic author
	 * @uses bbp_get_author_link() To get the topic author link
	 * @uses bbp_convert_date() To convert the date
	 * @uses bbp_get_time_since() To get the time in since format
	 * @uses apply_filters() Calls 'bbp_get_topic_revision_log' with the
	 *                        log and topic id
	 * @return string Revision log of the topic
	 */
	function bbp_get_topic_revision_log( $topic_id = 0 ) {
		// Create necessary variables
		$topic_id     = bbp_get_topic_id( $topic_id );
		$revision_log = bbp_get_topic_raw_revision_log( $topic_id );

		if ( empty( $topic_id ) || empty( $revision_log ) || !is_array( $revision_log ) )
			return false;

		if ( !$revisions = bbp_get_topic_revisions( $topic_id ) )
			return false;

		$r = "\n\n" . '<ul id="bbp-topic-revision-log-' . $topic_id . '" class="bbp-topic-revision-log">' . "\n\n";

		// Loop through revisions
		foreach ( (array) $revisions as $revision ) {

			if ( empty( $revision_log[$revision->ID] ) ) {
				$author_id = $revision->post_author;
				$reason    = '';
			} else {
				$author_id = $revision_log[$revision->ID]['author'];
				$reason    = $revision_log[$revision->ID]['reason'];
			}

			$author = bbp_get_author_link( array( 'size' => 14, 'link_text' => bbp_get_topic_author_display_name( $revision->ID ), 'post_id' => $revision->ID ) );
			$since  = bbp_get_time_since( bbp_convert_date( $revision->post_modified ) );

			$r .= "\t" . '<li id="bbp-topic-revision-log-' . $topic_id . '-item-' . $revision->ID . '" class="bbp-topic-revision-log-item">' . "\n";
			$r .= "\t\t" . sprintf( __( empty( $reason ) ? 'This topic was modified %1$s ago by %2$s.' : 'This topic was modified %1$s ago by %2$s. Reason: %3$s', 'bbpress' ), $since, $author, $reason ) . "\n";
			$r .= "\t" . '</li>' . "\n";

		}

		$r .= "\n" . '</ul>' . "\n\n";

		return apply_filters( 'bbp_get_topic_revision_log', $r, $topic_id );
	}
		/**
		 * Return the raw revision log of the topic
		 *
		 * @since bbPress (r2782)
		 *
		 * @param int $topic_id Optional. Topic id
		 * @uses bbp_get_topic_id() To get the topic id
		 * @uses get_post_meta() To get the revision log meta
		 * @uses apply_filters() Calls 'bbp_get_topic_raw_revision_log'
		 *                        with the log and topic id
		 * @return string Raw revision log of the topic
		 */
		function bbp_get_topic_raw_revision_log( $topic_id = 0 ) {
			$topic_id = bbp_get_topic_id( $topic_id );

			$revision_log = get_post_meta( $topic_id, '_bbp_revision_log', true );
			$revision_log = empty( $revision_log ) ? array() : $revision_log;

			return apply_filters( 'bbp_get_topic_raw_revision_log', $revision_log, $topic_id );
		}

/**
 * Return the revisions of the topic
 *
 * @since bbPress (r2782)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_id() To get the topic id
 * @uses wp_get_post_revisions() To get the topic revisions
 * @uses apply_filters() Calls 'bbp_get_topic_revisions'
 *                        with the revisions and topic id
 * @return string Topic revisions
 */
function bbp_get_topic_revisions( $topic_id = 0 ) {
	$topic_id  = bbp_get_topic_id( $topic_id );
	$revisions = wp_get_post_revisions( $topic_id, array( 'order' => 'ASC' ) );

	return apply_filters( 'bbp_get_topic_revisions', $revisions, $topic_id );
}

/**
 * Return the revision count of the topic
 *
 * @since bbPress (r2782)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_revisions() To get the topic revisions
 * @uses apply_filters() Calls 'bbp_get_topic_revision_count'
 *                        with the revision count and topic id
 * @return string Topic revision count
 */
function bbp_get_topic_revision_count( $topic_id = 0 ) {
	return apply_filters( 'bbp_get_topic_revisions', count( bbp_get_topic_revisions( $topic_id ) ), $topic_id );
}

/**
 * Output the status of the topic
 *
 * @since bbPress (r2667)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_status() To get the topic status
 */
function bbp_topic_status( $topic_id = 0 ) {
	echo bbp_get_topic_status( $topic_id );
}
	/**
	 * Return the status of the topic
	 *
	 * @since bbPress (r2667)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses get_post_status() To get the topic status
	 * @uses apply_filters() Calls 'bbp_get_topic_status' with the status
	 *                        and topic id
	 * @return string Status of topic
	 */
	function bbp_get_topic_status( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );

		return apply_filters( 'bbp_get_topic_status', get_post_status( $topic_id ), $topic_id );
	}

/**
 * Is the topic open to new replies?
 *
 * @since bbPress (r2727)
 *
 * @uses bbp_get_topic_status()
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_is_topic_closed() To check if the topic is closed
 * @return bool True if open, false if closed.
 */
function bbp_is_topic_open( $topic_id = 0 ) {
	return !bbp_is_topic_closed( $topic_id );
}

	/**
	 * Is the topic closed to new replies?
	 *
	 * @since bbPress (r2746)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_status() To get the topic status
	 * @return bool True if closed, false if not.
	 */
	function bbp_is_topic_closed( $topic_id = 0 ) {
		if ( bbp_get_closed_status_id() == bbp_get_topic_status( $topic_id ) )
			return true;

		return false;
	}

/**
 * Is the topic a sticky or super sticky?
 *
 * @since bbPress (r2754)
 *
 * @param int $topic_id Optional. Topic id
 * @param int $check_super Optional. If set to true and if the topic is not a
 *                           normal sticky, it is checked if it is a super
 *                           sticky or not. Defaults to true.
 * @uses bbp_get_topic_id() To get the topic id
 * @uses bbp_get_topic_forum_id() To get the topic forum id
 * @uses bbp_get_stickies() To get the stickies
 * @uses bbp_is_topic_super_sticky() To check if the topic is a super sticky
 * @return bool True if sticky or super sticky, false if not.
 */
function bbp_is_topic_sticky( $topic_id = 0, $check_super = true ) {
	$topic_id = bbp_get_topic_id( $topic_id );
	$forum_id = bbp_get_topic_forum_id( $topic_id );
	$stickies = bbp_get_stickies( $forum_id );

	if ( in_array( $topic_id, $stickies ) || ( !empty( $check_super ) && bbp_is_topic_super_sticky( $topic_id ) ) )
		return true;

	return false;
}

/**
 * Is the topic a super sticky?
 *
 * @since bbPress (r2754)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_id() To get the topic id
 * @uses bbp_get_super_stickies() To get the super stickies
 * @return bool True if super sticky, false if not.
 */
function bbp_is_topic_super_sticky( $topic_id = 0 ) {
	$topic_id = bbp_get_topic_id( $topic_id );
	$stickies = bbp_get_super_stickies( $topic_id );

	return in_array( $topic_id, $stickies );
}

/**
 * Is the topic not spam or deleted?
 *
 * @since bbPress (r3496)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_id() To get the topic id
 * @uses bbp_get_topic_status() To get the topic status
 * @return bool True if published, false if not.
 */
function bbp_is_topic_published( $topic_id = 0 ) {
	$topic_status = bbp_get_topic_status( bbp_get_topic_id( $topic_id ) );
	return bbp_get_public_status_id() == $topic_status;
}

/**
 * Is the topic marked as spam?
 *
 * @since bbPress (r2727)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_id() To get the topic id
 * @uses bbp_get_topic_status() To get the topic status
 * @return bool True if spam, false if not.
 */
function bbp_is_topic_spam( $topic_id = 0 ) {
	$topic_status = bbp_get_topic_status( bbp_get_topic_id( $topic_id ) );
	return bbp_get_spam_status_id() == $topic_status;
}

/**
 * Is the topic trashed?
 *
 * @since bbPress (r2888)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_id() To get the topic id
 * @uses bbp_get_topic_status() To get the topic status
 * @return bool True if trashed, false if not.
 */
function bbp_is_topic_trash( $topic_id = 0 ) {
	$topic_status = bbp_get_topic_status( bbp_get_topic_id( $topic_id ) );
	return bbp_get_trash_status_id() == $topic_status;
}

/**
 * Is the posted by an anonymous user?
 *
 * @since bbPress (r2753)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_id() To get the topic id
 * @uses bbp_get_topic_author_id() To get the topic author id
 * @uses get_post_meta() To get the anonymous user name and email meta
 * @return bool True if the post is by an anonymous user, false if not.
 */
function bbp_is_topic_anonymous( $topic_id = 0 ) {
	$topic_id = bbp_get_topic_id( $topic_id );

	if ( 0 != bbp_get_topic_author_id( $topic_id ) )
		return false;

	if ( false == get_post_meta( $topic_id, '_bbp_anonymous_name', true ) )
		return false;

	if ( false == get_post_meta( $topic_id, '_bbp_anonymous_email', true ) )
		return false;

	// The topic is by an anonymous user
	return true;
}

/**
 * Output the author of the topic
 *
 * @since bbPress (r2590)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_author() To get the topic author
 */
function bbp_topic_author( $topic_id = 0 ) {
	echo bbp_get_topic_author( $topic_id );
}
	/**
	 * Return the author of the topic
	 *
	 * @since bbPress (r2590)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_is_topic_anonymous() To check if the topic is by an
	 *                                 anonymous user
	 * @uses bbp_get_topic_author_id() To get the topic author id
	 * @uses get_the_author_meta() To get the display name of the author
	 * @uses get_post_meta() To get the name of the anonymous poster
	 * @uses apply_filters() Calls 'bbp_get_topic_author' with the author
	 *                        and topic id
	 * @return string Author of topic
	 */
	function bbp_get_topic_author( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );

		if ( !bbp_is_topic_anonymous( $topic_id ) )
			$author = get_the_author_meta( 'display_name', bbp_get_topic_author_id( $topic_id ) );
		else
			$author = get_post_meta( $topic_id, '_bbp_anonymous_name', true );

		return apply_filters( 'bbp_get_topic_author', $author, $topic_id );
	}

/**
 * Output the author ID of the topic
 *
 * @since bbPress (r2590)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_author_id() To get the topic author id
 */
function bbp_topic_author_id( $topic_id = 0 ) {
	echo bbp_get_topic_author_id( $topic_id );
}
	/**
	 * Return the author ID of the topic
	 *
	 * @since bbPress (r2590)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses get_post_field() To get the topic author id
	 * @uses apply_filters() Calls 'bbp_get_topic_author_id' with the author
	 *                        id and topic id
	 * @return string Author of topic
	 */
	function bbp_get_topic_author_id( $topic_id = 0 ) {
		$topic_id  = bbp_get_topic_id( $topic_id );
		$author_id = get_post_field( 'post_author', $topic_id );

		return apply_filters( 'bbp_get_topic_author_id', (int) $author_id, $topic_id );
	}

/**
 * Output the author display_name of the topic
 *
 * @since bbPress (r2590)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_author_display_name() To get the topic author's display
 *                                            name
 */
function bbp_topic_author_display_name( $topic_id = 0 ) {
	echo bbp_get_topic_author_display_name( $topic_id );
}
	/**
	 * Return the author display_name of the topic
	 *
	 * @since bbPress (r2485)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_is_topic_anonymous() To check if the topic is by an
	 *                                 anonymous user
	 * @uses bbp_get_topic_author_id() To get the topic author id
	 * @uses get_the_author_meta() To get the author meta
	 * @uses get_post_meta() To get the anonymous user name
	 * @uses apply_filters() Calls 'bbp_get_topic_author_id' with the
	 *                        display name and topic id
	 * @return string Topic's author's display name
	 */
	function bbp_get_topic_author_display_name( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );

		// Check for anonymous user
		if ( !bbp_is_topic_anonymous( $topic_id ) )
			$author_name = get_the_author_meta( 'display_name', bbp_get_topic_author_id( $topic_id ) );
		else
			$author_name = get_post_meta( $topic_id, '_bbp_anonymous_name', true );

		return apply_filters( 'bbp_get_topic_author_id', esc_attr( $author_name ), $topic_id );
	}

/**
 * Output the author avatar of the topic
 *
 * @since bbPress (r2590)
 *
 * @param int $topic_id Optional. Topic id
 * @param int $size Optional. Avatar size. Defaults to 40
 * @uses bbp_get_topic_author_avatar() To get the topic author avatar
 */
function bbp_topic_author_avatar( $topic_id = 0, $size = 40 ) {
	echo bbp_get_topic_author_avatar( $topic_id, $size );
}
	/**
	 * Return the author avatar of the topic
	 *
	 * @since bbPress (r2590)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @param int $size Optional. Avatar size. Defaults to 40
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_is_topic_anonymous() To check if the topic is by an
	 *                                 anonymous user
	 * @uses bbp_get_topic_author_id() To get the topic author id
	 * @uses get_post_meta() To get the anonymous user's email
	 * @uses get_avatar() To get the avatar
	 * @uses apply_filters() Calls 'bbp_get_topic_author_avatar' with the
	 *                        avatar, topic id and size
	 * @return string Avatar of the author of the topic
	 */
	function bbp_get_topic_author_avatar( $topic_id = 0, $size = 40 ) {
		$author_avatar = '';

		if ( $topic_id = bbp_get_topic_id( $topic_id ) ) {

			// Check for anonymous user
			if ( !bbp_is_topic_anonymous( $topic_id ) )
				$author_avatar = get_avatar( bbp_get_topic_author_id( $topic_id ), $size );
			else
				$author_avatar = get_avatar( get_post_meta( $topic_id, '_bbp_anonymous_email', true ), $size );
		}

		return apply_filters( 'bbp_get_topic_author_avatar', $author_avatar, $topic_id, $size );
	}

/**
 * Output the author link of the topic
 *
 * @since bbPress (r2717)
 *
 * @param mixed|int $args If it is an integer, it is used as topic_id. Optional.
 * @uses bbp_get_topic_author_link() To get the topic author link
 */
function bbp_topic_author_link( $args = '' ) {
	echo bbp_get_topic_author_link( $args );
}
	/**
	 * Return the author link of the topic
	 *
	 * @since bbPress (r2717)
	 *
	 * @param mixed|int $args If it is an integer, it is used as topic id.
	 *                         Optional.
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic_author_display_name() To get the topic author
	 * @uses bbp_is_topic_anonymous() To check if the topic is by an
	 *                                 anonymous user
	 * @uses bbp_get_topic_author_avatar() To get the topic author avatar
	 * @uses bbp_get_topic_author_url() To get the topic author url
	 * @uses apply_filters() Calls 'bbp_get_topic_author_link' with the link
	 *                        and args
	 * @return string Author link of topic
	 */
	function bbp_get_topic_author_link( $args = '' ) {
		$defaults = array (
			'post_id'    => 0,
			'link_title' => '',
			'type'       => 'both',
			'size'       => 80,
			'sep'        => '&nbsp;'
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r );

		// Used as topic_id
		if ( is_numeric( $args ) )
			$topic_id = bbp_get_topic_id( $args );
		else
			$topic_id = bbp_get_topic_id( $post_id );

		if ( !empty( $topic_id ) ) {
			if ( empty( $link_title ) )
				$link_title = sprintf( !bbp_is_topic_anonymous( $topic_id ) ? __( 'View %s\'s profile', 'bbpress' ) : __( 'Visit %s\'s website', 'bbpress' ), bbp_get_topic_author_display_name( $topic_id ) );

			$link_title = !empty( $link_title ) ? ' title="' . $link_title . '"' : '';
			$author_url = bbp_get_topic_author_url( $topic_id );
			$anonymous  = bbp_is_topic_anonymous( $topic_id );

			// Get avatar
			if ( 'avatar' == $type || 'both' == $type )
				$author_links['avatar'] = bbp_get_topic_author_avatar( $topic_id, $size );

			// Get display name
			if ( 'name' == $type   || 'both' == $type )
				$author_links['name'] = bbp_get_topic_author_display_name( $topic_id );

			// Link class
			$link_class = ' class="bbp-author-' . $type . '"';

			// Add links if not anonymous
			if ( empty( $anonymous ) ) {
				foreach ( $author_links as $link => $link_text ) {
					$link_class = ' class="bbp-author-' . $link . '"';
					$author_link[] = sprintf( '<a href="%1$s"%2$s%3$s>%4$s</a>', $author_url, $link_title, $link_class, $link_text );
				}
				$author_link = join( $sep, $author_link );

			// No links if anonymous
			} else {
				$author_link = join( $sep, $author_links );
			}

		} else {
			$author_link = '';
		}

		return apply_filters( 'bbp_get_topic_author_link', $author_link, $args );
	}

/**
 * Output the author url of the topic
 *
 * @since bbPress (r2590)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_author_url() To get the topic author url
 */
function bbp_topic_author_url( $topic_id = 0 ) {
	echo bbp_get_topic_author_url( $topic_id );
}

	/**
	 * Return the author url of the topic
	 *
	 * @since bbPress (r2590)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_is_topic_anonymous() To check if the topic
	 *                                 is by an anonymous
	 *                                 user or not
	 * @uses bbp_get_topic_author_id() To get topic author
	 *                                  id
	 * @uses bbp_get_user_profile_url() To get profile url
	 * @uses get_post_meta() To get anonmous user's website
	 * @uses apply_filters() Calls
	 *                        'bbp_get_topic_author_url'
	 *                        with the link & topic id
	 * @return string Author URL of topic
	 */
	function bbp_get_topic_author_url( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );

		// Check for anonymous user
		if ( !bbp_is_topic_anonymous( $topic_id ) ) {
			$author_url = bbp_get_user_profile_url( bbp_get_topic_author_id( $topic_id ) );
		} else {
			if ( !$author_url = get_post_meta( $topic_id, '_bbp_anonymous_website', true ) ) {
				$author_url = '';
			}
		}

		return apply_filters( 'bbp_get_topic_author_url', $author_url, $topic_id );
	}

/**
 * Output the topic author email address
 *
 * @since bbPress (r3445)
 *
 * @param int $topic_id Optional. Reply id
 * @uses bbp_get_topic_author_email() To get the topic author email
 */
function bbp_topic_author_email( $topic_id = 0 ) {
	echo bbp_get_topic_author_email( $topic_id );
}
	/**
	 * Return the topic author email address
	 *
	 * @since bbPress (r3445)
	 *
	 * @param int $topic_id Optional. Reply id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_is_topic_anonymous() To check if the topic is by an anonymous
	 *                                 user
	 * @uses bbp_get_topic_author_id() To get the topic author id
	 * @uses get_userdata() To get the user data
	 * @uses get_post_meta() To get the anonymous poster's email
	 * @uses apply_filters() Calls bbp_get_topic_author_email with the author
	 *                        email & topic id
	 * @return string Topic author email address
	 */
	function bbp_get_topic_author_email( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );

		// Not anonymous user
		if ( !bbp_is_topic_anonymous( $topic_id ) ) {

			// Use topic author email address
			$user_id      = bbp_get_topic_author_id( $topic_id );
			$user         = get_userdata( $user_id );
			$author_email = !empty( $user->user_email ) ? $user->user_email : '';

		// Anonymous
		} else {

			// Get email from post meta
			$author_email = get_post_meta( $topic_id, '_bbp_anonymous_email', true );

			// Sanity check for missing email address
			if ( empty( $author_email ) ) {
				$author_email = '';
			}
		}

		return apply_filters( 'bbp_get_topic_author_email', $author_email, $topic_id );
	}

/**
 * Output the title of the forum a topic belongs to
 *
 * @since bbPress (r2485)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_forum_title() To get the topic's forum title
 */
function bbp_topic_forum_title( $topic_id = 0 ) {
	echo bbp_get_topic_forum_title( $topic_id );
}
	/**
	 * Return the title of the forum a topic belongs to
	 *
	 * @since bbPress (r2485)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get topic id
	 * @uses bbp_get_topic_forum_id() To get topic's forum id
	 * @uses apply_filters() Calls 'bbp_get_topic_forum' with the forum
	 *                        title and topic id
	 * @return string Topic forum title
	 */
	function bbp_get_topic_forum_title( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );
		$forum_id = bbp_get_topic_forum_id( $topic_id );

		return apply_filters( 'bbp_get_topic_forum', bbp_get_forum_title( $forum_id ), $topic_id );
	}

/**
 * Output the forum id a topic belongs to
 *
 * @since bbPress (r2491)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_forum_id()
 */
function bbp_topic_forum_id( $topic_id = 0 ) {
	echo bbp_get_topic_forum_id( $topic_id );
}
	/**
	 * Return the forum id a topic belongs to
	 *
	 * @since bbPress (r2491)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get topic id
	 * @uses get_post_meta() To retrieve get topic's forum id meta
	 * @uses apply_filters() Calls 'bbp_get_topic_forum_id' with the forum
	 *                        id and topic id
	 * @return int Topic forum id
	 */
	function bbp_get_topic_forum_id( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );
		$forum_id = get_post_meta( $topic_id, '_bbp_forum_id', true );

		return apply_filters( 'bbp_get_topic_forum_id', (int) $forum_id, $topic_id );
	}

/**
 * Output the topics last active ID
 *
 * @since bbPress (r2860)
 *
 * @param int $topic_id Optional. Forum id
 * @uses bbp_get_topic_last_active_id() To get the topic's last active id
 */
function bbp_topic_last_active_id( $topic_id = 0 ) {
	echo bbp_get_topic_last_active_id( $topic_id );
}
	/**
	 * Return the topics last active ID
	 *
	 * @since bbPress (r2860)
	 *
	 * @param int $topic_id Optional. Forum id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses get_post_meta() To get the topic's last active id
	 * @uses apply_filters() Calls 'bbp_get_topic_last_active_id' with
	 *                        the last active id and topic id
	 * @return int Forum's last active id
	 */
	function bbp_get_topic_last_active_id( $topic_id = 0 ) {
		$topic_id  = bbp_get_topic_id( $topic_id );
		$active_id = get_post_meta( $topic_id, '_bbp_last_active_id', true );

		return apply_filters( 'bbp_get_topic_last_active_id', (int) $active_id, $topic_id );
	}

/**
 * Output the topics last update date/time (aka freshness)
 *
 * @since bbPress (r2625)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_last_active_time() To get topic freshness
 */
function bbp_topic_last_active_time( $topic_id = 0 ) {
	echo bbp_get_topic_last_active_time( $topic_id );
}
	/**
	 * Return the topics last update date/time (aka freshness)
	 *
	 * @since bbPress (r2625)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get topic id
	 * @uses get_post_meta() To get the topic lst active meta
	 * @uses bbp_get_topic_last_reply_id() To get topic last reply id
	 * @uses get_post_field() To get the post date of topic/reply
	 * @uses bbp_convert_date() To convert date
	 * @uses bbp_get_time_since() To get time in since format
	 * @uses apply_filters() Calls 'bbp_get_topic_last_active' with topic
	 *                        freshness and topic id
	 * @return string Topic freshness
	 */
	function bbp_get_topic_last_active_time( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );

		// Try to get the most accurate freshness time possible
		if ( !$last_active = get_post_meta( $topic_id, '_bbp_last_active_time', true ) ) {
			if ( $reply_id = bbp_get_topic_last_reply_id( $topic_id ) ) {
				$last_active = get_post_field( 'post_date', $reply_id );
			} else {
				$last_active = get_post_field( 'post_date', $topic_id );
			}
		}

		$last_active = !empty( $last_active ) ? bbp_get_time_since( bbp_convert_date( $last_active ) ) : '';

		// Return the time since
		return apply_filters( 'bbp_get_topic_last_active', $last_active, $topic_id );
	}

/** Topic Last Reply **********************************************************/

/**
 * Output the id of the topics last reply
 *
 * @since bbPress (r2625)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_last_reply_id() To get the topic last reply id
 */
function bbp_topic_last_reply_id( $topic_id = 0 ) {
	echo bbp_get_topic_last_reply_id( $topic_id );
}
	/**
	 * Return the topics last update date/time (aka freshness)
	 *
	 * @since bbPress (r2625)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses get_post_meta() To get the last reply id meta
	 * @uses apply_filters() Calls 'bbp_get_topic_last_reply_id' with the
	 *                        last reply id and topic id
	 * @return int Topic last reply id
	 */
	function bbp_get_topic_last_reply_id( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );
		$reply_id = get_post_meta( $topic_id, '_bbp_last_reply_id', true );

		if ( empty( $reply_id ) )
			$reply_id = $topic_id;

		return apply_filters( 'bbp_get_topic_last_reply_id', (int) $reply_id, $topic_id );
	}

/**
 * Output the title of the last reply inside a topic
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_last_reply_title() To get the topic last reply title
 */
function bbp_topic_last_reply_title( $topic_id = 0 ) {
	echo bbp_get_topic_last_reply_title( $topic_id );
}
	/**
	 * Return the title of the last reply inside a topic
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic_last_reply_id() To get the topic last reply id
	 * @uses bbp_get_reply_title() To get the reply title
	 * @uses apply_filters() Calls 'bbp_get_topic_last_topic_title' with
	 *                        the reply title and topic id
	 * @return string Topic last reply title
	 */
	function bbp_get_topic_last_reply_title( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );
		return apply_filters( 'bbp_get_topic_last_topic_title', bbp_get_reply_title( bbp_get_topic_last_reply_id( $topic_id ) ), $topic_id );
	}

/**
 * Output the link to the last reply in a topic
 *
 * @since bbPress (r2464)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_last_reply_permalink() To get the topic's last reply link
 */
function bbp_topic_last_reply_permalink( $topic_id = 0 ) {
	echo bbp_get_topic_last_reply_permalink( $topic_id );
}
	/**
	 * Return the link to the last reply in a topic
	 *
	 * @since bbPress (r2464)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic_last_reply_id() To get the topic last reply id
	 * @uses bbp_get_reply_permalink() To get the reply permalink
	 * @uses apply_filters() Calls 'bbp_get_topic_last_topic_permalink' with
	 *                        the reply permalink and topic id
	 * @return string Permanent link to the reply
	 */
	function bbp_get_topic_last_reply_permalink( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );
		return apply_filters( 'bbp_get_topic_last_reply_permalink', bbp_get_reply_permalink( bbp_get_topic_last_reply_id( $topic_id ) ) );
	}

/**
 * Output the link to the last reply in a topic
 *
 * @since bbPress (r2683)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_last_reply_url() To get the topic last reply url
 */
function bbp_topic_last_reply_url( $topic_id = 0 ) {
	echo bbp_get_topic_last_reply_url( $topic_id );
}
	/**
	 * Return the link to the last reply in a topic
	 *
	 * @since bbPress (r2683)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic_last_reply_id() To get the topic last reply id
	 * @uses bbp_get_reply_url() To get the reply url
	 * @uses bbp_get_reply_permalink() To get the reply permalink
	 * @uses apply_filters() Calls 'bbp_get_topic_last_topic_url' with
	 *                        the reply url and topic id
	 * @return string Topic last reply url
	 */
	function bbp_get_topic_last_reply_url( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );
		$reply_id = bbp_get_topic_last_reply_id( $topic_id );

		if ( !empty( $reply_id ) && ( $reply_id != $topic_id ) )
			$reply_url = bbp_get_reply_url( $reply_id );
		else
			$reply_url = bbp_get_topic_permalink( $topic_id );

		return apply_filters( 'bbp_get_topic_last_reply_url', $reply_url );
	}

/**
 * Output link to the most recent activity inside a topic, complete with link
 * attributes and content.
 *
 * @since bbPress (r2625)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_freshness_link() To get the topic freshness link
 */
function bbp_topic_freshness_link( $topic_id = 0 ) {
	echo bbp_get_topic_freshness_link( $topic_id );
}
	/**
	 * Returns link to the most recent activity inside a topic, complete
	 * with link attributes and content.
	 *
	 * @since bbPress (r2625)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic_last_reply_url() To get the topic last reply url
	 * @uses bbp_get_topic_last_reply_title() To get the reply title
	 * @uses bbp_get_topic_last_active_time() To get the topic freshness
	 * @uses apply_filters() Calls 'bbp_get_topic_freshness_link' with the
	 *                        link and topic id
	 * @return string Topic freshness link
	 */
	function bbp_get_topic_freshness_link( $topic_id = 0 ) {
		$topic_id   = bbp_get_topic_id( $topic_id );
		$link_url   = bbp_get_topic_last_reply_url( $topic_id );
		$title      = bbp_get_topic_last_reply_title( $topic_id );
		$time_since = bbp_get_topic_last_active_time( $topic_id );

		if ( !empty( $time_since ) )
			$anchor = '<a href="' . $link_url . '" title="' . esc_attr( $title ) . '">' . $time_since . '</a>';
		else
			$anchor = __( 'No Replies', 'bbpress' );

		return apply_filters( 'bbp_get_topic_freshness_link', $anchor, $topic_id );
	}

/**
 * Output the replies link of the topic
 *
 * @since bbPress (r2740)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_replies_link() To get the topic replies link
 */
function bbp_topic_replies_link( $topic_id = 0 ) {
	echo bbp_get_topic_replies_link( $topic_id );
}

	/**
	 * Return the replies link of the topic
	 *
	 * @since bbPress (r2740)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic() To get the topic
	 * @uses bbp_get_topic_reply_count() To get the topic reply count
	 * @uses bbp_get_topic_permalink() To get the topic permalink
	 * @uses remove_query_arg() To remove args from the url
	 * @uses bbp_get_topic_reply_count_hidden() To get the topic hidden
	 *                                           reply count
	 * @uses current_user_can() To check if the current user can edit others
	 *                           replies
	 * @uses add_query_arg() To add custom args to the url
	 * @uses apply_filters() Calls 'bbp_get_topic_replies_link' with the
	 *                        replies link and topic id
	 */
	function bbp_get_topic_replies_link( $topic_id = 0 ) {

		$topic    = bbp_get_topic( bbp_get_topic_id( (int) $topic_id ) );
		$topic_id = $topic->ID;
		$replies  = bbp_get_topic_reply_count( $topic_id );
		$replies  = sprintf( _n( '%s reply', '%s replies', $replies, 'bbpress' ), $replies );
		$retval   = '';

		// First link never has view=all
		if ( bbp_get_view_all( 'edit_others_replies' ) )
			$retval .= "<a href='" . esc_url( bbp_remove_view_all( bbp_get_topic_permalink( $topic_id ) ) ) . "'>$replies</a>";
		else
			$retval .= $replies;

		// This forum has hidden topics
		if ( current_user_can( 'edit_others_replies' ) && ( $deleted = bbp_get_topic_reply_count_hidden( $topic_id ) ) ) {

			// Extra text
			$extra = sprintf( __( ' (+ %d hidden)', 'bbpress' ), $deleted );

			// No link
			if ( bbp_get_view_all() )
				$retval .= " $extra";

			// Link
			else
				$retval .= " <a href='" . esc_url( bbp_add_view_all( bbp_get_topic_permalink( $topic_id ), true ) ) . "'>$extra</a>";
		}

		return apply_filters( 'bbp_get_topic_replies_link', $retval, $topic_id );
	}

/**
 * Output total reply count of a topic
 *
 * @since bbPress (r2485)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_reply_count() To get the topic reply count
 */
function bbp_topic_reply_count( $topic_id = 0 ) {
	echo bbp_get_topic_reply_count( $topic_id );
}
	/**
	 * Return total reply count of a topic
	 *
	 * @since bbPress (r2485)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses get_post_meta() To get the topic reply count meta
	 * @uses apply_filters() Calls 'bbp_get_topic_reply_count' with the
	 *                        reply count and topic id
	 * @return int Reply count
	 */
	function bbp_get_topic_reply_count( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );
		$replies  = get_post_meta( $topic_id, '_bbp_reply_count', true );

		return apply_filters( 'bbp_get_topic_reply_count', (int) $replies, $topic_id );
	}

/**
 * Output total post count of a topic
 *
 * @since bbPress (r2954)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_post_count() To get the topic post count
 */
function bbp_topic_post_count( $topic_id = 0 ) {
	echo bbp_get_topic_post_count( $topic_id );
}
	/**
	 * Return total post count of a topic
	 *
	 * @since bbPress (r2954)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses get_post_meta() To get the topic post count meta
	 * @uses apply_filters() Calls 'bbp_get_topic_post_count' with the
	 *                        post count and topic id
	 * @return int Post count
	 */
	function bbp_get_topic_post_count( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );
		$replies  = get_post_meta( $topic_id, '_bbp_reply_count', true );

		return apply_filters( 'bbp_get_topic_post_count', (int) $replies + 1, $topic_id );
	}

/**
 * Output total hidden reply count of a topic (hidden includes trashed and
 * spammed replies)
 *
 * @since bbPress (r2740)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_reply_count_hidden() To get the topic hidden reply count
 */
function bbp_topic_reply_count_hidden( $topic_id = 0 ) {
	echo bbp_get_topic_reply_count_hidden( $topic_id );
}
	/**
	 * Return total hidden reply count of a topic (hidden includes trashed
	 * and spammed replies)
	 *
	 * @since bbPress (r2740)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses get_post_meta() To get the hidden reply count
	 * @uses apply_filters() Calls 'bbp_get_topic_reply_count_hidden' with
	 *                        the hidden reply count and topic id
	 * @return int Topic hidden reply count
	 */
	function bbp_get_topic_reply_count_hidden( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );
		$replies  = get_post_meta( $topic_id, '_bbp_reply_count_hidden', true );

		return apply_filters( 'bbp_get_topic_reply_count_hidden', (int) $replies, $topic_id );
	}

/**
 * Output total voice count of a topic
 *
 * @since bbPress (r2567)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_voice_count() To get the topic voice count
 */
function bbp_topic_voice_count( $topic_id = 0 ) {
	echo bbp_get_topic_voice_count( $topic_id );
}
	/**
	 * Return total voice count of a topic
	 *
	 * @since bbPress (r2567)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses get_post_meta() To get the voice count meta
	 * @uses apply_filters() Calls 'bbp_get_topic_voice_count' with the
	 *                        voice count and topic id
	 * @return int Voice count of the topic
	 */
	function bbp_get_topic_voice_count( $topic_id = 0 ) {
		$topic_id = bbp_get_topic_id( $topic_id );
		$voices   = get_post_meta( $topic_id, '_bbp_voice_count', true );

		return apply_filters( 'bbp_get_topic_voice_count', (int) $voices, $topic_id );
	}

/**
 * Output a the tags of a topic
 *
 * @param int $topic_id Optional. Topic id
 * @param mixed $args See {@link bbp_get_topic_tag_list()}
 * @uses bbp_get_topic_tag_list() To get the topic tag list
 */
function bbp_topic_tag_list( $topic_id = 0, $args = '' ) {
	echo bbp_get_topic_tag_list( $topic_id, $args );
}
	/**
	 * Return the tags of a topic
	 *
	 * @param int $topic_id Optional. Topic id
	 * @param array $args This function supports these arguments:
	 *  - before: Before the tag list
	 *  - sep: Tag separator
	 *  - after: After the tag list
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses get_the_term_list() To get the tags list
	 * @return string Tag list of the topic
	 */
	function bbp_get_topic_tag_list( $topic_id = 0, $args = '' ) {

		$defaults = array(
			'before' => '<div class="bbp-topic-tags"><p>' . __( 'Tagged:', 'bbpress' ) . '&nbsp;',
			'sep'    => ', ',
			'after'  => '</p></div>'
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r );

		$topic_id = bbp_get_topic_id( $topic_id );

		// Topic is spammed, so display pre-spam terms
		if ( bbp_is_topic_spam( $topic_id ) ) {

			// Get pre-spam terms
			$terms = get_post_meta( $topic_id, '_bbp_spam_topic_tags', true );

			// If terms exist, explode them and compile the return value
			if ( !empty( $terms ) ) {
				$terms  = implode( $sep, $terms );
				$retval = $before . $terms . $after;

			// No terms so return emty string
			} else {
				$retval = '';
			}

		// Topic is not spam so display a clickable term list
		} else {
			$retval = get_the_term_list( $topic_id, bbp_get_topic_tag_tax_id(), $before, $sep, $after );
		}

		return $retval;
	}

/**
 * Output the row class of a topic
 *
 * @since bbPress (r2667)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_class() To get the topic class
 */
function bbp_topic_class( $topic_id = 0 ) {
	echo bbp_get_topic_class( $topic_id );
}
	/**
	 * Return the row class of a topic
	 *
	 * @since bbPress (r2667)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_is_topic_sticky() To check if the topic is a sticky
	 * @uses bbp_is_topic_super_sticky() To check if the topic is a super
	 *                                    sticky
	 * @uses get_post_class() To get the topic classes
	 * @uses apply_filters() Calls 'bbp_get_topic_class' with the classes
	 *                        and topic id
	 * @return string Row class of a topic
	 */
	function bbp_get_topic_class( $topic_id = 0 ) {
		global $bbp;

		$topic_id  = bbp_get_topic_id( $topic_id );
		$count     = isset( $bbp->topic_query->current_post ) ? $bbp->topic_query->current_post : 1;
		$classes   = array();
		$classes[] = ( (int) $count % 2 )                    ? 'even'         : 'odd';
		$classes[] = bbp_is_topic_sticky( $topic_id, false ) ? 'sticky'       : '';
		$classes[] = bbp_is_topic_super_sticky( $topic_id  ) ? 'super-sticky' : '';
		$classes   = array_filter( $classes );
		$retval    = get_post_class( $classes, $topic_id );
		$retval    = 'class="' . join( ' ', $retval ) . '"';

		return apply_filters( 'bbp_get_topic_class', $retval, $topic_id );
	}

/** Topic Admin Links *********************************************************/

/**
 * Output admin links for topic
 *
 * @param mixed $args See {@link bbp_get_topic_admin_links()}
 * @uses bbp_get_topic_admin_links() To get the topic admin links
 */
function bbp_topic_admin_links( $args = '' ) {
	echo bbp_get_topic_admin_links( $args );
}
	/**
	 * Return admin links for topic.
	 *
	 * Move topic functionality is handled by the edit topic page.
	 *
	 * @param mixed $args This function supports these arguments:
	 *  - id: Optional. Topic id
	 *  - before: Before the links
	 *  - after: After the links
	 *  - sep: Links separator
	 *  - links: Topic admin links array
	 * @uses current_user_can() To check if the current user can edit/delete
	 *                           the topic
	 * @uses bbp_get_topic_edit_link() To get the topic edit link
	 * @uses bbp_get_topic_trash_link() To get the topic trash link
	 * @uses bbp_get_topic_close_link() To get the topic close link
	 * @uses bbp_get_topic_spam_link() To get the topic spam link
	 * @uses bbp_get_topic_stick_link() To get the topic stick link
	 * @uses bbp_get_topic_merge_link() To get the topic merge link
	 * @uses bbp_get_topic_status() To get the topic status
	 * @uses apply_filters() Calls 'bbp_get_topic_admin_links' with the
	 *                        topic admin links and args
	 * @return string Topic admin links
	 */
	function bbp_get_topic_admin_links( $args = '' ) {

		if ( !bbp_is_single_topic() )
			return;

		$defaults = array (
			'id'     => bbp_get_topic_id(),
			'before' => '<span class="bbp-admin-links">',
			'after'  => '</span>',
			'sep'    => ' | ',
			'links'  => array()
		);

		$r = wp_parse_args( $args, $defaults );

		if ( !current_user_can( 'edit_topic', $r['id'] ) )
			return;

		if ( empty( $r['links'] ) ) {
			$r['links'] = array(
				'edit'  => bbp_get_topic_edit_link ( $r ),
				'close' => bbp_get_topic_close_link( $r ),
				'stick' => bbp_get_topic_stick_link( $r ),
				'merge' => bbp_get_topic_merge_link( $r ),
				'trash' => bbp_get_topic_trash_link( $r ),
				'spam'  => bbp_get_topic_spam_link ( $r ),
			);
		}

		// Check caps for trashing the topic
		if ( !current_user_can( 'delete_topic', $r['id'] ) && !empty( $r['links']['trash'] ) )
			unset( $r['links']['trash'] );

		// See if links need to be unset
		$topic_status = bbp_get_topic_status( $r['id'] );
		if ( in_array( $topic_status, array( bbp_get_spam_status_id(), bbp_get_trash_status_id() ) ) ) {

			// Close link shouldn't be visible on trashed/spammed topics
			unset( $r['links']['close'] );

			// Spam link shouldn't be visible on trashed topics
			if ( $topic_status == bbp_get_trash_status_id() )
				unset( $r['links']['spam'] );

			// Trash link shouldn't be visible on spam topics
			elseif ( $topic_status == bbp_get_spam_status_id() )
				unset( $r['links']['trash'] );
		}

		// Process the admin links
		$links = implode( $r['sep'], array_filter( $r['links'] ) );

		return apply_filters( 'bbp_get_topic_admin_links', $r['before'] . $links . $r['after'], $args );
	}

/**
 * Output the edit link of the topic
 *
 * @since bbPress (r2727)
 *
 * @param mixed $args See {@link bbp_get_topic_edit_link()}
 * @uses bbp_get_topic_edit_link() To get the topic edit link
 */
function bbp_topic_edit_link( $args = '' ) {
	echo bbp_get_topic_edit_link( $args );
}

	/**
	 * Return the edit link of the topic
	 *
	 * @since bbPress (r2727)
	 *
	 * @param mixed $args This function supports these args:
	 *  - id: Optional. Topic id
	 *  - link_before: Before the link
	 *  - link_after: After the link
	 *  - edit_text: Edit text
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic() To get the topic
	 * @uses current_user_can() To check if the current user can edit the
	 *                           topic
	 * @uses bbp_get_topic_edit_url() To get the topic edit url
	 * @uses apply_filters() Calls 'bbp_get_topic_edit_link' with the link
	 *                        and args
	 * @return string Topic edit link
	 */
	function bbp_get_topic_edit_link( $args = '' ) {
		$defaults = array (
			'id'           => 0,
			'link_before'  => '',
			'link_after'   => '',
			'edit_text'    => __( 'Edit', 'bbpress' )
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r );

		$topic = bbp_get_topic( bbp_get_topic_id( (int) $id ) );

		// Bypass check if user has caps
		if ( !current_user_can( 'edit_others_topics' ) ) {

			// User cannot edit or it is past the lock time
			if ( empty( $topic ) || !current_user_can( 'edit_topic', $topic->ID ) || bbp_past_edit_lock( $topic->post_date_gmt ) )
				return;
		}

		// No uri to edit topic
		if ( !$uri = bbp_get_topic_edit_url( $id ) )
			return;

		return apply_filters( 'bbp_get_topic_edit_link', $link_before . '<a href="' . $uri . '">' . $edit_text . '</a>' . $link_after, $args );
	}

/**
 * Output URL to the topic edit page
 *
 * @since bbPress (r2753)
 *
 * @param int $topic_id Optional. Topic id
 * @uses bbp_get_topic_edit_url() To get the topic edit url
 */
function bbp_topic_edit_url( $topic_id = 0 ) {
	echo bbp_get_topic_edit_url( $topic_id );
}
	/**
	 * Return URL to the topic edit page
	 *
	 * @since bbPress (r2753)
	 *
	 * @param int $topic_id Optional. Topic id
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic() To get the topic
	 * @uses add_query_arg() To add custom args to the url
	 * @uses home_url() To get the home url
	 * @uses apply_filters() Calls 'bbp_get_topic_edit_url' with the edit
	 *                        url and topic id
	 * @return string Topic edit url
	 */
	function bbp_get_topic_edit_url( $topic_id = 0 ) {
		global $wp_rewrite, $bbp;

		if ( !$topic = bbp_get_topic( bbp_get_topic_id( $topic_id ) ) )
			return;

		// Pretty permalinks
		if ( $wp_rewrite->using_permalinks() ) {
			$url = $wp_rewrite->root . $bbp->topic_slug . '/' . $topic->post_name . '/edit';
			$url = home_url( user_trailingslashit( $url ) );

		// Unpretty permalinks
		} else {
			$url = add_query_arg( array( bbp_get_topic_post_type() => $topic->post_name, 'edit' => '1' ), home_url( '/' ) );
		}

		return apply_filters( 'bbp_get_topic_edit_url', $url, $topic_id );
	}

/**
 * Output the trash link of the topic
 *
 * @since bbPress (r2727)
 *
 * @param mixed $args See {@link bbp_get_topic_trash_link()}
 * @uses bbp_get_topic_trash_link() To get the topic trash link
 */
function bbp_topic_trash_link( $args = '' ) {
	echo bbp_get_topic_trash_link( $args );
}

	/**
	 * Return the trash link of the topic
	 *
	 * @since bbPress (r2727)
	 *
	 * @param mixed $args This function supports these args:
	 *  - id: Optional. Topic id
	 *  - link_before: Before the link
	 *  - link_after: After the link
	 *  - sep: Links separator
	 *  - trash_text: Trash text
	 *  - restore_text: Restore text
	 *  - delete_text: Delete text
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic() To get the topic
	 * @uses current_user_can() To check if the current user can delete the
	 *                           topic
	 * @uses bbp_is_topic_trash() To check if the topic is trashed
	 * @uses bbp_get_topic_status() To get the topic status
	 * @uses add_query_arg() To add custom args to the url
	 * @uses wp_nonce_url() To nonce the url
	 * @uses esc_url() To escape the url
	 * @uses apply_filters() Calls 'bbp_get_topic_trash_link' with the link
	 *                        and args
	 * @return string Topic trash link
	 */
	function bbp_get_topic_trash_link( $args = '' ) {

		$defaults = array (
			'id'           => 0,
			'link_before'  => '',
			'link_after'   => '',
			'sep'          => ' | ',
			'trash_text'   => __( 'Trash',   'bbpress' ),
			'restore_text' => __( 'Restore', 'bbpress' ),
			'delete_text'  => __( 'Delete',  'bbpress' )
		);
		$r = wp_parse_args( $args, $defaults );
		extract( $r );

		$actions = array();
		$topic   = bbp_get_topic( bbp_get_topic_id( (int) $id ) );

		if ( empty( $topic ) || !current_user_can( 'delete_topic', $topic->ID ) ) {
			return;
		}

		if ( bbp_is_topic_trash( $topic->ID ) ) {
			$actions['untrash'] = '<a title="' . esc_attr( __( 'Restore this item from the Trash', 'bbpress' ) ) . '" href="' . esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'bbp_toggle_topic_trash', 'sub_action' => 'untrash', 'topic_id' => $topic->ID ) ), 'untrash-' . $topic->post_type . '_' . $topic->ID ) ) . '">' . esc_html( $restore_text ) . '</a>';
		} elseif ( EMPTY_TRASH_DAYS ) {
			$actions['trash']   = '<a title="' . esc_attr( __( 'Move this item to the Trash', 'bbpress' ) ) . '" href="' . esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'bbp_toggle_topic_trash', 'sub_action' => 'trash', 'topic_id' => $topic->ID ) ), 'trash-' . $topic->post_type . '_' . $topic->ID ) ) . '">' . esc_html( $trash_text ) . '</a>';
		}

		if ( bbp_is_topic_trash( $topic->ID ) || !EMPTY_TRASH_DAYS ) {
			$actions['delete']  = '<a title="' . esc_attr( __( 'Delete this item permanently', 'bbpress' ) ) . '" href="' . esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'bbp_toggle_topic_trash', 'sub_action' => 'delete', 'topic_id' => $topic->ID ) ), 'delete-' . $topic->post_type . '_' . $topic->ID ) ) . '" onclick="return confirm(\'' . esc_js( __( 'Are you sure you want to delete that permanently?', 'bbpress' ) ) . '\' );">' . esc_html( $delete_text ) . '</a>';
		}

		// Process the admin links
		$actions = implode( $sep, $actions );

		return apply_filters( 'bbp_get_topic_trash_link', $link_before . $actions . $link_after, $args );
	}

/**
 * Output the close link of the topic
 *
 * @since bbPress (r2727)
 *
 * @param mixed $args See {@link bbp_get_topic_close_link()}
 * @uses bbp_get_topic_close_link() To get the topic close link
 */
function bbp_topic_close_link( $args = '' ) {
	echo bbp_get_topic_close_link( $args );
}

	/**
	 * Return the close link of the topic
	 *
	 * @since bbPress (r2727)
	 *
	 * @param mixed $args This function supports these args:
	 *  - id: Optional. Topic id
	 *  - link_before: Before the link
	 *  - link_after: After the link
	 *  - close_text: Close text
	 *  - open_text: Open text
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic() To get the topic
	 * @uses current_user_can() To check if the current user can edit the
	 *                           topic
	 * @uses bbp_is_topic_open() To check if the topic is open
	 * @uses add_query_arg() To add custom args to the url
	 * @uses wp_nonce_url() To nonce the url
	 * @uses esc_url() To escape the url
	 * @uses apply_filters() Calls 'bbp_get_topic_close_link' with the link
	 *                        and args
	 * @return string Topic close link
	 */
	function bbp_get_topic_close_link( $args = '' ) {
		$defaults = array (
			'id'          => 0,
			'link_before' => '',
			'link_after'  => '',
			'sep'         => ' | ',
			'close_text'  => __( 'Close', 'bbpress' ),
			'open_text'   => __( 'Open',  'bbpress' )
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r );

		$topic = bbp_get_topic( bbp_get_topic_id( (int) $id ) );

		if ( empty( $topic ) || !current_user_can( 'moderate', $topic->ID ) )
			return;

		$display = bbp_is_topic_open( $topic->ID ) ? $close_text : $open_text;

		$uri = add_query_arg( array( 'action' => 'bbp_toggle_topic_close', 'topic_id' => $topic->ID ) );
		$uri = esc_url( wp_nonce_url( $uri, 'close-topic_' . $topic->ID ) );

		return apply_filters( 'bbp_get_topic_close_link', $link_before . '<a href="' . $uri . '">' . $display . '</a>' . $link_after, $args );
	}

/**
 * Output the stick link of the topic
 *
 * @since bbPress (r2754)
 *
 * @param mixed $args See {@link bbp_get_topic_stick_link()}
 * @uses bbp_get_topic_stick_link() To get the topic stick link
 */
function bbp_topic_stick_link( $args = '' ) {
	echo bbp_get_topic_stick_link( $args );
}

	/**
	 * Return the stick link of the topic
	 *
	 * @since bbPress (r2754)
	 *
	 * @param mixed $args This function supports these args:
	 *  - id: Optional. Topic id
	 *  - link_before: Before the link
	 *  - link_after: After the link
	 *  - stick_text: Stick text
	 *  - unstick_text: Unstick text
	 *  - super_text: Stick to front text
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic() To get the topic
	 * @uses current_user_can() To check if the current user can edit the
	 *                           topic
	 * @uses bbp_is_topic_sticky() To check if the topic is a sticky
	 * @uses add_query_arg() To add custom args to the url
	 * @uses wp_nonce_url() To nonce the url
	 * @uses esc_url() To escape the url
	 * @uses apply_filters() Calls 'bbp_get_topic_stick_link' with the link
	 *                        and args
	 * @return string Topic stick link
	 */
	function bbp_get_topic_stick_link( $args = '' ) {
		$defaults = array (
			'id'           => 0,
			'link_before'  => '',
			'link_after'   => '',
			'stick_text'   => __( 'Stick',    'bbpress' ),
			'unstick_text' => __( 'Unstick',  'bbpress' ),
			'super_text'   => __( 'to front', 'bbpress' ),
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r );

		$topic = bbp_get_topic( bbp_get_topic_id( (int) $id ) );

		if ( empty( $topic ) || !current_user_can( 'moderate', $topic->ID ) )
			return;

		$is_sticky = bbp_is_topic_sticky( $topic->ID );

		$stick_uri = add_query_arg( array( 'action' => 'bbp_toggle_topic_stick', 'topic_id' => $topic->ID ) );
		$stick_uri = esc_url( wp_nonce_url( $stick_uri, 'stick-topic_' . $topic->ID ) );

		$stick_display = true == $is_sticky ? $unstick_text : $stick_text;
		$stick_display = '<a href="' . $stick_uri . '">' . $stick_display . '</a>';

		if ( empty( $is_sticky ) ) {
			$super_uri = add_query_arg( array( 'action' => 'bbp_toggle_topic_stick', 'topic_id' => $topic->ID, 'super' => 1 ) );
			$super_uri = esc_url( wp_nonce_url( $super_uri, 'stick-topic_' . $topic->ID ) );

			$super_display = ' (<a href="' . $super_uri . '">' . $super_text . '</a>)';
		} else {
			$super_display = '';
		}

		return apply_filters( 'bbp_get_topic_stick_link', $link_before . $stick_display . $super_display . $link_after, $args );
	}

/**
 * Output the merge link of the topic
 *
 * @since bbPress (r2756)
 *
 * @param mixed $args
 * @uses bbp_get_topic_merge_link() To get the topic merge link
 */
function bbp_topic_merge_link( $args = '' ) {
	echo bbp_get_topic_merge_link( $args );
}

	/**
	 * Return the merge link of the topic
	 *
	 * @since bbPress (r2756)
	 *
	 * @param mixed $args This function supports these args:
	 *  - id: Optional. Topic id
	 *  - link_before: Before the link
	 *  - link_after: After the link
	 *  - merge_text: Merge text
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic() To get the topic
	 * @uses bbp_get_topic_edit_url() To get the topic edit url
	 * @uses add_query_arg() To add custom args to the url
	 * @uses esc_url() To escape the url
	 * @uses apply_filters() Calls 'bbp_get_topic_merge_link' with the link
	 *                        and args
	 * @return string Topic merge link
	 */
	function bbp_get_topic_merge_link( $args = '' ) {
		$defaults = array (
			'id'           => 0,
			'link_before'  => '',
			'link_after'   => '',
			'merge_text'    => __( 'Merge', 'bbpress' ),
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r );

		$topic = bbp_get_topic( bbp_get_topic_id( (int) $id ) );

		if ( empty( $topic ) || !current_user_can( 'moderate', $topic->ID ) )
			return;

		$uri = esc_url( add_query_arg( array( 'action' => 'merge' ), bbp_get_topic_edit_url( $topic->ID ) ) );

		return apply_filters( 'bbp_get_topic_merge_link', $link_before . '<a href="' . $uri . '">' . $merge_text . '</a>' . $link_after, $args );
	}

/**
 * Output the spam link of the topic
 *
 * @since bbPress (r2727)
 *
 * @param mixed $args See {@link bbp_get_topic_spam_link()}
 * @uses bbp_get_topic_spam_link() Topic spam link
 */
function bbp_topic_spam_link( $args = '' ) {
	echo bbp_get_topic_spam_link( $args );
}

	/**
	 * Return the spam link of the topic
	 *
	 * @since bbPress (r2727)
	 *
	 * @param mixed $args This function supports these args:
	 *  - id: Optional. Topic id
	 *  - link_before: Before the link
	 *  - link_after: After the link
	 *  - spam_text: Spam text
	 *  - unspam_text: Unspam text
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic() To get the topic
	 * @uses current_user_can() To check if the current user can edit the
	 *                           topic
	 * @uses bbp_is_topic_spam() To check if the topic is marked as spam
	 * @uses add_query_arg() To add custom args to the url
	 * @uses wp_nonce_url() To nonce the url
	 * @uses esc_url() To escape the url
	 * @uses apply_filters() Calls 'bbp_get_topic_spam_link' with the link
	 *                        and args
	 * @return string Topic spam link
	 */
	function bbp_get_topic_spam_link( $args = '' ) {
		$defaults = array (
			'id'           => 0,
			'link_before'  => '',
			'link_after'   => '',
			'sep'          => ' | ',
			'spam_text'    => __( 'Spam',   'bbpress' ),
			'unspam_text'  => __( 'Unspam', 'bbpress' )
		);

		$r = wp_parse_args( $args, $defaults );
		extract( $r );

		$topic = bbp_get_topic( bbp_get_topic_id( (int) $id ) );

		if ( empty( $topic ) || !current_user_can( 'moderate', $topic->ID ) )
			return;

		$display = bbp_is_topic_spam( $topic->ID ) ? $unspam_text : $spam_text;

		$uri = add_query_arg( array( 'action' => 'bbp_toggle_topic_spam', 'topic_id' => $topic->ID ) );
		$uri = esc_url( wp_nonce_url( $uri, 'spam-topic_' . $topic->ID ) );

		return apply_filters( 'bbp_get_topic_spam_link', $link_before . '<a href="' . $uri . '">' . $display . '</a>' . $link_after, $args );
	}

/** Topic Pagination **********************************************************/

/**
 * Output the pagination count
 *
 * @since bbPress (r2519)
 *
 * @uses bbp_get_forum_pagination_count() To get the forum pagination count
 */
function bbp_forum_pagination_count() {
	echo bbp_get_forum_pagination_count();
}
	/**
	 * Return the pagination count
	 *
	 * @since bbPress (r2519)
	 *
	 * @uses bbp_number_format() To format the number value
	 * @uses apply_filters() Calls 'bbp_get_forum_pagination_count' with the
	 *                        pagination count
	 * @return string Forum Pagintion count
	 */
	function bbp_get_forum_pagination_count() {
		global $bbp;

		if ( empty( $bbp->topic_query ) )
			return false;

		// Set pagination values
		$start_num = intval( ( $bbp->topic_query->paged - 1 ) * $bbp->topic_query->posts_per_page ) + 1;
		$from_num  = bbp_number_format( $start_num );
		$to_num    = bbp_number_format( ( $start_num + ( $bbp->topic_query->posts_per_page - 1 ) > $bbp->topic_query->found_posts ) ? $bbp->topic_query->found_posts : $start_num + ( $bbp->topic_query->posts_per_page - 1 ) );
		$total     = bbp_number_format( !empty( $bbp->topic_query->found_posts ) ? $bbp->topic_query->found_posts : $bbp->topic_query->post_count );

		/**
		 * Translators - _n() should not be needed, as singular/plural strings
		 * are already separated into unique strings for you
		 */

		// More than one topic
		if ( $total > 1 ) {

			// Single topic in a forum with several pages
			if ( (int) $from_num == (int) $to_num ) {
				$retstr = sprintf( __( 'Viewing topic %1$s (of %2$s total)', 'bbpress' ), $from_num, $total );

			// Several topics in a forum with a single page
			} elseif ( empty( $to_num ) ) {
				$retstr = sprintf( __( 'Viewing %1$s topics', 'bbpress' ), $total );

			// Several topics in a forum with several pages
			} elseif ( (int) $from_num != (int) $to_num ) {
				$retstr = sprintf( __( 'Viewing %1$s topics - %2$s through %3$s (of %4$s total)', 'bbpress' ), $bbp->topic_query->post_count, $from_num, $to_num, $total );
			}

		// Only 1 topic
		} else {
			$retstr = sprintf( __( 'Viewing %1$s topic', 'bbpress' ), $total );
		}

		// Filter and return
		return apply_filters( 'bbp_get_topic_pagination_count', $retstr );
	}

/**
 * Output pagination links
 *
 * @since bbPress (r2519)
 *
 * @uses bbp_get_forum_pagination_links() To get the pagination links
 */
function bbp_forum_pagination_links() {
	echo bbp_get_forum_pagination_links();
}
	/**
	 * Return pagination links
	 *
	 * @since bbPress (r2519)
	 *
	 * @uses bbPress::topic_query::pagination_links To get the links
	 * @return string Pagination links
	 */
	function bbp_get_forum_pagination_links() {
		global $bbp;

		if ( empty( $bbp->topic_query ) )
			return false;

		return apply_filters( 'bbp_get_forum_pagination_links', $bbp->topic_query->pagination_links );
	}

/**
 * Displays topic notices
 *
 * @since bbPress (r2744)
 *
 * @uses bbp_is_single_topic() To check if it's a topic page
 * @uses bbp_get_topic_status() To get the topic status
 * @uses bbp_get_topic_id() To get the topic id
 * @uses apply_filters() Calls 'bbp_topic_notices' with the notice text, topic
 *                        status and topic id
 * @uses bbPress::errors::add() To add the notices to the error handler
 */
function bbp_topic_notices() {

	// Bail if not viewing a topic
	if ( !bbp_is_single_topic() )
		return;

	// Get the topic_status
	$topic_status = bbp_get_topic_status();

	// Get the topic status
	switch ( $topic_status ) {

		// Spam notice
		case bbp_get_spam_status_id() :
			$notice_text = __( 'This topic is marked as spam.', 'bbpress' );
			break;

		// Trashed notice
		case bbp_get_trash_status_id() :
			$notice_text = __( 'This topic is in the trash.',   'bbpress' );
			break;

		// Standard status
		default :
			$notice_text = '';
			break;
	}

	// Filter notice text and bail if empty
	if ( !$notice_text = apply_filters( 'bbp_topic_notices', $notice_text, $topic_status, bbp_get_topic_id() ) )
		return;

	bbp_add_error( 'topic_notice', $notice_text, 'message' );
}

/**
 * Displays topic type select box (normal/sticky/super sticky)
 *
 * @since bbPress (r2784)
 *
 * @param $args This function supports these arguments:
 *  - stick_text: Sticky text
 *  - super_text: Super Sticky text
 *  - unstick_text: Unstick (normal) text
 *  - select_id: Select id. Defaults to bbp_stick_topic
 *  - tab: Tabindex
 *  - topic_id: Topic id
 * @uses bbp_get_topic_id() To get the topic id
 * @uses bbp_is_topic_edit() To check if it is the topic edit page
 * @uses bbp_is_topic_super_sticky() To check if the topic is a super sticky
 * @uses bbp_is_topic_sticky() To check if the topic is a sticky
 */
function bbp_topic_type_select( $args = '' ) {

	$defaults = array (
		'unstick_text' => __( 'Normal',       'bbpress' ),
		'stick_text'   => __( 'Sticky',       'bbpress' ),
		'super_text'   => __( 'Super Sticky', 'bbpress' ),
		'select_id'    => 'bbp_stick_topic',
		'tab'          => bbp_get_tab_index(),
		'topic_id'     => 0
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );

	// Get current topic id
	$topic_id = bbp_get_topic_id( $topic_id );

	// Edit topic
	if ( bbp_is_topic_edit() ) {

		// Post value is passed
		if ( 'post' == strtolower( $_SERVER['REQUEST_METHOD'] ) && isset( $_POST[$select_id] ) ) {
			$sticky_current = $_POST[$select_id];

		// Topic is super sticky
		} elseif ( bbp_is_topic_super_sticky( $topic_id ) ) {
			$sticky_current = 'super';

		// Topic is sticky or normal
		} else {
			$sticky_current = bbp_is_topic_sticky( $topic_id, false ) ? 'stick' : 'unstick';
		}

	// New topic
	} else {

		// Post value is passed
		if ( 'post' == strtolower( $_SERVER['REQUEST_METHOD'] ) && isset( $_POST[$select_id] ) ) {
			$sticky_current = $_POST[$select_id];

		// Default to unstick
		} else {
			$sticky_current = 'unstick';
		}
	}

	// Used variables
	$tab             = !empty( $tab ) ? ' tabindex="' . $tab . '"' : '';
	$select_id       = esc_attr( $select_id );
	$sticky_statuses = array (
		'unstick' => $unstick_text,
		'stick'   => $stick_text,
		'super'   => $super_text,
	); ?>

	<select name="<?php echo $select_id; ?>" id="<?php echo $select_id; ?>"<?php echo $tab; ?>>

		<?php foreach ( $sticky_statuses as $sticky_status => $label ) : ?>

			<option value="<?php echo $sticky_status; ?>"<?php selected( $sticky_current, $sticky_status ); ?>><?php echo $label; ?></option>

		<?php endforeach; ?>

	</select>

	<?php
}

/** Single Topic **************************************************************/

/**
 * Output a fancy description of the current topic, including total topics,
 * total replies, and last activity.
 *
 * @since bbPress (r2860)
 *
 * @param array $args See {@link bbp_get_single_topic_description()}
 * @uses bbp_get_single_topic_description() Return the eventual output
 */
function bbp_single_topic_description( $args = '' ) {
	echo bbp_get_single_topic_description( $args );
}
	/**
	 * Return a fancy description of the current topic, including total topics,
	 * total replies, and last activity.
	 *
	 * @since bbPress (r2860)
	 *
	 * @param mixed $args This function supports these arguments:
	 *  - topic_id: Topic id
	 *  - before: Before the text
	 *  - after: After the text
	 *  - size: Size of the avatar
	 * @uses bbp_get_topic_id() To get the topic id
	 * @uses bbp_get_topic_voice_count() To get the topic voice count
	 * @uses bbp_get_topic_reply_count() To get the topic reply count
	 * @uses bbp_get_topic_freshness_link() To get the topic freshness link
	 * @uses bbp_get_topic_last_active_id() To get the topic last active id
	 * @uses bbp_get_reply_author_link() To get the reply author link
	 * @uses apply_filters() Calls 'bbp_get_single_topic_description' with
	 *                        the description and args
	 * @return string Filtered topic description
	 */
	function bbp_get_single_topic_description( $args = '' ) {
		// Default arguments
		$defaults = array (
			'topic_id'  => 0,
			'before'    => '<div class="bbp-template-notice info"><p class="bbp-topic-description">',
			'after'     => '</p></div>',
			'size'      => 14
		);
		$r = wp_parse_args( $args, $defaults );
		extract( $r );

		// Validate topic_id
		$topic_id = bbp_get_topic_id( $topic_id );

		// Unhook the 'view all' query var adder
		remove_filter( 'bbp_get_topic_permalink', 'bbp_add_view_all' );

		// Build the topic description
		$forum_id        = bbp_get_topic_forum_id      ( $topic_id );
		$voice_count     = bbp_get_topic_voice_count   ( $topic_id );
		$reply_count     = bbp_get_topic_replies_link  ( $topic_id );
		$time_since      = bbp_get_topic_freshness_link( $topic_id );

		// Singular/Plural
		$voice_count     = sprintf( _n( '%s voice', '%s voices', $voice_count, 'bbpress' ), $voice_count );

		// Topic has replies
		if ( $last_reply = bbp_get_topic_last_active_id( $topic_id ) ) {
			$last_updated_by = bbp_get_author_link( array( 'post_id' => $last_reply, 'size' => $size ) );
			$retstr = sprintf( __( 'This topic has %1$s, contains %2$s, and was last updated by %3$s %4$s ago.', 'bbpress' ), $voice_count, $reply_count, $last_updated_by, $time_since );

		// Topic has no replies
		} else {
			$retstr = sprintf( __( 'This topic has %1$s, contains %2$s.', 'bbpress' ), $voice_count, $reply_count );
		}

		// Add the 'view all' filter back
		add_filter( 'bbp_get_topic_permalink', 'bbp_add_view_all' );

		// Combine the elements together
		$retstr = $before . $retstr . $after;

		// Return filtered result
		return apply_filters( 'bbp_get_single_topic_description', $retstr, $args );
	}

/** Topic Tags ****************************************************************/

/**
 * Output the unique id of the topic tag taxonomy
 *
 * @since bbPress (r3348)
 *
 * @uses bbp_get_topic_post_type() To get the topic post type
 */
function bbp_topic_tag_tax_id() {
	echo bbp_get_topic_tag_tax_id();
}
	/**
	 * Return the unique id of the topic tag taxonomy
	 *
	 * @since bbPress (r3348)
	 *
	 * @uses apply_filters() Calls 'bbp_get_topic_tag_tax_id' with the topic tax id
	 * @return string The unique topic tag taxonomy
	 */
	function bbp_get_topic_tag_tax_id() {
		global $bbp;

		return apply_filters( 'bbp_get_topic_tag_tax_id', $bbp->topic_tag_tax_id );
	}

/**
 * Output the id of the current tag
 *
 * @since bbPress (r3109)
 *
 * @uses bbp_get_topic_tag_id()
 */
function bbp_topic_tag_id( $tag = '' ) {
	echo bbp_get_topic_tag_id( $tag );
}
	/**
	 * Return the id of the current tag
	 *
	 * @since bbPress (r3109)
	 *
	 * @uses get_term_by()
	 * @uses get_query_var()
	 * @uses apply_filters()
	 *
	 * @return string Term Name
	 */
	function bbp_get_topic_tag_id( $tag = '' ) {

		// Get the term
		$tag  = !empty( $tag ) ? $tag : get_query_var( 'term' );
		$term = get_term_by( 'slug', $tag, bbp_get_topic_tag_tax_id() );

		// Add before and after if description exists
		if ( !empty( $term->term_id ) )
			$retval = $term->term_id;

		// No id
		else
			$retval = '';

		return apply_filters( 'bbp_get_topic_tag_id', (int) $retval );
	}

/**
 * Output the name of the current tag
 *
 * @since bbPress (r3109)
 *
 * @uses bbp_get_topic_tag_name()
 */
function bbp_topic_tag_name( $tag = '' ) {
	echo bbp_get_topic_tag_name( $tag );
}
	/**
	 * Return the name of the current tag
	 *
	 * @since bbPress (r3109)
	 *
	 * @uses get_term_by()
	 * @uses get_query_var()
	 * @uses apply_filters()
	 *
	 * @return string Term Name
	 */
	function bbp_get_topic_tag_name( $tag = '' ) {

		// Get the term
		$tag  = !empty( $tag ) ? $tag : get_query_var( 'term' );
		$term = get_term_by( 'slug', $tag, bbp_get_topic_tag_tax_id() );

		// Add before and after if description exists
		if ( !empty( $term->name ) )
			$retval = $term->name;

		// No name
		else
			$retval = '';

		return apply_filters( 'bbp_get_topic_tag_name', $retval );
	}

/**
 * Output the slug of the current tag
 *
 * @since bbPress (r3109)
 *
 * @uses bbp_get_topic_tag_slug()
 */
function bbp_topic_tag_slug( $tag = '' ) {
	echo bbp_get_topic_tag_slug( $tag );
}
	/**
	 * Return the slug of the current tag
	 *
	 * @since bbPress (r3109)
	 *
	 * @uses get_term_by()
	 * @uses get_query_var()
	 * @uses apply_filters()
	 *
	 * @return string Term Name
	 */
	function bbp_get_topic_tag_slug( $tag = '' ) {

		// Get the term
		$tag  = !empty( $tag ) ? $tag : get_query_var( 'term' );
		$term = get_term_by( 'slug', $tag, bbp_get_topic_tag_tax_id() );

		// Add before and after if description exists
		if ( !empty( $term->slug ) )
			$retval = $term->slug;

		// No slug
		else
			$retval = '';

		return apply_filters( 'bbp_get_topic_tag_slug', $retval );
	}

/**
 * Output the link of the current tag
 *
 * @since bbPress (r3348)
 *
 * @uses bbp_get_topic_tag_link()
 */
function bbp_topic_tag_link( $tag = '' ) {
	echo bbp_get_topic_tag_link( $tag );
}
	/**
	 * Return the link of the current tag
	 *
	 * @since bbPress (r3348)
	 *
	 * @uses get_term_by()
	 * @uses get_query_var()
	 * @uses apply_filters()
	 *
	 * @return string Term Name
	 */
	function bbp_get_topic_tag_link( $tag = '' ) {

		// Get the term
		$tag  = !empty( $tag ) ? $tag : get_query_var( 'term' );
		$term = get_term_by( 'slug', $tag, bbp_get_topic_tag_tax_id() );

		// Add before and after if description exists
		if ( !empty( $term->term_id ) )
			$retval = get_term_link( $term, bbp_get_topic_tag_tax_id() );

		// No link
		else
			$retval = '';

		return apply_filters( 'bbp_get_topic_tag_link', $retval );
	}

/**
 * Output the link of the current tag
 *
 * @since bbPress (r3348)
 *
 * @uses bbp_get_topic_tag_edit_link()
 */
function bbp_topic_tag_edit_link( $tag = '' ) {
	echo bbp_get_topic_tag_edit_link( $tag );
}
	/**
	 * Return the link of the current tag
	 *
	 * @since bbPress (r3348)
	 *
	 * @uses get_term_by()
	 * @uses get_query_var()
	 * @uses apply_filters()
	 *
	 * @return string Term Name
	 */
	function bbp_get_topic_tag_edit_link( $tag = '' ) {
		global $wp_query, $wp_rewrite;

		// Get the term
		$tag  = !empty( $tag ) ? $tag : get_query_var( 'term' );
		$term = get_term_by( 'slug', $tag, bbp_get_topic_tag_tax_id() );

		// Add before and after if description exists
		if ( !empty( $term->term_id ) ) {

			// Pretty
			if ( $wp_rewrite->using_permalinks() ) {
				$retval = user_trailingslashit( trailingslashit( bbp_get_topic_tag_link() ) . 'edit' );

			// Ugly
			} else {
				$retval = add_query_arg( array( 'edit' => '1' ), bbp_get_topic_tag_link() );
			}

		// No link
		} else {
			$retval = '';
		}

		return apply_filters( 'bbp_get_topic_tag_edit_link', $retval );
	}

/**
 * Output the description of the current tag
 *
 * @since bbPress (r3109)
 *
 * @uses bbp_get_topic_tag_description()
 */
function bbp_topic_tag_description( $args = array() ) {
	echo bbp_get_topic_tag_description( $args );
}
	/**
	 * Return the description of the current tag
	 *
	 * @since bbPress (r3109)
	 *
	 * @uses get_term_by()
	 * @uses get_query_var()
	 * @uses apply_filters()
	 *
	 * @return string Term Name
	 */
	function bbp_get_topic_tag_description( $args = array() ) {

		$defaults = array(
			'before' => '<div class="bbp-topic-tag-description"><p>',
			'after'  => '</p></div>',
			'tag'    => ''
		);
		$r = wp_parse_args( $args, $defaults );
		extract( $r );

		// Get the term
		$tag  = !empty( $tag ) ? $tag : get_query_var( 'term' );
		$term = get_term_by( 'slug', $tag, bbp_get_topic_tag_tax_id() );

		// Add before and after if description exists
		if ( !empty( $term->description ) )
			$retval = $before . $term->description . $after;

		// No description, no HTML
		else
			$retval = '';

		return apply_filters( 'bbp_get_topic_tag_description', $retval, $args );
	}

/** Forms *********************************************************************/

/**
 * Output the value of topic title field
 *
 * @since bbPress (r2976)
 *
 * @uses bbp_get_form_topic_title() To get the value of topic title field
 */
function bbp_form_topic_title() {
	echo bbp_get_form_topic_title();
}
	/**
	 * Return the value of topic title field
	 *
	 * @since bbPress (r2976)
	 *
	 * @uses bbp_is_topic_edit() To check if it's topic edit page
	 * @uses apply_filters() Calls 'bbp_get_form_topic_title' with the title
	 * @return string Value of topic title field
	 */
	function bbp_get_form_topic_title() {
		global $post;

		// Get _POST data
		if ( 'post' == strtolower( $_SERVER['REQUEST_METHOD'] ) && isset( $_POST['bbp_topic_title'] ) )
			$topic_title = $_POST['bbp_topic_title'];

		// Get edit data
		elseif ( !empty( $post->post_title ) && bbp_is_topic_edit() )
			$topic_title = $post->post_title;

		// No data
		else
			$topic_title = '';

		return apply_filters( 'bbp_get_form_topic_title', esc_attr( $topic_title ) );
	}

/**
 * Output the value of topic content field
 *
 * @since bbPress (r2976)
 *
 * @uses bbp_get_form_topic_content() To get value of topic content field
 */
function bbp_form_topic_content() {
	echo bbp_get_form_topic_content();
}
	/**
	 * Return the value of topic content field
	 *
	 * @since bbPress (r2976)
	 *
	 * @uses bbp_is_topic_edit() To check if it's the topic edit page
	 * @uses apply_filters() Calls 'bbp_get_form_topic_content' with the content
	 * @return string Value of topic content field
	 */
	function bbp_get_form_topic_content() {
		global $post;

		// Get _POST data
		if ( 'post' == strtolower( $_SERVER['REQUEST_METHOD'] ) && isset( $_POST['bbp_topic_content'] ) )
			$topic_content = $_POST['bbp_topic_content'];

		// Get edit data
		elseif ( !empty( $post->post_title ) && bbp_is_topic_edit() )
			$topic_content = $post->post_content;

		// No data
		else
			$topic_content = '';

		return apply_filters( 'bbp_get_form_topic_content', esc_textarea( $topic_content ) );
	}

/**
 * Output value of topic tags field
 *
 * @since bbPress (r2976)
 * @uses bbp_get_form_topic_tags() To get the value of topic tags field
 */
function bbp_form_topic_tags() {
	echo bbp_get_form_topic_tags();
}
	/**
	 * Return value of topic tags field
	 *
	 * @since bbPress (r2976)
	 *
	 * @uses bbp_is_topic_edit() To check if it's the topic edit page
	 * @uses apply_filters() Calls 'bbp_get_form_topic_tags' with the tags
	 * @return string Value of topic tags field
	 */
	function bbp_get_form_topic_tags() {
		global $post;

		// Get _POST data
		if ( 'post' == strtolower( $_SERVER['REQUEST_METHOD'] ) && isset( $_POST['bbp_topic_tags'] ) ) {
			$topic_tags = $_POST['bbp_topic_tags'];

		// Get edit data
		} elseif ( !empty( $post ) ) {

			// Post is a topic
			if ( bbp_get_topic_post_type() == $post->post_type ) {
				$topic_id = $post->ID;

			// Post is a reply
			} elseif ( bbp_get_reply_post_type() == $post->post_type ) {
				$topic_id = bbp_get_reply_topic_id( $post->ID );
			}

			// Topic exists
			if ( !empty( $topic_id ) ) {

				// Topic is spammed so display pre-spam terms
				if ( bbp_is_topic_spam( $topic_id ) ) {

					// Get pre-spam terms
					$new_terms = get_post_meta( $topic_id, '_bbp_spam_topic_tags', true );

					// If terms exist, explode them and compile the return value
					if ( empty( $new_terms ) ) {
						$new_terms = '';
					}

				// Topic is not spam so get real terms
				} else {
					$terms = array_filter( (array) get_the_terms( $topic_id, bbp_get_topic_tag_tax_id() ) );

					// Loop through them
					foreach( $terms as $term ) {
						$new_terms[] = $term->name;
					}
				}

			// Define local variable(s)
			} else {
				$new_terms = '';
			}

			// Set the return value
			$topic_tags = ( !empty( $new_terms ) ) ? implode( ', ', $new_terms ) : '';

		// No data
		} else {
			$topic_tags = '';
		}

		return apply_filters( 'bbp_get_form_topic_tags', esc_attr( $topic_tags ) );
	}

/**
 * Output value of topic forum
 *
 * @since bbPress (r2976)
 *
 * @uses bbp_get_form_topic_forum() To get the topic's forum id
 */
function bbp_form_topic_forum() {
	echo bbp_get_form_topic_forum();
}
	/**
	 * Return value of topic forum
	 *
	 * @since bbPress (r2976)
	 *
	 * @uses bbp_is_topic_edit() To check if it's the topic edit page
	 * @uses bbp_get_topic_forum_id() To get the topic forum id
	 * @uses apply_filters() Calls 'bbp_get_form_topic_forum' with the forum
	 * @return string Value of topic content field
	 */
	function bbp_get_form_topic_forum() {

		// Get _POST data
		if ( 'post' == strtolower( $_SERVER['REQUEST_METHOD'] ) && isset( $_POST['bbp_forum_id'] ) )
			$topic_forum = $_POST['bbp_forum_id'];

		// Get edit data
		elseif ( bbp_is_topic_edit() )
			$topic_forum = bbp_get_topic_forum_id();

		// No data
		else
			$topic_forum = 0;

		return apply_filters( 'bbp_get_form_topic_forum', esc_attr( $topic_forum ) );
	}

/**
 * Output checked value of topic subscription
 *
 * @since bbPress (r2976)
 *
 * @uses bbp_get_form_topic_subscribed() To get the subscribed checkbox value
 */
function bbp_form_topic_subscribed() {
	echo bbp_get_form_topic_subscribed();
}
	/**
	 * Return checked value of topic subscription
	 *
	 * @since bbPress (r2976)
	 *
	 * @uses bbp_is_topic_edit() To check if it's the topic edit page
	 * @uses bbp_is_user_subscribed() To check if the user is subscribed to
	 *                                 the topic
	 * @uses apply_filters() Calls 'bbp_get_form_topic_subscribed' with the
	 *                        option
	 * @return string Checked value of topic subscription
	 */
	function bbp_get_form_topic_subscribed() {
		global $post;

		// Get _POST data
		if ( 'post' == strtolower( $_SERVER['REQUEST_METHOD'] ) && isset( $_POST['bbp_topic_subscription'] ) ) {
			$topic_subscribed = $_POST['bbp_topic_subscription'];

		// Get edit data
		} elseif ( bbp_is_topic_edit() || bbp_is_reply_edit() ) {

			// Post author is not the current user
			if ( $post->post_author != bbp_get_current_user_id() ) {
				$topic_subscribed = bbp_is_user_subscribed( $post->post_author );

			// Post author is the current user
			} else {
				$topic_subscribed = bbp_is_user_subscribed( bbp_get_current_user_id() );
			}

		// Get current status
		} elseif ( bbp_is_single_topic() ) {
			$topic_subscribed = bbp_is_user_subscribed( bbp_get_current_user_id() );

		// No data
		} else {
			$topic_subscribed = 0;
		}

		// Get checked output
		$checked = checked( $topic_subscribed, true, false );

		return apply_filters( 'bbp_get_form_topic_subscribed', $checked, $topic_subscribed );
	}

/**
 * Output checked value of topic log edit field
 *
 * @since bbPress (r2976)
 *
 * @uses bbp_get_form_topic_log_edit() To get the topic log edit value
 */
function bbp_form_topic_log_edit() {
	echo bbp_get_form_topic_log_edit();
}
	/**
	 * Return checked value of topic log edit field
	 *
	 * @since bbPress (r2976)
	 *
	 * @uses apply_filters() Calls 'bbp_get_form_topic_log_edit' with the
	 *                        log edit value
	 * @return string Topic log edit checked value
	 */
	function bbp_get_form_topic_log_edit() {
		global $post;

		// Get _POST data
		if ( 'post' == strtolower( $_SERVER['REQUEST_METHOD'] ) && isset( $_POST['bbp_log_topic_edit'] ) )
			$topic_revision = $_POST['bbp_log_topic_edit'];

		// No data
		else
			$topic_revision = 1;

		return apply_filters( 'bbp_get_form_topic_log_edit', checked( $topic_revision, true, false ) );
	}

/**
 * Output the value of the topic edit reason
 *
 * @since bbPress (r2976)
 *
 * @uses bbp_get_form_topic_edit_reason() To get the topic edit reason value
 */
function bbp_form_topic_edit_reason() {
	echo bbp_get_form_topic_edit_reason();
}
	/**
	 * Return the value of the topic edit reason
	 *
	 * @since bbPress (r2976)
	 *
	 * @uses apply_filters() Calls 'bbp_get_form_topic_edit_reason' with the
	 *                        topic edit reason value
	 * @return string Topic edit reason value
	 */
	function bbp_get_form_topic_edit_reason() {
		global $post;

		// Get _POST data
		if ( 'post' == strtolower( $_SERVER['REQUEST_METHOD'] ) && isset( $_POST['bbp_topic_edit_reason'] ) )
			$topic_edit_reason = $_POST['bbp_topic_edit_reason'];

		// No data
		else
			$topic_edit_reason = '';

		return apply_filters( 'bbp_get_form_topic_edit_reason', esc_attr( $topic_edit_reason ) );
	}

?>
