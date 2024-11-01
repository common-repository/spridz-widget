<?php
/*
    Plugin Name: Spridz Widget Plugin
    Plugin URI: https://wordpress.org/plugins/spridz-widget
    Description: Your own Spridz customer survey widget.
    Version: 0.3
    Author: Spridz Ventures
    Author URI: http://spridz.com
    License: GPL2
*/

if( !class_exists( 'WP_Http' ) ) {
  include_once( ABSPATH . WPINC . '/class-http.php' );
}

class Spridz_Widget extends WP_Widget {

  public function __construct() {
    parent::__construct(
      'spridz_widget',
      __( 'Spridz Customer Survey', 'text_domain' ),
      array(
                'classname' => 'spridz_widget',
                'customize_selective_refresh' => true,
                'description' => 'Display your Spridz customer survey.'
      )
    );
  }

  // Widget settings
  public function form( $instance ) {

    $defaults = array(
        'api_token'     => '',
        'host'          => 'https://spridz.com',
        'html'          => '',
        'message'       => 'Your feedback matters. Rate us by clicking a smiley.'
    );

    extract( wp_parse_args( ( array ) $instance, $defaults ) ); ?>

    <?php ?>
    <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'message' ) ); ?>"><?php _e( 'Message', 'text_domain' ); ?></label>
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'message' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'message' ) ); ?>" type="text" value="<?php echo esc_attr( $message ); ?>" />

        <label for="<?php echo esc_attr( $this->get_field_id( 'api_token' ) ); ?>"><?php _e( 'API Token', 'text_domain' ); ?></label>
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'api_token' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'api_token' ) ); ?>" type="text" value="<?php echo esc_attr( $api_token ); ?>" />

        <label for="<?php echo esc_attr( $this->get_field_id( 'host' ) ); ?>"><?php _e( 'Host', 'text_domain' ); ?></label>
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'host' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'host' ) ); ?>" type="text" value="<?php echo esc_attr( $host ); ?>" />

        <input id="<?php echo esc_attr( $this->get_field_id( 'html' ) ); ?>" type="hidden" value="<?php echo esc_attr( $html ); ?>" />
    </p>

  <?php }

  // Update widget settings
  //   This will use the provided api_token to retrieve the widget HTML from the host.
  public function update( $new_instance, $old_instance ) {
    $instance = $old_instance;

    $instance['api_token']     = isset( $new_instance['api_token'] ) ? wp_strip_all_tags( $new_instance['api_token'] ) : '';
    $instance['host']          = isset( $new_instance['host'] ) ? wp_strip_all_tags( $new_instance['host'] ) : '';
    $instance['message']       = isset( $new_instance['message']) ? wp_strip_all_tags( $new_instance['message']) : '';

    $url = $instance['host'] . "/admin/api/widget";

    $request = new WP_Http();
    $headers = array( 'X-Auth-Token' => $instance['api_token']);

    $result = $request->request( $url, array( 'method' => 'GET', 'headers' => $headers ) );

    $instance['html'] = $result['body'];

    return $instance;
  }

  // Render the widget
  public function widget( $args, $instance ) {

    extract( $args );

    $html     = isset( $instance['html'] ) ? $instance['html'] : '';
    $message  = isset( $instance['message'] ) ? $instance['message'] : '';

    echo $before_widget;

    echo '<div class="widget-text wp_widget_plugin_box">';

      if ( $message ) {
        echo '<div>' . $message . '</div>';
      }

      if ( $html ) {
        echo $html;
      }

    echo '</div>';

    echo $after_widget;
  }
}

function register_spridz_widget() {
  register_widget( 'Spridz_Widget' );
}

add_action( 'widgets_init', 'register_spridz_widget' );
