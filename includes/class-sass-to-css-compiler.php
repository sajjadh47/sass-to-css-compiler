<?php
/**
 * This file contains the definition of the Sass_To_Css_Compiler class, which
 * is used to begin the plugin's functionality.
 *
 * @package       Sass_To_Css_Compiler
 * @subpackage    Sass_To_Css_Compiler/includes
 * @author        Sajjad Hossain Sagor <sagorh672@gmail.com>
 */

use ScssPhp\ScssPhp\Compiler;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since    2.0.0
 */
class Sass_To_Css_Compiler {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since     2.0.0
	 * @access    protected
	 * @var       Sass_To_Css_Compiler_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since     2.0.0
	 * @access    protected
	 * @var       string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since     2.0.0
	 * @access    protected
	 * @var       string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function __construct() {
		$this->version     = defined( 'SASS_TO_CSS_COMPILER_PLUGIN_VERSION' ) ? SASS_TO_CSS_COMPILER_PLUGIN_VERSION : '1.0.0';
		$this->plugin_name = 'sass-to-css-compiler';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Sass_To_Css_Compiler_Loader. Orchestrates the hooks of the plugin.
	 * - Sass_To_Css_Compiler_i18n.   Defines internationalization functionality.
	 * - Sajjad_Dev_Settings_API.     Provides an interface for interacting with the WordPress Settings API.
	 * - Sass_To_Css_Compiler_Admin.  Defines all hooks for the admin area.
	 * - Sass_To_Css_Compiler_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since     2.0.0
	 * @access    private
	 */
	private function load_dependencies() {
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once SASS_TO_CSS_COMPILER_PLUGIN_PATH . 'includes/class-sass-to-css-compiler-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once SASS_TO_CSS_COMPILER_PLUGIN_PATH . 'includes/class-sass-to-css-compiler-i18n.php';

		/**
		 * The class responsible for defining an interface for interacting with the WordPress Settings API.
		 */
		require_once SASS_TO_CSS_COMPILER_PLUGIN_PATH . 'includes/class-sajjad-dev-settings-api.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once SASS_TO_CSS_COMPILER_PLUGIN_PATH . 'admin/class-sass-to-css-compiler-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once SASS_TO_CSS_COMPILER_PLUGIN_PATH . 'public/class-sass-to-css-compiler-public.php';

		$this->loader = new Sass_To_Css_Compiler_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Sass_To_Css_Compiler_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since     2.0.0
	 * @access    private
	 */
	private function set_locale() {
		$plugin_i18n = new Sass_To_Css_Compiler_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since     2.0.0
	 * @access    private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Sass_To_Css_Compiler_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'plugin_action_links_' . SASS_TO_CSS_COMPILER_PLUGIN_BASENAME, $plugin_admin, 'add_plugin_action_links' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'admin_init' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'admin_notices' );
		$this->loader->add_action( 'admin_bar_menu', $plugin_admin, 'admin_bar_menu', 99 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since     2.0.0
	 * @access    private
	 */
	private function define_public_hooks() {
		$plugin_public = new Sass_To_Css_Compiler_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_filter( 'style_loader_src', $plugin_public, 'style_loader_src', 101, 2 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since     2.0.0
	 * @access    public
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @return    string The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @return    Sass_To_Css_Compiler_Loader Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     2.0.0
	 * @access    public
	 * @return    string The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Compile the provided source code.
	 *
	 * @since     2.0.0
	 * @static
	 * @access    public
	 * @param     string $source_code The original sass source code.
	 * @param     string $import_path The import path of @import directive used in the sass source code.
	 * @return    string              Compiled source code
	 */
	public static function compile( $source_code, $import_path ) {
		// add the compiling library [https://github.com/scssphp/scssphp/].
		require SASS_TO_CSS_COMPILER_PLUGIN_PATH . 'vendor/autoload.php';

		if ( empty( $source_code ) ) {
			return '';
		}

		$compiling_mode = self::get_option( 'mode', 'sasstocss_basic_settings', 0 );

		$formatter = array(
			\ScssPhp\ScssPhp\OutputStyle::EXPANDED,
			\ScssPhp\ScssPhp\OutputStyle::EXPANDED,
			\ScssPhp\ScssPhp\OutputStyle::COMPRESSED,
			\ScssPhp\ScssPhp\OutputStyle::COMPRESSED,
		);

		$formatter_style = $formatter[0];

		if ( isset( $formatter[ $compiling_mode ] ) ) {
			$formatter_style = $formatter[ $compiling_mode ];
		}

		$compiler = new Compiler();

		$compiler->setOutputStyle( $formatter_style );

		$compiler->setImportPaths( $import_path );

		try {
			// Compile the SCSS to CSS.
			$css = $compiler->compileString( $source_code )->getCss();

			// if selected Compact mode.
			if ( 1 === intval( $compiling_mode ) ) {
				// Remove leading/trailing whitespace around braces, colons, semicolons.
				$css = preg_replace( '/\s*([:;])\s*/', '$1 ', $css );
				$css = preg_replace( '/\s*([{}])\s*/', ' $1 ', $css );

				// Add a newline after each closing brace.
				$css = preg_replace( '/}\s*/', "}\n\n", $css );
			}

			// if selected Super Compressed mode.
			if ( 3 === intval( $compiling_mode ) ) {
				// remove comments.
				$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
			}

			return trim( $css );
		} catch ( Exception $e ) {
			return false;
		}
	}

	/**
	 * Saves compiled CSS code to a file in the cache directory.
	 *
	 * This function writes the provided CSS code to a file with the specified
	 * filename in the cache directory. It first checks if the upload directory
	 * is writable before attempting to save the file.
	 *
	 * @since     2.0.0
	 * @static
	 * @access    public
	 * @param     string $code     The compiled CSS code to be saved.
	 * @param     string $filename The name of the file to save the CSS code to.
	 * @return    bool|void        Returns true if the save was successful, or void if the upload directory is not writable.
	 */
	public static function save( $code, $filename ) {
		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		WP_Filesystem();

		if ( ! self::is_upload_dir_writable() ) {
			return;
		}

		$wp_filesystem->put_contents( $filename, $code, FS_CHMOD_FILE );
	}

	/**
	 * Purges all cache files from the cache directory.
	 *
	 * This function deletes all files within the cache directory, effectively
	 * purging the cache. It first checks if the upload directory is writable
	 * before attempting to delete any files.
	 *
	 * @since     2.0.0
	 * @static
	 * @access    public
	 * @return    bool|void Returns true if the purge was successful, or void if the upload directory is not writable.
	 */
	public static function purge() {
		if ( ! self::is_upload_dir_writable() ) {
			return;
		}

		// get cache folder.
		$cache_folder = self::get_cache_dir();

		self::delete_folders_recursively( $cache_folder );
	}

	/**
	 * Deletes a folder and all its contents recursively.
	 *
	 * This function deletes a specified folder and all files and subfolders within it.
	 * It recursively traverses subfolders to ensure all content is removed.
	 *
	 * @since     2.0.0
	 * @static
	 * @access    public
	 * @param     string $folder The path to the folder to be deleted.
	 * @return    bool           True if the folder and its contents were successfully deleted, false otherwise.
	 */
	public static function delete_folders_recursively( $folder ) {
		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		WP_Filesystem();

		// Ensure the folder exists and is a directory.
		if ( ! $wp_filesystem->is_dir( $folder ) ) {
			return false;
		}

		// Get the list of files and folders, excluding '.' and '..'.
		$files = array_diff( $wp_filesystem->dirlist( $folder ), array( '.', '..' ) );

		foreach ( $files as $file => $file_info ) {
			$path = trailingslashit( $folder ) . $file;

			if ( 'd' === $file_info['type'] ) {
				// Recursively delete subfolder.
				self::delete_folders_recursively( $path );
			}

			// Delete file or folder.
			$wp_filesystem->delete( $path, true );
		}

		// Finally, delete the main folder.
		return $wp_filesystem->delete( $folder, true );
	}

	/**
	 * Create a directory recursively using WP_Filesystem.
	 *
	 * @since     2.0.0
	 * @static
	 * @access    public
	 * @param     string $path The directory path.
	 * @return    bool         True on success, false on failure.
	 */
	public static function create_folders_recursively( $path ) {
		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		WP_Filesystem();

		$path = wp_normalize_path( $path );

		// If the directory already exists, return true.
		if ( $wp_filesystem->is_dir( $path ) ) {
			return true;
		}

		// Get the parent directory.
		$parent_dir = dirname( $path );

		// Recursively create parent directories first.
		if ( ! $wp_filesystem->is_dir( $parent_dir ) ) {
			self::create_folders_recursively( $parent_dir );
		}

		// Create the directory.
		return $wp_filesystem->mkdir( $path, FS_CHMOD_DIR );
	}

	/**
	 * Creates the cache directory if it does not exist.
	 *
	 * This function checks if the cache directory, as determined by
	 * `self::get_cache_dir()`, exists. If it does not, it creates the directory
	 * with permissions 0700 (owner read, write, execute).
	 *
	 * @since     2.0.0
	 * @static
	 * @access    public
	 * @return    void
	 */
	public static function create_cache_dir() {
		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		WP_Filesystem();

		$cache_dir = self::get_cache_dir();

		if ( ! $cache_dir ) {
			return;
		}

		// Check if the directory already exists.
		if ( ! $wp_filesystem->is_dir( $cache_dir ) ) {
			$wp_filesystem->mkdir( $cache_dir, FS_CHMOD_DIR );
		}
	}

	/**
	 * Checks if the WordPress upload directory is writable.
	 *
	 * This function determines if the WordPress upload directory is writable by
	 * checking the directory returned by `self::get_cache_dir(true)`.
	 *
	 * @since     2.0.0
	 * @static
	 * @access    public
	 * @return    bool True if the upload directory is writable, false otherwise.
	 */
	public static function is_upload_dir_writable() {
		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		WP_Filesystem();

		$cache_dir = self::get_cache_dir( true );

		if ( ! $cache_dir ) {
			return false;
		}

		return $wp_filesystem->is_writable( $cache_dir );
	}

	/**
	 * Gets the compiled file storage cache directory.
	 *
	 * This function retrieves the cache directory path for storing compiled files.
	 * It uses WordPress's `wp_upload_dir()` function to determine the base upload
	 * directory and appends '/scss_cache' to it, unless `$base_dir_only` is set to true.
	 *
	 * @since     2.0.0
	 * @static
	 * @access    public
	 * @param     bool   $base_dir_only Optional. If true, returns only the base upload directory.
	 * @param     string $dir           Optional. The directory within the uploads array to use. Defaults to 'basedir'.
	 * @return    string $dir           The cache directory path.
	 */
	public static function get_cache_dir( $base_dir_only = false, $dir = 'basedir' ) {
		$upload     = wp_upload_dir();
		$upload_dir = $upload[ $dir ];

		if ( $base_dir_only ) {
			return $upload_dir;
		}

		return $upload_dir . '/scss_cache';
	}

	/**
	 * Retrieves the value of a specific settings field.
	 *
	 * This method fetches the value of a settings field from the WordPress options database.
	 * It retrieves the entire option group for the given section and then extracts the
	 * value for the specified field.
	 *
	 * @since     2.0.0
	 * @static
	 * @access    public
	 * @param     string $option        The name of the settings field.
	 * @param     string $section       The name of the section this field belongs to. This corresponds
	 *                                  to the option name used in `register_setting()`.
	 * @param     string $default_value Optional. The default value to return if the field's value
	 *                                  is not found in the database. Default is an empty string.
	 * @return    string|mixed          The value of the settings field, or the default value if not found.
	 */
	public static function get_option( $option, $section, $default_value = '' ) {
		$options = get_option( $section ); // Get all options for the section.

		// Check if the option exists within the section's options array.
		if ( isset( $options[ $option ] ) ) {
			return $options[ $option ]; // Return the option value.
		}

		return $default_value; // Return the default value if the option is not found.
	}
}
