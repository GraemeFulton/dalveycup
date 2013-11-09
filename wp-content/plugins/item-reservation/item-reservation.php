<?php
/*
 Plugin Name: Item Reservation
Plugin URI: http://keyituk.com/wordpress-plugin-item-reservation/
Description: Manage reservation of items by users. Originally designed as a wish list organiser for weddings.
Version: 1.0
Author: Nicola Marriott & Mark Jackson
Author URI: http://keyituk.com
License: GPLv2
*/

/*  Copyright 2013  Nicola Marriott  (email : nicky@keyituk.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//Global variables
$tablename = $wpdb->prefix."keyit_wp_reservation";
$kit_post_type = 'kit_item';
// Call function when plugin is activated
register_activation_hook( __FILE__, 'gift_list_kit_install' );

function gift_list_kit_install() {
	global $wpdb;
	global $tablename;
	//Setup default option values
	$glkit_options_arr = array(
			'currency_sign' => '&pound;',
			'item_type' => 'Item',
			'show_user_email' => 'on'
	);

	//save our default option values
	update_option( 'glkit_options', $glkit_options_arr );
	$glkit_currency_sign = $glkit_options_arr['currency_sign'];

	if( $wpdb->get_var("SHOW TABLES LIKE '$tablename'") != $tablename ) {
		// Table does not exist!
		$sql = "CREATE  TABLE  $tablename (
		ID BIGINT(20) NOT NULL AUTO_INCREMENT ,
		wp_users_ID BIGINT(20) UNSIGNED NOT NULL ,
		wp_posts_ID BIGINT(20) UNSIGNED NOT NULL ,
		date_stamp datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		quantity INT NOT NULL ,
		INDEX fk_keyit_wp_reservation_wp_users1_idx (".$wpdb->prefix."users_ID ASC) ,
		INDEX fk_keyit_wp_reservation_wp_posts1_idx (".$wpdb->prefix."posts_ID ASC) ,
		PRIMARY KEY (ID) ,
		CONSTRAINT fk_keyit_wp_reservation_wp_users1
		FOREIGN KEY (".$wpdb->prefix."users_ID )
		REFERENCES ".$wpdb->prefix."users (ID )
		ON DELETE NO ACTION
		ON UPDATE NO ACTION,
		CONSTRAINT fk_keyit_wp_reservation_wp_posts1
		FOREIGN KEY (".$wpdb->prefix."posts_ID )
		REFERENCES ".$wpdb->prefix."posts (ID )
		ON DELETE CASCADE
		ON UPDATE CASCADE
		)
		ENGINE = InnoDB;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}

// Action hook to initialize the plugin
add_action( 'init', 'gift_list_kit_init' );

//Initialize the Gift List plugin
function gift_list_kit_init() {
	global $wpdb;
	//Bad thing to load my js in admin //TODO fix
	if (!is_admin()) {
		//wp_enqueue_script( 'keyit_custom', plugins_url( 'js/keyit_custom.js' , __FILE__ ), array('jquery'));
		wp_register_style( 'keyit-style', plugins_url('css/keyitstyle.css', __FILE__) );
		wp_enqueue_style( 'keyit-style' );
	}

	//Admin labels
	$labels = array(
			'name' => __( 'Items', 'glkit-plugin' ),
			'singular_name' => __( 'Item', 'glkit-plugin' ),
			'add_new' => __( 'Add New', 'glkit-plugin' ),
			'add_new_item' => __( 'Add New Item', 'glkit-plugin' ),
			'edit_item' => __( 'Edit Item', 'glkit-plugin' ),
			'new_item' => __( 'New Item', 'glkit-plugin' ),
			'all_items' => __( 'All Items', 'glkit-plugin' ),
			'view_item' => __( 'View Item', 'glkit-plugin' ),
			'search_items' => __( 'Search Items', 'glkit-plugin' ),
			'not_found' =>  __( 'No items found', 'glkit-plugin' ),
			'not_found_in_trash' => __( 'No items found in Trash', 'glkit-plugin' ),
			'menu_name' => __( 'Item Reservation', 'glkit-plugin' )
	);

	$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'query_var' => true,
			'rewrite' => true,
			'capability_type' => 'post',
			'has_archive' => true,
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt' )
	);

	//Register the products custom post type
	register_post_type( 'kit_item', $args );

	//Remove WYSIWYG we're driving presentation
	//remove_post_type_support( 'kit_item', 'editor');

	//Are we submitting an insert/update on reservation
	if(isset( $_POST["page"])&&$_POST["page"]=="reserve");

	{
		//Done this way instead of javascript callback so will process once if javascript enabled and will
		//still process if javascript disabled
		gift_list_kit_callback();
	}
}

//We are submitting an insert/update on reservation
function gift_list_kit_save_reservation() {

}

//Need to process if javascript disabled
//add_action('wp_ajax_gift_list_kit', 'gift_list_kit_callback');

function gift_list_kit_callback() {
	global $wpdb;
	global $tablename;
	//It's no good without the other bits
	if(isset($_POST["user_id"])&&isset($_POST["post_id"])&&isset($_POST["quantity"]))
	{
		$user_id=$_POST["user_id"];
		$post_id=$_POST["post_id"];
		$quantity=$_POST["quantity"];

		//Has this user already got a reservation for this post
		$sql='SELECT * FROM '. $tablename .' where wp_users_ID= '.$wpdb->escape($user_id).' and wp_posts_ID= '.$wpdb->escape($post_id);
		$wpdb->get_results( $sql );
		$nuw_rows = $wpdb->num_rows;

		//If so update
		if($nuw_rows)
		{
			//Update
			$newdata = array(

					'quantity'  =>  $wpdb->escape($quantity),
					'date_stamp' => current_time('mysql')
			);
			$where = array(
					'wp_posts_ID' => $wpdb->escape($post_id),
					'wp_users_ID' => $wpdb->escape($user_id)
			);


			$wpdb-> update(
					$tablename,
					$newdata,
					$where
			);
		}
		else
		{
			//Insert
			$newdata = array(
					wp_users_ID => $wpdb->escape($user_id),
					wp_posts_ID => $wpdb->escape($post_id),
					quantity  =>  $wpdb->escape($quantity),
					date_stamp => current_time('mysql')
			);
			$wpdb-> insert(
					$tablename,
					$newdata
			);
		}

	}

}

function gift_list_kit_javascript() {
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	 var $j = jQuery.noConflict();
	 var d = new Date();
		 d = d.getTime();
		
		if ($j("#reservation-submit").length > 0){
			$j("#reservation-submit").hide();
		}
		if ($j("#reloadValue").length > 0){
			if ($j('#reloadValue').val().length == 0)
			{
				$j('#reloadValue').val(d);
				$j('body').show();
			}
			else
			{
				$j('#reloadValue').val('');
			    location.reload();
			}
		}
		
		// Validate number
		$j.fn.ForceNumericOnly =
		function(int)
		{
		    return this.each(function()
		    {
		        $(this).keydown(function(e)
		        {
		            var key = e.charCode || e.keyCode || 0;
		            // If not integer allow decimal point
		           if(!int) {
		        	   return ( key == 8 || 
		   	                key == 9 ||
			                key == 46 ||
			                key == 110 ||
			                key == 190 ||
			                (key >= 35 && key <= 40) ||
			                (key >= 48 && key <= 57) ||
			                (key >= 96 && key <= 105));
		           }
		           else
		        	   {
		        	   return ( key == 8 || 
			   	                key == 9 ||
				                key == 110 ||
				                key == 190 ||
				                (key >= 35 && key <= 40) ||
				                (key >= 48 && key <= 57) ||
				                (key >= 96 && key <= 105));
		        	   }
		        });
		    });
		};
		var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';

		$("input[name=gift-list-gift_sku]").ForceNumericOnly(false);
		$("input[name=gift-list-gift_price]").ForceNumericOnly(false);
		$("input[name=gift-list-gift_required]").ForceNumericOnly(true);
		$j("select[name=quantity]").change(function(e) {
			e.preventDefault(); // this disables the submit button so the user stays on the page
		    // this collects all of the data submitted by the form
	       	//var url = location.pathname+"wp-content/plugins/item-reservation/resources/process.php";
			$quantity = $j('select[name=quantity]' ).val();
			$user_reserved = $j('input[name=user_reserved]' ).val();
			$total_reserved = $j('input[name=total_reserved]' ).val();
			$still_required = $j('input[name=still_required]' ).val();
			$total_required = $j('input[name=total_required]' ).val();
			$user_id = $j('input[name=user_id]' ).val();
			$post_id = $j('input[name=post_id]' ).val();
			$page = $j('input[name=page]' ).val();
			$link = $j('input[name=current]' ).val();
			//The action is appended to hook wp_ajax_ to give wp_ajax_gift_list_kit which calls callback function gift_list_kit_callback and processes data
			//Removed action as if javascript enabled goes through processing twice
			data = 'user_id='+$user_id+'&post_id='+$post_id+'&quantity='+$quantity+'&page='+$page;
			
			   $j.ajax({	
			   type: "POST", 
			   url:ajaxurl ,
			   data: data,
			   success: function(msg){
					//Get the number reserved by other users
					$number_reserved_by_others = parseInt($total_reserved)-parseInt($user_reserved);
					//The total reserved will be the new quantity = what the others have reserved
					$number_reserved_by_all =  parseInt($number_reserved_by_others)+parseInt($quantity);
					//Get the number reserved by other users
					$number_reserved_by_others =  parseInt($total_reserved)- parseInt($user_reserved);
					//The total reserved will be the new quantity = what the others have reserved
					$number_reserved_by_all =  parseInt($number_reserved_by_others)+parseInt($quantity);
					$j('span[name=still_required]' ).text(parseInt($total_required)-parseInt($number_reserved_by_others)-parseInt($quantity));
					$j('span[name=user_reserved]' ).text(parseInt($quantity));
					$j('span[name=total_reserved]' ).text(parseInt($quantity)+parseInt($number_reserved_by_others));
					$j('#quick-alert').remove();
					$j('<span id="quick-alert" class="glkit-quick-alert">Quantity changed!</span>')
					    .insertAfter( $('#note') )
					    .fadeIn('fast')
					    .animate({opacity: 1.0}, 2000)
					    .fadeOut('slow', function() {
					      $j('#quick-alert').remove();
					});
				},	
				fail: function(msg){

					$j('#quick-alert').remove();
					$j('<span id="quick-alert" class="glkit-quick-alert-error">An error ocurred!</span>')
				    .insertAfter( $('#note') )
				    .fadeIn('fast')
				    .animate({opacity: 1.0}, 2000)
				    .fadeOut('slow', function() {
				      $j('#quick-alert').remove();
				    });
				}
			 });
		});		
});
</script>
<?php
}

function gift_list_kit_load_scripts()
{
	//If we're displaying a single custom post
	if ('kit_item' === get_post_type()&& is_singular())
	{
		//Load jquery
		wp_enqueue_script( 'jquery');
		//Add our script in the header
		add_action( 'wp_head', 'gift_list_kit_javascript' );
	}
}
//After WP object is set up (ref array)
add_action( 'wp', 'gift_list_kit_load_scripts' );

// Action hook to initialize i18n
add_action('plugins_loaded', 'glkit_i18n_init');

function glkit_i18n_init() {
	load_plugin_textdomain( 'glkit-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

// Action hook to add the Items menu item
add_action( 'admin_menu', 'gift_list_kit_menu' );

//Create the Items sub-menu
function gift_list_kit_menu() {

	add_options_page( __( 'Item List Settings Page', 'glkit-plugin' ), __( 'Item List Settings', 'glkit-plugin' ), 'manage_options', 'gift_list-settings', 'gift_list_settings_page' );

}

//Build the plugin settings page
function gift_list_settings_page() {

	//Load the plugin options array
	$glkit_options_arr = get_option( 'glkit_options' );

	//Set the option array values to variables
	$glkit_show_user_email = ( ! empty(  $glkit_options_arr['show_user_email'] ) ) ?  $glkit_options_arr['show_user_email'] : '';
	$glkit_currency_sign = $glkit_options_arr['currency_sign'];
	$glkit_item_type = $glkit_options_arr['item_type'];
	//The admin settings form
	?>
<div class="wrap">
	<h2>
		<?php _e( 'Gift List Options', 'glkit-plugin' ) ?>
	</h2>
	<form method="post" action="options.php">
		<?php settings_fields( 'glkit-settings-group' ); ?>
		<table class="form-table">
			<tr valign="top">
			
			
			<tr valign="top">
				<th scope="row"><?php _e( 'Item Type', 'glkit-plugin' ) ?></th>
				<td><input type="text" name="glkit_options[item_type]"
					value="<?php echo  $glkit_item_type ; ?>" size="20" maxlength="20" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Show User Email', 'glkit-plugin' ) ?>
				</th>
				<td><input type="checkbox" name="glkit_options[show_user_email]"
				<?php echo checked( $glkit_show_user_email, 'on' ); ?> /></td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e( 'Currency Sign', 'glkit-plugin' ) ?></th>
				<td><input type="text" name="glkit_options[currency_sign]"
					value="<?php echo  $glkit_currency_sign ; ?>" size="1"
					maxlength="1" /></td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" class="button-primary"
				value="<?php _e( 'Save Changes', 'glkit-plugin' ); ?>" />
		</p>
	</form>
</div>
<?php
}


// Action hook to register the plugin option settings
add_action( 'admin_init', 'glkit_register_settings' );

function glkit_register_settings() {

	//register the array of settings
	register_setting( 'glkit-settings-group', 'glkit_options', 'glkit_sanitize_options' );

}

function glkit_sanitize_options( $options ) {

	$options['show_user_email'] = ( ! empty( $options['show_user_email'] ) ) ? sanitize_text_field( $options['show_user_email'] ) : '';
	$options['currency_sign'] = ( ! empty( $options['currency_sign'] ) ) ? sanitize_text_field( $options['currency_sign'] ) : '';
	$options['item_type'] = ( ! empty( $options['item_type'] ) ) ? sanitize_text_field( $options['item_type'] ) : 'Item';
	return $options;

}

//Action hook to register the Items meta box
add_action( 'add_meta_boxes', 'glkit_register_meta_box' );

function glkit_register_meta_box() {

	// Create our custom meta box
	add_meta_box( 'gift-list-gift-meta', __( 'Item Information','glkit-plugin' ), 'gift_list_kit_meta_box', 'kit_item', 'side', 'default' );

}

function my_admin_notice() {
	?>
<div class="error">
	<p>
		<?php _e( 'Error!', 'my-text-domain' ); ?>
	</p>
</div>
<?php
}


//Build product meta box
function gift_list_kit_meta_box( $post ) {


	//Retrieve our custom meta box values
	$glkit_sku = get_post_meta( $post->ID, 'gift_sku', true );
	$glkit_price = get_post_meta( $post->ID, 'gift_price', true );
	$glkit_supplier = get_post_meta( $post->ID, 'gift_supplier', true );
	$glkit_url = get_post_meta( $post->ID, 'gift_url', true );
	$glkit_required = get_post_meta( $post->ID, 'gift_required', true );
	$glkit_colour = get_post_meta( $post->ID, 'gift_colour', true );


	//Nonce field for security
	wp_nonce_field( 'meta-box-save', 'glkit-plugin' );

	//Display meta box form
	echo '<table >';
	echo '<tr>';
	echo '<td>' .__('Id', 'glkit-plugin').':</td><td><input type="text" name="gift-list-gift_sku" value="'.esc_attr( $glkit_sku ).'" size="5"></td>';
	echo '</tr><tr>';
	echo '<td>' .__('Price', 'glkit-plugin').':</td><td><input type="number" required name="gift-list-gift_price" value="'.esc_attr( $glkit_price ).'" size="5"></td>';
	echo '</tr><tr>';
	echo '<td>' .__('Colour', 'glkit-plugin').':</td><td><input type="text" required name="gift-list-gift_colour" value="'.esc_attr( $glkit_colour ).'" size="10"></td>';
	echo '</tr><tr>';
	echo '<td>' .__('Supplier', 'glkit-plugin').':</td><td><input type="text" required name="gift-list-gift_supplier" value="'.esc_attr( $glkit_supplier ).'" size="30"></td>';
	echo '</tr><tr>';
	echo '<td>' .__('URL', 'glkit-plugin').':</td><td><input type="url" title="'.__('Supplier URL', 'glkit-plugin').'" required name="gift-list-gift_url" value="'.esc_attr( $glkit_url ).'" size="30"></td>';
	echo '</tr><tr>';
	echo '<td>' .__('Required', 'glkit-plugin').':</td><td><input type="number" step="1" title="'.__('Number Required', 'glkit-plugin').'" required name="gift-list-gift_required" value="'.esc_attr( $glkit_required ).'" size="5"></td>';
	echo '</tr><tr>';
	echo '<td>' .__('Description', 'glkit-plugin').':</td><td><textarea name="gift-list-gift_description" value="" cols="25" rows="5"></textarea></td>';
	echo '</tr><tr>';

	echo '</tr>';

	//display the meta box shortcode legend section
	echo '<tr><td colspan="2"><hr></td></tr>';
	echo '<tr><td colspan="2"><strong>' .__( 'Shortcode Legend', 'glkit-plugin' ).'</strong></td></tr>';
	echo '<tr><td>' .__( 'Id', 'glkit-plugin' ) .':</td><td>[glkit show=id]</td></tr>';
	echo '<tr><td>' .__( 'Price', 'glkit-plugin' ).':</td><td>[glkit show=price]</td></tr>';
	echo '<tr><td>' .__( 'Colour', 'glkit-plugin' ).':</td><td>[glkit show=colour]</td></tr>';
	echo '<tr><td>' .__( 'Supplier', 'glkit-plugin' ).':</td><td>[glkit show=supplier]</td></tr>';
	echo '<tr><td>' .__( 'URL', 'glkit-plugin' ).':</td><td>[glkit show=url]</td></tr>';
	echo '<tr><td>' .__( 'Required', 'glkit-plugin' ).':</td><td>[glkit show=required]</td></tr>';

	echo '</table>';
}

// Action hook to save the meta box data when the post is saved
add_action( 'save_post','glkit_save_meta_box' );

//Save meta box data
function glkit_save_meta_box( $post_id ) {

	//Verify the post type is for Items and metadata has been posted
	if ( get_post_type( $post_id ) == 'kit_item' && isset( $_POST['gift-list-gift_sku'] ) ) {

		if (! current_user_can( 'edit_page', $post_id ) )
			return;

		//If autosave skip saving data
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		//Check nonce for security
		check_admin_referer( 'meta-box-save', 'glkit-plugin' );

		//add_action( 'admin_notices', 'my_admin_notice' );

		//Save the meta box data as post metadata
		update_post_meta( $post_id, 'gift_sku', sanitize_text_field( $_POST['gift-list-gift_sku'] ) );
		update_post_meta( $post_id, 'gift_price', sanitize_text_field( $_POST['gift-list-gift_price'] ) );
		update_post_meta( $post_id, 'gift_colour', sanitize_text_field( $_POST['gift-list-gift_colour'] ) );
		update_post_meta( $post_id, 'gift_supplier', sanitize_text_field( $_POST['gift-list-gift_supplier'] ) );
		update_post_meta( $post_id, 'gift_url', sanitize_text_field( $_POST['gift-list-gift_url'] ) );
		update_post_meta( $post_id, 'gift_required',sanitize_text_field( $_POST['gift-list-gift_required'] ) );
		update_post_meta( $post_id, 'gift_description',sanitize_text_field( $_POST['gift-list-gift_description'] ) );
	}

}

//Create shortcode
add_shortcode( 'glkit', 'glkit_shortcodes' );

/*
 * Shortcode to display gift list
* [glkit show=""]
*
*/
function glkit_shortcodes( $atts, $content = null ) {
	global $post;

	extract( shortcode_atts( array(

			"show" => ''
	), $atts ) );

	//Load options array
	$glkit_options_arr = get_option( 'glkit_options' );

	if ( $show == 'id') {

		$gl_show = get_post_meta( $post->ID, 'gift_sku', true );

	}elseif ( $show == 'price' ) {

		$gl_show =  $glkit_options_arr['currency_sign']. get_post_meta( $post->ID, 'gift_price', true );

	}elseif ( $show == 'colour' ) {

		$gl_show = get_post_meta( $post->ID, 'gift_colour', true );

	}elseif ( $show == 'supplier' ) {

		$gl_show = get_post_meta( $post->ID, 'gift_supplier', true );

	}elseif ( $show == 'url' ) {

		$gl_show = '<a href="'.get_post_meta( $post->ID, 'gift_url', true ).'" >Link</a>';

	}elseif ( $show == 'required' ) {

		$gl_show = get_post_meta( $post->ID, 'gift_required', true );

	}

	//Return the shortcode value to display
	return $gl_show;
}

