=== Visitor force login page ===
Contributors: sumitsingh 
Tags: privacy, private, protected, registered only, restricted, access, closed, force user login, hidden, login, password
Requires at least: 4.0
Donate link: https://profiles.wordpress.org/sumitsingh
Tested up to: 6.2.2
Stable tag: trunk
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily hide your WordPress site from public viewing by requiring visitors to redirect specific or login page in first.


== Description ==

Easily hide your WordPress site from public viewing by requiring visitors to log in first. As simple as flipping a switch.

Make your website private until it's ready to share publicly, or keep it private for members only.

**Features**

- WordPress Multisite compatible.
- Specific page redirects visitors back to the url they tried to visit without login.
- Extensive Developer API (hooks & filters).
- Customizable. Set a specific URL to always redirect to on login.
- Filter exceptions for certain pages or posts.
- Restrict REST API to authenticated users.
- Translation Ready & WPML certified.



== Installation ==

Upload the Force Login plugin to your site, then Activate it.

Go to setting page & select menu => select Visitor force page & select a page.

You're done!


== Frequently Asked Questions ==

= 1. How can I specify a URL to redirect to on login? =

By default, the plugin sends visitors back to the URL they tried to visit. However, you can set a specific URL to always redirect users to by adding the following filter to your functions.php file.


`
/**
 * Set the URL to redirect to on login.
 *
 * @return string URL to redirect to on login. Must be absolute.
 */
1) Go to setting page & select menu => select Visitor force page & select a page.

**Requires:** WordPress 4.5 or higher

== Screenshots ==

1. Settings

== Changelog ==
= 1.0.2 =
* Fix some issue with latest wordpress version
* Tested with latest version
= 1.0.1 =
* Fix some security issue
* Tested with latest version
= 1.0 =
* just release plugin


== Upgrade Notice ==

= 1.0 =
New feature: added redirect to send visitors to specifict page to the URL they tried to visit site.
