<?php
/*
Plugin Name: Custom WP Login Widget
Plugin URI: http://www.brandonlassiter.com/clw
Description: Creates a simple login/logout widget that you can place anywhere
Version: 1.0
Author: Brandon Lassiter
Author URI: http://www.brandonlassiter.com
*/

include 'clw-functions.php';

register_activation_hook (__FILE__, 'clw_activate');
function clw_activate ()
{
	return true;
}

add_action ('init', 'clw_init');
function clw_init ()
{
	return true;
}

?>