// Action hook to create plugin widget
add_action( 'widgets_init', 'glkit_register_widgets' );

//Register the widget
function glkit_register_widgets() {
	register_widget( 'glkit_widget' );
}

//The_widget class
class glkit_widget extends WP_Widget {

	//Process our new widget
	function glkit_widget() {
		$widget_ops = array(
				'classname'   => 'glkit-widget-class',
				'description' => __( 'Display Items','glkit-plugin' ) );
		$this->WP_Widget( 'glkit_widget', __( 'Items Widget','glkit-plugin'), $widget_ops );
	}

	//Build our widget settings form
	function form( $instance ) {
		$defaults = array(
				'title'           => __( 'Items', 'glkit-plugin' ),
				'number_items' => '3' );

		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = $instance['title'];
		$number_items = $instance['number_items'];

		?>
<p>
	<?php _e('Title', 'glkit-plugin') ?>
	: <input class="widefat"
		name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
		value="<?php echo esc_attr( $title ); ?>" />
</p>
<p>
	<?php _e( 'Number in Widget', 'glkit-plugin' ) ?>
	: <input name="<?php echo $this->get_field_name( 'number_items' ); ?>"
		type="text" value="<?php echo esc_attr( $number_items ); ?>" size="2"
		maxlength="2" />
</p>
<?php
	}

