<?php

use ScssPhp\ScssPhp\Compiler;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      2.0.0
 * @package    Sass_To_Css_Compiler
 * @subpackage Sass_To_Css_Compiler/includes
 * @author     Sajjad Hossain Sagor <sagorh672@gmail.com>
 */
class Sass_To_Css_Compiler
{
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      Sass_To_Css_Compiler_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since 	2.0.0
	 * @access  public
	 */
	public function __construct()
	{
		if ( defined( 'SASS_TO_CSS_COMPILER_VERSION' ) )
		{
			$this->version = SASS_TO_CSS_COMPILER_VERSION;
		}
		else
		{
			$this->version = '2.0.0';
		}
		
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
	 * - Sass_To_Css_Compiler_i18n. Defines internationalization functionality.
	 * - Sass_To_Css_Compiler_Admin. Defines all hooks for the admin area.
	 * - Sass_To_Css_Compiler_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once SASS_TO_CSS_COMPILER_PLUGIN_PATH . 'includes/class-plugin-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once SASS_TO_CSS_COMPILER_PLUGIN_PATH . 'includes/class-plugin-i18n.php';

		/**
		 * The class responsible for defining options api wrapper
		 */
		require_once SASS_TO_CSS_COMPILER_PLUGIN_PATH . 'includes/class-plugin-settings-api.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once SASS_TO_CSS_COMPILER_PLUGIN_PATH . 'admin/class-plugin-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once SASS_TO_CSS_COMPILER_PLUGIN_PATH . 'public/class-plugin-public.php';

		$this->loader = new Sass_To_Css_Compiler_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Sass_To_Css_Compiler_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function set_locale()
	{
		$plugin_i18n = new Sass_To_Css_Compiler_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{
		$plugin_admin = new Sass_To_Css_Compiler_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'plugin_action_links_' . SASS_TO_CSS_COMPILER_PLUGIN_BASENAME, $plugin_admin, 'add_plugin_action_links' );
		
		$this->loader->add_action( 'admin_init', $plugin_admin, 'admin_init' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menu' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'admin_notices' );
		$this->loader->add_action( 'admin_bar_menu', $plugin_admin, 'admin_bar_menu', 99 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{
		$plugin_public = new Sass_To_Css_Compiler_Public( $this->get_plugin_name(), $this->get_version() );
		
		$this->loader->add_filter( 'style_loader_src', $plugin_public, 'style_loader_src', 101, 2 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since 	2.0.0
	 * @access  public
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since 	2.0.0
	 * @access  public
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since 	2.0.0
	 * @access  public
	 * @return    Sass_To_Css_Compiler_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since 	2.0.0
	 * @access  public
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}

	/**
	 * Compile the provided source code
	 * 
	 * @since 	2.0.0
	 * @access  public
	 * @return 	string source code
	 */
	public static function compile( $source_code = '', $import_path )
	{
		// add the compiling library [https://github.com/scssphp/scssphp/]
		require SASS_TO_CSS_COMPILER_PLUGIN_PATH . 'vendor/autoload.php';

		if ( $source_code == '' ) return '';

		$formatter 			= array(
			"ScssPhp\ScssPhp\Formatter\Expanded",
			"ScssPhp\ScssPhp\Formatter\Nested",
			"ScssPhp\ScssPhp\Formatter\Compact",
			"ScssPhp\ScssPhp\Formatter\Compressed",
			"ScssPhp\ScssPhp\Formatter\Crunched"
		);

		$setFormatter 		= "ScssPhp\ScssPhp\Formatter\Expanded";

		$compiling_mode 	= self::get_option( 'mode', 'sasstocss_basic_settings', '1' );

		if ( isset( $formatter[$compiling_mode] ) )
		{    
			$setFormatter 	= $formatter[$compiling_mode];
		}

		$compiler 			= new Compiler;
		
		$compiler->setFormatter( $setFormatter );
		
		$compiler->setImportPaths( $import_path );

		try
		{
			// Compile the SCSS to CSS
			$compiled_css 	= $compiler->compile( $source_code );

			return $compiled_css;
		}
		catch ( Exception $e )
		{
			error_log( $e->getMessage() );

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
	 * @since   2.0.0
	 * @access  public
	 * @param 	string $code     	The compiled CSS code to be saved.
	 * @param 	string $filename 	The name of the file to save the CSS code to.
	 * @return 	bool|void 			Returns true if the save was successful, or void if the upload directory is not writable.
	 */
	public static function save( $code, $filename )
	{
		if ( ! self::is_upload_dir_writable() ) return;
		
		file_put_contents( $filename, $code );
	}

	/**
	 * Purges all cache files from the cache directory.
	 *
	 * This function deletes all files within the cache directory, effectively
	 * purging the cache. It first checks if the upload directory is writable
	 * before attempting to delete any files.
	 *
	 * @since   2.0.0
	 * @access  public
	 * @return 	bool|void Returns true if the purge was successful, or void if the upload directory is not writable.
	 */
	public static function purge()
	{
		if ( ! self::is_upload_dir_writable() ) return;
		
		// get cache folder
		$cache_folder 	= self::get_cache_dir();

		self::delete_folders_recursively( $cache_folder );
	}

	/**
	 * Deletes a folder and all its contents recursively.
	 *
	 * This function deletes a specified folder and all files and subfolders within it.
	 * It recursively traverses subfolders to ensure all content is removed.
	 *
	 * @since    2.0.0
	 * @access   public
	 * @param    string $folder The path to the folder to be deleted.
	 * @return   bool   True if the folder and its contents were successfully deleted, false otherwise.
	 */
	public static function delete_folders_recursively( $folder )
	{
		// Check if the folder exists.
		if ( ! is_dir( $folder ) )
		{
			return false; // Folder doesn't exist
		}

		// Get a list of files and subfolders within the folder, excluding '.' and '..'.
		$files 		= array_diff( scandir( $folder ), array( '.', '..' ) );

		// Iterate through each file or subfolder.
		foreach ( $files as $file )
		{
			$path 	= $folder . DIRECTORY_SEPARATOR . $file;

			// If it's a subfolder, recursively delete it.
			if ( is_dir( $path ) )
			{
				self::delete_folders_recursively( $path ); // Recursively delete subfolder
				
				rmdir( $path ); // Remove empty folder
			}
			else
			{
				unlink( $path ); // If it's a file, delete it.
			}
		}

		return true;
	}

	/**
	 * Creates the cache directory if it does not exist.
	 *
	 * This function checks if the cache directory, as determined by
	 * `self::get_cache_dir()`, exists. If it does not, it creates the directory
	 * with permissions 0700 (owner read, write, execute).
	 *
	 * @since   2.0.0
	 * @access  public
	 * @return  void
	 */
	public static function create_cache_dir()
	{
		$cache_dir = self::get_cache_dir();
		
		// create cache dir if not already there
		if ( ! is_dir( $cache_dir ) )
		{
			mkdir( $cache_dir, 0700 );
		}
	}

	/**
	 * Checks if the WordPress upload directory is writable.
	 *
	 * This function determines if the WordPress upload directory is writable by
	 * checking the directory returned by `self::get_cache_dir(true)`.
	 *
	 * @since   2.0.0
	 * @access  public
	 * @return  bool True if the upload directory is writable, false otherwise.
	 */
	public static function is_upload_dir_writable()
	{
		return is_writable( self::get_cache_dir( true ) );
	}

	/**
	 * Gets the compiled file storage cache directory.
	 *
	 * This function retrieves the cache directory path for storing compiled files.
	 * It uses WordPress's `wp_upload_dir()` function to determine the base upload
	 * directory and appends '/scss_cache' to it, unless `$base_dir_only` is set to true.
	 *
	 * @since   2.0.0
	 * @access  public
	 * @param 	bool   $base_dir_only 	Optional. If true, returns only the base upload directory.
	 * @param 	string $dir            	Optional. The directory within the uploads array to use. Defaults to 'basedir'.
	 * @return 	string 					The cache directory path.
	 */
	public static function get_cache_dir( $base_dir_only = false, $dir = 'basedir' )
	{
		$upload 	= wp_upload_dir();
	
		$upload_dir = $upload[$dir];

		if ( $base_dir_only )
		{
			return $upload_dir;
		}

		return $upload_dir . '/scss_cache';
	}

	/**
	 * Retrieves an option value from the WordPress options table.
	 *
	 * This function retrieves a specific option value from a given section within the WordPress options table.
	 * If the option is not found, it returns a default value.
	 *
	 * @since    2.0.0
	 * @access   public
	 * @param    string $option  			The name of the option to retrieve.
	 * @param    string $section 			The name of the option section (the key under which options are stored in the database).
	 * @param    mixed  $default Optional. 	The default value to return if the option is not found. Defaults to an empty string.
	 * @return   mixed 						The option value, or the default value if the option is not set.
	 */
	public static function get_option( $option, $section, $default = '' )
	{
		// Retrieve the options array for the given section.
		$options = get_option( $section );

		// Check if the option exists within the retrieved options array.
		if ( isset( $options[$option] ) )
		{
			// Return the option value.
			return $options[$option];
		}

		// If the option is not found, return the default value.
		return $default;
	}
}
