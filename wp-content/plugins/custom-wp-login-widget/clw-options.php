<?php
// create custom plugin settings menu
add_action('admin_menu', 'baw_create_menu');

function baw_create_menu() {

	//create new top-level menu
	add_menu_page('Custom Login Widget', 'Custom Login Widget', 'administrator', __FILE__, 'baw_settings_page',plugins_url('/images/icon.png', __FILE__));

}

function baw_settings_page() {
?>
<div class="wrap">
<h2>Custom Wordpress Login Widget</h2>
This widget has no specific setup or options. You can include the login form in two different ways. The first is to simply drag the widget into any widgetized area. You can also include the login form using the shortcode [clw_add_form].<br><br>This plugin is provided free of charge however if you enjoyed this plugin please consider donating using the following method:<br><br><form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="3Q3ZLSBEWL4BQ">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

</div>
<?php } ?>