<?php
/**
 * Clean up WordPress Admin Area
 *
 * @category   Plugin
 * @package    Mo\Support
 * @author     Christoph Schüßler <schuessler@montagmorgens.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GNU/GPLv2
 * @since      1.4.0
 */

namespace Mo\Support;

// Don't call this file directly.
defined( 'ABSPATH' ) || die();

/**
 * Plugin code.
 *
 * @var object|null $instance The plugin singleton.
 */
final class Cleanup {

	/**
	 * The plugin singleton.
	 *
	 * @var Cleanup Class instance.
	 */
	protected static $instance = null;

	/**
	 * Gets a singelton instance of our plugin.
	 *
	 * @return Cleanup
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		// Admin hooks.
		if ( is_admin() ) {
			\add_action( 'admin_init', [ $this, 'cleanup_admin' ] );
		}
	}

	/**
	 * Hide other plugins promo and advertisement crap in WP Admin.
	 */
	public static function cleanup_admin() {

		// iThemes Security.
		if ( \is_plugin_active( 'better-wp-security/better-wp-security.php' ) ) {
			// Enqueue kill stylesheet.
			add_action(
				'admin_enqueue_scripts',
				function( $hook ) {
					if ( strpos( $hook, 'itsec' ) ) {
						\wp_enqueue_style( 'mo-plugin-itsec-cleanup', \plugins_url( '../assets/css/plugin-itsec-cleanup.css', __FILE__ ), null, PLUGIN_VERSION );
					}
				}
			);
		}

		// Yoast SEO.
		if ( \is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) {
			// Enqueue kill stylesheet.
			add_action(
				'admin_enqueue_scripts',
				function( $hook ) {
					if ( strpos( $hook, 'wpseo' ) ) {
						\wp_enqueue_style( 'mo-plugin-wpseo-cleanup', \plugins_url( '../assets/css/plugin-wpseo-cleanup.css', __FILE__ ), null, PLUGIN_VERSION );
					}
				}
			);

			// Kill Yoast notifications.
			add_action(
				'admin_init',
				function() {
					if ( class_exists( 'Yoast_Notification_Center' ) ) {
						$yoast_nc = \Yoast_Notification_Center::get();
						remove_action( 'admin_notices', array( $yoast_nc, 'display_notifications' ) );
						remove_action( 'all_admin_notices', array( $yoast_nc, 'display_notifications' ) );
					}
				}
			);
		}

		// Google Analytics Germanized.
		if ( \is_plugin_active( 'ga-germanized/ga-germanized.php' ) ) {
			// Enqueue kill stylesheet.
			add_action(
				'admin_enqueue_scripts',
				function( $hook ) {
					if ( strpos( $hook, 'ga-germanized' ) ) {
						\wp_enqueue_style( 'mo-plugin-ga-germanized-cleanup', \plugins_url( '../assets/css/plugin-ga-germanized-cleanup.css', __FILE__ ), null, PLUGIN_VERSION );
					}
				}
			);
		}

	}

}