	//Save our widget settings
	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['number_items'] = absint( $new_instance['number_items'] );

		return $instance;

	}

	//Display our widget
	function widget( $args, $instance ) {
		global $post;

		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'] );
		$number_items = $instance['number_items'];

		if ( ! empty( $title ) ) {
			echo $before_title . esc_html( $title ) . $after_title;
		};

		//Custom query to retrieve items
		$output="";
		$dispGifts = new WP_Query(array(
				'post_type' => 'kit_item',
				'orderby' => 'title',
				'order' => 'ASC',
				'posts_per_page' => absint( $number_items )
		));


		while ( $dispGifts->have_posts() ) : $dispGifts->the_post();
		$glkit_required = get_post_meta( $post->ID, 'gift_required', true );
		//Only show items we still require
		if(glkit_get_no_reserved_array($post->ID)<$glkit_required)
		{
			//load options array
			$glkit_options_arr = get_option( 'glkit_options' );

			//load custom meta values
			$glkit_price = get_post_meta( $post->ID, 'gift_price', true );
			$glkit_colour = get_post_meta( $post->ID, 'gift_colour', true );
			$glkit_supplier = get_post_meta( $post->ID, 'gift_supplier', true );
			$glkit_url = get_post_meta( $post->ID, 'gift_url', true );

			//Link to single post
			$link=esc_html(get_permalink());
			$output .='<h2><a href="'.$link.'" >'.get_the_title(get_the_ID()).'</a></h2>';

			//If featured image display it
			if (has_post_thumbnail(get_the_ID())) {
				$image = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID() ), 'single-post-thumbnail' );
				$output .= '		<div class="glkit-thumb"><img src="'.$image[0].'" ></div>';
			}
			else {
				$image = plugins_url( 'images/placeholder.gif', __FILE__);
				$output .= '		<div class="glkit-thumb"><img src="'.$image.'" ></div>';
			}
			$price = is_float((float)esc_attr($glkit_price))?(float)esc_attr($glkit_price):0.00;
			$price = number_format((float)esc_attr($glkit_price), 2, '.', '');
			$output .='<br />';
			$output .= __( 'Price', 'glkit-plugin' ). ': '. $glkit_options_arr['currency_sign'] .$price .'<br/>';
			$output .= __( 'Colour', 'glkit-plugin' ). ': '. esc_attr($glkit_colour) .'<br/>';
			$output .= __( 'Required', 'glkit-plugin' ). ': '. esc_attr($glkit_required) .'<br/>';
			$output .= __( 'Supplier', 'glkit-plugin' ). ': '. esc_attr($glkit_supplier) .'<br/>';

			$output .='<a href="'.esc_url($glkit_url).'" >'.__( 'View Online', 'glkit-plugin' ).'<br/>';
			$output .= '<hr class="glkit-rule">';

		}
		endwhile;
		echo $output;
		wp_reset_postdata();

		echo $after_widget;

	}
}//End widget class

