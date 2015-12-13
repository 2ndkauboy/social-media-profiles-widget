<?php
/*
 * Plugin Name: Social Media Profiles Widget
 * Description: A Widget to add links to social media profiles
 * Version: 1.0.0
 * Author: Bernhard Kau
 * Author URI: http://kau-boys.de
 * Plugin URI: https://github.com/2ndkauboy/social-media-profiles-widget
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0
*/

/**
 * Adds Social_Media_Profiles_Widget widget.
 */
class Social_Media_Profiles_Widget extends WP_Widget {

	public $social_profiles;

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'social_profiles_widget', // Base ID
			__( 'Social Profiles Widget', 'social-media-profiles-widget' ), // Name
			array( 'description' => __( 'A Widget to show links to the social media profiles', 'social-media-profiles-widget' ), ) // Args
		);
		// set the available social media profiles
		$this->social_profiles = apply_filters( 'social_profile_widget_available_profiles', array(
			'feed'       => array( 'title' => __( 'Feed', 'social-media-profiles-widget' ) ),
			'wordpress'  => array( 'title' => __( 'WordPress', 'social-media-profiles-widget' ) ),
			'github'     => array( 'title' => __( 'GitHub', 'social-media-profiles-widget' ) ),
			'googleplus' => array( 'title' => __( 'Google+', 'social-media-profiles-widget' ) ),
			'facebook'   => array( 'title' => __( 'Facebook', 'social-media-profiles-widget' ) ),
			'twitter'    => array( 'title' => __( 'Twitter', 'social-media-profiles-widget' ) ),
		) );
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		// only register the styles if the widget is used
		wp_enqueue_style( 'social-media-profiles-widget', plugins_url( 'social-media-profiles-widget.css', __FILE__) );

		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		// print the social media profiles list
		echo '<ul class="social-media-profiles-widget-list">';

		foreach ( $this->social_profiles as $profile_key => $social_profile ) {
			if ( ! empty( $instance[ $profile_key ] ) ) {
				echo '<li><a href="' . esc_url( $instance[ $profile_key ] ) . '" class="' . esc_attr( $profile_key ) . '" title="' . esc_attr( $social_profile['title'] ) . '">' . esc_attr( $social_profile['title'] ) . '</a></li>';
			}
		}

		echo '</ul>';

		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		// the current widget title
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Title', 'social-media-profiles-widget' );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php

		// get the current values of the services
		$profile_urls = array();

		foreach ( $this->social_profiles as $profile_key => $social_profile ) {
			$profile_urls[ $profile_key ] = ! empty( $instance[ $profile_key ] ) ? $instance[ $profile_key ] : '';
			?>
			<p>
				<label for="<?php echo $this->get_field_id( $profile_key ); ?>"><?php echo $social_profile['title'] . ':'; ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( $profile_key ); ?>" name="<?php echo $this->get_field_name( $profile_key ); ?>" type="text" value="<?php echo esc_attr( $profile_urls[ $profile_key ] ); ?>">
			</p>
			<?php
		}
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		// save the social media profiles
		foreach ( $this->social_profiles as $profile_key => $social_profile ) {
			$instance[ $profile_key ] = ( ! empty( $new_instance[ $profile_key ] ) ) ? esc_url( $new_instance[ $profile_key ] ) : '';
		}

		return $instance;
	}

} // class Social_Media_Profiles_Widget

function social_media_profiles_widget_init() {
	register_widget( 'Social_Media_Profiles_Widget' );
}
add_action( 'widgets_init', 'social_media_profiles_widget_init' );