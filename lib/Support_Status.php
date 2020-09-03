<?php
/**
 * Customize WP Admin Dashboard with Support Info
 *
 * @category   Plugin
 * @package    Mo\Support
 * @author     Christoph Schüßler <schuessler@montagmorgens.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GNU/GPLv2
 * @since      1.3.0
 */

namespace Mo\Support;

// Don't call this file directly.
defined( 'ABSPATH' ) || die();

/**
 * Plugin code.
 *
 * @var object|null $instance The plugin singleton.
 */
final class Support_Status {

	/**
	 * The plugin singleton.
	 *
	 * @var Support_Status Class instance.
	 */
	protected static $instance = null;

	/**
	 * Gets a singelton instance of our plugin.
	 *
	 * @return Support_Status
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Support contract type.
	 *
	 * @var string/bool A support contract type, can be 'free', 'active', 'self' or false.
	 */
	protected static $support_contract = false;

	/**
	 * Free support period end date.
	 *
	 * @var string/bool self::$support_free_until Date in format YYYY-MM-DD.
	 */
	protected static $support_remaining_free_days = false;

	/**
	 * Constructor.
	 */
	private function __construct() {

		self::$support_contract = defined( 'MO_SUPPORT' ) ? MO_SUPPORT : false;

		// Try to parse timestamp for end of support period.
		$support_free_until = defined( 'MO_SUPPORT_FREE_UNTIL' ) ? MO_SUPPORT_FREE_UNTIL : false;
		$support_free_until = ( is_string( $support_free_until ) && strtotime( $support_free_until ) ) ? $support_free_until : false;

		// Returns positive number for remainigs days, negative if past due date.
		self::$support_remaining_free_days = (int) ( new \DateTime( 'midnight' ) )->diff( new \DateTime( $support_free_until ) )->format( '%r%a' );

		// Exclude our own systems.
		if ( 'self' !== self::$support_contract ) {
			\add_action( 'wp_dashboard_setup', [ $this, 'mo_dashboard' ] );
			\add_action( 'admin_print_styles', [ $this, 'enqueue_assets' ] );
			\add_filter( 'site_status_tests', [ $this, 'add_support_type_test' ] );
		}
	}

	/**
	 * Enqueue plugin assets.
	 */
	public function enqueue_assets() {
		\wp_enqueue_style( 'mo-support', PLUGIN_URL . '/assets/css/mo-support-dashboard-widget.css', null, PLUGIN_VERSION );
	}