//Create taxonomies
add_action( 'init', 'glkit_create_taxonomies', 0 );

function glkit_create_taxonomies() {
	//Get options array
	$glkit_options_arr = get_option( 'glkit_options' );

	$name = __( ' Categories', 'glkit-plugin' );


	$add_new_item =  __('Add New Category', 'glkit-plugin');
	$new_item_name =  __('New Category', 'glkit-plugin');
	register_taxonomy(
			'gift_type',
			'kit_item',
			array(
					'labels' => array(
							'name' => $name,
							'add_new_item' => $add_new_item,
							'new_item_name' => $new_item_name
					),
					'show_ui' => true,
					'show_admin_column' => true,
					'show_tagcloud' => false,
					'hierarchical' => true
			)
	);
	$name = __( 'Price Ranges', 'glkit-plugin' );
	$add_new_item =  __('Add New Price Range', 'glkit-plugin');
	$add_new_item =  __('New Price Range', 'glkit-plugin');
	register_taxonomy(
			'gift_price',
			'kit_item',
			array(
					'labels' => array(
							'name' => $name,
							'add_new_item' => $add_new_item,
							'new_item_name' => $new_item_name
					),
					'show_ui' => true,
					'show_admin_column' => true,
					'show_tagcloud' => false,
					'hierarchical' => true
			)
	);
}

