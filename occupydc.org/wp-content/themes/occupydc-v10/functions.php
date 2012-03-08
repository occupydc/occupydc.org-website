<?php
update_option('WP_SITEURL', 'http://localhost:8080/occupydc.org');
update_option('WP_HOME', 'http://localhost:8080/occupydc.org'); 

//bootstrap menu//

add_action( 'after_setup_theme', 'bootstrap_setup' );

if ( ! function_exists( 'bootstrap_setup' ) ):

	function bootstrap_setup(){

		add_action( 'init', 'register_menu' );

		function register_menu(){
			register_nav_menu( 'top-bar', 'Bootstrap Top Menu' ); 
		}

		class Bootstrap_Walker_Nav_Menu extends Walker_Nav_Menu {


			function start_lvl( &$output, $depth ) {

				$indent = str_repeat( "\t", $depth );
				$output	   .= "\n$indent<ul class=\"dropdown-menu\">\n";

			}

			function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

				$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

				$li_attributes = '';
				$class_names = $value = '';

				$classes = empty( $item->classes ) ? array() : (array) $item->classes;
				$classes[] = ($args->has_children) ? 'dropdown' : '';
				$classes[] = ($item->current) ? 'active' : '';
				$classes[] = 'menu-item-' . $item->ID;


				$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
				$class_names = ' class="' . esc_attr( $class_names ) . '"';

				$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
				$id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';

				$output .= $indent . '<li' . $id . $value . $class_names . $li_attributes . '>';

				$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
				$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
				$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
				$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
				$attributes .= ($args->has_children) 	    ? ' class="dropdown-toggle" data-toggle="dropdown"' : '';

				$item_output = $args->before;
				$item_output .= '<a'. $attributes .'>';
				$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
				$item_output .= ($args->has_children) ? ' <b class="caret"></b></a>' : '</a>';
				$item_output .= $args->after;

				$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
			}

			function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {

				if ( !$element )
					return;

				$id_field = $this->db_fields['id'];

				//display this element
				if ( is_array( $args[0] ) ) 
					$args[0]['has_children'] = ! empty( $children_elements[$element->$id_field] );
				else if ( is_object( $args[0] ) ) 
					$args[0]->has_children = ! empty( $children_elements[$element->$id_field] ); 
				$cb_args = array_merge( array(&$output, $element, $depth), $args);
				call_user_func_array(array(&$this, 'start_el'), $cb_args);

				$id = $element->$id_field;

				// descend only when the depth is right and there are childrens for this element
				if ( ($max_depth == 0 || $max_depth > $depth+1 ) && isset( $children_elements[$id]) ) {

					foreach( $children_elements[ $id ] as $child ){

						if ( !isset($newlevel) ) {
							$newlevel = true;
							//start the child delimiter
							$cb_args = array_merge( array(&$output, $depth), $args);
							call_user_func_array(array(&$this, 'start_lvl'), $cb_args);
						}
						$this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
					}
						unset( $children_elements[ $id ] );
				}

				if ( isset($newlevel) && $newlevel ){
					//end the child delimiter
					$cb_args = array_merge( array(&$output, $depth), $args);
					call_user_func_array(array(&$this, 'end_lvl'), $cb_args);
				}

				//end this element
				$cb_args = array_merge( array(&$output, $element, $depth), $args);
				call_user_func_array(array(&$this, 'end_el'), $cb_args);

			}

		}

	}

endif;



// end bootstrap




// Localization
load_theme_textdomain('cp', TEMPLATEPATH.'/lang/');


// load admin menu
require_once ( TEMPLATEPATH . '/admin/admin-menu.php');


// Register Sidebar
if ( function_exists('register_sidebar') ) 
{
   register_sidebar(array(
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div><!-- #END widgetcontent --></div><!-- #END widget --><div class="clear"></div>',
		'before_title' => '<h2 class="widgettitle">',
		'after_title' => '</h2><div class="widgetcontent">',
   ));
}

// navigation menu
add_action( 'init', 'register_my_menu' );

