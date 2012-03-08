<?php

/*The Functions to automatically activate for Single WP Installs*/
if ( !bp_core_is_multisite() ) {

	function disable_validation( $user_id ) {
		global $wpdb;

		//Hook if you want to do something before the activation
		do_action('bp_disable_activation_before_activation');
		
		$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->users SET user_status = 0 WHERE ID = %d", $user_id ) );
		
		//Add note on Activity Stream
		if ( function_exists( 'bp_activity_add' ) ) {
			$userlink = bp_core_get_userlink( $user_id );
			
			bp_activity_add( array(
				'user_id' => $user_id,
				'action' => apply_filters( 'bp_core_activity_registered_member', sprintf( __( '%s became a registered member', 'buddypress' ), $userlink ), $user_id ),
				'component' => 'profile',
				'type' => 'new_member'
			) );
			
		}
		
		//Hook if you want to do something before the login
		do_action('bp_disable_activation_before_login');
		
		//Automatically log the user in	.
		//Thanks to Justin Klein's  wp-fb-autoconnect plugin for the basic code to login automatically
		$user_info = get_userdata($user_id);
		wp_set_auth_cookie($user_id);

		do_action('wp_signon', $user_info->user_login);
		
		//Hook if you want to do something after the login
		do_action('bp_disable_activation_after_login');
	}

	add_action( 'bp_core_signup_user', 'disable_validation' );

	function fix_signup_form_validation_text() {
		return false;
	}

	add_filter( 'bp_registration_needs_activation', 'fix_signup_form_validation_text' );

	function disable_activation_email() {
		return false;
	}

	add_filter( 'bp_core_signup_send_activation_key', 'disable_activation_email' );

/*END Functions to automatically activate for Single WP Installs*/

} else {

/*START Functions to automatically activate for WPMU (multi-site)  Installs (Activates User and Blogs)*/

/*
 Credit for most of the WPMU code goes to Brajesh Singh and his plugin "BP Auto activate User and Blog at Signup"
*/

//Remove filters which notifies users
remove_filter( 'wpmu_signup_user_notification', 'bp_core_activation_signup_user_notification', 1, 4 );

add_filter( 'wpmu_signup_user_notification', 'cc_auto_activate_on_user_signup', 1, 4 );
add_action("signup_finished","cc_auto_activate_finished");//for flusing the cache


function cc_auto_activate_on_user_signup($user, $user_email, $key, $meta) {
	
global $bp, $wpdb;
	global $current_site;
	
	// just do what wp-activate.php does
    ob_start();
	
	
		
		require_once( ABSPATH . WPINC . '/registration.php' );
		
		/* Activate the signup */
		$signup = apply_filters( 'bp_core_activate_account', wpmu_activate_signup( $key ) );
		
		/* If there was errors, add a message and redirect */
		if ( $signup->errors ) {
			bp_core_add_message( __( 'There was an error activating your account, please try again.', 'buddypress' ), 'error' );
			bp_core_redirect( $bp->root_domain . '/' . BP_ACTIVATION_SLUG );
		}
		
		/* Set the password */
		if ( !empty( $signup['meta']['password'] ) )
			$wpdb->update( $wpdb->users, array( 'user_pass' => $signup['meta']['password'] ), array( 'ID' => $signup['user_id'] ), array( '%s' ), array( '%d' ) );
		
		/* Set any profile data */ 
		if ( function_exists( 'xprofile_set_field_data' ) ) {
			
			if ( !empty( $signup['meta']['profile_field_ids'] ) ) {
				$profile_field_ids = explode( ',', $signup['meta']['profile_field_ids'] );
			
				foreach( $profile_field_ids as $field_id ) {
					$current_field = $signup['meta']["field_{$field_id}"];
				
					if ( !empty( $current_field ) )
						xprofile_set_field_data( $field_id, $signup['user_id'], $current_field );
				}
			}
			
		}
		

		/* Record the new user in the activity streams */
		if ( function_exists( 'bp_activity_add' ) ) {
			$userlink = bp_core_get_userlink( $signup['user_id'] );
			
			
			bp_activity_add( array(
				'user_id' => $signup['user_id'],
				'action' => apply_filters( 'bp_core_activity_registered_member', sprintf( __( '%s became a registered member', 'buddypress' ), $userlink ), $signup['user_id'] ),
				'component' => 'profile',
				'type' => 'new_member'
			) );
			
			/* Should I add the following to the bp_activity_add function?
				'primary_link' => apply_filters( 'bp_core_activity_registered_member_primary_link', $userlink ),;*/
		}

		do_action( 'bp_core_account_activated', &$signup, $_GET['key'] );
		bp_core_add_message( __( 'Your account is now active!', 'buddypress' ) );
		
		$bp->activation_complete = true;
		
		//Automatically log the user in	.
		//Thanks to Justin Klein's  wp-fb-autoconnect plugin for the basic code to login automatically
		$user_info = get_userdata($signup['user_id']);
		wp_set_auth_cookie($signup['user_id']);
		do_action('wp_login', $user_info->user_login);
		
		do_action('bp_auto_activate_after_login');
	
	return false; //stop  wpmu_signup_blog_notification () from sending mail to user
}

function cc_auto_activate_finished()
{
  
echo "</div>";//just close the hidden div,we use it for hiding actual notification
ob_end_flush();
}

/*END Functions to automatically activate for WPMU (multi-site)  Installs*/
}

/*Redirect after Activation and Login*/
function bp_redirect_cc()
{
	global $bp;
	
	$redirect_url_cc = $bp->root_domain;
	
	$redirect_url_cc = apply_filters( 'bp_diable_activation_redirect_url', $redirect_url_cc );
	
	//redirect to credit card processor/payment page
	bp_core_redirect($redirect_url_cc);
	
}

/*Add an action to redirect user after registration*/
add_action("bp_core_signup_user","bp_redirect_cc",100,1);

?>