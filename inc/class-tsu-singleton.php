<?php
/**
 * Base singleton class for plugin classes.
 *
 * @package Tsu
 * @subpackage Singleton_Base
 *
 * @since 1.0.0
 */
abstract class Tsu_Singleton {

  /**
   * Only instance of this class.
   *
   * @since 1.0.0
   *
   * @var Tsu_Class
   */
  protected static $instance;

  /**
   * If the class' setup function has already been called.
   *
   * @since 1.0.0
   *
   * @var boolean
   */
  protected static $is_set_up = false;

  /** Singleton, keep private. */
  final private function __clone() { }

  /** Singleton, keep private. */
  final private function __construct() { }

  /** Singleton, keep private. */
  final private function __wakeup() { }

  /**
   * Create / Get the instance of the current class.
   *
   * @since 1.0.0
   *
   * @return object Instance of the child class.
   */
  final public static function get_instance() {
    if ( ! isset( static::$instance ) ) {
      static::$instance = new static();
    }

    return static::$instance;
  }

  /**
   * Set $is_set_up for child class. This method must be extended by child class!
   *
   * @since 1.0.0
   *
   * @return boolean If the child instance is already set up.
   */
  public static function setup() {
    if ( ! static::$is_set_up ) {
      static::$is_set_up = true;
      return false;
    }
    return true;
  }
}

?>