<?php

/**
 * The bbPress Plugin
 *
 * bbPress is forum software with a twist from the creators of WordPress.
 *
 * @package bbPress
 * @subpackage Main
 */

/**
 * Plugin Name: bbPress
 * Plugin URI:  http://bbpress.org
 * Description: bbPress is forum software with a twist from the creators of WordPress.
 * Author:      The bbPress Community
 * Author URI:  http://bbpress.org
 * Version:     2.0.2
 * Text Domain: bbpress
 * Domain Path: /bbp-languages/
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'bbPress' ) ) :
/**
 * Main bbPress Class
 *
 * Tap tap tap... Is this thing on?
 *
 * @since bbPress (r2464)
 */
class bbPress {

	/**
	 * Note to Plugin and Theme authors:
	 *
	 * Do not directly reference the variables below in your code. Their names
	 * and locations in the bbPress class are subject to change at any time.
	 *
	 * Most of them have reference functions located in bbp-includes. The ones
	 * that don't can be accessed via their respective WordPress API's.
	 */

	/** Version ***************************************************************/

	/**
	 * @public string bbPress version
	 */
	public $version = '2.0.2';

	/**
	 * @public string bbPress DB version
	 */
	public $db_version = '202';

	/** Post types ************************************************************/

	/**
	 * @public string Forum post type id
	 */
	public $forum_post_type = '';

	/**
	 * @public string Topic post type id
	 */
	public $topic_post_type = '';

	/**
	 * @ string Reply post type id
	 */
	public $reply_post_type = '';

	/** Taxonomies ************************************************************/

	/**
	 * @public string Topic tag id
	 */
	public $topic_tag_tax_id = '';

	/** Permastructs **********************************************************/

	/**
	 * @public string User struct
	 */
	public $user_id = '';

	/**
	 * @public string View struct
	 */
	public $view_id = '';

	/**
	 * @public string Edit struct
	 */
	public $edit_id = '';

	/** Post statuses *********************************************************/

	/**
	 * @public string Closed post status id. Used by topics.
	 */
	public $closed_status_id = '';

	/**
	 * @public string Spam post status id. Used by topics and replies.
	 */
	public $spam_status_id = '';

	/**
	 * @public string Trash post status id. Used by topics and replies.
	 */
	public $trash_status_id = '';

	/**
	 * @public string Orphan post status id. Used by topics and replies.
	 */
	public $orphan_status_id = '';

	/**
	 * @public string Hidden post status id. Used by forums.
	 */
	public $hidden_status_id = '';

	/** Slugs *****************************************************************/

	/**
	 * @public string Root slug
	 */
	public $root_slug = '';

	/**
	 * @public string Forum slug
	 */
	public $forum_slug = '';

	/**
	 * @public string Topic slug
	 */
	public $topic_slug = '';

	/**
	 * @public string Topic archive slug
	 */
	public $topic_archive_slug = '';

	/**
	 * @public string Reply slug
	 */
	public $reply_slug = '';

	/**
	 * @public string Topic tag slug
	 */
	public $topic_tag_slug = '';

	/**
	 * @public string User slug
	 */
	public $user_slug = '';

	/**
	 * @public string View slug
	 */
	public $view_slug = '';

	/** Paths *****************************************************************/

	/**
	 * @public string Basename of the bbPress plugin directory
	 */
	public $basename = '';

	/**
	 * @public string Absolute path to the bbPress plugin directory
	 */
	public $plugin_dir = '';

	/**
	 * @public string Absolute path to the bbPress themes directory
	 */
	public $themes_dir = '';

	/**
	 * @public string Absolute path to the bbPress language directory
	 */
	public $lang_dir = '';

	/** URLs ******************************************************************/

	/**
	 * @public string URL to the bbPress plugin directory
	 */
	public $plugin_url = '';

	/**
	 * @public string URL to the bbPress themes directory
	 */
	public $themes_url = '';

	/** Current ID's **********************************************************/

	/**
	 * @public string Current forum id
	 */
	public $current_forum_id = 0;

	/**
	 * @public string Current topic id
	 */
	public $current_topic_id = 0;

	/**
	 * @public string Current reply id
	 */
	public $current_reply_id = 0;

