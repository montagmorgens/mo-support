<?php
/**
 * Customize WP Admin Dashboard
 *
 * @category   Plugin
 * @package    Mo\Support
 * @author     Christoph Schüßler <schuessler@montagmorgens.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GNU/GPLv2
 * @since      1.0.0
 */

namespace Mo\Support;

// Don't call this file directly.
defined( 'ABSPATH' ) || die();

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
		\add_action( 'admin_init', [ $this, 'remove_dashboard_widgets' ] );
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
		\remove_meta_box( 'wpseo-dashboard-overview', 'dashboard', 'normal' ); // Yoast SEO Plugin.
		\remove_meta_box( 'tribe_dashboard_widget', 'dashboard', 'normal' ); // News from Modern Tribe – The Events Calendar Plugin.
		\remove_meta_box( 'themefusion_news', 'dashboard', 'normal' ); // News from ThemeFusion – Avada Theme.

		\remove_action( 'welcome_panel', 'wp_welcome_panel' ); // Welcome panel.
	}

}
