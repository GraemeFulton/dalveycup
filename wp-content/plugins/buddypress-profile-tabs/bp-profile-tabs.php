<?php
/*
Plugin Name: BuddyPress Profile Tabs
Plugin URI: http://jacobschweitzer.com/
Description: Makes the profile groups BuddyPress provides into tabs to better organize and consolidate the profile page. 
Version: 1.4.2
Author: Jacob Schweitzer
Author URI: http://jacobschweitzer.com/
License: GPL2

Known conflict plugins:
	BP GTM System (datepicker error breaks jquery)
	
Works with:
	BP Profile Search
	Bp Recent Profile Visitors
	BuddyPress Activity Plus
	BuddyPress Gifts Rebirth
	BuddyPress Links
	BuddyPress Message privacy
	BuddyPress Password Strength Meter
	BuddyPress Portfolio
	Cleverness To-Do List
	Invite Anyone
	BP Resume Page
	Toolbar Remixed
	BuddyPress User Testimonials
	Events +
	visualCaptcha
	
Unknown:
	
	
	

*/
function bp_profile_tabs_actions() {
	add_options_page("BP Profile Tabs", "BP Profile Tabs", 'manage_options', "BPProfileTabs", "bp_profile_tabs_admin");
}
add_action('admin_menu', 'bp_profile_tabs_actions');
		
	
function bp_profile_tabs_admin() {

// Reset old option
$bpt_options = get_option("bp_profile_tabs_option");
if ( $bpt_options == '' ) {
	$jquery_theme_option = get_option("bp_profile_tabs_theme_option");
	if ( $jquery_theme_option != '' ) {
		$bpt_options['bpt_theme'] = $jquery_theme_option;
		$bpt_options['bpt_cdn'] = 'google';
		update_option("bp_profile_tabs_option",$bpt_options);
		delete_option("bp_profile_tabs_theme_option");
	}
	if ( $jquery_theme_option == '' ) {
		$bpt_options['bpt_theme'] = 'cupertino';
		$bpt_options['bpt_cdn'] = 'google';
		update_option("bp_profile_tabs_option",$bpt_options);
	}
}
// Reset old option
	?>
	<h2>BuddyPress Profile Tabs Admin</h2>
	<form id="bp_profile_tabs_theme_form" class="bp_profile_tabs_theme_form_class">
		<h3>Theme</h3>
		<select name="bpt_theme" id="bpt_theme">
			<option value="base">Base</option>
			<option value="black-tie">Black Tie</option>
			<option value="blitzer">Blitzer</option>
			<option value="cupertino">Cupertino</option>
			<option value="dark-hive">Dark Hive</option>
			<option value="dot-luv">Dot Luv</option>
			<option value="eggplant">Eggplant</option>
			<option value="excite-bike">Excite Bike</option>
			<option value="flick">Flick</option>
			<option value="hot-sneaks">Hot Sneaks</option>
			<option value="humanity">Humanity</option>
			<option value="le-frog">Le Frog</option>
			<option value="mint-choc">Mint Choc</option>
			<option value="overcast">Overcast</option>
			<option value="pepper-grinder">Pepper Grinder</option>
			<option value="redmond">Redmond</option>
			<option value="smoothness">Smoothness</option>
			<option value="south-street">South Street</option>
			<option value="start">Start</option>
			<option value="sunny">Sunny</option>
			<option value="swanky-purse">Swanky Purse</option>
			<option value="trontastic">Trontastic</option>
			<option value="ui-darkness">Ui Darkness</option>
			<option value="ui-lightness">Ui lightness</option>
			<option value="vader">Vader</option>
		</select>
		<h3>Load Theme From</h3>
		<select name="bpt_cdn" id="bpt_cdn">
			<option value="google">Google</option>
			<option value="microsoft">Microsoft</option>
		</select>
		<br/><br/>
		<input type="hidden" name="action" id="action" value="bp_profile_tabs_ajax_save" />
		<input type="submit" value="Submit" id="submit" />
	</form>
	<div id="bp_profile_tabs_change_result">
		<span></span>
	</div>
	<script type="text/javascript">		
		jQuery(document).ready(function($) {
			$("#bp_profile_tabs_theme_form select#bpt_theme").val("<?php echo $bpt_options['bpt_theme']; ?>");
			$("#bp_profile_tabs_theme_form select#bpt_cdn").val("<?php echo $bpt_options['bpt_cdn']; ?>");

			$("form#bp_profile_tabs_theme_form input#submit").click(function(){
				var data = $("form#bp_profile_tabs_theme_form").serialize();
				
				$.post(ajaxurl, data, function(response) {
					$("div#bp_profile_tabs_change_result span").replaceWith("<span>"+response+"</span>");
					$("div#bp_profile_tabs_change_result span").contents().fadeOut(3000);
				});
			return false;
			});
		});
	</script>
			<?php
}
		
