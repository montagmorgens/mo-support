<?php
/**
 * Customize WP Admin Dashboard with Support Info
 *
 * @package     Mo\Support
 * @author      MONTAGMORGENS GmbH
 * @copyright   2020 MONTAGMORGENS GmbH
 *
 * @wordpress-plugin
 * Plugin Name: MONTAGMORGENS Support
 * Description: Dieses Plugin stellt Support-Informationen von MONTAGMORGENS zur Verfügung.
 * Version:     1.0.0
 * Author:      MONTAGMORGENS GmbH
 * Author URI:  https://www.montagmorgens.com/
 * License:     GNU General Public License v.2
 * Text Domain: mo-support
 * GitHub Plugin URI: montagmorgens/mo-support
 */

namespace Mo\Support;

// Don't call this file directly.
defined( 'ABSPATH' ) || die();

// Bail if not on admin screen.
if ( ! is_admin() ) {
	return;
}

// Init plugin instance.
\add_action( 'plugins_loaded', '\Mo\Support\Admin_Dashboard::get_instance' );

/**
 * Plugin code.
 *
 * @var object|null $instance The plugin singleton.
 */
final class Admin_Dashboard {

	/**
	 * The plugin singleton.
	 *
	 * @var Admin_Dashboard Class instance.
	 */
	protected static $instance = null;

	/**
	 * Gets a singelton instance of our plugin.
	 *
	 * @return Admin_Dashboard
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

		// Add action hooks.
		\add_action( 'admin_init', array( $this, 'remove_dashboard_widgets' ) );
		\add_action( 'wp_dashboard_setup', array( $this, 'mo_dashboard' ) );
	}

	/**
	 * Remove admin dashboard widgets and panels.
	 */
	public function remove_dashboard_widgets() {
		\remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
		\remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
		\remove_meta_box( 'dashboard_primary', 'dashboard', 'normal' ); // ‘WordPress Events and News’ dashboard widget.
		\remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' ); // deprecated.
		\remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
		\remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
		\remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
		\remove_meta_box( 'wpseo-dashboard-overview', 'dashboard', 'normal' ); // Yoast SEO.

		\remove_action( 'welcome_panel', 'wp_welcome_panel' ); // Welcome panel.
	}

	/**
	 * Add custom MONTAGMORGENS support dahsboard widget.
	 */
	public function mo_dashboard() {
		global $wp_meta_boxes;
		\add_meta_box(
			'mo_support',
			__( 'MONTAGMORGENS-Support', 'mo-dashboard' ),
			function() {
				printf(
					/* translators: %1$s: e-mail-address, %2$s: phone number */
					esc_html__( 'Bei Fragen zu und Problemen mit Ihrer WordPress-Installation erreichen sie uns per E-Mail unter %1$s oder telefonisch unter %2$s.', 'mo-support' ),
					'<a href="mailto:support@montagmorgens.com">support@montagmorgens.com</a>',
					'<a href="tel:+4921515374111">+49 (0)2151 5374-111</a>'
				);
			},
			'dashboard',
			'side',
			'high'
		);
	}
}