	/** Users *****************************************************************/

	/**
	 * @public object Current user
	 */
	public $current_user = array();

	/**
	 * @public object Displayed user
	 */
	public $displayed_user = array();

	/** Queries ***************************************************************/

	/**
	 * @public WP_Query For forums
	 */
	public $forum_query;

	/**
	 * @public WP_Query For topics
	 */
	public $topic_query;

	/**
	 * @public WP_Query For replies
	 */
	public $reply_query;

	/** Arrays ****************************************************************/

	/**
	 * @public array Sub Forums
	 */
	public $sub_forums = array();

	/** Errors ****************************************************************/

	/**
	 * @public WP_Error Used to log and display errors
	 */
	public $errors = array();

	/** Views *****************************************************************/

	/**
	 * @public array An array of registered bbPress views
	 */
	public $views = array();

	/** Forms *****************************************************************/

	/**
	 * @public int The current tab index for form building
	 */
	public $tab_index = 0;

	/** Theme Compat **********************************************************/

	/**
	 * @public string Theme to use for theme compatibility
	 */
	public $theme_compat = '';

	/** Extensions ************************************************************/

	/**
	 * @public mixed bbPress add-ons should append globals to this
	 */
	public $extend = false;

	/** Option Overload *******************************************************/

	/**
	 * @var array Optional Overloads default options retrieved from get_option()
	 */
	public $options = array();

	/** Functions *************************************************************/

	/**
	 * The main bbPress loader (PHP4 compat)
	 *
	 * @since bbPress (r2464)
	 *
	 * @uses bbPress::__construct() Setup the globals needed
	 */
	public function bbPress() {
		$this->__construct();
	}

	/**
	 * The main bbPress loader
	 *
	 * @since bbPress (r2464)
	 *
	 * @uses bbPress::setup_globals() Setup the globals needed
	 * @uses bbPress::includes() Include the required files
	 * @uses bbPress::setup_actions() Setup the hooks and actions
	 */
	public function __construct() {
		$this->setup_globals();
		$this->includes();
		$this->setup_actions();
	}

