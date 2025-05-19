<?php
/**
 * This file contains the definition of the Sass_To_Css_Compiler_Admin class, which
 * is used to load the plugin's admin-specific functionality.
 *
 * @package       Sass_To_Css_Compiler
 * @subpackage    Sass_To_Css_Compiler/admin
 * @author        Sajjad Hossain Sagor <sagorh672@gmail.com>
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version and other methods.
 *
 * @since    2.0.0
 */
class Sass_To_Css_Compiler_Admin {
	/**
	 * The ID of this plugin.
	 *
	 * @since     2.0.0
	 * @access    private
	 * @var       string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since     2.0.0
	 * @access    private
	 * @var       string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * The plugin options api wrapper object.
	 *
	 * @since     2.0.0
	 * @access    private
	 * @var       array $settings_api Holds the plugin options api wrapper class object.
	 */
	private $settings_api;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @param     string $plugin_name The name of this plugin.
	 * @param     string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name  = $plugin_name;
		$this->version      = $version;
		$this->settings_api = new Sajjad_Dev_Settings_API();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function enqueue_styles() {
		$current_screen = get_current_screen();

		// check if current page is plugin settings page.
		if ( 'toplevel_page_sass-to-css-compiler' === $current_screen->id ) {
			wp_enqueue_style( $this->plugin_name, SASS_TO_CSS_COMPILER_PLUGIN_URL . 'admin/css/admin.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function enqueue_scripts() {
		$current_screen = get_current_screen();

		// check if current page is plugin settings page.
		if ( 'toplevel_page_sass-to-css-compiler' === $current_screen->id ) {
			wp_enqueue_script( $this->plugin_name, SASS_TO_CSS_COMPILER_PLUGIN_URL . 'admin/js/admin.js', array( 'jquery' ), $this->version, false );

			wp_localize_script(
				$this->plugin_name,
				'SassToCssCompiler',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
				)
			);
		}
	}

	/**
	 * Adds a settings link to the plugin's action links on the plugin list table.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @param     array $links The existing array of plugin action links.
	 * @return    array $links The updated array of plugin action links, including the settings link.
	 */
	public function add_plugin_action_links( $links ) {
		$links[] = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'admin.php?page=sass-to-css-compiler' ) ), __( 'Settings', 'sass-to-css-compiler' ) );

		return $links;
	}

	/**
	 * Adds the plugin settings page to the WordPress dashboard menu.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function admin_menu() {
		add_menu_page(
			__( 'Sass To CSS Compiler', 'sass-to-css-compiler' ),
			__( 'Sass To CSS Compiler', 'sass-to-css-compiler' ),
			'manage_options',
			'sass-to-css-compiler',
			array( $this, 'menu_page' ),
			'dashicons-admin-tools'
		);
	}

	/**
	 * Renders the plugin menu page content.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function menu_page() {
		$this->settings_api->show_forms();
	}

	/**
	 * Initializes admin-specific functionality.
	 *
	 * This function is hooked to the 'admin_init' action and is used to perform
	 * various administrative tasks, such as registering settings, enqueuing scripts,
	 * or adding admin notices.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function admin_init() {
		// set the settings.
		$this->settings_api->set_sections( $this->get_settings_sections() );

		$this->settings_api->set_fields( $this->get_settings_fields() );

		// initialize settings.
		$this->settings_api->admin_init();
	}

	/**
	 * Returns the settings sections for the plugin settings page.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @return    array An array of settings sections, where each section is an array
	 *                  with 'id' and 'title' keys.
	 */
	public function get_settings_sections() {
		$settings_sections = array(
			array(
				'id'    => 'sasstocss_basic_settings',
				'title' => __( 'General Settings', 'sass-to-css-compiler' ),
			),
		);

		/**
		 * Filters the plugin settings sections.
		 *
		 * This filter allows you to modify the plugin settings sections.
		 * You can use this filter to add/remove/edit any settings sections.
		 *
		 * @since     2.0.2
		 * @param     array $settings_sections Default settings sections.
		 * @return    array $settings_sections Modified settings sections.
		 */
		return apply_filters( 'sasstocss_settings_sections', $settings_sections );
	}

	/**
	 * Returns all the settings fields for the plugin settings page.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @return    array An array of settings fields, organized by section ID.  Each
	 *                  section ID is a key in the array, and the value is an array
	 *                  of settings fields for that section. Each settings field is
	 *                  an array with 'name', 'label', 'type', 'desc', and other keys
	 *                  depending on the field type.
	 */
	public function get_settings_fields() {
		// get compiling mode.
		$mode        = Sass_To_Css_Compiler::get_option( 'mode', 'sasstocss_basic_settings', 0 );
		$preview_src = SASS_TO_CSS_COMPILER_PLUGIN_URL . 'admin/images/0.png'; // if not mode set default to Expanded.

		// if mode is set.
		if ( $mode ) {
			$preview_img = intval( $mode );
			$preview_src = SASS_TO_CSS_COMPILER_PLUGIN_URL . "admin/images/$preview_img.png";
		}

		$settings_fields = array(
			'sasstocss_basic_settings' => array(
				array(
					'name'  => 'enable',
					'label' => __( 'Enable Compiler', 'sass-to-css-compiler' ),
					'type'  => 'checkbox',
					'desc'  => __( 'Checking this box will enable compiling .scss files from themes & plugins folders', 'sass-to-css-compiler' ),
				),
				array(
					'name'        => 'include',
					'label'       => __( 'Files To Compile', 'sass-to-css-compiler' ),
					'type'        => 'text',
					'desc'        => __( 'Add comma separated scss files name to include it while Compiling... Note if added any! only those files will be compiled', 'sass-to-css-compiler' ),
					'placeholder' => __( 'menu.scss, footer.scss', 'sass-to-css-compiler' ),
				),
				array(
					'name'    => 'mode',
					'label'   => __( 'Compiling Mode', 'sass-to-css-compiler' ),
					'type'    => 'select',
					'options' => array(
						'0' => 'Expanded (Default : Recommended)',
						'1' => 'Compact (Each Rule in a New Line)',
						'2' => 'Compressed (Minified but Comments Are kept )',
						'3' => 'Crunched (Super Minified)',
					),
					'default' => '0',
					'desc'    => sprintf( '%s %s', __( 'See below Screenshots To Understand Formatting Output', 'sass-to-css-compiler' ), '<img class="formatting_preview" src="' . $preview_src . '">' ),
				),
			),
		);

		/**
		 * Filters the plugin settings fields.
		 *
		 * This filter allows you to modify the plugin settings fields.
		 * You can use this filter to add/remove/edit any settings field.
		 *
		 * @since     2.0.2
		 * @param     array $settings_fields Default settings fields.
		 * @return    array $settings_fields Modified settings fields.
		 */
		return apply_filters( 'sasstocss_settings_fields', $settings_fields );
	}

	/**
	 * Displays admin notices in the admin area.
	 *
	 * This function checks if the upload folder is writable.
	 * If not, it displays a warning notice and deactivates the current plugin.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function admin_notices() {
		// check if upload directory is writable...
		if ( ! Sass_To_Css_Compiler::is_upload_dir_writable() ) {
			// deactivate the plugin.
			deactivate_plugins( SASS_TO_CSS_COMPILER_PLUGIN_BASENAME );

			// unset activation notice.
			unset( $_GET['activate'] );

			wp_admin_notice(
				__( 'Upload Directory is not writable! Please make it writable to store cache files.', 'sass-to-css-compiler' ),
				array(
					'type'        => 'error',
					'dismissible' => true,
				),
			);
		}

		// check if purge was success, then show success notice.
		if ( isset( $_GET['purge_success'] ) ) {
			wp_admin_notice(
				__( 'Compiled Files Successfully Purged! New Cache Files Will Be Generated Soon On The Fly!', 'sass-to-css-compiler' ),
				array(
					'type'        => 'success',
					'dismissible' => true,
				),
			);
		}

		// check if requested for clearing cache...
		if ( isset( $_GET['action'] ) && 'purge_sass_to_css_compiled_files' === $_GET['action'] && isset( $_GET['_wpnonce'] ) ) {
			// Verify the nonce.
			if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'sass_to_css_compiler_action' ) ) {
				Sass_To_Css_Compiler::purge();

				// Get plugin page url.
				$link = admin_url( 'admin.php' );

				// Add the nonce to the URL.
				$link = add_query_arg(
					array(
						'page'          => 'sass-to-css-compiler',
						'purge_success' => 1,
					),
					$link
				);

				wp_safe_redirect( $link );
				exit;
			} else {
				wp_admin_notice(
					__( 'Nonce verification failed.', 'sass-to-css-compiler' ),
					array(
						'type'        => 'error',
						'dismissible' => true,
					),
				);
			}
		}
	}

	/**
	 * Add a admin node menu item for clearing the cache
	 *
	 * @since     2.0.0
	 * @access    public
	 * @param     array $wp_admin_bar class WP_Admin_Bar object.
	 */
	public function admin_bar_menu( $wp_admin_bar ) {
		// check if current page is plugin settings page & the user is logged in as well as admin bar is not disabled.
		if ( function_exists( 'get_current_screen' ) && current_user_can( 'manage_options' ) && is_admin_bar_showing() && 'toplevel_page_sass-to-css-compiler' === get_current_screen()->id ) {
			// Get plugin page url.
			$link = admin_url( 'admin.php' );

			// Generate a nonce.
			$nonce = wp_create_nonce( 'sass_to_css_compiler_action' );

			// Add the nonce to the URL.
			$link = add_query_arg(
				array(
					'page'     => 'sass-to-css-compiler',
					'_wpnonce' => $nonce,
					'action'   => 'purge_sass_to_css_compiled_files',
				),
				$link
			);

			$args = array(
				'id'    => 'sass-to-css-compiler-purge-cache',
				'title' => sprintf( '<a href="%s">%s</a>', esc_url( $link ), __( 'Purge Compiled SASS Cache', 'sass-to-css-compiler' ) ),
			);

			$wp_admin_bar->add_node( $args );
		}
	}
}