function glkit_filter_list() {
	global $wp_query;

	$screen = get_current_screen();
	if ( $screen->post_type == 'kit_item' ) {


		$glkit_options_arr = get_option( 'glkit_options' );
		$text= __( 'Show All Categories', 'glkit-plugin' );

		wp_dropdown_categories( array(
				'show_option_all' => $text,
				'taxonomy' => 'gift_type',
				'name' => 'gift_type',
				'orderby' => 'name',
				'selected' => ( isset( $wp_query->query['gift_type'] ) ? $wp_query->query['gift_type'] : '' ),
				'hierarchical' => false,
				'depth' => 3,
				'show_count' => false,
				'hide_empty' => true,
		) );
		$text= __( 'Show All Price Ranges', 'glkit-plugin' );
		wp_dropdown_categories( array(
				'show_option_all' => $text,
				'taxonomy' => 'gift_price',
				'name' => 'gift_price',
				'orderby' => 'name',
				'selected' => ( isset( $wp_query->query['gift_price'] ) ? $wp_query->query['gift_price'] : '' ),
				'hierarchical' => false,
				'depth' => 3,
				'show_count' => false,
				'hide_empty' => false,
		) );
	}
}

add_action( 'restrict_manage_posts','glkit_filter_list' );


//SORT COLUMNS
add_filter( 'manage_edit_kit_item_sortable_columns', 'sort_me' );

