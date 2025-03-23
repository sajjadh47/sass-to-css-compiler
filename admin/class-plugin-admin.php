<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version and other methods.
 *
 * @package    Sass_To_Css_Compiler
 * @subpackage Sass_To_Css_Compiler/admin
 * @author     Sajjad Hossain Sagor <sagorh672@gmail.com>
 */
class Sass_To_Css_Compiler_Admin
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
	 * The plugin options.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      array    $options    Holds saved/default value of plugin options.
	 */
	private $options;

	/**
	 * The plugin options api wrapper object.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      array    $settings_api    Holds the plugin settings api wrapper class object.
	 */
	private $settings_api;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 	 2.0.0
	 * @access   public
	 * @param    string    $plugin_name       The name of this plugin.
	 * @param    string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version )
	{
		$this->plugin_name 	= $plugin_name;
		
		$this->version 		= $version;

		$this->options 		= get_option( 'sasstocss_basic_settings', [] );
		
		$this->settings_api = new Sajjad_Dev_Settings_API;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 	 2.0.0
	 * @access   public
	 */
	public function enqueue_styles()
	{
		$current_screen = get_current_screen();

		// check if current page is plugin settings page
		if( $current_screen->id == 'toplevel_page_sass-to-css-compiler' )
		{
			wp_enqueue_style( $this->plugin_name, SASS_TO_CSS_COMPILER_PLUGIN_URL . 'admin/css/admin.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 	 2.0.0
	 * @access   public
	 */
	public function enqueue_scripts()
	{
		$current_screen = get_current_screen();

		// check if current page is plugin settings page
		if( $current_screen->id == 'toplevel_page_sass-to-css-compiler' )
		{
			wp_enqueue_script( $this->plugin_name, SASS_TO_CSS_COMPILER_PLUGIN_URL . 'admin/js/admin.js', array( 'jquery' ), $this->version, false );

			wp_localize_script( $this->plugin_name, 'SASS_TO_CSS_COMPILER', array(
				'ajaxurl'	=> admin_url( 'admin-ajax.php' ),
			) );
		}
	}

	/**
	 * Adds a settings link to the plugin's action links on the plugin list table.
	 *
	 * @since 	 2.0.0
	 * @access   public
	 * @param    array $links The existing array of plugin action links.
	 * @return   array The updated array of plugin action links, including the settings link.
	 */
	public function add_plugin_action_links( $links )
	{
		$links[] = sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=sass-to-css-compiler' ), __( 'Settings', 'sass-to-css-compiler' ) );
		
		return $links;
	}

	/**
	 * Adds the plugin settings page to the WordPress dashboard menu.
	 *
	 * @since 	 2.0.0
	 * @access   public
	 */
	public function admin_menu()
	{
		add_menu_page(
			__( 'Sass to CSS Compiler', 'sass-to-css-compiler' ),
			__( 'Sass to CSS Compiler', 'sass-to-css-compiler' ),
			'manage_options',
			'sass-to-css-compiler',
			array( $this, 'menu_page' ),
			'dashicons-admin-tools'
		);
	}

	/**
	 * Renders the plugin settings page form.
	 *
	 * @since 	 2.0.0
	 * @access   public
	 */
	public function menu_page()
	{
		$this->settings_api->show_forms();
	}

	/**
	 * Register Plugin Options Via Settings API
	 *
	 * @since 	 2.0.0
	 * @access   public
	 */
	public function admin_init()
	{
		//set the settings
		$this->settings_api->set_sections( $this->get_settings_sections() );
		
		$this->settings_api->set_fields( $this->get_settings_fields() );

		//initialize settings
		$this->settings_api->admin_init();
	}

	/**
	 * Returns the settings sections for the plugin settings page.
	 *
	 * @since 	 2.0.0
	 * @access   public
	 * @return 	 array An array of settings sections, where each section is an array
	 *               with 'id' and 'title' keys.
	 */
	public function get_settings_sections()
	{
		$sections = array(
			array(
				'id'    => 'sasstocss_basic_settings',
				'title' => __( 'General Settings', 'sass-to-css-compiler' )
			)
		);
		
		return $sections;
	}

	/**
	 * Returns all the settings fields for the plugin settings page.
	 *
	 * @since 	 2.0.0
	 * @access   public
	 * @return 	 array An array of settings fields, organized by section ID.  Each
	 *               section ID is a key in the array, and the value is an array
	 *               of settings fields for that section. Each settings field is
	 *               an array with 'name', 'label', 'type', 'desc', and other keys
	 *               depending on the field type.
	 */
	public function get_settings_fields()
	{
		// get compiling mode
		$mode 				= Sass_To_Css_Compiler::get_option( 'mode', 'sasstocss_basic_settings' );

		// if mode is set
		if ( $mode )
		{
			$preview_img 	= intval( $mode );
			
			$preview_src 	= SASS_TO_CSS_COMPILER_PLUGIN_URL . "admin/images/$preview_img.png";
			// if not mode set default to Expanded
		}
		else
		{
			$preview_src 	= SASS_TO_CSS_COMPILER_PLUGIN_URL . "admin/images/1.png";
		}

		$settings_fields 	= array(
			'sasstocss_basic_settings' => array(
				array(
					'name'    => 'enable',
					'label'   => __( 'Enable Compiler', 'sass-to-css-compiler' ),
					'type'    => 'checkbox',
					'desc'    => __( 'Checking this box will enable compiling .scss files from themes & plugins folders', 'sass-to-css-compiler' )
				),
				array(
					'name'    => 'include',
					'label'   => __( 'Files To Compile', 'sass-to-css-compiler' ),
					'type'    => 'text',
					'desc'    => __( 'Add comma separated scss files name to include it while Compiling... Note if added any! only those files will be compiled', 'sass-to-css-compiler' ),
					'placeholder' => __( 'menu.scss, footer.scss', 'sass-to-css-compiler' )
				),
				array(
					'name'    => 'mode',
					'label'   => __( 'Compiling Mode', 'sass-to-css-compiler' ),
					'type'    => 'select',
					'options' => array(
						'0' => 'Expanded (Default : Recommended)',
						'1' => 'Nested',
						'2' => 'Compact (Each Rule in a New Line)',
						'3' => 'Compressed (Minified but Comments Are kept )',
						'4' => 'Crunched (Super Minified)'
					),
					'default' => '0',
					'desc'    => __( 'See below Screenshots To Understand Formatting Output <img class="formatting_preview" style="display: block;margin-top: 10px;" src="'. $preview_src .'">', 'sass-to-css-compiler' ),
				)
			)
		);

		return $settings_fields;
	}

	/**
	 * Show plugin notices in the admin area.
	 *
	 * @since 	 2.0.0
	 * @access   public
	 */
	public function admin_notices()
	{
		// check if upload directory is writable...
		if ( ! Sass_To_Css_Compiler::is_upload_dir_writable() )
		{
			// deactivate the plugin
			deactivate_plugins( SASS_TO_CSS_COMPILER_PLUGIN_BASENAME );

			// unset activation notice
			unset( $_GET[ 'activate' ] );

			wp_admin_notice( __( 'Upload Directory is not writable! Please make it writable to store cache files.', 'sass-to-css-compiler' ), [ 'type' => 'error' ] );
		}

		// check if requested for clearing cache...
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'purge_sass_to_css_compiled_files' && isset( $_GET['_wpnonce'] ) )
		{
		    // Verify the nonce
		    if ( wp_verify_nonce( $_GET['_wpnonce'], 'sass_to_css_compiler_action' ) )
		    {
				Sass_To_Css_Compiler::purge();

				wp_admin_notice( __( 'Compiled Files Successfully Purged! New Cache Files Will Be Generated Soon if Enabled!', 'sass-to-css-compiler' ), [ 'type' => 'success' ] );
		    }
		    else
		    {
		    	wp_admin_notice( __( 'Nonce verification failed.', 'sass-to-css-compiler' ), [ 'type' => 'error' ] );
		    }
		}
	}

	/**
	 * Add a admin node menu item for clearing the cache
	 *
	 * @since 	 2.0.0
	 * @access   public
	 * @param    array $wp_admin_bar class WP_Admin_Bar object.
	 */
	public function admin_bar_menu( $wp_admin_bar )
	{
		// check if current page is plugin settings page
		// check if the user is logged in as well as admin bar is not disabled
		if ( function_exists( 'get_current_screen' ) && current_user_can( 'manage_options' ) && is_admin_bar_showing() && get_current_screen()->id == 'toplevel_page_sass-to-css-compiler' )
		{
			$link 		= admin_url( 'admin.php?page=sass-to-css-compiler' );

			// Generate a nonce
			$nonce 		= wp_create_nonce( 'sass_to_css_compiler_action' );

			// Add the nonce to the URL
			$link 		= add_query_arg( array( '_wpnonce' => $nonce, 'action' => 'purge_sass_to_css_compiled_files' ), $link );
			
			$args 		= array(
				'id'    => 'sass-to-css-compiler-purge-cache',
				'title' => sprintf( '<a href="%s">%s</a>', esc_url( $link ), __( 'Purge Compiled SASS Cache', 'sass-to-css-compiler' ) )
			);
			
			$wp_admin_bar->add_node( $args );
		}
	}
}
