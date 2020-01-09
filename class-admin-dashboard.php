<?php
/**
 * Customize WP Admin Dashboard with Support Info
 *
 * @category   Plugin
 * @package    Mo\Support
 * @author     Christoph Schüßler <schuessler@montagmorgens.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GNU/GPLv2
 * @since      1.0.0
 *
 * @wordpress-plugin
 * Plugin Name: MONTAGMORGENS Support
 * Description: Dieses Plugin stellt Support-Informationen von MONTAGMORGENS zur Verfügung.
 * Version:     1.0.0
 * Author:      MONTAGMORGENS GmbH
 * Author URI:  https://www.montagmorgens.com/
 * License:     GNU General Public License v.2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
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

// Define absolute path to plugin root.
if ( ! defined( 'Mo\Support\PLUGIN_PATH' ) ) {
	define( 'Mo\Support\PLUGIN_PATH', wp_normalize_path( plugin_dir_path( __FILE__ ) ) );
}

// Init plugin instance.
\add_action( 'plugins_loaded', '\Mo\Support\Admin_Dashboard::get_instance' );

/**
 * Plugin code.
 *
 * @var object|null $instance The plugin singleton.
 */
final class Admin_Dashboard {

	const PLUGIN_VERSION = '1.0.0';

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
		\add_action( 'admin_init', [ $this, 'remove_dashboard_widgets' ] );
		\add_action( 'wp_dashboard_setup', [ $this, 'mo_dashboard' ] );
		\add_action( 'admin_print_styles', [ $this, 'enqueue_assets' ] );
	}

	/**
	 * Enqueue plugin assets.
	 */
	public function enqueue_assets() {
		\wp_enqueue_style( 'mo-support', \plugins_url( '/assets/css/mo-support-dashboard-widget.css', __FILE__ ), null, self::PLUGIN_VERSION );
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
		\remove_meta_box( 'wpseo-dashboard-overview', 'dashboard', 'normal' ); // Yoast SEO.

		\remove_action( 'welcome_panel', 'wp_welcome_panel' ); // Welcome panel.
	}

	/**
	 * Print MONTAGMORGENS support dahsboard widget content.
	 *
	 * Add `define( 'MO_SUPPORT', 'active' );` to `wp-config.php` for systems with active support contract
	 * Add `define( 'MO_SUPPORT', 'free' );` to `wp-config.php` for systems within free support interval
	 */
	public function mo_dashboard_content() {
		$support_contract = defined( 'MO_SUPPORT' ) ? MO_SUPPORT : false;

		if ( 'active' === $support_contract ) {
			printf(
				'<p><strong>%s</strong></p><p><span class="mo-support-tag mo-support-tag--active">%s</span></p><p>%s</p><p><hr>',
				esc_html__( 'Ihr Wartungsvertrag', 'mo-support' ),
				esc_html__( 'Wartungsvertrag aktiv', 'mo-support' ),
				esc_html__( 'Wir halten Ihre WordPress-Installation immer auf dem neuesten Stand und legen tägliche Backups an.', 'mo-support' ),
			);
		}

		// Print contact info.
		printf(
			'<p><strong>%s</strong></p><p>%s</p>',
			esc_html__( 'WordPress-Notfall?', 'mo-support' ),
			sprintf(
				/* translators: %1$s: e-mail-address, %2$s: phone number */
				esc_html__( 'Bei Fragen zu und Problemen mit Ihrer WordPress-Installation erreichen sie uns per E-Mail unter %1$s oder telefonisch unter %2$s.', 'mo-support' ),
				'<a href="mailto:support@montagmorgens.com">support@montagmorgens.com</a>',
				'<a href="tel:+4921515374111">+49 (0)2151 5374-111</a>'
			)
		);
	}

	/**
	 * Add custom MONTAGMORGENS support dahsboard widget.
	 */
	public function mo_dashboard() {
		global $wp_meta_boxes;
		\add_meta_box(
			'mo_support',
			__( 'MONTAGMORGENS-Support', 'mo-dashboard' ),
			[ $this, 'mo_dashboard_content' ],
			'dashboard',
			'side',
			'high'
		);
	}
}
