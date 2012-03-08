=== Pop Your Notes ===
Contributors: Studio Switch
Donate link: http://studioswitch.net/
Tags: modal, jQuery
Requires at least: 2.5
Tested up to: 3.0.1
Stable tag: 1.0.1

Showing modal window as you want, setting with color, style, and wp's conditional branches.

== Description ==

This plugin shows modal window using jQuery simplemodal plugin.

While it's activated, a necessary JS and Stylesheet is be already inserted Wordpress's <Head> section.
And You can set the Content, Styles, Conditional branches(e.g. only showing frontpage, and 404 page.) from the management interface added to the submenu of Settings. 

* Also some options using RGB is possible to adjust by Colorpicker.
* For conditional branches as posts, pages section, you can set the arguments as ID or Slug to narrow range of modal showing, with Comma Separated format.

Usage example:
Only shows in front page - Display New item, band's next gig, or Ad banners. 
Shows in specific page - When the content of the page is updated, displaying important appendix about difference.
Shows in specific post,and 404 page - Maintenance information alerting.

== Installation ==

1. Upload Plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Set the options from setting page at submenu of WP's 'Settings'.

== Frequently Asked Questions ==

= Is the skill of jQuery necessary? =

No, almost options can be input on setting page.
Of course, a more advanced style will possible if you have knowledge of jQuery and CSS.

= More information about scripts used together =

This plugin uses not only jQuery, but SimpleModal, Really Simple Color Picker, jQuery simpleColor.
If you want to customize for more advanced function, You might read those documents.

[SimpleModal  /  Eric Martin / ericmmartin.com]: http://www.ericmmartin.com/projects/simplemodal/
"Documents of SimpleModal by Eric Martin."
[LakTEK - A Sri Lankan, A Rubyist and A Web Dude]: http://www.web2media.net/laktek/
"Site of Really Simple Color Picker author Lakshan Perera."
[jQuery SimpleColor Color-Picker  -  recurser]: http://recurser.com/articles/2007/12/18/jquery-simplecolor-color-picker/
"Documents of SimpleColor Color-Picker."

== Screenshots ==

1. Setting page with colorpicker.
2. Modal window sample.

== Changelog ==

= 1.0.2 =
Fixed conflict when loadging jQuery.

= 1.0.1 =
The first version for public, with readme.txt is appended.

= 1.0.0 =
Local developing version.

== Upgrade Notice ==

= 1.0.1 =
Appending Manuals, and tested several environments.

== Arbitrary section ==

None.