function sort_me($columns) {
	$columns['gift_type'] = 'gift_type';
	return $columns;
}

add_filter( 'request', 'column_orderby' );

function column_orderby ($vars ) {
	if ( !is_admin() )
		return $vars;
	if ( isset( $vars['orderby'] ) && 'gift_type' == $vars['orderby'] ) {
		$vars = array_merge( $vars, array( 'meta_key' => 'gift_type', 'orderby' => 'meta_value' ) );
	}
	return $vars;
}
// End sort

// Create filters with tax
add_filter( 'parse_query','perform_filtering' );

function perform_filtering( $query )
{
	$qv = &$query->query_vars;
	if(isset($qv['gift_type']))
	{
		if (( $qv['gift_type'] ) && is_numeric( $qv['gift_type'] ) ) {
			$term = get_term_by( 'id', $qv['gift_type'], 'gift_type' );
			$qv['gift_type'] = $term->slug;
		}
	}
	if(isset($qv['gift_price']))
	{
		if (( $qv['gift_price'] ) && is_numeric( $qv['gift_price'] ) ) {
			$term = get_term_by( 'id', $qv['gift_price'], 'gift_price' );
			$qv['gift_price'] = $term->slug;
		}
	}
}
// End create filters with tax

add_shortcode( 'glkit-list', 'glkit_shortcode' );
/*
 * Shortcode to display gifts
*
* [glkit-list]
*/
function glkit_shortcode( $atts ) {
	return gift_list_kit_display();
}

function gift_list_kit_display()
{
	global $wp_query;
	/* Query items from the database. */
	$loop = new WP_Query(
			array(
					'post_type' => 'kit_item',
					'orderby' => 'title',
					'order' => 'ASC',
					'posts_per_page' => -1,
			)
	);

	$output='';
	/* Check if any items were returned. */
	if ( $loop->have_posts() ) {

		/* Loop through the items (The Loop). */
		while ( $loop->have_posts() ) {

			$loop->the_post();
			//Only show title link on list not on single posts
			$showTitleLink=true;
			//Only logged in users can update on single post page
			$allowUserUpdates=false;

			$output.= glkit_get_single_post_output( $allowUserUpdates,$showTitleLink);
		}

	}
	/* If no items were found. */
	else {
		$output = '<p>'.__('No items have been added', 'glkit-plugin' ).'</p>';
	}
	/* Return the items list. */
	return $output;
}


add_filter('the_content', 'glkit_change_the_content');
function glkit_change_the_content( $output, $strip_teaser = false, $id = 0 )
{
	//Only show title link on list not on single posts
	$showTitleLink=false;

	if (is_singular('kit_item') && in_the_loop()) {
		wp_enqueue_script('jquery');
		$output .= glkit_get_single_post_output(true,$showTitleLink);
	}
	//This is the taxonomy archive list
	elseif(is_tax() )
	{
		$output .= glkit_get_single_post_output(false,$showTitleLink);
	}
	else
	{
		//Don't change
	}
	return $output;
}

function excerpt_read_more_link($output) {
	global $post;
	$showTitleLink=false;
	return glkit_get_single_post_output(true,$showTitleLink);
}
add_filter('the_excerpt', 'excerpt_read_more_link');

//Private function to get array of quantities reserved by user for item with emails
function glkit_get_reserved_array($post_id)
{
	global $current_user;
	global $wpdb;
	global $tablename;

	$array = array();

	$result = $wpdb->get_results("SELECT wp_users_ID, quantity  FROM ".$tablename." where wp_posts_ID = ".$wpdb->escape($post_id) );
	$i=0;
	foreach ($result  as $row) {
		$user_info = get_userdata($row->wp_users_ID);
		$array[$i] = array();
		$array[$i]['email'] = $user_info-> user_email;
		$array[$i]['quantity'] = $row->quantity;
		$i++;
	}
	return $array;
}

//Private function to get quantities reserved for item
function glkit_get_no_reserved_array($post_id)
{
	$array = glkit_get_reserved_array($post_id);
	$total=0;
	$max = sizeof($array);
	for($i = 0; $i < $max;$i++)
	{
		$total += $array[$i]["quantity"];
	}
	return $total;
}

//Private function to get  quantities reserved by user for item
function glkit_get_no_reserved_for_user($post_id, $user_id)
{
	$user_info = get_userdata($user_id);
	$array = glkit_get_reserved_array($post_id);
	$total=0;
	$max = sizeof($array);
	for($i = 0; $i < $max;$i++)
	{
		if($user_info-> user_email==$array[$i]["email"])
			$total += $array[$i]["quantity"];
	}
	return $total;
}

