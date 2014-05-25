<?php
include 'clw-form.php';
include 'clw-options.php';

add_shortcode ('clw_add_form', 'clw_shortcode');
function clw_shortcode ($attr, $content)
{
	ob_start ();
	clw_form ('clw_shortcode');
	return ob_get_clean ();
}

class clw_widget extends WP_Widget
{
	function clw_widget ()
	{
		$widget_ops = array ('description' => 'Your Custom WP Login Widget');
		$this->WP_Widget ('clw', 'Custom WP Login Widget', $widget_ops);
	}

	function widget ($args, $instance)
	{
		extract ($args);
		$title = apply_filters ('widget_title', esc_attr ($instance['title']));
	
		echo $before_widget;
		if ($title)
			echo $before_title. $title. $after_title;
		clw_form ('clw_widget');
		echo $after_widget;
	}

	function update ($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['title'] = strip_tags ($new_instance['title']);
		return $instance;
	}

	function form ($instance)
	{
		$title = strip_tags ($instance['title']);
	?>
		<p>
		<label for="<?php echo $this->get_field_id ('title'); ?>"><?php _e ('Title:', 'wpm'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id ('title'); ?>" name="<?php echo $this->get_field_name ('title'); ?>" type="text" value="<?php echo esc_attr ($title); ?>" />
		</p>
	<?php
	}
}

add_action ('widgets_init', 'clw_widget_init');
function clw_widget_init ()
{
	register_widget ('clw_widget');
}
?>