	/**
	 * Print MONTAGMORGENS support dahsboard widget content.
	 */
	public function mo_dashboard_content() {
		if ( 'active' === self::$support_contract ) {
			// Print info for active contracts.
			printf(
				'<p><strong>%s</strong></p><p><span class="mo-support-tag mo-support-tag--active">%s</span></p><p>%s</p><hr>',
				esc_html__( 'Ihr Wartungsvertrag', 'mo-support' ),
				esc_html__( 'Wartungsvertrag aktiv', 'mo-support' ),
				esc_html__( 'Wir halten Ihre WordPress-Installation immer auf dem neuesten Stand und legen tägliche Backups an.', 'mo-support' )
			);
		} elseif ( 'free' === self::$support_contract ) {

			// Set string indicating the number of remainig days.
			$remaining_days_string = esc_html__( 'abgelaufen', 'mo-support' );

			if ( 1 === self::$support_remaining_free_days ) {
				$remaining_days_string = esc_html__( '– noch 1 Tag', 'mo-support' );
			} elseif ( self::$support_remaining_free_days > 0 ) {
				$remaining_days_string = sprintf(
					/* translators: %s: number of days */
					esc_html__( '– noch %s Tage', 'mo-support' ),
					self::$support_remaining_free_days
				);
			}

			// Print contract status.
			printf(
				'<p><strong>%1$s</strong></p><p><span class="mo-support-tag mo-support-tag--%5$s">%2$s %3$s</span></p><p>%4$s</p><hr>',
				esc_html__( 'Ihr Wartungsvertrag', 'mo-support' ),
				esc_html__( 'Kostenlose Wartungsperiode', 'mo-support' ),
				$remaining_days_string,
				esc_html__( 'In den ersten drei Monaten nach Launch Ihrer Website halten wir Ihre WordPress-Installation immer auf dem neuesten Stand und legen tägliche Backups an. Im Anschluß bieten wir Ihnen diesen Service gerne im Rahmen eines Wartungsvertrags an. Wir melden uns vor Ablauf der kostenlosen Wartungsperiode dazu bei Ihnen.', 'mo-support' ),
				( self::$support_remaining_free_days > 0 ) ? 'free' : 'none'
			);
		} else {
			// Print info for no contract.
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
	 *
	 * Add `define( 'MO_SUPPORT', 'active' );` to `wp-config.php` for systems with active support contract
	 * Add `define( 'MO_SUPPORT', 'free' );` to `wp-config.php` for systems within free support interval
	 * Add `define( 'MO_SUPPORT', 'self' );` to `wp-config.php` for systems owned by MONTAGMORGENS GmbH
	 * Add `define( 'MO_SUPPORT_FREE_UNTIL', 'YYYY-MM-DD' );` to `wp-config.php` to set end date for free support period
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

		/**
		 * Add test for support type.
		 *
		 * @param array $tests The site-health tests.
		 */
	public function add_support_type_test( $tests ) {
		$tests['direct']['mo_support'] = [
			'label' => __( 'MONTAGMORGENS Support plugin test', 'mo-support' ),
			'test'  => [ $this, 'support_type_test' ],
		];
		return $tests;
	}

	/**
	 * The site-health test for development env adds a recommendation to turn off revelopment mode.
	 */
	public function support_type_test() {
		$result = [
			'label'       => esc_html__( 'Kein MONTAGMORGENS-Wartungsvertrag aktiv', 'mo-core' ),
			'status'      => 'critical',
			'badge'       => [
				'label' => esc_html__( 'Security' ),
				'color' => 'red',
			],
			'description' => sprintf(
				'<p>%s</p>',
				esc_html__( 'Bitte sorgen Sie dafür, dass Ihre WordPress-Installation aktuell und sicher bleibt und Backups erstellt werden. Gerne übernehmen wir dies im Rahmen eines Wartungsvertrags für Sie. Sprechen Sie uns einfach an!', 'mo-support' )
			),
			'actions'     => sprintf(
				'<a href="https://www.montagmorgens.com/kontakt" target="_blank">%s</a>',
				esc_html__( 'Nehmen Sie Kontakt zu uns auf!', 'mo-support' )
			),
			'test'        => 'mo_support',
		];

		if ( 'active' === self::$support_contract ) {
			$result['status']         = 'good';
			$result['label']          = esc_html__( 'Ihr MONTAGMORGENS-Wartungsvertrag ist aktiv', 'mo-core' );
			$result['badge']['color'] = 'green';
			$result['description']    = sprintf(
				'<p>%s</p>',
				esc_html__( 'Wir halten Ihre WordPress-Installation immer auf dem neuesten Stand und legen tägliche Backups an.', 'mo-support' )
			);
		} elseif ( 'free' === self::$support_contract && self::$support_remaining_free_days > 0 ) {
			$result['status']         = 'good';
			$result['label']          = esc_html__( 'Sie befinden sich in der kostenlosen MONTAGMORGENS-Wartungsperiode', 'mo-core' );
			$result['badge']['color'] = 'blue';
			$result['description']    = sprintf(
				'<p>%s</p>',
				esc_html__( 'In den ersten drei Monaten nach Launch Ihrer Website halten wir Ihre WordPress-Installation immer auf dem neuesten Stand und legen tägliche Backups an. Im Anschluß bieten wir Ihnen diesen Service gerne im Rahmen eines Wartungsvertrags an. Wir melden uns vor Ablauf der kostenlosen Wartungsperiode dazu bei Ihnen.', 'mo-support' ),
			);
		}

		return $result;
	}
}
