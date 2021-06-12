<?php

/**
 * @since             1.0.0
 * @package           Mb_Challenge_Response_Authentication
 *
 * @wordpress-plugin
 * Plugin Name:       MB Challenge response authentication
 * Plugin URI:        mb-challenge-response-authentication
 * Description:       This plugin implements challenge respsonse authentication. In addition, the WordPress hasher is replaced by native PHP libraries.
 * Version:           1.0.0
 * Author:            Yeora
 * Author URI:        https://github.com/Yeora/WP_Plugin_Challenge_Response_Auth
 * License:           GPL-3.0+
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       mb-challenge-response-authentication
 * Domain Path:       /languages
 */

use MbChallengeResponseAuthentication\Mb_Challenge_Response_Authentication;
use MbChallengeResponseAuthentication\Mb_Challenge_Response_Authentication_Activator;
use MbChallengeResponseAuthentication\Mb_Challenge_Response_Authentication_Deactivator;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version. In SemVer format https://semver.org
 */
define( 'MB_CHALLENGE_RESPONSE_AUTHENTICATION_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mb-challenge-response-authentication-activator.php
 */
function activate_mb_challenge_response_authentication() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mb-challenge-response-authentication-activator.php';
	Mb_Challenge_Response_Authentication_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mb-challenge-response-authentication-deactivator.php
 */
function deactivate_mb_challenge_response_authentication() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mb-challenge-response-authentication-deactivator.php';
	Mb_Challenge_Response_Authentication_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_mb_challenge_response_authentication' );
register_deactivation_hook( __FILE__, 'deactivate_mb_challenge_response_authentication' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-mb-challenge-response-authentication.php';

/**
 * Begins execution of the plugin.
 * @since    1.0.0
 */
function run_mb_challenge_response_authentication() {
	$plugin = new Mb_Challenge_Response_Authentication();
	$plugin->run();
}

run_mb_challenge_response_authentication();