	/**
	 * Component global variables
	 *
	 * @since bbPress (r2626)
	 * @access private
	 *
	 * @uses plugin_dir_path() To generate bbPress plugin path
	 * @uses plugin_dir_url() To generate bbPress plugin url
	 * @uses apply_filters() Calls various filters
	 */
	private function setup_globals() {

		/** Paths *************************************************************/

		// bbPress root directory
		$this->file       = __FILE__;
		$this->basename   = plugin_basename( $this->file );
		$this->plugin_dir = plugin_dir_path( $this->file );
		$this->plugin_url = plugin_dir_url ( $this->file );

		// Themes
		$this->themes_dir = $this->plugin_dir . 'bbp-themes';
		$this->themes_url = $this->plugin_url . 'bbp-themes';

		// Languages
		$this->lang_dir   = $this->plugin_dir . 'bbp-languages';

		/** Identifiers *******************************************************/

		// Post type identifiers
		$this->forum_post_type    = apply_filters( 'bbp_forum_post_type',  'forum'     );
		$this->topic_post_type    = apply_filters( 'bbp_topic_post_type',  'topic'     );
		$this->reply_post_type    = apply_filters( 'bbp_reply_post_type',  'reply'     );
		$this->topic_tag_tax_id   = apply_filters( 'bbp_topic_tag_tax_id', 'topic-tag' );

		// Status identifiers
		$this->spam_status_id     = apply_filters( 'bbp_spam_post_status',    'spam'    );
		$this->closed_status_id   = apply_filters( 'bbp_closed_post_status',  'closed'  );
		$this->orphan_status_id   = apply_filters( 'bbp_orphan_post_status',  'orphan'  );
		$this->public_status_id   = apply_filters( 'bbp_public_post_status',  'publish' );
		$this->private_status_id  = apply_filters( 'bbp_private_post_status', 'private' );
		$this->hidden_status_id   = apply_filters( 'bbp_hidden_post_status',  'hidden'  );
		$this->trash_status_id    = apply_filters( 'bbp_trash_post_status',   'trash'   );

		// Other identifiers
		$this->user_id            = apply_filters( 'bbp_user_id', 'bbp_user' );
		$this->view_id            = apply_filters( 'bbp_view_id', 'bbp_view' );
		$this->edit_id            = apply_filters( 'bbp_edit_id', 'edit'     );

		/** Slugs *************************************************************/

		// Root forum slug
		$this->root_slug          = apply_filters( 'bbp_root_slug',          get_option( '_bbp_root_slug',          'forums' ) );
		$this->topic_archive_slug = apply_filters( 'bbp_topic_archive_slug', get_option( '_bbp_topic_archive_slug', 'topics' ) );

		// Should we include the root slug in front of component slugs
		$prefix                   = !empty( $this->root_slug ) && get_option( '_bbp_include_root', true ) ? trailingslashit( $this->root_slug ) : '';

		// Component slugs
		$this->forum_slug         = apply_filters( 'bbp_forum_slug', $prefix . get_option( '_bbp_forum_slug', 'forum' ) );
		$this->topic_slug         = apply_filters( 'bbp_topic_slug', $prefix . get_option( '_bbp_topic_slug', 'topic' ) );
		$this->reply_slug         = apply_filters( 'bbp_reply_slug', $prefix . get_option( '_bbp_reply_slug', 'reply' ) );

		// Taxonomy slugs
		$this->topic_tag_slug     = apply_filters( 'bbp_topic_tag_slug', $prefix . get_option( '_bbp_topic_tag_slug', 'topic-tag'   ) );

		/** Other Slugs *******************************************************/

		$this->user_slug          = apply_filters( 'bbp_user_slug', $prefix . get_option( '_bbp_user_slug', 'user' ) );
		$this->view_slug          = apply_filters( 'bbp_view_slug', $prefix . get_option( '_bbp_view_slug', 'view' ) );

		/** Queries ***********************************************************/
		
		$this->forum_query        = new stdClass;
		$this->topic_query        = new stdClass;
		$this->reply_query        = new stdClass;

		/** Misc **************************************************************/

		// Errors
		$this->errors             = new WP_Error();

		// Views
		$this->views              = array();

		// Tab Index
		$this->tab_index          = apply_filters( 'bbp_default_tab_index', 100 );

		/** Cache *************************************************************/

		// Add bbPress to global cache groups
		wp_cache_add_global_groups( 'bbpress' );
	}

	/**
	 * Include required files
	 *
	 * @since bbPress (r2626)
	 * @access private
	 *
	 * @uses is_admin() If in WordPress admin, load additional file
	 */
	private function includes() {

		/** Core **************************************************************/

		require( $this->plugin_dir . 'bbp-includes/bbp-core-hooks.php'         ); // All filters and actions
		require( $this->plugin_dir . 'bbp-includes/bbp-core-options.php'       ); // Configuration Options
		require( $this->plugin_dir . 'bbp-includes/bbp-core-caps.php'          ); // Roles and capabilities
		require( $this->plugin_dir . 'bbp-includes/bbp-core-classes.php'       ); // Common classes
		require( $this->plugin_dir . 'bbp-includes/bbp-core-widgets.php'       ); // Sidebar widgets
		require( $this->plugin_dir . 'bbp-includes/bbp-core-shortcodes.php'    ); // Shortcodes for use with pages and posts
		require( $this->plugin_dir . 'bbp-includes/bbp-core-compatibility.php' ); // Theme compatibility for existing themes
		require( $this->plugin_dir . 'bbp-includes/bbp-core-update.php'        ); // Database updater
		
		/** Extensions ********************************************************/
		
		require( $this->plugin_dir . 'bbp-includes/bbp-extend-akismet.php'     ); // Spam prevention for topics and replies
		require( $this->plugin_dir . 'bbp-includes/bbp-extend-buddypress.php'  ); // Social network integration
		require( $this->plugin_dir . 'bbp-includes/bbp-extend-genesis.php'     ); // Popular theme framework

		/** Components ********************************************************/

		require( $this->plugin_dir . 'bbp-includes/bbp-common-functions.php' ); // Common functions
		require( $this->plugin_dir . 'bbp-includes/bbp-common-template.php'  ); // Common template tags

		require( $this->plugin_dir . 'bbp-includes/bbp-forum-functions.php'  ); // Forum functions
		require( $this->plugin_dir . 'bbp-includes/bbp-forum-template.php'   ); // Forum template tags

		require( $this->plugin_dir . 'bbp-includes/bbp-topic-functions.php'  ); // Topic functions
		require( $this->plugin_dir . 'bbp-includes/bbp-topic-template.php'   ); // Topic template tags

		require( $this->plugin_dir . 'bbp-includes/bbp-reply-functions.php'  ); // Reply functions
		require( $this->plugin_dir . 'bbp-includes/bbp-reply-template.php'   ); // Reply template tags

		require( $this->plugin_dir . 'bbp-includes/bbp-user-functions.php'   ); // User functions
		require( $this->plugin_dir . 'bbp-includes/bbp-user-template.php'    ); // User template tags

		/** Admin *************************************************************/

		// Quick admin check and load if needed
		if ( is_admin() )
			require( $this->plugin_dir . 'bbp-admin/bbp-admin.php' );
	}

