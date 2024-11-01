<?php
/**
 * @package Tsu
 */
/*
Plugin Name: Tsu
Plugin URI: https://www.tsu.co/noplanman
Description: Tsu widget to share your profile / invitation with your readers.
Version: 1.2.0
Author: Armando Lüscher
Author URI: https://www.tsu.co/noplanman
License: GPLv2 or later
Text Domain: tsu
*/

/**
 *
 *
 * THIS PLUGIN SERVES AS A MINI PLUGIN UNTIL TSU RELEASES AN OFFICIAL PUBLIC API!!
 *
 *
 */

// Define constants.
define( 'TSU_BASENAME', plugin_basename( __FILE__ ) );
define( 'TSU_ROOT_DIR', plugin_dir_path( __FILE__ ) );
define( 'TSU_ROOT_URL', plugins_url( '/', __FILE__ ) );
define( 'TSU_IMG_URL', TSU_ROOT_URL . 'images/' );
define( 'TSU_INC_DIR', TSU_ROOT_DIR . 'inc/' );


// Make sure the version check class is loaded.
if ( ! class_exists( 'Tsu_Version_Check' ) ) {
  require_once TSU_INC_DIR . 'class-tsu-version-check.php';
}

// Check if the minimum version requirements are met.
// If not, don't bother to continue loading the plugin.
add_action( 'admin_init', array( 'Tsu_Version_Check', 'check_version' ) );
if ( ! Tsu_Version_Check::compatible_version() ) {
  return;
}

// Check to see if we can activate the plugin.
register_activation_hook( __FILE__, array( 'Tsu_Version_Check', 'activation' ) );


// Make sure the base singleton class is loaded.
if ( ! class_exists( 'Tsu_Singleton' ) ) {
  require_once TSU_INC_DIR . 'class-tsu-singleton.php';
}

/**
 * Main class for plugin.
 *
 * @since 1.0.0
 */
class Tsu extends Tsu_Singleton {

  /**
   * Only instance of this class.
   *
   * @since 1.0.0
   *
   * @var Tsu
   */
  protected static $instance = null;

  /**
   * Remember if setup has already been called.
   *
   * @since 1.0.0
   *
   * @var boolean
   */
  protected static $is_set_up = false;

  /**
   * The base tsu.co url.
   *
   * @since 1.0.0
   *
   * @var string
   */
  public static $base_url = 'https://www.tsu.co/';


  /**
   * Set up the Tsu plugin.
   *
   * @since 1.0.0
   */
  public static function setup() {
    // Check if setup has already been called.
    if ( parent::setup() ) {
      return;
    }

    // Get the unique instance.
    //$instance = self::get_instance();

    // Load translations.
    load_plugin_textdomain( 'tsu', false, 'tsu/languages/' );

    // Require the widget class and register the widget.
    if ( ! class_exists( 'Tsu_Widget' ) ) {
      require_once TSU_INC_DIR . 'class-tsu-widget.php';
    }
    add_action( 'widgets_init', array( 'Tsu_Widget', 'register' ) );
  }

  /**
   * Get the profile url for the passed user ID.
   *
   * @since 1.0.2
   *
   * @param  string $user_id User ID.
   * @return string          The correct URL to the user profile.
   */
  public static function get_user_id_url( $user_id ) {
    if ( $user_id = self::get_user_id( $user_id ) ) {
      return self::$base_url . $user_id;
    }
    return null;
  }

  /**
   * Get the user id from the passed string, which could be a URL or the @user_id.
   *
   * @since 1.0.2
   *
   * @param  string $user_id User ID, which could be a URL or the @user_id.
   * @return string          The naked user ID.
   */
  public static function get_user_id( $user_id ) {
    // Get rid of all spaces and slashes.
    $user_id = trim( $user_id, ' /' );
    if ( '' == $user_id ) {
      return null;
    }

    // In case the whole url has been entered, get only the last part,
    // which should be the user ID and remove and @ if present.
    return trim( end( explode( '/', $user_id ) ), '@' );
  }
}

// Setup call for the entire plugin.
add_action( 'plugins_loaded', array( 'Tsu', 'setup' ) );

?>
