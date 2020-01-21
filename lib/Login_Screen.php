<?php
/**
 * Customize WP Admin Login Screen
 *
 * @category   Plugin
 * @package    Mo\Support
 * @author     Christoph Schüßler <schuessler@montagmorgens.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GNU/GPLv2
 * @since      1.1.0
 */

namespace Mo\Support;

// Don't call this file directly.
defined( 'ABSPATH' ) || die();

/**
 * Plugin code.
 *
 * @var object|null $instance The plugin singleton.
 */
final class Login_Screen {

	/**
	 * The plugin singleton.
	 *
	 * @var Login_Screen Class instance.
	 */
	protected static $instance = null;

	/**
	 * Gets a singelton instance of our plugin.
	 *
	 * @return Login_Screen
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
		\add_action( 'login_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		\add_filter( 'login_headertext', [ $this, 'login_headertext' ] );
		\add_filter( 'login_headerurl', [ $this, 'login_headerurl' ] );
	}

	/**
	 * Enqueue plugin assets.
	 */
	public function enqueue_assets() {
		\wp_enqueue_style( 'mo-support', PLUGIN_URL . '/assets/css/mo-support-login-screen.css', null, PLUGIN_VERSION );
	}

	/**
	 * Print custom login header markup.
	 */
	public function login_headertext() {
		return '<span>Powered by WordPress,</span> supported by <span>MONTAGMORGENS GmbH</span>';
	}

	/**
	 * Overwrite login header url.
	 */
	public function login_headerurl() {
		return 'https://montagmorgens.com';
	}
}