	/**
	 * Setup the default hooks and actions
	 *
	 * @since bbPress (r2644)
	 * @access private
	 *
	 * @uses register_activation_hook() To register the activation hook
	 * @uses register_deactivation_hook() To register the deactivation hook
	 * @uses add_action() To add various actions
	 */
	private function setup_actions() {

		// Add actions to plugin activation and deactivation hooks
		add_action( 'activate_'   . $this->basename, 'bbp_activation'   );
		add_action( 'deactivate_' . $this->basename, 'bbp_deactivation' );

		// If bbPress is being deactivated, do not add any actions
		if ( bbp_is_deactivation( $this->basename ) )
			return;

		// Array of bbPress core actions
		$actions = array(
			'setup_current_user',       // Setup currently logged in user
			'register_post_types',      // Register post types (forum|topic|reply)
			'register_post_statuses',   // Register post statuses (closed|spam|orphan|hidden)
			'register_taxonomies',      // Register taxonomies (topic-tag)
			'register_views',           // Register the views (no-replies)
			'register_theme_directory', // Register the theme directory (bbp-themes)
			'load_textdomain',          // Load textdomain (bbpress)
			'add_rewrite_tags',         // Add rewrite tags (view|user|edit)
			'generate_rewrite_rules'    // Generate rewrite rules (view|edit)
		);

		// Add the actions
		foreach( $actions as $class_action )
			add_action( 'bbp_' . $class_action, array( $this, $class_action ), 5 );
	}

	/**
	 * Load the translation file for current language. Checks the languages
	 * folder inside the bbPress plugin first, and then the default WordPress
	 * languages folder.
	 *
	 * Note that custom translation files inside the bbPress plugin folder
	 * will be removed on bbPress updates. If you're creating custom
	 * translation files, please use the global language folder.
	 *
	 * @since bbPress (r2596)
	 *
	 * @uses apply_filters() Calls 'bbpress_locale' with the
	 *                        {@link get_locale()} value
	 * @uses load_textdomain() To load the textdomain
	 * @return bool True on success, false on failure
	 */
	public function load_textdomain() {

		// Allow locale to be filtered
		$locale = apply_filters( 'bbpress_locale', get_locale() );

		// Get mo file name
		$mofile = sprintf( 'bbpress-%s.mo', $locale );

		// Setup paths to current locale file
		$mofile_local  = $this->lang_dir . '/' . $mofile;
		$mofile_global = WP_LANG_DIR . '/bbpress/' . $mofile;

		// Look in local /wp-content/plugins/bbpress/bbp-languages/ folder
		if ( file_exists( $mofile_local ) )
			return load_textdomain( 'bbpress', $mofile_local );

		// Look in global /wp-content/languages/ folder
		elseif ( file_exists( $mofile_global ) )
			return load_textdomain( 'bbpress', $mofile_global );

		// Nothing found
		return false;
	}

	/**
	 * Sets up the bbPress theme directory to use in WordPress
	 *
	 * @since bbPress (r2507)
	 *
	 * @uses register_theme_directory() To register the theme directory
	 * @return bool True on success, false on failure
	 */
	public function register_theme_directory() {
		return register_theme_directory( $this->themes_dir );
	}

