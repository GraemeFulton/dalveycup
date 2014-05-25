<?php
/*
Plugin Name: Secret Passcode
Plugin URI: http://gfulton.me.uk
Description: Simple plugin to add a honeypot passcode to the BuddyPress registration form to prevent spam registrations.
Version: 1.1
Author: Gray
Author URI: http://gfulton.me.uk
*/

/**
 * Copyright (c) 2012 Pixel Jar. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * **********************************************************************
 */

// INTERNATIONALIZATION
load_plugin_textdomain( 'pj-buddypress-honeypot', null, basename( dirname( __FILE__ ) ) );

class secret_passcode {

	
	define('SECRET_PASSCODE', 'est1897');

	function __construct() {
		add_action( 'bp_after_signup_profile_fields', array( &$this, 'add_secret_passcode' ) );
		add_filter( 'bp_core_validate_user_signup', array( &$this, 'check_secret_passcode' ) );
	}

	/**
	 * Add a hidden text input that users won't see
	 * so it should always be empty. If it's filled out
	 * we know it's a spambot or some other hooligan
	 *
	 * @filter bppj_honeypot_name
	 * @filter bppj_honeypot_id
	 */
	function add_secret_passcode() {
		
		echo '<h2>Enter Dalvey Cup Secret Code</h2>';
		echo "<p>What's the secret answer? If you don't know it, ask an existing Dalvey Cup Member";
		?>
    <p>
    <label for="secret_reg_code">Secret Code<br />
    <input type="text" name="secret_reg_code" id="secret_reg_code" class="input" value="" size="25" /></label>
    </p>
    <?php
	}

	/**
	 * Check to see if the honeypot field has a value.
	 * If it does, return an error
	 *
	 * @filter bppj_honeypot_name
	 * @filter bppj_honeypot_fail_message
	 */
	function check_secret_passcode( $result = array() ) {
		global $bp;

		$bppj_honeypot_name = apply_filters( 'bppj_honeypot_name', self::BPPJ_HONEYPOT_NAME );

		if( defined('SECRET_PASSCODE') && trim($_POST['secret_reg_code']) != SECRET_PASSCODE ){

			$result['errors']->add( 'pjbp_honeypot', apply_filters( 'bppj_honeypot_fail_message', __( "You're totally a spammer. Go somewhere else with your spammy ways." ) ) );

		}
		
		return $result;
	}

}
new secret_passcode;
