=== BP Disable Activation ===
Contributors: crashutah, apeatling
Donate link: http://crashutah.com
Tags: BuddyPress, activation, WPMU
Requires at least: 2.9.2
Tested up to: 2.9.2
Site Wide Only: true
Stable tag: .4

Disables the activation email and automatically activates new users in BuddyPress under a standard WP install and WPMU (multisite).  Also, automatically logs in the new user since the account is already active.

== Description ==

Disables the activation email and automatically activates new users in BuddyPress under a standard WP install and WPMU (multisite).  Also, automatically logs in the new user since the account is already active.

Possible Future Features:
-Option to turn off automatic login
-Option to not disable email
-Redirect options after account creation

Known Bugs:
-Doesn't do the automatic login if you allow blog creation during the user creation in WPMU (multisite)

== Installation ==

1. Upload the 'bp-disable-activation' folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Sit back, relax and enjoy the plugin

== Frequently Asked Questions ==

= Won't this allow more spammers to get in? =

Of course it could.  So, you should consider using other plugins and methods for preventing spammers from signing up on your site.  However, many people have seen spammers get through just fine even with email activation enabled.  Plus, some sites are designed so that email activation doesn't matter.  Thus the plugin.

= What if I don't want my users to automatically login? =

Why don't you?  Users will love that feature.  I'll look at adding an option to turn this on/off.  Until then you can comment out those lines if you don't want it.

== Changelog ==

= 0.4 =
* Made plugin sitewide for WPMU
* Fixed WPMU Activity Stream Addition
* Added Activity Stream New Member notation for WP

= 0.3 =
* Added support for WPMU (multisite).  Includes automatic activation, disabling activation email and automatic login
* Added redirect to home page after activation and login so the sidebar recognizes the login
* Added bp_diable_activation_redirect_url filter to redirect to somewhere other than the home page if people desire
* Added loader file that makes sure BuddyPress is activated first

= 0.2 =
* Added automatic login to BuddyPress after activating the user.

= 0.1 =
* Initial Release - Thanks to Andy for Most the Code