=== Embed RSS ===
Contributors: DeannaS, kgraeme

Tags: inline RSS, inline feed, post RSS, post feed, tinymce, shortcode
Requires at least: 2.7
Tested up to: 3.0
Stable tag: trunk

cets_EmbedRSS lets users embed an RSS feed into a post or page.

== Description ==

This tag places an icon in the tinymce visual editor and a button in the html editor that allows a user to enter an RSS feed url, choose the number of items to display, and choose to display content, author or date for each item. It works via the use of a shortcode in the following format:

[cetsEmbedRSS id='http://deannaschneider.wordpress.com' itemcount='2' itemauthor='1' itemdate='1' itemcontent='1']

== Installation ==

Extract the cets_EmbedRSS folder and place in the wp-content/plugins folder. Enable the plugin.

To upgrade from 1.3.3 to 1.4, delete the cets\_EmbeddRss\_config.php file and the tinymce/window.php file. Upload the rest of the files.

== Screenshots ==

1. User View of pop-up window for entering RSS link via visual editor.
2. User View of pop-up window for entering RSS link via html editor.
3. Site visitor view of embedded RSS with default attributes.

== Changelog ==
1.5 - fixed non-loading CSS. Updated for WP 3.0

1.4 - changed the structure to get rid of the wp-load. Uses different type of pop up box now.

1.3.3 - Tested on 2.9.1.1 and fixed a typo that resulted in an extra closing li

1.3.2 - Updated contributors and fixed minor typos (did not affect functionality).

1.3.1 - updated project structure to work with WordPress.org auto install/update.