function register_my_menu() {
	register_nav_menu( 'primary-menu', __( 'Primary Menu' ) );
}

// remove nofollow attribute
function remove_nofollow($string) 
{
	$string = str_ireplace(' rel="nofollow"', '', $string);
	return $string;
}
add_filter('the_content', 'remove_nofollow');
add_filter('comment_text', 'remove_nofollow');


// the new search widget
function cp_search_widget() 
{
	?>
	<div class="widget">
		<h2 class="widgettitle"><?php _e('Search', 'cp'); ?></h2>
		<div class="widgetcontent">
			<div class="widgetsearchform"><?php include(TEMPLATEPATH.'/searchform.php');?></div>
		</div>
	</div>
	<div class="clear"></div>
	<?php
}
register_sidebar_widget('CP Search', 'cp_search_widget');


// unregister the standard search widget
function cp_unregister_widgets() 
{
	unregister_widget('WP_Widget_Search');         
}
add_action('widgets_init', 'cp_unregister_widgets');  



// Add support for post thumbnails -- only WP 2.9+
if ( function_exists('add_theme_support') ) 
{
	add_theme_support('post-thumbnails');
	set_post_thumbnail_size(150,150);
}


// new comment function
function cp_comment_author_link() 
{
	$comment_author_link = get_comment_author_link();
	if ( ereg(']* class=[^>]+>', $comment_author_link) ) 
	{
		$comment_author_link = ereg_replace('(]* class=[\'"]?)', '\\1url ' , $comment_author_link );
	} 
	else 
	{
		$comment_author_link = ereg_replace('(<a )/', '\\1class="url "' , $comment_author_link );
	}
    echo $comment_author_link;
}


function cp_comment_author_avatar() 
{
	$email = get_comment_author_email();
	$avatar = str_replace("class='avatar", "class='photo avatar", get_avatar("$email", "42" ) );
	echo $avatar;
}