function bp_profile_tabs_ajax_save() {
	$data = $_POST;
	update_option("bp_profile_tabs_option", $data);
	echo '<p style="font-size:24px;color:#00FF00;">Options Updated</p>';
	die();
}
add_action('wp_ajax_bp_profile_tabs_ajax_save', 'bp_profile_tabs_ajax_save');

function bp_profile_tabs_top() {
	echo '<div id="tabs"><ul>';

	global $wpdb;
	$profile_field_groups_query = 'SELECT name FROM `'.$wpdb->prefix.'bp_xprofile_groups` order by group_order';
	$groups_list = $wpdb->get_col($profile_field_groups_query);
	$looper = 1;
	foreach ( $groups_list as $one_group_name ) {
	
		echo '<li><a href="#tabs-'.$looper.'">';
		echo $one_group_name;
		echo '</a></li>';
		$looper++;
	}
	

	echo '</ul>';
}
add_action('bp_before_profile_loop_content','bp_profile_tabs_top');

//function alter_group_names($groupname) {
//	return $groupname.' stuff';
//}
//add_filter('bp_get_profile_group_name','alter_group_names',5,1);

function bp_profile_tabs_div() {
	global $looper;
	$looper++;
	echo '<div id="tabs-'.$looper.'">';
}
add_action('bp_before_profile_field_content','bp_profile_tabs_div');


function bp_profile_tabs_end_content() { echo "</div>"; }
add_action('bp_after_profile_field_content','bp_profile_tabs_end_content');

function bp_profile_tabs_end() { echo "</div>"; }
add_action('bp_after_profile_loop_content','bp_profile_tabs_end');


function bp_profile_tabs() {	
	echo '
	<script type="text/javascript">
	jQuery(document).ready( function($) { 
		$( "div.profile > div#tabs" ).tabs();
	}); 
	</script>';
}
add_action('bp_before_profile_loop_content','bp_profile_tabs');

add_action('bp_before_profile_loop_content','bpt_load');
function bpt_load() {
	$bpt_options = get_option("bp_profile_tabs_option");
	if ( $bpt_options == '' ) {
		$jquery_theme_option = get_option("bp_profile_tabs_theme_option");
		if ( $jquery_theme_option != '' ) {
			$bpt_options['bpt_theme'] = $jquery_theme_option;
			$bpt_options['bpt_cdn'] = 'google';
			update_option("bp_profile_tabs_option",$bpt_options);
			delete_option("bp_profile_tabs_theme_option");
		}
		if ( $jquery_theme_option == '' ) {
			$bpt_options['bpt_theme'] = 'cupertino';
			$bpt_options['bpt_cdn'] = 'google';
			update_option("bp_profile_tabs_option",$bpt_options);
		}
	}

	echo '<link rel="stylesheet" href="';
	if ( $bpt_options['bpt_cdn'] == 'google' ) {
			echo 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/themes/'.$bpt_options['bpt_theme'].'/jquery-ui.css" />';
	}
	if ( $bpt_options['bpt_cdn'] == 'microsoft' ) {
		echo 'http://ajax.aspnetcdn.com/ajax/jquery.ui/1.10.2/themes/'.$bpt_options['bpt_theme'].'/jquery-ui.css" />';
	}

	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-tabs' );
}


/**
 * Returns current plugin version.
 *
 * @return string Plugin version
 */
function bpt_get_version() {
	if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
	$plugin_file = basename( ( __FILE__ ) );
	return $plugin_folder[$plugin_file]['Version'];
}