	/**
	 * Setup the post types for forums, topics and replies
	 *
	 * @since bbPress (r2597)
	 *
	 * @uses register_post_type() To register the post types
	 * @uses apply_filters() Calls various filters to modify the arguments
	 *                        sent to register_post_type()
	 */
	public function register_post_types() {

		/** Forums ************************************************************/

		// Forum labels
		$forum['labels'] = array(
			'name'               => __( 'Forums',                   'bbpress' ),
			'menu_name'          => __( 'Forums',                   'bbpress' ),
			'singular_name'      => __( 'Forum',                    'bbpress' ),
			'all_items'          => __( 'All Forums',               'bbpress' ),
			'add_new'            => __( 'New Forum',                'bbpress' ),
			'add_new_item'       => __( 'Create New Forum',         'bbpress' ),
			'edit'               => __( 'Edit',                     'bbpress' ),
			'edit_item'          => __( 'Edit Forum',               'bbpress' ),
			'new_item'           => __( 'New Forum',                'bbpress' ),
			'view'               => __( 'View Forum',               'bbpress' ),
			'view_item'          => __( 'View Forum',               'bbpress' ),
			'search_items'       => __( 'Search Forums',            'bbpress' ),
			'not_found'          => __( 'No forums found',          'bbpress' ),
			'not_found_in_trash' => __( 'No forums found in Trash', 'bbpress' ),
			'parent_item_colon'  => __( 'Parent Forum:',            'bbpress' )
		);

		// Forum rewrite
		$forum['rewrite'] = array(
			'slug'       => $this->forum_slug,
			'with_front' => false
		);

		// Forum supports
		$forum['supports'] = array(
			'title',
			'editor',
			'revisions'
		);

		// Forum filter
		$bbp_cpt['forum'] = apply_filters( 'bbp_register_forum_post_type', array(
			'labels'              => $forum['labels'],
			'rewrite'             => $forum['rewrite'],
			'supports'            => $forum['supports'],
			'description'         => __( 'bbPress Forums', 'bbpress' ),
			'capabilities'        => bbp_get_forum_caps(),
			'capability_type'     => array( 'forum', 'forums' ),
			'menu_position'       => 56,
			'has_archive'         => $this->root_slug,
			'exclude_from_search' => true,
			'show_in_nav_menus'   => true,
			'public'              => true,
			'show_ui'             => true,
			'can_export'          => true,
			'hierarchical'        => true,
			'query_var'           => true,
			'menu_icon'           => ''
		) );

		// Register Forum content type
		register_post_type( $this->forum_post_type, $bbp_cpt['forum'] );

		/** Topics ************************************************************/

		// Topic labels
		$topic['labels'] = array(
			'name'               => __( 'Topics',                   'bbpress' ),
			'menu_name'          => __( 'Topics',                   'bbpress' ),
			'singular_name'      => __( 'Topic',                    'bbpress' ),
			'all_items'          => __( 'All Topics',               'bbpress' ),
			'add_new'            => __( 'New Topic',                'bbpress' ),
			'add_new_item'       => __( 'Create New Topic',         'bbpress' ),
			'edit'               => __( 'Edit',                     'bbpress' ),
			'edit_item'          => __( 'Edit Topic',               'bbpress' ),
			'new_item'           => __( 'New Topic',                'bbpress' ),
			'view'               => __( 'View Topic',               'bbpress' ),
			'view_item'          => __( 'View Topic',               'bbpress' ),
			'search_items'       => __( 'Search Topics',            'bbpress' ),
			'not_found'          => __( 'No topics found',          'bbpress' ),
			'not_found_in_trash' => __( 'No topics found in Trash', 'bbpress' ),
			'parent_item_colon'  => __( 'Forum:',                   'bbpress' )
		);

		// Topic rewrite
		$topic['rewrite'] = array(
			'slug'       => $this->topic_slug,
			'with_front' => false
		);

		// Topic supports
		$topic['supports'] = array(
			'title',
			'editor',
			'revisions'
		);

		// Topic Filter
		$bbp_cpt['topic'] = apply_filters( 'bbp_register_topic_post_type', array(
			'labels'              => $topic['labels'],
			'rewrite'             => $topic['rewrite'],
			'supports'            => $topic['supports'],
			'description'         => __( 'bbPress Topics', 'bbpress' ),
			'capabilities'        => bbp_get_topic_caps(),
			'capability_type'     => array( 'topic', 'topics' ),
			'menu_position'       => 57,
			'has_archive'         => $this->topic_archive_slug,
			'exclude_from_search' => true,
			'show_in_nav_menus'   => false,
			'public'              => true,
			'show_ui'             => true,
			'can_export'          => true,
			'hierarchical'        => false,
			'query_var'           => true,
			'menu_icon'           => ''
		) );

		// Register Topic content type
		register_post_type( $this->topic_post_type, $bbp_cpt['topic'] );

		/** Replies ***********************************************************/

		// Reply labels
		$reply['labels'] = array(
			'name'               => __( 'Replies',                   'bbpress' ),
			'menu_name'          => __( 'Replies',                   'bbpress' ),
			'singular_name'      => __( 'Reply',                     'bbpress' ),
			'all_items'          => __( 'All Replies',               'bbpress' ),
			'add_new'            => __( 'New Reply',                 'bbpress' ),
			'add_new_item'       => __( 'Create New Reply',          'bbpress' ),
			'edit'               => __( 'Edit',                      'bbpress' ),
			'edit_item'          => __( 'Edit Reply',                'bbpress' ),
			'new_item'           => __( 'New Reply',                 'bbpress' ),
			'view'               => __( 'View Reply',                'bbpress' ),
			'view_item'          => __( 'View Reply',                'bbpress' ),
			'search_items'       => __( 'Search Replies',            'bbpress' ),
			'not_found'          => __( 'No replies found',          'bbpress' ),
			'not_found_in_trash' => __( 'No replies found in Trash', 'bbpress' ),
			'parent_item_colon'  => __( 'Topic:',                    'bbpress' )
		);

		// Reply rewrite
		$reply['rewrite'] = array(
			'slug'       => $this->reply_slug,
			'with_front' => false
		);

		// Reply supports
		$reply['supports'] = array(
			'title',
			'editor',
			'revisions'
		);

		// Reply filter
		$bbp_cpt['reply'] = apply_filters( 'bbp_register_reply_post_type', array(
			'labels'              => $reply['labels'],
			'rewrite'             => $reply['rewrite'],
			'supports'            => $reply['supports'],
			'description'         => __( 'bbPress Replies', 'bbpress' ),
			'capabilities'        => bbp_get_reply_caps(),
			'capability_type'     => array( 'reply', 'replies' ),
			'menu_position'       => 58,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'show_in_nav_menus'   => false,
			'public'              => true,
			'show_ui'             => true,
			'can_export'          => true,
			'hierarchical'        => false,
			'query_var'           => true,
			'menu_icon'           => ''
		) );

		// Register reply content type
		register_post_type( $this->reply_post_type, $bbp_cpt['reply'] );
	}

