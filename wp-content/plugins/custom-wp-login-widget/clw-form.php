<?php

add_action ('clw_form', 'clw_form');
function clw_form ($form_id)
{

	if ( is_user_logged_in() ) { ?>
		<? $current_user = wp_get_current_user(); ?>
		<h3>Hello <? echo $current_user->display_name ?>!</h3>
		<a href="<?php echo wp_logout_url($_SERVER['REQUEST_URI']); ?>">Logout</a>
	<?
	} else { ?>
		<h3>Login</h3>
		<form action="<?php echo get_option('home'); ?>/wp-login.php" method="post">
		<div>Username: <input type="text" name="log" id="log" value="<?php echo wp_specialchars(stripslashes($user_login), 1) ?>" size="20" /></div><br>
		<div>Password: <input type="password" name="pwd" id="pwd" size="20" /></div><br>
		<div><input type="submit" name="submit" value="Send" class="button" /></div><br>
		    <p>
		       <label for="rememberme"><input name="rememberme" id="rememberme" type="checkbox" checked="checked" value="forever" /> Remember me</label>
		       <input type="hidden" name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>" />
		    </p>
		</form>
		<a href="<?php echo get_option('home'); ?>/wp-login.php?action=lostpassword">Recover password</a> | <a href="<?php echo get_option('home'); ?>/wp-login.php?action=register">Create an Account</a>
<?
	} 

}
?>
