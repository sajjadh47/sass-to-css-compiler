<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @package           Sass_To_Css_Compiler
 * @author            Sajjad Hossain Sagor <sagorh672@gmail.com>
 *
 * Plugin Name:       Sass To CSS Compiler
 * Plugin URI:        https://wordpress.org/plugins/sass-to-css-compiler/
 * Description:       Compile Your Theme-Plugin Sass (.scss) files to .css on the fly.
 * Version:           2.0.2
 * Requires at least: 5.6
 * Requires PHP:      8.2
 * Author:            Sajjad Hossain Sagor
 * Author URI:        https://sajjadhsagor.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sass-to-css-compiler
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'SASS_TO_CSS_COMPILER_PLUGIN_VERSION', '2.0.2' );

/**
 * Define Plugin Folders Path
 */
define( 'SASS_TO_CSS_COMPILER_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

define( 'SASS_TO_CSS_COMPILER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

define( 'SASS_TO_CSS_COMPILER_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-sass-to-css-compiler-activator.php
 *
 * @since    2.0.0
 */
function on_activate_sass_to_css_compiler() {
	require_once SASS_TO_CSS_COMPILER_PLUGIN_PATH . 'includes/class-sass-to-css-compiler-activator.php';

	Sass_To_Css_Compiler_Activator::on_activate();
}

register_activation_hook( __FILE__, 'on_activate_sass_to_css_compiler' );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-sass-to-css-compiler-deactivator.php
 *
 * @since    2.0.0
 */
function on_deactivate_sass_to_css_compiler() {
	require_once SASS_TO_CSS_COMPILER_PLUGIN_PATH . 'includes/class-sass-to-css-compiler-deactivator.php';

	Sass_To_Css_Compiler_Deactivator::on_deactivate();
}

register_deactivation_hook( __FILE__, 'on_deactivate_sass_to_css_compiler' );

/**
 * The core plugin class that is used to define admin-specific and public-facing hooks.
 *
 * @since    2.0.0
 */
require SASS_TO_CSS_COMPILER_PLUGIN_PATH . 'includes/class-sass-to-css-compiler.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.0
 */
function run_sass_to_css_compiler() {
	$plugin = new Sass_To_Css_Compiler();

	$plugin->run();
}

run_sass_to_css_compiler();
