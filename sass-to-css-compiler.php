<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @since             2.0.0
 * @package           Sass_To_Css_Compiler
 *
 * Plugin Name:       Sass To CSS Compiler
 * Plugin URI:        https://wordpress.org/plugins/sass-to-css-compiler/
 * Description:       Compile Your Theme-Plugin Sass (.scss) files to .css on the fly.
 * Version:           2.0.1
 * Author:            Sajjad Hossain Sagor
 * Author URI:        https://sajjadhsagor.com/
 * Requires PHP:      8.1
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sass-to-css-compiler
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) die;

/**
 * Currently plugin version.
 */
define( 'SASS_TO_CSS_COMPILER_VERSION', '2.0.1' );

/**
 * Define Plugin Folders Path
 */
define( 'SASS_TO_CSS_COMPILER_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

define( 'SASS_TO_CSS_COMPILER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

define( 'SASS_TO_CSS_COMPILER_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-activator.php
 * 
 * @since    2.0.0
 */
function activate_sass_to_css_compiler()
{
	require_once SASS_TO_CSS_COMPILER_PLUGIN_PATH . 'includes/class-plugin-activator.php';
	
	Sass_To_Css_Compiler_Activator::activate();
}

register_activation_hook( __FILE__, 'activate_sass_to_css_compiler' );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-deactivator.php
 * 
 * @since    2.0.0
 */
function deactivate_sass_to_css_compiler()
{
	require_once SASS_TO_CSS_COMPILER_PLUGIN_PATH . 'includes/class-plugin-deactivator.php';
	
	Sass_To_Css_Compiler_Deactivator::deactivate();
}

register_deactivation_hook( __FILE__, 'deactivate_sass_to_css_compiler' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 * 
 * @since    2.0.0
 */
require SASS_TO_CSS_COMPILER_PLUGIN_PATH . 'includes/class-plugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.0
 */
function run_sass_to_css_compiler()
{
	$plugin = new Sass_To_Css_Compiler();
	
	$plugin->run();
}

run_sass_to_css_compiler();
