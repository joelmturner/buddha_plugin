<?php
/*
Plugin Name: Buddha Button
Plugin URI: joelmturner.com/buddha-button
Description: Adds a widget to the site. Ask Buddha about your situation and hit the <strong>What would Buddha do?</strong> button to receive some wisdom.
Version: 1.0
Author: joelmturner
Author URI: http://joelmturner.com
License: GPL2
*/

/**
 * Register the widget
 */
add_action('widgets_init', create_function('', 'return register_widget("Buddha_Button_Widget");'));

/**
 * Class Buddha_Button_Widget
 */
class Buddha_Button_Widget extends WP_Widget
{
	/** Basic Widget Settings */
	const WIDGET_NAME = "Buddha Button";
	const WIDGET_DESCRIPTION = "What would Buddha do?";

	var $textdomain;
	var $fields;

	/**
	 * Construct the widget
	 */
	function __construct()
	{
		//We're going to use $this->textdomain as both the translation domain and the widget class name and ID
		$this->textdomain = strtolower(get_class($this));

		//Figure out your textdomain for translations via this handy debug print
		//var_dump($this->textdomain);

		//Add fields
		$this->add_field('title', 'Enter title', '', 'text');
		$this->add_field('plugin_bg_color', 'Enter the background color of the plugin', '', 'text');
		$this->add_field('quote_text_color', 'Enter the color of the quote text', '', 'text');
		$this->add_field('btn_color', 'Button Color', 'Enter the hex code for your button color.', 'text');
		$this->add_field('btn_text_color', 'Button Text Color', 'Enter the hex code for your button text color.', 'text');

		//Translations
		load_plugin_textdomain($this->textdomain, false, basename(dirname(__FILE__)) . '/languages' );

		//Init the widget
		parent::__construct($this->textdomain, __(self::WIDGET_NAME, $this->textdomain), array( 'description' => __(self::WIDGET_DESCRIPTION, $this->textdomain), 'classname' => $this->textdomain));
	}

	/**
	 * Widget frontend
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance)
	{
		$title = apply_filters('widget_title', $instance['title']);

		/* Before and after widget arguments are usually modified by themes */
		echo $args['before_widget'];

		if (!empty($title))
			echo $args['before_title'] . $title . $args['after_title'];

		/* Widget output here */
		$this->widget_output($args, $instance);

		/* After widget */
		echo $args['after_widget'];
	}
	
	/**
	 * This function will execute the widget frontend logic.
	 * Everything you want in the widget should be output here.
	 */
	private function widget_output($args, $instance)
	{
		extract($instance);

		/**
		 * Add the script and stylesheet
		 */
		wp_enqueue_style( 'buddha-style', plugins_url( '/css/custom.css' , __FILE__ ) );
		wp_enqueue_script( 'buddha-script', plugins_url( '/js/custom.js' , __FILE__ ), array(), '1.0.0', true );
		
		$img = plugins_url( 'img/buddha.svg' , __FILE__ );
		$plugin_bg_color = $instance['plugin_bg_color'];
		$quote_text_color = $instance['quote_text_color'];
		$btn_color = $instance['btn_color'];
		$btn_text_color = $instance['btn_text_color'];
		
		function buddha_button_quotes() {
		    $quotes = array(
		        'Do not dwell in the past, do not dream of the future, concentrate the mind on the present moment.',
		        'Health is the greatest gift, contentment the greatest wealth, faithfulness the best relationship.',
		        'Three things cannot be long hidden: the sun, the moon, and the truth.',
		        'The mind is everything. What you think you become.'
		    );

		    $output = '';

		    foreach($quotes as $quote_list) {
		    		$output .= '<li>"' . $quote_list . '"</li>';
			}

			return $output;
		     
		    //return $quotes[rand(0, count($quotes)-1)];
		}
		?>

		<div class="bb-wrap" style="background-color: <?php echo $plugin_bg_color; ?>; color:<?php echo $quote_text_color; ?>">
            <img src="<?php echo $img; ?>" alt="buddha button" />
            <ul class="bb-quotes">
            	<?php echo buddha_button_quotes(); ?>
            </ul>
            <button class="getBuddha"<?php if (isset($btn_color)) echo ' style="background-color: ' . $btn_color . '; color: ' . $btn_text_color . '"';?>>What would Buddha do?</button>
        </div>
		<?php
	}

	/**
	 * Widget backend
	 *
	 * @param array $instance
	 * @return string|void
	 */
	public function form( $instance )
	{
		/* Generate admin for fields */
		foreach($this->fields as $field_name => $field_data)
		{
			if($field_data['type'] === 'text'):
				?>
				<p>
					<label for="<?php echo $this->get_field_id($field_name); ?>"><?php _e($field_data['description'], $this->textdomain ); ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id($field_name); ?>" name="<?php echo $this->get_field_name($field_name); ?>" type="text" value="<?php echo esc_attr(isset($instance[$field_name]) ? $instance[$field_name] : $field_data['default_value']); ?>" />
				</p>
			<?php
			//elseif($field_data['type'] == 'textarea'):
			//You can implement more field types like this.
			else:
				echo __('Error - Field type not supported', $this->textdomain) . ': ' . $field_data['type'];
			endif;
		}
	}

	/**
	 * Adds a text field to the widget
	 *
	 * @param $field_name
	 * @param string $field_description
	 * @param string $field_default_value
	 * @param string $field_type
	 */
	private function add_field($field_name, $field_description = '', $field_default_value = '', $field_type = 'text')
	{
		if(!is_array($this->fields))
			$this->fields = array();

		$this->fields[$field_name] = array('name' => $field_name, 'description' => $field_description, 'default_value' => $field_default_value, 'type' => $field_type);
	}

	/**
	 * Updating widget by replacing the old instance with new
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	public function update($new_instance, $old_instance)
	{
		return $new_instance;
	}
}