<?php
/**
 * Class autoloader.
 *
 * @package WCUS_Demo
 * @subpackage Theme
 */

namespace Chance_Digital\WCUS_Demo\Theme\Autoload;

if ( file_exists( WCUS_DEMO_PATH . 'vendor/autoload.php' ) ) {
	require_once WCUS_DEMO_PATH . 'vendor/autoload.php';
} else {
	/**
	 * Backup custom autoloader function for theme classes.
	 *
	 * @access private
	 * @param  string $class_name Class name to load.
	 * @return bool               True if the class was loaded, false otherwise.
	 */
	function _autoload( $class_name ) {
		if ( strpos( $class_name, WCUS_DEMO_NAMESPACE . '\\' ) !== 0 ) {
			return false;
		}
		$parts = explode( '\\', substr( $class_name, strlen( WCUS_DEMO_NAMESPACE . '\\' ) ) );
		$path = untrailingslashit( WCUS_DEMO_INC );
		foreach ( $parts as $part ) {
			$path .= '/' . $part;
		}
		$path .= '.php';
		if ( file_exists( $path ) ) {
			require_once $path;
			return true;
		}
		return false;
	}
	spl_autoload_register( __NAMESPACE__ . '\\_autoload' );
}
