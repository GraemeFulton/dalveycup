=== Plugin Name ===
Contributors: primetimejas
Donate link: http://jacobschweitzer.com/
Tags: BuddyPress, jQuery, jQueryUI, Profile, Tabs
Requires at least: 3.4.2
Tested up to: 3.6
Stable tag: 1.4.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

BuddyPress Profile Tabs is a plugin that uses the Profile Groups provided in the BuddyPress options to make nice looking tabs using the jQuery UI. 


== Description ==

There is an admin section Options -> BP Profile Tabs where you can change the jQuery UI Theme, the default is cupertino.

Any of the themes available at http://jqueryui.com/themeroller/ can be used for the tabs, it loads either Google or Microsoft hosted jQuery UI theme CSS.


== Installation ==

1. Upload bp-profile-tabs.zip to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the options under Options -> BP Profile Tabs in the Wordpress Admin section.

== Upgrade Notice ==

1.

== Frequently Asked Questions == 

= Why is the theme cupertino? =
You can change the theme BuddyPress Profile Tabs is using in the options section for the plugin under Options -> BP Profile Tabs. 

== Screenshots ==

1. Profile Page Using BuddyPress Profile Tabs and the Redmond theme
2. BuddyPress Profile Tabs Options Page
3. BuddyPress User Profile Fields Options

== Changelog ==
= 1.4 = 
New jQuery UI CSS
Fixed tab order issue 
Verified to work with BuddyPress 1.8
Verified to work with WordPress 3.6

= 1.3 =
Allows either Google or Microsoft hosted jQuery UI CSS
Changed how the menu names are loading, using PHP instead of several small Javascript insertions

= 1.1 =
* A bit of code cleanup to make it easier to read for developers.
* Re-organizing the code to make it a little more efficient.
* Re-worked a bit of the javascript to ensure compatibility. 
