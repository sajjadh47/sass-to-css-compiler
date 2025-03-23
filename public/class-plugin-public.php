<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version and other methods.
 *
 * @package    Sass_To_Css_Compiler
 * @subpackage Sass_To_Css_Compiler/public
 * @author     Sajjad Hossain Sagor <sagorh672@gmail.com>
 */
class Sass_To_Css_Compiler_Public
{
	/**
	 * The ID of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 	 2.0.0
	 * @access   public
	 * @param    string    $plugin_name   The name of the plugin.
	 * @param    string    $version   The version of this plugin.
	 */
	public function __construct( $plugin_name, $version )
	{
		$this->plugin_name 	= $plugin_name;
		
		$this->version 		= $version;
	}

	/**
	 * Compile on run time and cache it for next time use.
	 *
	 * @since 	 2.0.0
	 * @access   public
	 * @param    string    $src   The src uri of the stylesheet.
	 * @param    string    $handle   The name of the stylesheet.
	 * @return 	 string    $src   The compiled src uri of the stylesheet or original src uri.
	 */
	public function style_loader_src( $src, $handle )
	{
		// if stylesheet is built in wp don't touch it
		$built_in_script 			= preg_match_all( '/(\/wp-includes\/)|(\/wp-admin\/)/', $src, $matches );

		if ( $built_in_script === 1 ) return $src;
		
		$compiler_enabled 			= Sass_To_Css_Compiler::get_option( 'enable', 'sasstocss_basic_settings', 'off' );

		// compiler is not enabled so nothing to do here...
		if ( $compiler_enabled !== 'on' ) return $src;

		// Get the site URL
		$parsed_site_url 			= parse_url( get_site_url() );

		$parsed_src_url 			= parse_url( $src );

		$pathinfo 					= pathinfo( $parsed_src_url['path'] );

		// Check if it's a scss file or not
		if ( ! isset( $pathinfo['extension'] ) || $pathinfo['extension'] !== 'scss' ) return $src;

		// Check if the file is from an external domain or CDN or is a relative path (does not start with http or https)
		if ( ! isset( $parsed_src_url['host'] ) || ! isset( $parsed_src_url['scheme'] ) || $parsed_src_url['host'] !== $parsed_site_url['host'] ) return $src;

		$relative_path 				= $pathinfo['dirname'];
		
		$basename 					= $pathinfo['basename'];
		
		$filename 					= $pathinfo['filename'] . '.css';

		$file_full_path				= rtrim( ABSPATH, '/' ) . $parsed_src_url['path'];

		$included_files 			= Sass_To_Css_Compiler::get_option( 'include', 'sasstocss_basic_settings', '' );

		$cache_target_dir_path 		= Sass_To_Css_Compiler::get_cache_dir() . $relative_path;
		
		$cache_target_dir_url 		= Sass_To_Css_Compiler::get_cache_dir( false, 'baseurl' ) . $relative_path;

		// check if include files is not empty
		if ( ! empty( $included_files ) )
		{
			// get all comma separated file list
			$included_files_list 	= explode( ',', $included_files );

			// check if any valid comma separated file exists
			if ( ! empty( $included_files_list ) )
			{
				// if not included don't continue
				if ( ! in_array( $basename , $included_files_list ) ) return $src;
			}
			else
			{
				return $src;
			}
		}
		else
		{
			return $src;
		}

		if ( ! is_dir( $cache_target_dir_path ) )
		{
			mkdir( $cache_target_dir_path, 0755, true ); // 0755 permission & recursive creation
		}

		// check if file is already generated... if so load cache file
		if ( file_exists( $cache_target_dir_path . DIRECTORY_SEPARATOR . $filename ) )
		{
			$src 					= $cache_target_dir_url . '/' . $filename;
		}
		else
		{
			// get stylesheet file content
			$scss_code 				= file_get_contents( $file_full_path );

			// Create a complete path
			$import_path 			= rtrim( ABSPATH, '/' ) . $relative_path;

			$compiled_content 		= Sass_To_Css_Compiler::compile( $scss_code, $import_path );

			if ( $compiled_content )
			{
				$src 				= $cache_target_dir_url . '/' . $filename;
				
				Sass_To_Css_Compiler::save( $compiled_content, $cache_target_dir_path . DIRECTORY_SEPARATOR . $filename );
			}
		}

		// keep any stylesheets file args... like ver
		return empty( $parsed_src_url['query'] ) ? $src : $src . '?' . $parsed_src_url['query'];
	}
}
