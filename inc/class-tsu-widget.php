<?php
/**
 * Widget class for Tsu.
 *
 * @package Tsu
 * @subpackage Widget
 *
 * @since 1.0.0
 */
class Tsu_Widget extends WP_Widget {

  /**
   * All valid logo sizes.
   *
   * @since 1.2.0
   *
   * @var array
   */
  private static $_logo_sizes = array( 16, 32, 48, 64, 96, 128 );

  /**
   * Constructor. Sets up and creates the widget with appropriate settings.
   *
   * @since 1.0.0
   */
  public function __construct() {
    $widget_ops = apply_filters( 'tsu_widget_ops', array(
      'classname'   => 'widget-tsu',
      'description' => __( 'Place a link to your Tsu profile in your Sidebar.', 'tsu' )
    ) );

    $control_ops = apply_filters( 'tsu_widget_control_ops', array(
      'id_base' => 'tsu',
      'height'  => 350,
      'width'   => 225
    ) );

    $this->WP_Widget( 'tsu', apply_filters( 'tsu_widget_name', __( 'Tsu Widget', 'tsu' ) ), $widget_ops, $control_ops );
  }

  /**
   * Register the widget.
   *
   * @since 1.0.0
   */
  public static function register() {
    // Add inline styles for displaying the logo.
    add_action( 'wp_head', array( 'Tsu_Widget', 'add_inline_css' ) );

    // Register the widget.
    return register_widget( 'Tsu_Widget' );
  }

  /**
   * Outputs the widget within the sidebar.
   *
   * @since 1.0.0
   *
   * @param array $args     The default widget arguments.
   * @param array $instance The input settings for the current widget instance.
   */
  public function widget( $args, $instance ) {

    // Take arguments array and turn keys into variables.
    extract( $args, EXTR_SKIP );
    $title = apply_filters( 'tsu_widget_title', $instance['title'] );

    $user_id = ( isset( $instance['user_id'] ) ) ? $instance['user_id'] : null;

    // Only display the widget if a valid user profile url is found.
    if ( $user_id && $user_url = Tsu::get_user_id_url( $user_id ) ) {

      $name      = ( isset( $instance['name'] ) )      ? $instance['name']      : null;
      $show_logo = ( isset( $instance['show_logo'] ) ) ? $instance['show_logo'] : null;
      $logo_size = ( isset( $instance['logo_size'] ) ) ? self::_get_valid_logo_size( $instance['logo_size'] ) : 32;

      echo $before_widget;

      // If a title exists, output it.
      if ( $title ) {
        echo $before_title . $title . $after_title;
      }

      // Print the link itself.
      printf( '<a href="%1$s" title="%2$s">%4$s%3$s</a>',
        $user_url,
        ( '' != $name ) ? $name : '@' . $user_id,
        ( '' != $name ) ? '<span>' . $name . '</span>' : '',
        ( $show_logo ) ? '<img src="' . TSU_IMG_URL . 'tsu-logo-' . $logo_size . 'x' . $logo_size . '.png" alt="' . __( 'Tsu Logo', 'tsu' ) . '" />' : ''
      );

      echo $after_widget;
    }
  }

  /**
   * Sanitizes and updates the widget.
   *
   * @since 1.0.0
   *
   * @param array $new_instance The new input settings for the current widget instance.
   * @param array $old_instance The old input settings for the current widget instance.
   */
  public function update( $new_instance, $old_instance ) {

    // Set $instance to the old instance in case no new settings have been updated for a particular field.
    $instance = $old_instance;

    // Sanitize inputs.
    $instance['title']     = strip_tags( $new_instance['title'] );

    // Tsu user ID.
    $instance['user_id']   = Tsu::get_user_id( $new_instance['user_id'] );

    // Name to display. If the name is empty, use the user ID.
    $instance['name']      = strip_tags( $new_instance['name'] );

    // Checkbox if the Tsu logo should be displayed.
    $instance['show_logo'] = (bool) $new_instance['show_logo'];

    // Get the closest size in case the HTML was tampered with.
    $instance['logo_size'] = self::_get_valid_logo_size( $new_instance['logo_size'] );

    return $instance;
  }

  /**
   * Outputs the form where the user can specify settings.
   *
   * @since 1.0.0
   *
   * @param array $instance The input settings for the current widget instance.
   */
  public function form( $instance ) {

    // Set up the default widget settings.
    $defaults = array(
      'title'     => __( 'Follow me on Tsu', 'tsu' ),
      'show_logo' => true,
      'logo_size' => 32
    );
    $instance = wp_parse_args( (array) $instance, $defaults );

    ?>
    <p><label><?php _e( 'Title', 'tsu' ); ?>:<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" /></label></p>
    <p><label><?php _e( 'User ID', 'tsu' ); ?>:<input type="text" id="<?php echo $this->get_field_id( 'user_id' ); ?>" name="<?php echo $this->get_field_name( 'user_id' ); ?>" value="<?php echo $instance['user_id']; ?>" class="widefat" /></label></p>
    <p><label><?php _e( 'Name to display', 'tsu' ); ?>:<input type="text" id="<?php echo $this->get_field_id( 'name' ); ?>" name="<?php echo $this->get_field_name( 'name' ); ?>" value="<?php echo $instance['name']; ?>" class="widefat" /></label></p>
    <p><label><input type="checkbox" id="<?php echo $this->get_field_id( 'show_logo' ); ?>" name="<?php echo $this->get_field_name( 'show_logo' ); ?>" value="1" <?php checked( $instance['show_logo'], true, true ); ?> /><?php _e( 'Show logo?', 'tsu' ); ?></label></p>
    <p><label><?php _e( 'Logo size', 'tsu' ); ?>:
      <select id="<?php echo $this->get_field_id( 'logo_size' ); ?>" name="<?php echo $this->get_field_name( 'logo_size' ); ?>">
      <?php foreach ( self::$_logo_sizes as $size ) : ?>
        <option value="<?php echo $size; ?>" <?php selected( $instance['logo_size'], $size, true ); ?>><?php echo $size; ?></option>
      <?php endforeach; ?>
      </select>px
    </label></p>
    <?php
  }

  /**
   * Get the closest valid logo size.
   *
   * @since 1.2.0
   *
   * @param  integer $size The size to validate.
   * @return integer       The validated logo size.
   */
  private static function _get_valid_logo_size( $size ) {
    // Make sure we have an integer here.
    $size = intval( $size );
    $distances = array();
    foreach ( self::$_logo_sizes as $key => $num ) {
      $distances[ $key ] = abs( $size - $num );
    }
    return self::$_logo_sizes[ array_search ( min ( $distances ) , $distances ) ];
  }

  /**
   * Add inline CSS for the logo display.
   *
   * @since 1.0.0
   */
  public static function add_inline_css() {
    // This makes sure that the positioning is also good for right-to-left languages.
    $dir = is_rtl() ? 'right' : 'left';

    echo "
    <style>
    .widget-tsu a {
      text-align: $dir;
    }
    .widget-tsu a img,
    .widget-tsu a span {
      display: inline-block;
    }
    .widget-tsu a img {
      vertical-align: middle;
    }
    .widget-tsu a span {
      padding-$dir: 10px;
    }
    </style>
    ";
  }
}
