=== Plugin Name ===
Contributors: dlgoodchild, paultgoodchild
Donate link: http://www.hostliketoast.com/
Tags: CSS, Twitter Bootstrap, Twitter Bootstrap Javascript, Bootstrap CSS, WordPress Bootstrap, normalize, reset, YUI
Requires at least: 3.2.0
Tested up to: 3.3.1
Stable tag: 2.0.1c

With Wordpress Twitter Bootstrap CSS you can easily include the latest Twitter Bootstrap CSS and Javascript libraries in your Wordpress site.

== Description ==

To see the latest release notes for the new version 2.0.0 for the [WordPress Twitter Bootstrap CSS plugin](http://www.hostliketoast.com/2012/02/wordpress-twitter-bootstrap-css-plugin-v2-0-final/ "WordPress Twitter Bootstrap CSS plugin v2.0 (Final) - Notes on important changes") see the release article
on our site.

We love to use Twitter Bootstrap at [Host Like Toast](http://www.hostliketoast.com/ "Host Like Toast: Managed Wordpress Hosting").

We wanted a way to quickly link the latest bootstrap CSS and Javascript to all pages, regardless of the Wordpress Theme.

Now you can!

*	Works with *any* Wordpress Theme without ever editing Theme files and NO programming needed.
*	Handy WordPress [SHORTCODES] to add Twitter Bootstrap elements to your site quickly
*	Add your own custom CSS reset file
*	Option to add JavaScript to the [HEAD] (defaults to end of [BODY] as is good practice)

The home for documentation of this plugin is: [Documentation for this WordPress Plugin](http://www.hostliketoast.com/wordpress-resource-centre/wordpress-plugins/ "WordPress Bootstrap CSS Plugin Documentation Home")

**Why use Twitter Bootstrap?** 
It's good practice to have a core, underlying CSS definition so that your website appears and acts consistently across all
browsers as far as possible.

Twitter Bootstrap does this extremely well.

From Twitter Bootstrap:
*Bootstrap is a toolkit from Twitter designed to kickstart development of webapps and sites.
It includes base CSS and HTML for typography, forms, buttons, tables, grids, navigation, and more*

Many themes do not allow you to add custom CSS files easily. Even Thesis! So we take another approach
and inject the CSS as one of the FIRST items in the HTML HEAD section. This way, no other CSS
interferes and these may be used as a foundation/reset CSS.

The CSS is only part of the solution. They have now released Javascript libraries
to complement their Bootstrap solution. These may also be added to your site with the option to
add them to the HEAD (by default they are added to the footer).

We also wanted the option to alternatively include "reset.css" and "normalize.css".  These both form related roles
as bootstrap, but are lighter.

You could look at the difference between the styles as:

*	reset.css - used to *strip/remove* the differences and reduce browser inconsistencies. It is typically generic and
will not be any use alone. It is to be treated as a starting point for your styling.
*	normalize.css - aims to make built-in browser styling consistent across browsers and adds *basic* styles for modern
expectations of certain elements. E.g. H1-6 will all appear bold.
*	bootstrap.css - is a level above normalize where it adds much more styling but retains consistency across modern
browsers. It makes site and web application development much faster.

**Some References**:

Yahoo Reset CSS, YUI 2: http://developer.yahoo.com/yui/2/

Normalize CSS: http://necolas.github.com/normalize.css/

Bootstrap, from Twitter: http://twitter.github.com/bootstrap/

== Installation ==

This plugin should install as any other Wordpress.org respository plugin.

1.	Browse to Plugins -> Add Plugin
1.	Search: Wordpress Bootstrap CSS
1.	Click Install
1.	Click to Activate.

Alternatively using FTP:

1.	Download the zip file using the download link to the right.
1.	Extract the contents of the file and locate the folder called 'wordpress-bootstrap-css' containing the plugin files.
1.	Upload this whole folder to your '/wp-content/plugins/' directory
1.	From the plugins page within Wordpress locate the plugin 'Wordpress Bootstrap CSS' and click Activate

A new menu item will appear on the left-hand side called 'Host Like Toast'.  Click this menu and select
Bootstrap CSS.

Select the CSS file as desired.

== Frequently Asked Questions ==

= Can I link more than one CSS? =

No. There's absolutely no point in doing that and serves only to add a performance penalty to your page loads.

With version 0.4+, you can now add your own custom reset CSS that will follow the standard reset/Twittter Bootstrap CSS. 

= What happens if uninstall this plugin after I design a site with it installed? =

In all likelihood your site design/layout will change. How much so depends on which CSS you used and how much of
your own customizations you have done.

= Why does my site not look any different? =

There are severals reasons for this, most likely it is that you or your Wordpress Theme has defined all the styles
already in such a manner that the CSS applied with this plugin is overwritten.

CSS is hierarchical. This means that any styles defined that apply to an element that *already* has
styles applied to it will take precedence over any previous styles.

= Is Wordpress Bootstrap CSS compatible with caching plugins? =

The only caching plugin that Host Like Toast recommends, and has decent experience with, is W3
Total Cache.

This plugin will automatically flush your W3TC cache when you save changes on this plugin (assuming you have
the other plugin installed).

Otherwise, consult your caching program's documentation.

= Is the CSS "minified"? =

Yes, but only in the case of Yahoo! YUI 2, and Twitter Bootstrap CSS.

= Where is the CSS served from - my server or the source of the CSS? =

It's up to you. We provide the option for you to choose whether it's direct from the source, or served from
your server.

= What's the reason for the Host Like Toast menu? =

We're planning on releasing more plugins in the future and they'll use much of the same code base. In this way
we hope to minimize extra and unnecessary code and give your website a far superior browsing experience without
the typical performance penalty that comes with too many plugins.

Our plugin interface will be consistent and grouped together where possible so you don't have to hunt down the
settings page each time (as is the case with most plugins out there).

== Screenshots ==

1. Here you select which CSS to use and then save settings

2. Assuming you select Twitter Bootstrap CSS, you may now select which Twitter Bootstrap libraries to include

3. You may input an URL of a CSS file that you would like to have included immediately following the reset/Bootstrap CSS

4. You may choose to hotlink the CSS and Javascript files, or serve them from your web server. (All files are included with the plugin download)

== Changelog ==

= 2.0.1c =
* ADDED: Ability to add the "disabled" option to Twitter Bootstrap button components.
* FIXED: a couple of bugs in the shortcodes

= 2.0.1b =
* ADDED: New shortcode [TBS_ICON](http://bit.ly/zmGUeD "Twitter Bootstrap Glyph Icon WordPress Shortcode") to allow you to easily make use of [Twitter Bootstrap Glyphicons](http://bit.ly/AxCdQj)
* ADDED: New shortcode [TBS_BUTTONGROUP] to allow you to easily make use of [Twitter Bootstrap Button Groups](http://bit.ly/z13ICu)
* CHANGED: Rewrote [TBS_BUTTON]. Now you can add "toggle" option, and specify the exact html element type, eg [a], [button], [input]
* CHANGED: Rewrote [TBS_ALERT]. Now you can add the Alert Heading using the parameter: heading="my lovely heading"
* With [TBS_ALERT], parameter "type" is no longer supported - use parameter "class" instead
* CHANGED: Added inline Javascript for activating Popover and Tooltips - nice page-loading optimization and also only execute JS code necessary
* Throughout, attempted to retain support for Twitter Bootstrap 1.4.0. But no guarantees - you should upgrade and convert asap.
* TODO: necessary javascript snippet to enable button toggling - couldn't get it working.

= 2.0.1a =
* Skipped due to missing elements in [TBS_ICON] shortcode.

= 2.0.1 =
* Twitter Bootstrap library upgraded to v2.0.1

= 2.0.0 =
* Added the options for Twitter Bootstrap Library 2.0.0
* Maintained compatibility with Twitter Bootstrap Library 1.4.0
* Removed option to HotLink to resources
* Added more Javascript libraries for 1.4.0 and 2.0.0
* Fixed several bugs.
* Keeping plugin version numbering in-line with Twitter Bootstrap versioning.
* References to "Twipsy" renamed to "Tooltips" to be inline with version 2.0.0
* Most SHORTCODES work between both versions. [Latest Notes](http://bit.ly/wLkYjf "Host Like Toast WordPress Twitter Bootstrap plugin release notes v2.0")

= 0.9.1 =
* Restructured and centralized CSS on admin side.
* Revamped the Host Like Toast Developer Channel subscription box - the previous one wasn't working so well.

= 0.9 =
* Fixed bug where styles were being reapplied when HTML [HEADER] element was defined (thanks to Matt Sims!) 
* Improved compatibility with WordPress 3.3 with more correct enqueue of scripts/stylesheets.

= 0.8.6 =
* [TBS_TWIPSY] and [TBS_POPOVER] are now by default SPAN elements (There may be an option at a later date to specify the element)

= 0.8.5 =
* Made some functional improvements to [TBS_TWIPSY]
* Fixed [TBS_POPOVER]

= 0.8.4 =
* Fixed a quoting bug in [TBS_BLOCK]
* Added [TBS_ALERT] shortcode (see guide below for TBS_BLOCK)

= 0.8.3 =
* Added option to inline "style" labels, blocks, and code.
* Added Shortcode [TBS_BLOCKQUOTE] : produces a Twitter Bootstrap styled BLOCKQUOTE with parameter "source" for citing source 
[Guide on Blockquote shortcode here](http://www.hostliketoast.com/2011/12/master-twitter-bootstrap-using-wordpress-shortcodes-part-3-blockquotes/ "Master Twitter Bootstrap Blockquotes using WordPress Shortcodes")

= 0.8.2 =
* Added option to "style" buttons inline.
* Some bug fixes with shortcodes.

= 0.8 =
* This is a huge release. We have implemented some of the major Twitter Bootstrap feature through [Wordpress Shortcodes](http://www.hostliketoast.com/2011/12/how-extend-wordpress-powerful-shortcodes/ "What are WordPress Shortcodes?").
* Shortcode [TBS_BUTTON] : produces a Twitter Bootstrap styled BUTTON [Guide on Button shortcode here](http://www.hostliketoast.com/2011/12/master-twitter-bootstrap-using-wordpress-shortcodes-part-1-buttons/ "Master Twitter Bootstrap Buttons using WordPress Shortcodes")
* Shortcode [TBS_LABEL] : produces a Twitter Bootstrap styled LABEL [Guide on Label shortcode here](http://www.hostliketoast.com/2011/12/master-twitter-bootstrap-using-wordpress-shortcodes-part-2-labels/ "Master Twitter Bootstrap Labels using WordPress Shortcodes")
* Shortcode [TBS_BLOCK] : produces a Twitter Bootstrap styled BLOCK Messages [Guide on Blockquote shortcode here](http://www.hostliketoast.com/2011/12/master-twitter-bootstrap-using-wordpress-shortcodes-part-4-alerts-and-block-messages/ "Master Twitter Bootstrap Labels using WordPress Shortcodes")
* Shortcode [TBS_CODE] : produces a Twitter Bootstrap styled CODE BLOCK
* Shortcode [TBS_TWIPSY] : produces a Twitter Bootstrap TWIPSY mouse over effect [Guide on Twipsy shortcode here](http://www.hostliketoast.com/2011/12/master-twitter-bootstrap-using-wordpress-shortcodes-part-5-twipsy-rollovers/ "Master Twitter Bootstrap Labels using WordPress Shortcodes")
* Shortcode [TBS_POPOVER] : produces a Twitter Bootstrap POPOVER window
* Shortcode [TBS_DROPDOWN] + [TBS_DROPDOWN_OPTION] : produces a Twitter Bootstrap styled DROPDOWN MENU
* Shortcode [TBS_TABGROUP] + [TAB] : produces a Twitter Bootstrap TAB! Now you can create TAB'd content in your posts!
* More documentation will be forthcoming in the [Host Like Toast WordPress Plugins Page](http://www.hostliketoast.com/wordpress-resource-centre/wordpress-plugins/ "Host Like Toast WordPress Plugins").

= 0.7 =
* Quick fix for Login and Register pages - for now there is no Bootstrap added to the login/register pages whatsoever.

= 0.6 =
* Updated to account for the latest version of Twitter Bootsrap version 1.4.0

= 0.5 =
* Re-added the attempt utilize W3 Total Cache "flush all" if the plugin is active (compatible with W3 Total Cache v0.9.2.4)
* Added some more screenshots to the docs

= 0.4 =
* Added the ability to include your own custom CSS file using a URL for the source. This custom CSS
file will be linked immediately after the bootstrap CSS (if you add it).

= 0.3 =
* Added support for 'Bootstrap, from Twitter' Javascript libraries. You can now select which of the invididual JS libraries to include.
* Inclusion of Javascript libraries is dependent upon selection of Twitter Bootstrap CSS. If this is not selected, no Javascript is added.
* Option to load Javascript files in the "HEAD" (using wp_head). The default, and recommended, is just before the closing html "BODY" (using wp_footer).

= 0.2 =
* Updated Twitter Bootstrap CSS link to version 1.3.0.

= 0.1.2 =
* Removed support for automatic W3 Total Cache flushing as the author of the other plugin has altered his code. This
is temporary until we fix.

= 0.1.1 =
* bugfix for 'None' option. Update recommended.

= 0.1 =
* First public release
* Allows you to select 1 of 3 possible styles: YUI 2 Reset; normalize CSS; or Twitter Bootstrap CSS.
* YUI 2 version 2.9.0
* Normalize CSS version 2011-08-31
* Twitter Bootstrap version 1.2.0

== Upgrade Notice ==

= 2.0.1c =
* ADDED: Ability to add the "disabled" option to Twitter Bootstrap button components.
* FIXED: a couple of bugs in the shortcodes

= 2.0.1b =
* ADDED: New shortcode [TBS_ICON](http://bit.ly/zmGUeD "Twitter Bootstrap Glyph Icon WordPress Shortcode") to allow you to easily make use of [Twitter Bootstrap Glyphicons](http://bit.ly/AxCdQj)
* ADDED: New shortcode [TBS_BUTTONGROUP] to allow you to easily make use of [Twitter Bootstrap Button Groups](http://bit.ly/z13ICu)
* CHANGED: Rewrote [TBS_BUTTON]. Now you can add "toggle" option, and specify the exact html element type, eg [a], [button], [input]
* CHANGED: Rewrote [TBS_ALERT]. Now you can add the Alert Heading using the parameter: heading="my lovely heading"
* With [TBS_ALERT], parameter "type" is no longer supported - use parameter "class" instead
* CHANGED: Added inline Javascript for activating Popover and Tooltips - nice page-loading optimization and also only execute JS code necessary
* Throughout, attempted to retain support for Twitter Bootstrap 1.4.0. But no guarantees - you should upgrade and convert asap.
* TODO: necessary javascript snippet to enable button toggling - couldn't get it working.

= 2.0.1 =
* Twitter Bootstrap library upgraded to v2.0.1

= 2.0.0 =
* Added the options for Twitter Bootstrap Library 2.0.0
* Maintained compatibility with Twitter Bootstrap Library 1.4.0
* Removed option to HotLink to resources
* Added more Javascript libraries for 1.4.0 and 2.0.0
* Fixed several bugs.
* Keeping plugin version numbering in-line with Twitter Bootstrap versioning.
* References to "Twipsy" renamed to "Tooltips" to be inline with version 2.0.0
* Most SHORTCODES work between both versions. [Latest Notes](http://bit.ly/wLkYjf "Host Like Toast WordPress Twitter Bootstrap plugin release notes v2.0")

= 0.9.1 =
* Restructured and centralized CSS on admin side.

= 0.9 =
* Improved compatibility with WordPress 3.3 and some bug fixes.

= 0.8.6 =
* [TBS_TWIPSY] and [TBS_POPOVER] are by default <SPAN> tags (There may be an option at a later date to specify the element)

= 0.8.5 =
* Made some functional improvements to [TBS_TWIPSY]
* Fixed [TBS_POPOVER].

= 0.8.4 =
* Fixed a quoting bug in [TBS_BLOCK]
* Added [TBS_ALERT] shortcode

= 0.8.3 =
* Added option to inline "style" labels, blocks, and code.
* Added [TBS_BLOCKQUOTE] shortcode with parameter "source" for citing source.

= 0.8.2 =
* Added option to "style" buttons inline.
* Some bug fixes with shortcodes.

= 0.8 =
* This is a huge release. We have implemented some of the major Twitter Bootstrap feature through [Wordpress Shortcodes](http://www.hostliketoast.com/2011/12/how-extend-wordpress-powerful-shortcodes/ "What are WordPress Shortcodes?").

= 0.7 =
* Quick fix for Login and Register pages - for now there is no Bootstrap added to the login/register pages whatsoever.

= 0.6 =
* Updated to account for the latest version of Twitter Bootsrap version 1.4.0

= 0.5 =
* Re-added the attempt utilize W3 Total Cache "flush all" if the plugin is active (compatible with W3 Total Cache v0.9.2.4)

= 0.4 =
* Added the ability to include your own custom CSS file using a URL for the source. This custom CSS
file will be linked immediately after the bootstrap CSS (if you add one).

= 0.3 =
* Added support for 'Bootstrap, from Twitter' Javascript libraries. You can now select which of the invididual JS libraries to include.

= 0.2 =
* Updated Twitter Bootstrap CSS link to version 1.3.0.

= 0.1.2 =
* Removed support for automatic W3 Total Cache flushing as the author of the other plugin has altered his code. This
is temporary until we fix.

= 0.1.1 =
* bugfix for 'None' option. Update recommended.

= 0.1 =
* First public release