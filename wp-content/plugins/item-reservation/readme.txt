=== Item Reservation ===
Contributors: keyit
Tags: plugin
Requires at least: 3.5.1
Tested up to: 3.5.1
Stable tag: trunk
License: GPLv2 or later

Manage reservation of items by users. Originally designed as a wedding gift list but can be used as a general gift list/wish list reservation plugin. 

== Description ==

Manage reservation of items by users. Originally designed as a wedding gift list but can be used as a general gift list/wish list reservation plugin. 
Uses shortcode to display lists on a page. Only allows logged in users to reserve items and edit item reservations. 
Admin settings determine the type of the item e.g Gift, whether to display users associated with items or remain anonymous and the currency.

== Requirements ==

* WordPress version 3.5.1 and later

Please visit [the official website](http://keyituk.com/wordpress-plugin-item-reservation/ "Item Reservation") for further details and the latest information on this plugin.

== Details ==

= Shortcode =

Uses shortcode to display lists on a page.

= Page Shortcodes =

* [glkit-list] Display the list of items.
* [glkit-list-users] Display the list of items for the logged in user.

= Item Shortcodes =

Uses shortcode to display meta data for an item (not page).

* [glkit show=id]Display the id of the item from meta data
* [glkit show=price] Display the price of the item from meta data
* [glkit show=colour] Display the colour of the item from meta data
* [glkit show=supplier] Display the supplier of the item from meta data
* [glkit show=url] Display the url as a link from the item's meta data
* [glkit show=required] Display the required number of items from meta data


= Widget =

There is a widget to display a specified number of items. 
Only items that are available for reservation are displayed.

= Taxonomy classes =

There are 2 taxonomy classes.

1. Category - originally designed for the event type if running items for multiple events.
2. Price Range.

= Meta Data =

Corresponds to item shortcodes.

* id - string
* price - number
* colour - string
* supplier - string
* url - url
* required - integer

= Translations =

The application is translation ready, feel free to make your translation related to your native language.
If you would like to submit translations please email me.

== Installation ==

This section describes how to install the plugin and get it working.

1. Uncompress the download package.
2. Upload folder including all files and sub directories to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Create items and display them on pages using shortcode.

== Frequently Asked Questions ==

= Can users purchase items =

No but a link to the supplier can be provided