	/**
	 * Register the post statuses
	 *
	 * @since bbPress (r2727)
	 *
	 * @uses register_post_status() To register post statuses
	 * @uses $wp_post_statuses To modify trash and private statuses
	 * @uses current_user_can() To check if the current user is capable &
	 *                           modify $wp_post_statuses accordingly
	 */
	public function register_post_statuses() {
		global $wp_post_statuses;

		// Closed
		$status = apply_filters( 'bbp_register_closed_post_status', array(
			'label'             => _x( 'Closed', 'post', 'bbpress' ),
			'label_count'       => _nx_noop( 'Closed <span class="count">(%s)</span>', 'Closed <span class="count">(%s)</span>', 'bbpress' ),
			'public'            => true,
			'show_in_admin_all' => true
		) );
		register_post_status( $this->closed_status_id, $status );

		// Spam
		$status = apply_filters( 'bbp_register_spam_post_status', array(
			'label'                     => _x( 'Spam', 'post', 'bbpress' ),
			'label_count'               => _nx_noop( 'Spam <span class="count">(%s)</span>', 'Spam <span class="count">(%s)</span>', 'bbpress' ),
			'protected'                 => true,
			'exclude_from_search'       => true,
			'show_in_admin_status_list' => true,
			'show_in_admin_all_list'    => false
		) );
		register_post_status( $this->spam_status_id, $status );

		// Orphan
		$status = apply_filters( 'bbp_register_orphan_post_status', array(
			'label'                     => _x( 'Orphan', 'post', 'bbpress' ),
			'label_count'               => _nx_noop( 'Orphan <span class="count">(%s)</span>', 'Orphans <span class="count">(%s)</span>', 'bbpress' ),
			'protected'                 => true,
			'exclude_from_search'       => true,
			'show_in_admin_status_list' => true,
			'show_in_admin_all_list'    => false
		) );
		register_post_status( $this->orphan_status_id, $status );

		// Hidden
		$status = apply_filters( 'bbp_register_hidden_post_status', array(
			'label'                     => _x( 'Hidden', 'post', 'bbpress' ),
			'label_count'               => _nx_noop( 'Hidden <span class="count">(%s)</span>', 'Hidden <span class="count">(%s)</span>', 'bbpress' ),
			'private'                   => true,
			'exclude_from_search'       => true,
			'show_in_admin_status_list' => true,
			'show_in_admin_all_list'    => true
		) );
		register_post_status( $this->hidden_status_id, $status );

		/**
		 * Trash fix
		 *
		 * We need to remove the internal arg and change that to
		 * protected so that the users with 'view_trash' cap can view
		 * single trashed topics/replies in the front-end as wp_query
		 * doesn't allow any hack for the trashed topics to be viewed.
		 */
		if ( !empty( $wp_post_statuses['trash'] ) ) {

			// User can view trash so set internal to false
			if ( current_user_can( 'view_trash' ) ) {
				$wp_post_statuses['trash']->internal  = false;
				$wp_post_statuses['trash']->protected = true;

			// User cannot view trash so set internal to true
			} elseif ( !current_user_can( 'view_trash' ) ) {
				$wp_post_statuses['trash']->internal = true;
			}
		}
	}