function cp_comments( $comment, $args, $depth ) 
{

	$GLOBALS['comment'] = $comment; ?>
                 
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
		
		<a name="comment-<?php comment_ID() ?>"></a>
		
		<div class="comment-meta">
      
			<span class="name"><?php cp_comment_author_avatar(); ?></span>

			<?php if ( (get_comment_type() == "comment") ) { cp_comment_author_link(); } ?>
			<?php if ( (get_comment_type() == "pingback") or (get_comment_type() == "trackback") ) { ?><?php cp_comment_author_link(); ?><br /><?php } ?>
      	
			<?php if ( (get_comment_type() == "comment") or (get_comment_type() == "pingback") or (get_comment_type() == "trackback") ) { ?>
           		
				<span class="comment-date"> <?php _e('on', 'cp'); ?> <?php echo get_comment_date( __('F jS, Y', 'cp') ); ?> <?php _e('at', 'cp'); ?> <?php echo get_comment_time(); ?></span>
				<span class="edit"><?php edit_comment_link( __('Edit', 'cp'), '', '' ); ?></span>
				<span class="permalink"><a class="comment-permalink" href="<?php echo get_comment_link(); ?>" title="<?php _e('Direct link to this comment', 'cp'); ?>">#</a></span>
           		
			<?php } ?>
	     
		</div>
    	
		<div class="comment-entry" id="comment-<?php comment_ID(); ?>">
			
			<?php comment_text() ?>

			<?php if ( $comment->comment_approved == '0') { ?> <p class='comment-unapproved'><?php _e('Your comment is awaiting moderation.', 'cp'); ?></p> <?php } ?>
			
			<div class="comment-reply">

				<?php comment_reply_link( array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']) ) ) ?>

			</div>
			
		</div>

		<div class="clear"></div>   

	<?php 
} 

/* ------------------------ */
/*  define some shortcodes  */
/*  used in footer text     */
/* ------------------------ */

function cp_shortcode_copyright()
{
	return sprintf('&copy; %1$s <a href="%2$s" title="%3$s">%3$s</a> ', date('Y'), get_bloginfo('url'), get_bloginfo('name') );
}

function cp_shortcode_wordpress()
{
	return sprintf('%1$s <a href="http://wordpress.org" title="Wordpress">Wordpress</a> ', __('Powered by', 'cp') );
}

function cp_shortcode_credit()
{
	return sprintf('%1$s <a target="_blank" href="http://cproell.de/" title="Christian Proell">CP-Themes</a> ', __('Theme by', 'cp') );
}

add_shortcode ('copyright', 'cp_shortcode_copyright'); 
add_shortcode ('wordpress', 'cp_shortcode_wordpress');
add_shortcode ('credit', 'cp_shortcode_credit');



function handcraftedwp_widgets_init() {
    register_sidebar(array(  
        'name' => 'Homepage Left Column',  
        'id'   => 'left_column',  
        'description'   => 'Widget area for home age left column',  
        'before_widget' => '<div id="%1$s" class="widget %2$s">',  
        'after_widget'  => '</div>',  
        'before_title'  => '<h2>',  
        'after_title'   => '</h2>'  
    ));  
    register_sidebar(array(  
        'name' => 'Homepage Center Column',  
        'id'   => 'center_column',  
        'description'   => 'Widget area for homepage center column',  
        'before_widget' => '<div id="%1$s" class="widget %2$s">',  
        'after_widget'  => '</div>',  
        'before_title'  => '<h2>',  
        'after_title'   => '</h2>'  
    ));  
    register_sidebar(array(  
        'name' => 'Homepage Right Column',  
        'id'   => 'right_column',  
        'description'   => 'Widget area for homepage right column',  
        'before_widget' => '<div id="%1$s" class="widget %2$s">',  
        'after_widget'  => '</div>',  
        'before_title'  => '<h2>',  
        'after_title'   => '</h2>'  
    ));  
	register_sidebar( array (
		'name' => __( 'Sidebar', 'themename' ),
		'id' => 'sidebar',
		'before_widget' => '<aside id="%1$s" class="widget %2$s" role="complementary">',
		'after_widget' => "</aside>",
		'before_title' => '<h4 class="widget-title">',
		'after_title' => '</h4>'
	) );
	register_sidebar(array(  
        'name' => 'Footer Left Column',  
        'id'   => 'f_left_column',  
        'description'   => 'Widget area for footer center column',  
        'before_widget' => '<div id="%1$s" class="widget %2$s">',  
        'after_widget'  => '</div>',  
        'before_title'  => '<h2>',  
        'after_title'   => '</h2>'  
    ));  
    register_sidebar(array(  
        'name' => 'Footer Center Column',  
        'id'   => 'f_center_column',  
        'description'   => 'Widget area for footer right column',  
        'before_widget' => '<div id="%1$s" class="widget %2$s">',  
        'after_widget'  => '</div>',  
        'before_title'  => '<h2>',  
        'after_title'   => '</h2>'  
    ));  
	register_sidebar(array(  
        'name' => 'Footer Right Column',  
        'id'   => 'f_right_column',  
        'description'   => 'Widget area for footer right column',  
        'before_widget' => '<div id="%1$s" class="widget %2$s">',  
        'after_widget'  => '</div>',  
        'before_title'  => '<h2>',  
        'after_title'   => '</h2>'  
    ));  

}
add_action( 'init', 'handcraftedwp_widgets_init' );


register_sidebar(
	array(
		'name' => 'Sidebar 2',
		'id' => 'sidebar',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div><!-- #END widgetcontent --></div><!-- #END widget --><div class="clear"></div>',
		'before_title' => '<h2 class="widgettitle">',
		'after_title' => '</h2><div class="widgetcontent">',
	)
);
register_sidebar(
	array(
		'name' => 'Sidebar reg',
		'id' => 'padder',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div><!-- #END widgetcontent --></div><!-- #END widget --><div class="clear"></div>',
		'before_title' => '<h2 class="widgettitle">',
		'after_title' => '</h2><div class="widgetcontent">',
	)
);

//announcement block
register_sidebar(
	array(
		'name' => 'announcements',
		'id' => 'announcements',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div><!-- #END widgetcontent --></div><!-- #END widget --><div class="clear"></div>',
		'before_title' => '<h2 class="widgettitle">',
		'after_title' => '</h2><div class="widgetcontent">',
	)
);

//slider block
register_sidebar(
	array(
		'name' => 'slider',
		'id' => 'slider',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div><!-- #END widgetcontent --></div><!-- #END widget --><div class="clear"></div>',
		'before_title' => '<h2 class="widgettitle">',
		'after_title' => '</h2><div class="widgetcontent">',
	)
);





function bbg_change_home_tab_name() {
  global $bp;

  if ( bp_is_group() ) {
    $bp->bp_options_nav[bp_get_current_group_slug()]['home']['name'] = 'Activity';
  }
}
add_action( 'groups_setup_nav', 'bbg_change_home_tab_name' );


function my_bp_search_form_type_select() {
	global $bp;

	$options = array();

	if ( bp_is_active( 'groups' ) )
		$options['groups']  = __( 'Groups',  'buddypress' );
		
	$options['events'] = __( 'Events', 'buddypress' );

	if ( bp_is_active( 'xprofile' ) )
		$options['members'] = __( 'Members', 'buddypress' );

	if ( bp_is_active( 'forums' ) && bp_forums_is_installed_correctly() && bp_forums_has_directory() )
		$options['forums']  = __( 'Forums',  'buddypress' );

	$options['posts'] = __( 'Posts', 'buddypress' );

	// Eventually this won't be needed and a page will be built to integrate all search results.
	$selection_box  = '<label for="search-which" class="accessibly-hidden">' . __( 'Search these:', 'buddypress' ) . '</label>';
	$selection_box .= '<select name="search-which" id="search-which" style="width: auto">';

	$options = apply_filters( 'bp_search_form_type_select_options', $options );
	foreach( (array)$options as $option_value => $option_title ) {
		$selection_box .= sprintf( '<option id="%s" value="%s">%s</option>', $option_value . "-dropdown-option", $option_value, $option_title );

	}

	$selection_box .= '</select>';
	return $selection_box;

}
add_filter('bp_search_form_type_select','my_bp_search_form_type_select');


function add_script() {
   if (!is_admin()) {
       // comment out the next two lines to load the local copy of jQuery
       	// wp_deregister_script('jquery');
       	wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js', false, '1.5.2');
		wp_enqueue_script('jquery');
		wp_enqueue_script('toggler', get_bloginfo('url') . '/wp-content/js/hide-form/toggler.js');
		}
	}



// Adding paragraph tag to more link
function add_p_tag($link){
	return "<p>$link</p>";
}
add_filter('the_content_more_link', 'add_p_tag');
?>
<?php
// Remove more jump
function remove_more_jump_link($link) { 
$offset = strpos($link, '#more-');
if ($offset) {
$end = strpos($link, '"',$offset);
}
if ($end) {
$link = substr_replace($link, '', $offset, $end-$offset);
}
return $link;
}
add_filter('the_content_more_link', 'remove_more_jump_link');




// Wordpress Hack by Knowtebook.com
// Shorten any text you want

function ShortenText($text)

{

// Change to the number of characters you want to display

$chars_limit = 35;

$chars_text = strlen($text);

$text = $text." ";

$text = substr($text,0,$chars_limit);

$text = substr($text,0,strrpos($text,' '));

// If the text has more characters that your limit,
//add ... so the user knows the text is actually longer

if ($chars_text > $chars_limit)

{

$text = $text.'';

}

return $text;

}

add_action('init', 'add_script');

add_action('wp_footer', 'add_search_form_script');


function custom_excerpt_length( $length ) {
	return 100;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );


function new_excerpt_more($more) {
	return '...';
}
add_filter('excerpt_more', 'new_excerpt_more');

function add_search_form_script() {
	?>
	<script>
	// $(document).ready(function() {
	// 	$('#other').click(function() {
	// 	  $('#target').click();
	// 	});
	// }
	// );
	</script>
	<?php
}




?>
