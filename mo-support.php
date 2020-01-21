<?php
/**
 * MONTAGMORGENS Support Plugin
 *
 * @category   Plugin
 * @package    Mo\Support
 * @author     Christoph Schüßler <schuessler@montagmorgens.com>
 * @license    https://www.gnu.org/licenses/gpl-2.0.txt GNU/GPLv2
 * @since      1.0.0
 *
 * @wordpress-plugin
 * Plugin Name: MONTAGMORGENS Support
 * Description: Dieses Plugin stellt Support-Informationen und Branding von MONTAGMORGENS zur Verfügung.
 * Version:     1.1.2
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

// Define absolute path to plugin root.
if ( ! defined( 'Mo\Support\PLUGIN_PATH' ) ) {
	define( 'Mo\Support\PLUGIN_PATH', wp_normalize_path( plugin_dir_path( __FILE__ ) ) );
}

// Define relative path to plugin root.
if ( ! defined( 'Mo\Support\PLUGIN_URL' ) ) {
	define( 'Mo\Support\PLUGIN_URL', plugins_url( '', __FILE__ ) );
}

// Define plugin version.
if ( ! defined( 'Mo\Support\PLUGIN_VERSION' ) ) {
	define( 'Mo\Support\PLUGIN_VERSION', '1.1.2' );
}

// Require composer autoloader.
require_once \Mo\Support\PLUGIN_PATH . '/vendor/autoload.php';

// Init plugin instances.
\add_action( 'plugins_loaded', '\Mo\Support\Admin_Dashboard::get_instance' );
\add_action( 'plugins_loaded', '\Mo\Support\Login_Screen::get_instance' );