	/**
	 * Register the topic tag taxonomy
	 *
	 * @since bbPress (r2464)
	 *
	 * @uses register_taxonomy() To register the taxonomy
	 */
	public function register_taxonomies() {

		// Topic tag labels
		$topic_tag['labels'] = array(
			'name'          => __( 'Topic Tags',     'bbpress' ),
			'singular_name' => __( 'Topic Tag',      'bbpress' ),
			'search_items'  => __( 'Search Tags',    'bbpress' ),
			'popular_items' => __( 'Popular Tags',   'bbpress' ),
			'all_items'     => __( 'All Tags',       'bbpress' ),
			'edit_item'     => __( 'Edit Tag',       'bbpress' ),
			'update_item'   => __( 'Update Tag',     'bbpress' ),
			'add_new_item'  => __( 'Add New Tag',    'bbpress' ),
			'new_item_name' => __( 'New Tag Name',   'bbpress' ),
			'view_item'     => __( 'View Topic Tag', 'bbpress' )
		);

		// Topic tag rewrite
		$topic_tag['rewrite'] = array(
			'slug'       => $this->topic_tag_slug,
			'with_front' => false
		);

		// Topic tag filter
		$bbp_tt = apply_filters( 'bbp_register_topic_taxonomy', array(
			'labels'                => $topic_tag['labels'],
			'rewrite'               => $topic_tag['rewrite'],
			'capabilities'          => bbp_get_topic_tag_caps(),
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'show_tagcloud'         => true,
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true
		) );

		// Register the topic tag taxonomy
		register_taxonomy(
			$this->topic_tag_tax_id, // The topic tag id
			$this->topic_post_type,  // The topic post type
			$bbp_tt
		);
	}

	/**
	 * Register the bbPress views
	 *
	 * @since bbPress (r2789)
	 *
	 * @uses bbp_register_view() To register the views
	 */
	public function register_views() {

		// Topics with no replies
		$no_replies = apply_filters( 'bbp_register_view_no_replies', array(
			'meta_key'     => '_bbp_reply_count',
			'meta_value'   => 1,
			'meta_compare' => '<',
			'orderby'      => ''
		) );

		bbp_register_view( 'no-replies', __( 'Topics with no replies', 'bbpress' ), $no_replies );
	}

