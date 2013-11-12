<?php



class Ajax_Auth_users
{
	
	public static function authenticate_users ( $action ){
	
	
	
		// allow plugins to override the default actions, and to add extra actions if they want
		do_action( 'login_init' );
	
	
		$http_post = ('POST' == $_SERVER['REQUEST_METHOD']);
	
		switch ( $action ){
	
	
			case 'register':
					
				if ( !get_option('users_can_register') ) {
	
					return array('operation' => 'message', 'message' => "<p class='sp-error'>".__('Sorry, register to this site is disabled at this moment.', 'pwLogWi')."<p>" ) ;
				}
					
				$user_login = '';
				$user_email = '';
				if ( $http_post ) {
					$user_login = $_POST['user_login'];
					$user_email = $_POST['user_email'];
					$errors = self::register_new_user($user_login, $user_email);
					if ( !is_wp_error($errors) ) {
						return array('operation' => 'message' , 'message' => "<p class='sp-message'>".__('Register successfull, Please check your email.', 'pwLogWi')."</p>" );
					}else{
						return array('operation' => 'message' , 'message' => self::login_header('', '', $errors) );
					}
				}
					
				break;
					
					
			case 'lostpassword':
			case 'retrievepassword' :
					
				if ( $http_post ) {
					$errors = self::retrieve_password();
					if ( !is_wp_error($errors) ) {
						return array('operation' => 'message' , 'message' => "<p class='sp-message'>".__('Check your e-mail for the confirmation link.', 'pwLogWi')."</p>");
					}else{
	
						do_action('lost_password');
	
						return array('operation' => 'message' , 'message' => self::login_header('', '', $errors) );
						
					}
				}
					
				break;
					
			case 'login':
			default:
				
				//fixup limit loging attempts bug
				if (function_exists('limit_login_add_error_message')){
					limit_login_add_error_message();
				}
				
				
				$secure_cookie = '';
					
				if ( isset( $_REQUEST['redirect_to'] ) ) {
					$redirect_to = $_REQUEST['redirect_to'];
					// Redirect to https if user wants ssl
					if ( $secure_cookie && false !== strpos($redirect_to, 'wp-admin') )
						$redirect_to = preg_replace('|^http://|', 'https://', $redirect_to);
				} else {
					$redirect_to = admin_url();
				}
					
				$errors = wp_signon('', $secure_cookie);
					
				//$redirect_to = apply_filters('login_redirect', $redirect_to, isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '', $user);
					
					
				if (is_wp_error($errors)){
					//fix error message for invalid username or inccorrect password
					if (array_key_exists('incorrect_password', $errors->errors) || array_key_exists('invalid_username', $errors->errors)){
						unset($errors->errors['incorrect_password']);
						unset($errors->errors['invalid_username']);
						$errors->add('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.', 'pwLogWi'));
					}
					
					return array('operation' => 'message' , 'message' => self::login_header('', '', $errors) );
					
				}else{
					
					return array( 'operation' => 'redirect' , 'redirect_to' => $redirect_to);
				
				}
					
					
				break;
		}
	
	}
	
	/**
	 * Handles registering a new user.
	 *
	 * @param string $user_login User's username for logging in
	 * @param string $user_email User's email address to send password and add
	 * @return int|WP_Error Either user's ID or error on failure.
	 */
	static function register_new_user( $user_login, $user_email ) {
		$errors = new WP_Error();
	
		$sanitized_user_login = sanitize_user( $user_login );
		$user_email = apply_filters( 'user_registration_email', $user_email );
	
		// Check the username
		if ( $sanitized_user_login == '' ) {
			$errors->add( 'empty_username', __( '<strong>ERROR</strong>: Please enter a username.' ) );
		} elseif ( ! validate_username( $user_login ) ) {
			$errors->add( 'invalid_username', __( '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.' ) );
			$sanitized_user_login = '';
		} elseif ( username_exists( $sanitized_user_login ) ) {
			$errors->add( 'username_exists', __( '<strong>ERROR</strong>: This username is already registered. Please choose another one.' ) );
		}
	
		// Check the e-mail address
		if ( $user_email == '' ) {
			$errors->add( 'empty_email', __( '<strong>ERROR</strong>: Please type your e-mail address.' ) );
		} elseif ( ! is_email( $user_email ) ) {
			$errors->add( 'invalid_email', __( '<strong>ERROR</strong>: The email address isn&#8217;t correct.' ) );
			$user_email = '';
		} elseif ( email_exists( $user_email ) ) {
			$errors->add( 'email_exists', __( '<strong>ERROR</strong>: This email is already registered, please choose another one.' ) );
		}
	
		do_action( 'register_post', $sanitized_user_login, $user_email, $errors );
	
		$errors = apply_filters( 'registration_errors', $errors, $sanitized_user_login, $user_email );
	
		if ( $errors->get_error_code() )
			return $errors;
	
		$user_pass = wp_generate_password( 12, false);
		$user_id = wp_create_user( $sanitized_user_login, $user_pass, $user_email );
		if ( ! $user_id ) {
			$errors->add( 'registerfail', sprintf( __( '<strong>ERROR</strong>: Couldn&#8217;t register you... please contact the <a href="mailto:%s">webmaster</a> !' ), get_option( 'admin_email' ) ) );
			return $errors;
		}
	
		update_user_option( $user_id, 'default_password_nag', true, true ); //Set up the Password change nag.
	
		wp_new_user_notification( $user_id, $user_pass );
	
		return $user_id;
	}
	
	/**
	 * Handles sending password retrieval email to user.
	 *
	 * @uses $wpdb WordPress Database object
	 *
	 * @return bool|WP_Error True: when finish. WP_Error on error
	 */
	static function retrieve_password() {
		global $wpdb, $current_site;
	
		$errors = new WP_Error();
	
		if ( empty( $_POST['user_login'] ) ) {
			$errors->add('empty_username', __('<strong>ERROR</strong>: Enter a username or e-mail address.'));
		} else if ( strpos( $_POST['user_login'], '@' ) ) {
			$user_data = get_user_by( 'email', trim( $_POST['user_login'] ) );
			if ( empty( $user_data ) )
				$errors->add('invalid_email', __('<strong>ERROR</strong>: There is no user registered with that email address.'));
		} else {
			$login = trim($_POST['user_login']);
			$user_data = get_user_by('login', $login);
		}
	
		do_action('lostpassword_post');
	
		if ( $errors->get_error_code() )
			return $errors;
	
		if ( !$user_data ) {
			$errors->add('invalidcombo', __('<strong>ERROR</strong>: Invalid username or e-mail.'));
			return $errors;
		}
	
		// redefining user_login ensures we return the right case in the email
		$user_login = $user_data->user_login;
		$user_email = $user_data->user_email;
	
		do_action('retreive_password', $user_login);  // Misspelled and deprecated
		do_action('retrieve_password', $user_login);
	
		$allow = apply_filters('allow_password_reset', true, $user_data->ID);
	
		if ( ! $allow )
			return new WP_Error('no_password_reset', __('Password reset is not allowed for this user'));
		else if ( is_wp_error($allow) )
			return $allow;
	
		$key = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE user_login = %s", $user_login));
		if ( empty($key) ) {
			// Generate something random for a key...
			$key = wp_generate_password(20, false);
			do_action('retrieve_password_key', $user_login, $key);
			// Now insert the new md5 key into the db
			$wpdb->update($wpdb->users, array('user_activation_key' => $key), array('user_login' => $user_login));
		}
		$message = __('Someone requested that the password be reset for the following account:') . "\r\n\r\n";
		$message .= network_home_url( '/' ) . "\r\n\r\n";
		$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
		$message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
		$message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
		$message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";
	
		if ( is_multisite() )
			$blogname = $GLOBALS['current_site']->site_name;
		else
			// The blogname option is escaped with esc_html on the way into the database in sanitize_option
			// we want to reverse this for the plain text arena of emails.
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	
		$title = sprintf( __('[%s] Password Reset'), $blogname );
	
		$title = apply_filters('retrieve_password_title', $title);
		$message = apply_filters('retrieve_password_message', $message, $key);
	
		if ( $message && !wp_mail($user_email, $title, $message) )
			wp_die( __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function...') );
	
		return true;
	}
	
	/**
	 * Return the header for the login page.
	 *
	 * @uses apply_filters() Calls 'login_message' on the message to display in the
	 *		header.
	 * @uses $error The error global, which is checked for displaying errors.
	 *
	 * @param string $title Optional. WordPress Log In Page title to display in
	 *		<title/> element.
	 * @param string $message Optional. Message to display in header.
	 * @param WP_Error $wp_error Optional. WordPress Error Object
	 */
	static function login_header($title = 'Log In', $message = '', $wp_error = '') {
		global $error;
		
		$return ="";
	
		if ( empty($wp_error) )
			$wp_error = new WP_Error();
	
	
		//do_action( 'login_head' );
	
		$message = apply_filters('login_message', $message);
		if ( !empty( $message ) )
			$return .= $message . "\n";
	
		// In case a plugin uses $error rather than the $wp_errors object
		if ( !empty( $error ) ) {
			$wp_error->add('error', $error);
			unset($error);
		}
	
		if ( $wp_error->get_error_code() ) {
			$errors = '';
			$messages = '';
			foreach ( $wp_error->get_error_codes() as $code ) {
				$severity = $wp_error->get_error_data($code);
				foreach ( $wp_error->get_error_messages($code) as $error ) {
					if ( 'message' == $severity )
						$messages .= '	' . $error . "<br />\n";
					else
						$errors .= '	' . $error . "<br />\n";
				}
			}
			if ( !empty($errors) )
				$return .=  '<div id="login_error">' . apply_filters('login_errors', $errors) . "</div>\n";
			if ( !empty($messages) )
				$return .=  '<p class="message">' . apply_filters('login_messages', $messages) . "</p>\n";
		}
		
		return $return;
	} // End of login_header()
	
	
	
	
	
}

?>