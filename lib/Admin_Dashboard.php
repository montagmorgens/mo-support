<?php
/**
 * Customize WP Admin Dashboard with Support Info
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
		\add_action( 'wp_dashboard_setup', [ $this, 'mo_dashboard' ] );
		\add_action( 'admin_print_styles', [ $this, 'enqueue_assets' ] );
	}

	/**
	 * Enqueue plugin assets.
	 */
	public function enqueue_assets() {
		\wp_enqueue_style( 'mo-support', PLUGIN_URL . '/assets/css/mo-support-dashboard-widget.css', null, PLUGIN_VERSION );
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

	/**
	 * Print MONTAGMORGENS support dahsboard widget content.
	 *
	 * Add `define( 'MO_SUPPORT', 'active' );` to `wp-config.php` for systems with active support contract
	 * Add `define( 'MO_SUPPORT', 'free' );` to `wp-config.php` for systems within free support interval
	 * Add `define( 'MO_SUPPORT_FREE_UNTIL', 'YYYY-MM-DD' );` to `wp-config.php` to set end date for free support period
	 */
	public function mo_dashboard_content() {
		$support_contract   = defined( 'MO_SUPPORT' ) ? MO_SUPPORT : false;
		$support_free_until = defined( 'MO_SUPPORT_FREE_UNTIL' ) ? MO_SUPPORT_FREE_UNTIL : false;

		// Print info for active contracts.
		if ( 'active' === $support_contract ) {
			printf(
				'<p><strong>%s</strong></p><p><span class="mo-support-tag mo-support-tag--active">%s</span></p><p>%s</p><hr>',
				esc_html__( 'Ihr Wartungsvertrag', 'mo-support' ),
				esc_html__( 'Wartungsvertrag aktiv', 'mo-support' ),
				esc_html__( 'Wir halten Ihre WordPress-Installation immer auf dem neuesten Stand und legen tägliche Backups an.', 'mo-support' )
			);
		}

		// Print info for free support.
		if ( 'free' === $support_contract ) {

			// Try to parse timestamp for end of support period.
			$support_free_until = ( is_string( $support_free_until ) && strtotime( $support_free_until ) ) ? $support_free_until : false;

			// Returns positive number for remainigs days, negative if past due date.
			$remaining_days = (int) ( new \DateTime( 'midnight' ) )->diff( new \DateTime( $support_free_until ) )->format( '%r%a' );

			// Set string indicating the number of remainig days.
			$remaining_days_string = esc_html__( 'abgelaufen', 'mo-support' );

			if ( 1 === $remaining_days ) {
				$remaining_days_string = esc_html__( '– noch 1 Tag', 'mo-support' );
			} elseif ( $remaining_days > 0 ) {
				$remaining_days_string = sprintf(
					/* translators: %s: number of days */
					esc_html__( '– noch %s Tage', 'mo-support' ),
					$remaining_days
				);
			}

			// Print contract status.
			printf(
				'<p><strong>%1$s</strong></p><p><span class="mo-support-tag mo-support-tag--%5$s">%2$s %3$s</span></p><p>%4$s</p><hr>',
				esc_html__( 'Ihr Wartungsvertrag', 'mo-support' ),
				esc_html__( 'Kostenlose Wartungsperiode', 'mo-support' ),
				$remaining_days_string,
				esc_html__( 'In den ersten drei Monaten nach Launch Ihrer Website halten wir Ihre WordPress-Installation immer auf dem neuesten Stand und legen tägliche Backups an. Im Anschluß bieten wir Ihnen diesen Service gerne im Rahmen eines Wartungsvertrags an. Wir melden uns vor Ablauf der kostenlosen Wartungsperiode dazu bei Ihnen.', 'mo-support' ),
				( $remaining_days > 0 ) ? 'free' : 'none'
			);
		}

		// Print info for no contract.
		if ( ! $support_contract ) {
			printf(
				'<p><span class="mo-support-tag mo-support-tag--none">%s</span></p><p>%s</p><hr>',
				esc_html__( 'Kein Wartungsvertrag aktiv', 'mo-support' ),
				esc_html__( 'Bitte sorgen Sie dafür, dass Ihre WordPress-Installation aktuell und sicher bleibt und Backups erstellt werden. Gerne übernehmen wir dies im Rahmen eines Wartungsvertrags für Sie. Sprechen Sie uns einfach an!', 'mo-support' )
			);
		}

		// Print general contact info.
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