//Get the html for a single post
function glkit_get_single_post_output($allowChange=false,$showTitleLink=false)
{
	global $wpdb;
	global $current_user;
	global $wp_query;


	//Load the plugin options array
	$glkit_options_arr = get_option( 'glkit_options' );

	//Load custom meta values
	$glkit_price = get_post_meta(get_the_ID(), 'gift_price', true );
	$glkit_colour = get_post_meta( get_the_ID(), 'gift_colour', true );
	$glkit_supplier = get_post_meta( get_the_ID(), 'gift_supplier', true );
	$glkit_url = get_post_meta(get_the_ID(), 'gift_url', true );
	$link=esc_html(get_permalink());

	//Get the toatal number reserved
	$total_reserved=glkit_get_no_reserved_array(get_the_ID());

	//If logged in get user details
	if(is_user_logged_in())
	{
		$current_user = wp_get_current_user();
		$reserved_by_me=glkit_get_no_reserved_for_user(get_the_ID(), $current_user->ID);
		$total_reserved_by_others=$total_reserved-$reserved_by_me;
	}

	$no_required = get_post_meta( get_the_ID(), 'gift_required',true )==""?"0":get_post_meta( get_the_ID(), 'gift_required',true );
	$still_required=$no_required-$total_reserved;

	$html='<div id="glkit">';

	if($showTitleLink)
	{
		$link=esc_url(get_permalink());
		$html .='<h2><a href="'.$link.'" >'.esc_attr(get_the_title(get_the_ID())).'</a></h2>';
	}

	if (has_post_thumbnail(get_the_ID())) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID() ), 'single-post-thumbnail' );
		$html .= '		<div class="glkit-image"><img src="'.esc_url($image[0]).'" ></div>';
	}
	else {
		$image = plugins_url( 'images/placeholder.gif', __FILE__);
		$html .= '		<div class="glkit-image"><img src="'.esc_url($image).'" ></div>';
	}
	$html .='<form id="glkit_reservation" action="" method="post">';
	$html .= '<ul class="glkit-collection">';

	$price = is_float((float)esc_attr($glkit_price))?(float)esc_attr($glkit_price):0.00;
	$price = number_format((float)esc_attr($glkit_price), 2, '.', '');

	$html .= '<li>'.__( 'Price', 'glkit-plugin' ).' : '.$glkit_options_arr['currency_sign'] .$price .'</li>' ;
	$html .= '<li>'.__( 'Colour', 'glkit-plugin' ).' : '. esc_attr($glkit_colour) .'</li>' ;
	$html .= '<li>'.__( 'No Requested', 'glkit-plugin' ).' : '.esc_attr($no_required) .'</li>' ;
	$html .= '<li>'.__( 'Supplier', 'glkit-plugin' ).' : '. esc_attr($glkit_supplier).'</li>' ;

	/* Show the category. */
	if(get_the_term_list( get_the_ID(), 'gift_type',	'Occasion : ', ', ', ' ' )!="")
	{
		$html .= '<li>'.get_the_term_list( get_the_ID(), 'gift_type',	__( 'Category', 'glkit-plugin' )." : ", ', ', ' ' ).'</li>';
	}
	else {
		$html .= '<li>'.__( 'Category', 'glkit-plugin' ).' : '.__( 'None', 'glkit-plugin' ).' </li>';
	}


	/* Show the price range. */
	if(get_the_term_list( get_the_ID(), 'gift_price',	'Price Range : ', ', ', ' ' )!="")
	{
		$html .= '<li>'.get_the_term_list( get_the_ID(), 'gift_price', __( 'Price Range', 'glkit-plugin' )." : ", ', ', ' ' ).'</li>';
	}
	else
	{
		$html .= '<li>'.__( 'Price Range', 'glkit-plugin' ).' : '.__( 'None', 'glkit-plugin' ).' </li>';
	}

	$html .='<li><a href="'.esc_url($glkit_url).'" >'.__( 'View Online', 'glkit-plugin' ).'</a></li>';
	$html .= '</ul>';
	$html .= '<h2>'.__( '', 'glkit-plugin' ).'</h2>';
	$html .= '<ul class="glkit-reservation-collection">';

	/* If user is logged in show the reserved no. */
	if(is_user_logged_in())
	{
		$html .= '<li>'.__( 'Reserved For Me', 'glkit-plugin' ).' : <span name="user_reserved">'.esc_attr($reserved_by_me).'</span></li>' ;
		$html .= '<li>'.__( 'Reserved By Others', 'glkit-plugin' ).' : <span name="total_reserved_by_others">'.esc_attr($total_reserved_by_others).'</span></li>' ;
	}

	$html .= '<li>'.__( 'Total Reserved', 'glkit-plugin' ).' : <span name="total_reserved">'.esc_attr($total_reserved).'</span></li>' ;
	/* Show the no available for reservation. */
	$html .= '<li>'.__( 'Available For Reservation', 'glkit-plugin' ).' : <span name="still_required">'. esc_attr($still_required).'</span></li>';
	//Allow logged in user on single post to reserve
	if($allowChange&&is_user_logged_in())
	{
		$html .= '<li>'.__( 'Change Reservation', 'glkit-plugin' ).' : '.glkit_get_form_text($reserved_by_me,$no_required,$total_reserved).'<input type="submit" id="reservation-submit" value="change"> <span id="note"></span</li>' ;
	}
	$html .= '</ul>';

	//This is nifty and original!!! If using js going back in browser displays cached page so forcing refresh
	$back=wp_get_referer();

	if($allowChange)
	{
		$regex="&";
		$index = strrpos ( $back , $regex);
		if($index>-1)
		{
			$back=substr($back, 0,$index);
		}

		//If following is true using as home page and back button will not work
		if(get_site_url()!=$back&&get_site_url().'/'!=$back)
		{
			$html .= '<a href="'.$back.'&reload='.rand().'" onMouseOver="self.status=document.referrer;return true">'.__( 'Back', 'glkit-plugin' ).'</a>';
		}

	}

	//If there are some available to reserve and single post and logged in

	if(is_user_logged_in()&&$total_reserved_by_others<$no_required&&!$allowChange)
	{
		$html .='<a href="'.$link.'" >'.__( 'Reserve', 'glkit-plugin' ).'</a></h2>';
	}

	//If admin and settings allow emails to be shown
	$glkit_show_user_email = $glkit_options_arr['show_user_email'] ;

	if (current_user_can( 'manage_options' )) {
		if($glkit_show_user_email=='on')
		{

			$html .= glkit_get_admin_data();
		}
	}

	$html .='<input type="hidden" name="total_required" value="'.esc_attr($no_required).'">';
	$html .='<input type="hidden" name="still_required" value="'.esc_attr($still_required).'">';
	$html .='<input type="hidden" name="total_reserved" value="'.esc_attr($total_reserved).'">';
	if(is_user_logged_in())
	{
		$html .='<input type="hidden" name="user_reserved" value="'.esc_attr($reserved_by_me).'">';
	}

	$html .='<input type="hidden" name="post_id" value="'.esc_attr(get_the_ID()).'">';
	$html .='<input type="hidden" name="user_id" value="'.esc_attr($current_user->ID).'">';
	$html .='<input type="hidden" name="page" value="reserve">';
	$html .='</form>';
	$html .='</div>';
	return $html;
}