	/**
	 * Setup the currently logged-in user
	 *
	 * Do not to call this prematurely, I.E. before the 'init' action has
	 * started. This function is naturally hooked into 'init' to ensure proper
	 * execution. get_currentuserinfo() is used to check for XMLRPC_REQUEST to
	 * avoid xmlrpc errors.
	 *
	 * @since bbPress (r2697)
	 *
	 * @uses wp_get_current_user()
	 */
	public function setup_current_user() {
		$this->current_user = &wp_get_current_user();
	}

	/**
	 * Add the bbPress-specific rewrite tags
	 *
	 * @since bbPress (r2753)
	 *
	 * @uses add_rewrite_tag() To add the rewrite tags
	 */
	public function add_rewrite_tags() {

		// Pad attributes
		$pad      = 2;
		$wrapper  = '%';

		// Setup the tags
		$bbp_user = str_pad( $this->user_id, strlen( $this->user_id ) + $pad, $wrapper, STR_PAD_BOTH );
		$bbp_view = str_pad( $this->view_id, strlen( $this->view_id ) + $pad, $wrapper, STR_PAD_BOTH );
		$bbp_edit = str_pad( $this->edit_id, strlen( $this->edit_id ) + $pad, $wrapper, STR_PAD_BOTH );

		// User Profile tag
		add_rewrite_tag( $bbp_user, '([^/]+)'   );

		// View Page tag
		add_rewrite_tag( $bbp_view, '([^/]+)'   );

		// Edit Page tag
		add_rewrite_tag( $bbp_edit, '([1]{1,})' );
	}

	/**
	 * Register bbPress-specific rewrite rules
	 *
	 * @since bbPress (r2688)
	 *
	 * @param WP_Rewrite $wp_rewrite bbPress-sepecific rules are appended in
	 *                                $wp_rewrite->rules
	 */
	public function generate_rewrite_rules( $wp_rewrite ) {

		// New rules to merge with existing
		$bbp_rules = array(

			// Edit Topic/Reply
			$this->topic_slug     . '/([^/]+)/edit/?$' => 'index.php?' . $this->topic_post_type  . '=' . $wp_rewrite->preg_index( 1 ) . '&edit=1',
			$this->reply_slug     . '/([^/]+)/edit/?$' => 'index.php?' . $this->reply_post_type  . '=' . $wp_rewrite->preg_index( 1 ) . '&edit=1',

			// Edit Topic Tag
			$this->topic_tag_slug . '/([^/]+)/edit/?$' => 'index.php?' . $this->topic_tag_tax_id . '=' . $wp_rewrite->preg_index( 1 ) . '&edit=1',

			// Profile Page
			$this->user_slug      . '/([^/]+)/page/?([0-9]{1,})/?$' => 'index.php?' . $this->user_id  . '=' . $wp_rewrite->preg_index( 1 ) . '&paged=' . $wp_rewrite->preg_index( 2 ),
			$this->user_slug      . '/([^/]+)/?$'                   => 'index.php?' . $this->user_id  . '=' . $wp_rewrite->preg_index( 1 ),
			$this->user_slug      . '/([^/]+)/edit/?$'              => 'index.php?' . $this->user_id  . '=' . $wp_rewrite->preg_index( 1 ) . '&edit=1',

			// View Page
			$this->view_slug . '/([^/]+)/page/?([0-9]{1,})/?$' => 'index.php?' . $this->view_id . '=' . $wp_rewrite->preg_index( 1 ) . '&paged=' . $wp_rewrite->preg_index( 2 ),
			$this->view_slug . '/([^/]+)/?$'                   => 'index.php?' . $this->view_id . '=' . $wp_rewrite->preg_index( 1 )
		);

		// Merge bbPress rules with existing
		$wp_rewrite->rules = array_merge( $bbp_rules, $wp_rewrite->rules );

		// Return merged rules
		return $wp_rewrite;
	}
}

// "And now here's something we hope you'll really like!"
$GLOBALS['bbp'] = new bbPress();

endif; // class_exists check

?>
