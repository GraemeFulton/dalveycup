<?php
/**
 * WP-Members Sidebar Functions
 *
 * Handles functions for the sidebar.
 * 
 * This file is part of the WP-Members plugin by Chad Butler
 * You can find out more about this plugin at http://rocketgeek.com
 * Copyright (c) 2006-2014  Chad Butler
 * WP-Members(tm) is a trademark of butlerblog.com
 *
 * @package WordPress
 * @subpackage WP-Members
 * @author Chad Butler
 * @copyright 2006-2014
 */


/*****************************************************
LOGIN STATUS AND WIDGET FUNCTIONS
*****************************************************/


if( ! function_exists( 'wpmem_inc_status' ) ):
/**
 * Generate users login status if logged in and gives logout link
 *
 * @since 1.8
 *
 * @uses apply_filters Calls 'wpmem_logout_link' filter to change the default logout link
 *
 * @global $user_login
 * @return string $status
 */
function wpmem_inc_status()
{
	global $user_login;
	$logout = apply_filters( 'wpmem_logout_link', $url . '/?a=logout' );

	$status = '<p>' . sprintf( __( 'You are logged in as %s', 'wp-members' ), $user_login )
		. ' | <a href="' . $logout . '">' . __( 'click to log out', 'wp-members' ) . '</a></p>';

	return $status;
}
endif;


if( ! function_exists( 'wpmem_do_sidebar' ) ):
/**
 * Creates the sidebar login form and status
 *
 * This function determines if the user is logged in
 * and displays either a login form, or the user's 
 * login status. Typically used for a sidebar.		
 * You can call this directly, or with the widget
 *
 * @since 2.4
 *
 * Filters for logged out state
 * @uses apply_filters Calls 'wpmem_sb_login_args'
 * @uses apply_filters Calls 'wpmem_sb_hidden_fields'
 * @uses apply_filters Calls 'wpmem_forgot_link' filter to change the sidebar link to reset a forgotten password
 * @uses apply_filters Calls 'wpmem_reg_link' filter to change the sidebar link to the registration page
 * @uses apply_filters Calls 'wpmem_sidebar_form' filter to change the sidebar login form
 * @uses apply_filters Calls 'wpmem_login_failed_sb' filter to change the sidebar login failed message
 *
 * Filters for logged in state
 * @uses apply_filters Calls 'wpmem_logout_link' filter to change the default logout link
 * @uses apply_filters Calls 'wpmem_sidebar_status' filter to change the status message for a logged in user
 *
 * @global string $wpmem_regchk
 * @global string $user_login
 */
