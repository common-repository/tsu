<?php
/**
 * Version Check class for Tsu.
 *
 * Before the plugin gets initialized, the PHP and WordPress minimum version requirements
 * must be checked. If they are not enough, either don't let the plugin get activated
 * or deactivate the active plugin, if it is still maked as activated for some reason.
 *
 * @package Tsu
 * @subpackage Version_Check
 *
 * @since 1.0.2
 */
class Tsu_Version_Check {

  /**
   * The minimum required WordPress version.
   *
   * @since 1.2.0
   *
   * @var string
   */
  const MIN_WP = '3.0.1';

  /**
   * The minimum required PHP version.
   *
   * This requirement is due to late static binding and anonymous functions.
   *
   * @since 1.2.0
   *
   * @var string
   */
  const MIN_PHP = '5.3';

  /**
   * Generate and return the error message if the minimum version requirements are not met.
   *
   * @since 1.0.2
   *
   * @return string Incompatible version error message.
   */
  private static function _get_version_error_message() {
    return sprintf( __( 'Tsu plugin requires at least WordPress %1$s (you have %2$s) and PHP %3$s (you have %4$s)!', 'tsu' ),
      self::MIN_WP,
      $GLOBALS['wp_version'],
      self::MIN_PHP,
      PHP_VERSION
    );
  }

  /**
   * Plugin Activation hook function to check for minimum PHP and WordPress versions.
   *
   * If for whatever reason the plugin has been enabled and he PHP or WordPress minimum requirements
   * aren't met anymore, make sure the plugin gets disabled.
   *
   * @since 1.0.2
   */
  public static function activation() {
    if ( ! self::compatible_version() ) {
      // Deactivate the plugin.
      deactivate_plugins( TSU_BASENAME );

      // Show the error message.
      wp_die( self::_get_version_error_message(), __( 'Plugin Activation Error', 'tsu' ),  array( 'response' => 200, 'back_link' => true ) );
    }
  }

  /**
   * Backup check, in case the plugin is activated in a weird way or the versions change after activation.
   *
   * @since 1.0.2
   */
  public static function check_version() {
    // Check for version compatibility.
    if ( ! self::compatible_version() && is_plugin_active( TSU_BASENAME ) ) {
      // Deactivate the plugin if it is active and create an admin notice.
      deactivate_plugins( TSU_BASENAME );
      add_action( 'admin_notices', array( 'Tsu_Version_Check', 'disabled_notice' ) );
      unset( $_GET['activate'] );
    }
  }

  /**
   * Callback to display admin notice.
   *
   * @since 1.0.2
   */
  public static function disabled_notice() {
    printf( '<div class="error"><p>%s</p></div>', self::_get_version_error_message() );
  }

  /**
   * Are the WordPress and PHP versions sufficient?
   *
   * @since 1.0.2
   *
   * @return boolean True: compatible, False: incompatible.
   */
  public static function compatible_version() {
    if ( version_compare( $GLOBALS['wp_version'], self::MIN_WP, '<' )
      || version_compare( PHP_VERSION, self::MIN_PHP, '<' ) ) {
      return false;
    }
    return true;
  }
}

?>