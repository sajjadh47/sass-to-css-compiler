<?php
/**
 * This file contains the definition of the Sass_To_Css_Compiler_I18n class, which
 * is used to load the plugin's internationalization.
 *
 * @package       Sass_To_Css_Compiler
 * @subpackage    Sass_To_Css_Compiler/includes
 * @author        Sajjad Hossain Sagor <sagorh672@gmail.com>
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since    2.0.0
 */
class Sass_To_Css_Compiler_I18n {
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'sass-to-css-compiler',
			false,
			dirname( SASS_TO_CSS_COMPILER_PLUGIN_BASENAME ) . '/languages/'
		);
	}
}