//Return the list of reservations with emails
function glkit_get_admin_data(){

	global $wpdb;
	global $current_user;
	global $tablename;

	//Get the reservations for this user for this post
	$totalforuser=0;
	$total=0;

	$myrows = $wpdb->get_results("SELECT wp_users_ID, quantity, date_stamp  FROM ".$tablename." where wp_posts_ID = ".$wpdb->escape(get_the_ID()) );
	$output = '			<h2>'.__( 'Reservations', 'glkit-plugin' ).'</h2>';
	$output .= '			<ul class="glkit-reservation-collection">';
	foreach ($myrows  as $row) {
		$user_info = get_userdata($row->wp_users_ID);
		$date = date_create($row->date_stamp);
		$output .= sprintf( __( '%1$s reserved %2$s on %3$s ', 'my-text-domain' ), '<a href="mailto:'.$user_info-> user_email.'">'.$user_info-> user_email.'</a>', $row->quantity, date_format($date, 'd/m/Y') );

	}
	$output .= '			</ul>';
	return $output;
}


function glkit_get_form_text($reserved_by_me,$no_required,$total_reserved)
{
	global $current_user;

	$total_available_this_user = $no_required-$total_reserved;

	//If there are some available or I have some reserved return a dropdown with max I can reserve
	if($total_available_this_user>0 ||$reserved_by_me!=0)
	{

		$gifts ='<select class="glkit-inline" name="quantity">';
		$max= $no_required-($total_reserved-$reserved_by_me);
		for($i=0;$i<=$max;$i++)
		{
			$boo = $reserved_by_me==$i?'selected="selected"':'';
			$gifts .='<option value="'.$i.'"  '.$boo.'>'.$i.'</option>';
		}
		$gifts .='</select>';
	}
	//Else just return number I reserved
	else
	{
		$gifts=$reserved_by_me;
	}
	return $gifts;
}

//Function to return list of items the logged in user has reserved
function glkit_get_users_gifts( $atts ){

	if(!is_user_logged_in())
	{
		return '<p>'.__('Please login to view your reservations', 'glkit-plugin' ).'</p>';
	}
	global $current_user;
	global $wpdb;
	global $tablename;
	extract( shortcode_atts( array(
			'posts_per_page' => '5',
			'orderby' => 'none',
			'gift_id' => '',
	), $atts ) );

	$user_info = get_userdata($current_user->ID);

	$loop = new WP_Query(
			array(
					'post_type' => 'kit_item',
					'orderby' => 'title',
					'order' => 'ASC',
					'posts_per_page' => -1,
			)
	);

	$output="";

	/* Check if any gifts were returned. */
	if ( $loop->have_posts() ) {

		/* Loop through the gifts (The Loop). */
		while ( $loop->have_posts() ) {

			$loop->the_post();

			$myrows = $wpdb->get_results( "SELECT COUNT(*) As count FROM ".$tablename." where wp_posts_ID = ".$wpdb->escape(get_the_ID())." AND wp_users_ID= ".$wpdb->escape($current_user->ID)." AND quantity > 0"  );
			if($myrows[0]->count>0)
			{
				$showTitleLink=true;
				$allowUserUpdates=false;
				$output .= glkit_get_single_post_output( $allowUserUpdates,$showTitleLink);
			}
		}
	}
	/* If no items were found. */
	else {
		$output = '<p>'.__('No items have been reserved', 'glkit-plugin' ).'</p>';
	}

	return $output;

}

add_shortcode( 'glkit-list-users', 'glkit_get_users_gifts' );

