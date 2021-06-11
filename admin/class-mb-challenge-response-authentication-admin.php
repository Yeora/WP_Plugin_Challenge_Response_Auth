<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       -
 * @since      1.0.0
 *
 * @package    Mb_Challenge_Response_Authentication
 * @subpackage Mb_Challenge_Response_Authentication/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Mb_Challenge_Response_Authentication
 * @subpackage Mb_Challenge_Response_Authentication/admin
 * @author     Yeora
 */
class Mb_Challenge_Response_Authentication_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
	}

	public function add_menu(): void {

		$title = __( 'Challenge Response Authentifizierung', 'mb-challenge-response-authentication' );

		add_submenu_page(
			'options-general.php',
			$title,
			$title,
			'manage_options',
			'challenge_response_auth',
			[ $this, 'challenge_response_options_page_html' ]
		);
	}

	public function challenge_response_options_page_html(): void {
		require_once( plugin_dir_path( __FILE__ ) . 'partials/mb-challenge-response-options-page.php' );
	}

	public function sanitize_bool( $value ) {
		$value['mb_challenge_response_field_force_cr'] = rest_sanitize_boolean( $value['mb_challenge_response_field_force_cr'] );

		return $value;
	}

	public function challenge_response_settings_init(): void {

		register_setting( 'mb_challenge_response', 'mb_challenge_response_options',
			[
				'type'              => 'boolean',
				'sanitize_callback' => [ $this, 'sanitize_bool' ]
			]
		);

		add_settings_section(
			'mb_challenge_response_section_developers',
			'',
			'',
			'mb_challenge_response'
		);

		add_settings_field(
			'mb_challenge_response_field_force_cr',
			__( 'CR Authentifizierung erzwingen', 'mb-challenge-response-authentication' ),
			[ $this, 'challenge_response_field_cb' ],
			'mb_challenge_response',
			'mb_challenge_response_section_developers',
			array(
				'label_for'                         => 'mb_challenge_response_field_force_cr',
				'mb_challenge_response_custom_data' => 'custom',
			)
		);
	}

	public function challenge_response_field_cb( $args ) {
		require_once( plugin_dir_path( __FILE__ ) . 'partials/mb-challenge-response-field-cb.php' );
	}

}
