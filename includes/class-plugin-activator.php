<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      2.0.0
 * @package    Sass_To_Css_Compiler
 * @subpackage Sass_To_Css_Compiler/includes
 * @author     Sajjad Hossain Sagor <sagorh672@gmail.com>
 */
class Sass_To_Css_Compiler_Activator
{
	/**
	 * Create cache folder for the plugin if not exists
	 *
	 * @since 	2.0.0
	 * @access  public
	 */
	public static function activate()
	{
		// check if upload directory is writable...
		if ( Sass_To_Css_Compiler::is_upload_dir_writable() )
		{
			// create cache dir if not already there
			Sass_To_Css_Compiler::create_cache_dir();
		}
	}
}
