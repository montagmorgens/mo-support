<?php
/**
 * Set Recovery Mode
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
final class Recovery_Mode {

	/**
	 * The plugin singleton.
	 *
	 * @var Recovery_Mode Class instance.
	 */
	protected static $instance = null;

	/**
	 * Gets a singelton instance of our plugin.
	 *
	 * @return Recovery_Mode
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
		\add_action( 'recovery_mode_email', [ $this, 'recovery_mode_email' ] );
	}

	/**
	 * Get recovary mode email.
	 *
	 * @param array  $email Used to build wp_mail().
	 * @param string $url URL to enter recovery mode.
	 */
	public function recovery_mode_email( $email, $url ) {
		$email['to'] = 'wordpress@montagmorgens.com';
		return $email;
	}

}
