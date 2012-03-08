=== CloudFlare ===
Contributors: i3149
Tags: cloudflare, comments, spam, cdn, free, website, performance, speed
Requires at least: 2.8
Tested up to: 3.3
Stable tag: 1.2.0
License: GPLv2

The CloudFlare WordPress Plugin ensures your WordPress blog is running optimally on the CloudFlare platform.

== Description ==

CloudFlare has developed a plugin for WordPress. By using the CloudFlare WordPress Plugin, you receive: 

* Correct IP Address information for comments posted to your site

* Optimization of your server database 

* Better protection as spammers from your WordPress blog get reported to CloudFlare

THINGS YOU NEED TO KNOW:

* The main purpose of this plugin is to ensure you have no change to your originating IPs when using CloudFlare. Since CloudFlare acts a reverse proxy, connecting IPs now come from CloudFlare's range. This plugin will ensure you can continue to see the originating IP. 

* This plugin can also help to ensure your server database is running optimally. If you are going to run the Database Optimizer associated with this plugin, then run it at a low traffic time. While the database optimizer is running, your site will go into Read Only mode, which means that you or your visitors will not be allowed to post. The optimizer should run quickly. Once the optimizer is done running, you will be able to post to your site again. 

* Every time you click the 'spam' button on your blog, this threat information is sent to CloudFlare to ensure you are constantly getting the best site protection.

* We recommend that any user on WordPress and CloudFlare should use this plugin. 

MORE INFORMATION ON CLOUDFLARE:

CloudFlare is a service that makes websites load faster and protects sites from online spammers and hackers. Any website with a root domain (ie www.mydomain.com) can use CloudFlare. On average, it takes less than 5 minutes to sign up. You can learn more here: [CloudFlare.com](https://www.cloudflare.com/overview.html).

== Installation ==

Upload the CloudFlare plugin to your blog, Activate it, and you're done!

You will also want to sign up your blog with CloudFlare.com

[Read more](http://blog.cloudflare.com/introducing-the-cloudflare-wordpress-plugin) on why we created this plugin.

== Changelog ==

= 1.2.0 =

* WP 3.3 compatibility.

= 1.1.9 =

* Includes latest CloudFlare IP allocation -- 108.162.192.0/18.

= 1.1.8 =

* WP 3.2 compatibility.

= 1.1.7 =

* Implements several security updates.

= 1.1.6 =

* Includes latest CloudFlare IP allocation -- 141.101.64.0/18.

= 1.1.5 =

* Includes latest CloudFlare IP allocation -- 103.22.200.0/22.

= 1.1.4 =

* Updated messaging.

= 1.1.3 =

* Better permission checking for DB optimizer.
* Added CloudFlare's latest /20 to the list of CloudFlare IP ranges.

= 1.1.2 =

* Fixed several broken help links.
* Fixed confusing error message.

= 1.1.1 =

* Fix for Admin menus which are breaking when page variable contains '-'.

= 1.1.0 =

* Added a box to input CloudFlare API credentials.
* Added a call to CloudFlare's report spam API when a comment is marked as spam.

= 1.0.1 =

* Fix to check that it is OK to add a header before adding one.

= 1.0.0 =

* Initial feature set
* Set RemoteIP header correctly.
* On comment spam, send the offending IP to CloudFlare.
* Clean up DB on load.
