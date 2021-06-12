<?php

use MbChallengeResponseAuthentication\Mb_Password_Hasher;

defined( 'ABSPATH' ) or die( 'No direct access' );

if ( ! class_exists( 'Mb_Challenge_Response_Authentication_Helper' ) ) {

	class Mb_Challenge_Response_Authentication_Helper {

		public static function get_mb_password_hasher(): Mb_Password_Hasher {
			static $hasher;
			global $wpdb;

			if ( ! $hasher ) {
				$hasher = new Mb_Password_Hasher( $wpdb );
			}

			return $hasher;
		}
	}

} else {
	$message = 'Error in Plugin Mb_Challenge_Response_Authentication! Please uninstall.';
	add_action( 'admin_notices', static function () use ( $message ) {
		echo "<div class='notice notice-error'><p>$message</p></div>";
	} );

	return;
}

if ( function_exists( 'wp_hash_password' ) ) {

	$hasher = Mb_Challenge_Response_Authentication_Helper::get_mb_password_hasher();

	$message = __( 'Plugin Fehler! Die wp_hash_password() Funktion wurde bereits überschrieben!',
		'mb-challenge-response-authentication' );

	$hasher::set_error_message( $message );
} elseif ( ! function_exists( 'password_hash' ) ) {
	$hasher = Mb_Challenge_Response_Authentication_Helper::get_mb_password_hasher();

	$message = __( 'Plugin Fehler! Die password_hash() Funktion wurde nicht gefunden!',
		'mb-challenge-response-authentication' );

	$hasher::set_error_message( $message );
}

if ( ! function_exists( 'wp_hash_password' ) && function_exists( 'password_hash' ) ) :

	function wp_check_password( string $password, string $hash, string $user_id ): bool {
		$hasher = Mb_Challenge_Response_Authentication_Helper::get_mb_password_hasher();

		return $hasher->check_password( $password, $hash, (int) $user_id );
	}

	function wp_hash_password( string $password ): string {
		$hasher = Mb_Challenge_Response_Authentication_Helper::get_mb_password_hasher();

		return $hasher->get_hash( $password );
	}

	function wp_set_password( string $password, string $user_id ): string {

		$hasher = Mb_Challenge_Response_Authentication_Helper::get_mb_password_hasher();

		return $hasher->update_hash( $password, (int) $user_id );
	}
endif;