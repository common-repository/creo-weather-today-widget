<?php
/**
 * Plugin Name: Creo Weather Today
 * Author: Rashedamin 
 * Author URI: https://profiles.wordpress.org/rashedamin/
 * Description: A widget with for Showing current weather conditions. See in Appearance->Widgets.
 * Version: 1.0.0
 * Text Domain: creo-weather-today
 * Domain Path: /languages/
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'creo_weather_action_links' );

function creo_weather_action_links( $links ) {
   $links[] = '<a href="http://codecanyon.net/item/creo-weather-today-wordpress-widget-plugin/12040146" target="_blank">Purchase Pro</a>';
   return $links;
}

class Weather_Today extends WP_Widget {
	/**
	 * Widget constructor.
	 *
	 * @since  1.0
	 *
	 * @access public
	 */
	function __construct() {
		parent::__construct(
			'creo-weather-today', 
			__( 'Creo Weather Today', 'text_domain' ), 
			array( 'description' => __( 'An Awesome Widget for knowing today\'s weather', 'text_domain' ), ) 
		);
		add_action( 'admin_enqueue_scripts', array( $this, 'cwt_enqueue_scripts' ) );
		add_action( 'admin_footer-widgets.php', array( $this, 'cwt_print_scripts' ), 9999 );
	}
	/**
	 * Enqueue scripts.
	 *
	 * @since 1.0
	 *
	 * @param string $hook_suffix
	 */
	public function cwt_enqueue_scripts( $hook_suffix ) {
		if ( 'widgets.php' !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'underscore' );
	}
	/**
	 * Print scripts.
	 *
	 * @since 1.0
	 */
	public function cwt_print_scripts() {
		?>
		<script>
			( function( $ ){
				function initColorPicker( widget ) {
					widget.find( '.color-picker' ).wpColorPicker( {
						change: _.throttle( function() { // For Customizer
							$(this).trigger( 'change' );
						}, 3000 )
					});
				}

				function onFormUpdate( event, widget ) {
					initColorPicker( widget );
				}

				$( document ).on( 'widget-added widget-updated', onFormUpdate );

				$( document ).ready( function() {
					$( '#widgets-right .widget:has(.color-picker)' ).each( function () {
						initColorPicker( $( this ) );
					} );
				} );
			}( jQuery ) );
		</script>
		<?php
	}

	/**
	 * Widget output.
	 *
	 * @since  1.0
	 *
	 * @access public
	 * @param  array $args
	 * @param  array $instance
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo '<h4 id="title">'.$instance['title'].'</h4>';
		}
		echo __(cwt_setBaseWidgetTemplate($instance['background_color'],$instance['font_color']), 'text_domain');
		echo $args['after_widget'];
	}

	/**
	 * Back-end Form.
	 *
	 * @since  1.0
	 *
	 * @see WP_Widget::form()
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$defaults = array(
			'title' => 'My Weather Today',
            'background_color' => '#01b0f1',
            'font_color' => '#faebd7'
        );?>
        <?php
        $instance = wp_parse_args( (array) $instance, $defaults ); ?>
        <p>
        	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" /></p>
            <label for="<?php echo $this->get_field_id( 'background_color' ); ?>"></label>
            <h4><?php _e( 'Choose a Background Color for Widget', 'text_domain' ); ?><br></h4>
            <input type="text" name="<?php echo $this->get_field_name( 'background_color' ); ?>" class="color-picker" id="<?php echo $this->get_field_id( 'background_color' ); ?>" value="<?php echo esc_attr( $instance['background_color'] ); ?>" data-default-color="#fff" />
            <label for="<?php echo $this->get_field_id( 'font_color' ); ?>"></label>
            <h4><?php _e( 'Choose a Font Color for Widget', 'text_domain' ); ?><br></h4>
            <input type="text" name="<?php echo $this->get_field_name( 'font_color' ); ?>" class="color-picker" id="<?php echo $this->get_field_id( 'font_color' ); ?>" value="<?php echo esc_attr( $instance['font_color'] ); ?>" data-default-color="#fff" />
        </p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @since  1.0
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = $new_instance['title'];
		$instance['background_color'] = $new_instance['background_color'];
		$instance['font_color'] = $new_instance['font_color'];
        return $instance;
	}

} // class Weather_Today ends


/**
	 * Widget HTML template
	 *
	 * @param $bg_color background color selected from palette.
	 * @param $font_color font color selected from palette.
	 *
	 * @return complete markup in form of string
	 */
function cwt_setBaseWidgetTemplate($bg_color,$font_color)
{
    return '
        <div class="table-container">
		    <div class="header" id="weather-block" style="background:'.$bg_color.';color:'.$font_color.';">
		    	<table>
		    	<tr>
		    	    <td colspan="2"><div id="temp_label"></div></td>
		    	</tr>
		    	<tr>
		      		<td colspan="2"><div id="temp_today"></div></td>
		      	</tr>
                <tr>
		      		<td><img id="hum_icon" src="'.plugin_dir_url(__FILE__).'" /><h5 id="humidity"></h5></td>
		      		<td><img id="wind_icon" src="'.plugin_dir_url(__FILE__).'" /><h5 id="wind"></h5></td>
                </tr>
                <tr>
                    <td colspan="2"><h4 id="forecast"></h4></td>
                </tr>
		      	</table>
		    </div>
        </div>
		';
}


function register_weather_widget() {
    register_widget( 'Weather_Today' );
}
add_action( 'widgets_init', 'register_weather_widget' );
add_action( 'wp_head', 'cwt_scripts' );

function cwt_scripts(){
	wp_enqueue_script('jquery');
	wp_enqueue_style( 'weather_box_style', plugin_dir_url( __FILE__ ) .'css/style.css' );
	wp_enqueue_style('weather_bootstrap_style','//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css');
	wp_enqueue_script( 'weather_condn_script', plugin_dir_url( __FILE__ ) .'js/weather.condition.js', array('jquery'));
	wp_enqueue_script('weather_bootstrap_script','//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js');
}

