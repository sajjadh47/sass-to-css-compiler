<?php

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, other methods and
 * enqueue the public-facing stylesheet and JavaScript.
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
	 * @since    2.0.0
	 * @param    string    $plugin_name   The name of the plugin.
	 * @param    string    $version   The version of this plugin.
	 */
	public function __construct( $plugin_name, $version )
	{
		$this->plugin_name 	= $plugin_name;
		
		$this->version 		= $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style( $this->plugin_name, SASS_TO_CSS_COMPILER_PLUGIN_URL . 'public/css/public.scss', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script( $this->plugin_name, SASS_TO_CSS_COMPILER_PLUGIN_URL . 'public/js/public.js', array( 'jquery' ), $this->version, false );

		wp_localize_script( $this->plugin_name, 'SASS_TO_CSS_COMPILER', array(
			'ajaxurl'	=> admin_url( 'admin-ajax.php' ),
		) );
	}

	/**
	 * Compile on run time and cache it for next time use.
	 *
	 * @since    2.0.0
	 * @param    string    $src   The src uri of the stylesheet.
	 * @param    string    $handle   The name of the stylesheet.
	 * @return 	 string    $src   The compiled src uri of the stylesheet or original src uri.
	 */
	public function style_loader_src( $src, $handle )
	{
		$compiler_enabled 			= Sass_To_Css_Compiler::get_option( 'enable', 'sasstocss_basic_settings', 'off' );

		// compiler is not enabled so nothing to do here...
		if ( $compiler_enabled !== 'on' ) return $src;

		$url 						= parse_url( $src );

		$pathinfo 					= pathinfo( $url['path'] );

		// Check if it's a scss file or not
		if ( isset( $pathinfo['extension'] ) && $pathinfo['extension'] !== 'scss' ) return $src;

		// if stylesheet is built in wp don't touch it
		$built_in_script 			= preg_match_all( '/(\/wp-includes\/)|(\/wp-admin\/)/', $src, $matches );

		if ( $built_in_script === 1 ) return $src;

		// Convert $src to relative paths
		$relative_path 				= preg_replace( '/^' . preg_quote( site_url(), '/' ) . '/i', '', $src );

		// Don't do anything if file is from CDN, external site or relative path
		if ( preg_match( '#^//#', $relative_path ) || strpos( $relative_path, '/' ) !== 0 ) return $src;

		$excluded_files 			= Sass_To_Css_Compiler::get_option( 'exclude', 'sasstocss_basic_settings', '' );

		$included_files 			= Sass_To_Css_Compiler::get_option( 'include', 'sasstocss_basic_settings', '' );

		$cache_dir 					= Sass_To_Css_Compiler::get_cache_dir();
		
		$cache_dir_url 				= Sass_To_Css_Compiler::get_cache_dir( false, 'baseurl' );

		// get script file name
		$filename 					= basename( $src );

		// check if include files is not empty
		if ( $included_files !== '' )
		{
			// get all comma separated file list
			$included_files_list 	= explode( ',', $included_files );

			// check if any valid comma separated file exists
			if ( ! empty( $included_files_list ) )
			{
				// if not included don't continue
				if ( ! in_array( $filename , $included_files_list ) ) return $src;
			}
		}
		else
		{
			if ( $excluded_files !== '' )
			{
				// get all comma separated file list
				$excluded_files_list = explode( ',', $excluded_files );

				// check if any valid comma separated file exists
				if ( ! empty( $excluded_files_list ) )
				{
					// if not excluded don't continue
					if ( in_array( $filename , $excluded_files_list ) ) return $src;
				}
			}
		}

		// check if file is already generated... if so load cache file
		if ( file_exists( $cache_dir . '/' . $filename ) )
		{
			$src 					= $cache_dir_url . '/' . $pathinfo['filename'] . '.css';
		}
		else
		{
			// get stylesheet file content
			$response 				= wp_remote_get( $src );

			if ( ! is_wp_error( $response ) )
			{
				$scss_code 			= wp_remote_retrieve_body( $response );

				// Create a complete path
				$import_path 		= rtrim( $_SERVER['DOCUMENT_ROOT'], '/') . $pathinfo['dirname'];

				$compiled_content 	= Sass_To_Css_Compiler::compile( $scss_code, $import_path );

				if ( $compiled_content )
				{
					$src 			= $cache_dir_url . '/' . $pathinfo['filename'] . '.css';
					
					Sass_To_Css_Compiler::save( $compiled_content, $pathinfo['filename'] . '.css' );
				}
			}
		}

		// keep any stylesheets file args... like ver
		return empty( $url['query'] ) ? $src : $src . '?' . $url['query'];
	}
}