function wpmem_do_sidebar()
{
	global $wpmem_regchk;
	
	$url = get_bloginfo('url'); // used here and in the logout

	//this returns us to the right place
	if( isset( $_REQUEST['redirect_to'] ) ) {
		$post_to = $_REQUEST['redirect_to'];
		
	} elseif( is_home() || is_front_page() ) {
		$post_to = $_SERVER['REQUEST_URI'];
			
	} elseif( is_single() || is_page() ) {
		$post_to = get_permalink();

	} elseif( is_category() ) {
		global $wp_query;
		$cat_id  = get_query_var( 'cat' );
		$post_to = get_category_link( $cat_id );
		
	} elseif( is_search() ) {
		$post_to = $url . '/?s=' . get_search_query();
		
	} else {
		
		$post_to = $_SERVER['REQUEST_URI'];

	}
	
	// clean whatever the url is
	$post_to = esc_url( $post_to );

	if( ! is_user_logged_in() ){
	
		// if the user is not logged in, we need the form
		
		// defaults
		$defaults = array(
			// wrappers
			'error_before'    => '<p class="err">',
			'error_after'     => '</p>',
			'fieldset_before' => '<fieldset>',
			'fieldset_after'  => '</fieldset>',
			'inputs_before'   => '<div class="div_texbox">',
			'inputs_after'    => '</div>',
			'buttons_before'  => '<div class="button_div">',
			'buttons_after'   => '</div>',
			
			// messages
			'error_msg'  => __( 'Login Failed!<br />You entered an invalid username or password.', 'wp-members' ),
			'status_msg' => __( 'You are not logged in.', 'wp-members' ) . '<br />',
			
			// other
			'strip_breaks'    => true,
			'wrap_inputs'     => true,
			'n'               => "\n",
			't'               => "\t",
		);
		
		// filter $args
		$args = apply_filters( 'wpmem_sb_login_args', '' );
	
		// merge $args with defaults and extract
		extract( wp_parse_args( $args, $defaults ) );
		
		$form = '';
		
		$label = '<label for="username">' . __( 'Username', 'wp-members' ) . '</label>';
		$input = '<input type="text" name="log" class="username" id="username" />';
		
		$input = ( $wrap_inputs ) ? $inputs_before . $input . $inputs_after : $input;
		$row1  = $label . $n . $input . $n;
		
		$label = '<label for="password">' . __( 'Password', 'wp-members' ) . '</label>';
		$input = '<input type="password" name="pwd" class="password" id="password" />';
		
		$input = ( $wrap_inputs ) ? $inputs_before . $input . $inputs_after : $input;
		$row2  = $label . $n . $input . $n;
		
		$form = $row1 . $row2;

		$hidden = '<input type="hidden" name="rememberme" value="forever" />' . $n .
				'<input type="hidden" name="redirect_to" value="' . $post_to . '" />' . $n .
				'<input type="hidden" name="a" value="login" />' . $n .
				'<input type="hidden" name="slog" value="true" />';
		$form = $form . apply_filters( 'wpmem_sb_hidden_fields', $hidden );	


		$buttons = '<input type="submit" name="Submit" class="buttons" value="' . __( 'log in', 'wp-members' ) . '" />';
				
			if( WPMEM_MSURL != null ) { 
				$link = apply_filters( 'wpmem_forgot_link', wpmem_chk_qstr( WPMEM_MSURL ) . 'a=pwdreset' );	
				$buttons.= ' <a href="' . $link . '">' . __( 'Forgot?', 'wp-members' ) . '</a>&nbsp;';
			} 			
	
			if( WPMEM_REGURL != null ) {
				$link = apply_filters( 'wpmem_reg_link', WPMEM_REGURL );
				$buttons.= ' <a href="' . $link . '">' . __( 'Register', 'wp-members' ) . '</a>';
			}
		
		$form = $form . $n . $buttons_before . $buttons . $n . $buttons_after;
		
		$form = $fieldset_before . $n . $form . $n . $fieldset_after;
		
		$form = '<form name="form" method="post" action="' . $post_to . '">' . $n . $form . $n . '</form>';
		
		// add status message
		$form = $status_msg . $n . $form;
		
		// strip breaks
		$form = ( $strip_breaks ) ? str_replace( array( "\n", "\r", "\t" ), array( '','','' ), $form ) : $form;
		
		$form = apply_filters( 'wpmem_sidebar_form', $form );
		
		$do_error_msg = '';
		if( isset( $_POST['slog'] ) && $wpmem_regchk == 'loginfailed' ) {
			$do_error_msg = true;
			$error_msg = $error_before . $error_msg . $error_after;
			$error_msg = apply_filters( 'wpmem_login_failed_sb', $error_msg );
		}
		$form = ( $do_error_msg ) ? $error_msg . $form : $form;
		
		echo $form;

	} else { 
	
		global $user_login; 
		$logout = apply_filters( 'wpmem_logout_link', $url . '/?a=logout' );
		
		$str = '<p>' . sprintf( __( 'You are logged in as %s', 'wp-members' ), $user_login ) . '<br />
		  <a href="' . $logout . '">' . __( 'click here to logout', 'wp-members' ) . '</a></p>';
		
		$str = apply_filters( 'wpmem_sidebar_status', $str );
		
		echo $str;
	}
}
endif;


/**
 * Class for the sidebar login widget
 *
 * @since 2.7
 */
class widget_wpmemwidget extends WP_Widget 
{

    /**
	 * Sets up the WP-Members login widget.
	 */
    function widget_wpmemwidget() 
	{
        $widget_ops = array( 
			'classname'   => 'wp-members', 
			'description' => 'Display the WP-Members sidebar login.' 
			); 
        $this->WP_Widget( 'widget_wpmemwidget', 'WP-Members Login', $widget_ops );
    }
 
    /**
	 * Displays the WP-Members login widget settings 
	 * controls on the widget panel.
	 *
	 * @param array $instance
	 */
    function form( $instance ) 
	{
	
		/* Default widget settings. */
		$defaults = array( 'title' => __('Login Status', 'wp-members') );
		$instance = wp_parse_args( ( array ) $instance, $defaults );
		
		/* Title input */ ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'wp-members'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:95%;" />
		</p>
		<?php
    }
 
	/**
	 * Update the WP-Members login widget settings.
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array $instance
	 */
    function update( $new_instance, $old_instance ) 
	{
		$instance = $old_instance;
		
		/* Strip tags for title to remove HTML. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		
        return $instance;
    }
 
    /**
	 * Displays the WP-Members login widget.
	 *
	 * @uses apply_filters Calls 'widget_title' a WP filter to change the widget title
	 *
	 * @param array $args
	 * @param array $instance
	 */
    function widget( $args, $instance ) 
	{
		extract( $args );

		// Get the Widget Title
		$title = ( array_key_exists( 'title', $instance ) ) ? $instance['title'] : __( 'Login Status', 'wp-members' );
		
		echo $before_widget;
		echo '<div id="' . apply_filters( 'wpmem_widget_id', 'wp-members' ) . '">';

			// The Widget Title
			echo $before_title . apply_filters( 'wpmem_widget_title', $title ) . $after_title;

			// The Widget
			if( function_exists( 'wpmem' ) ) { wpmem_do_sidebar(); }

		echo '</div>';
		echo $after_widget;
    }
}

/** End of File **/