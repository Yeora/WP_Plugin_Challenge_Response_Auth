<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       -
 * @since      1.0.0
 *
 * @package    Mb_Challenge_Response_Authentication
 * @subpackage Mb_Challenge_Response_Authentication/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Mb_Challenge_Response_Authentication
 * @subpackage Mb_Challenge_Response_Authentication/includes
 * @author     Yeora
 */
class Mb_Challenge_Response_Authentication {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Mb_Challenge_Response_Authentication_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'MB_CHALLENGE_RESPONSE_AUTHENTICATION_VERSION' ) ) {
			$this->version = MB_CHALLENGE_RESPONSE_AUTHENTICATION_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'mb-challenge-response-authentication';

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
	 * - Mb_Challenge_Response_Authentication_Loader. Orchestrates the hooks of the plugin.
	 * - Mb_Challenge_Response_Authentication_i18n. Defines internationalization functionality.
	 * - Mb_Challenge_Response_Authentication_Admin. Defines all hooks for the admin area.
	 * - Mb_Challenge_Response_Authentication_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies(): void {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-mb-challenge-response-authentication-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-mb-challenge-response-authentication-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-mb-challenge-response-authentication-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'public/class-mb-challenge-response-authentication-public.php';

		/**
		 * The class responsible for the Rest Endpoint for the challenge response authentication
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/custom/class-mb-rest-endpoint.php';

		/**
		 * The class responsible for the new Password Hasher
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/custom/class-mb-password-hasher.php';

		/**
		 * Overwrites the WordPress Password Hasher
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/custom/mb-password-hasher.php';
		/**
		 * The class responsible for the Login Helper
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/custom/class-mb-login-helper.php';

		$this->loader = new Mb_Challenge_Response_Authentication_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Mb_Challenge_Response_Authentication_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale(): void {

		$plugin_i18n = new Mb_Challenge_Response_Authentication_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks(): void {
		$plugin_admin = new Mb_Challenge_Response_Authentication_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );


		$this->loader->add_action( 'admin_init', $plugin_admin, 'challenge_response_settings_init' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks(): void {
		$plugin_public = new Mb_Challenge_Response_Authentication_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$rest_endpoint = new Mb_Rest_Endpoint();
		$this->loader->add_action( 'rest_api_init', $rest_endpoint, 'mb_register_rest_endpoint_route' );

		$mb_login_helper = new Mb_Login_Helper();
		$this->loader->add_action( 'login_enqueue_scripts', $mb_login_helper, 'login_script' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run(): void {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name(): string {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Mb_Challenge_Response_Authentication_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version(): string {
		return $this->version;
	}

}
