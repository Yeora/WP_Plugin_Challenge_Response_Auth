<?php

defined( 'ABSPATH' ) or die( 'No direct access' );

class Mb_Password_Hasher {

	private const CLIENT_HASH_ALGORITHM = '$2a$';
	private const SERVER_HASH_ALGORITHM = '2y';
	private const SERVER_HASH_ALGORITHM_OPTIONS = [];
	private wpdb $wpdb;

	public function __construct( \wpdb $wpdb ) {
		$this->wpdb = $wpdb;
	}

	public static function set_error_message( string $message ): void {
		add_action( 'admin_notices', static function () use ( $message ) {
			echo "<div class='notice notice-error'><p>$message</p></div>";
		}
		);
	}

	/**
	 * @param string $password
	 * @param string $hash
	 * @param int $user_id
	 *
	 * @return bool
	 */
	public function check_password( string $password, string $hash, int $user_id ): bool {
		$info = password_get_info( $hash );

		$user_hash_algorithm = ( strpos( $password, self::CLIENT_HASH_ALGORITHM ) === 0 );

		$option                   = get_option( 'mb_challenge_response_options' );
		$force_challenge_response = $option['mb_challenge_response_field_force_cr'] ?? true;

		if ( ( ! empty( $info['algo'] ) && $user_hash_algorithm ) || $force_challenge_response ) {
			return $this->check_password_native( $password, $user_id );
		}

		// Fallback to PHPass
		return $this->check_password_PHPass( $password, $hash );
	}

	/**
	 * @param string $password
	 *
	 * @return false|string|null
	 */
	public function get_hash( string $password ) {
		return password_hash( $password, self::SERVER_HASH_ALGORITHM, self::SERVER_HASH_ALGORITHM_OPTIONS );
	}

	/**
	 * @param string $password
	 * @param int $user_id
	 *
	 * @return string
	 */
	public function update_hash( string $password, int $user_id ): string {
		$hash   = $this->get_hash( $password );
		$fields = [ 'user_pass' => $hash, 'user_activation_key' => '' ];
		$this->wpdb->update( $this->wpdb->users, $fields, [ 'ID' => $user_id ] );
		wp_cache_delete( $user_id, 'users' );

		return $hash;
	}

	/**
	 * @param string $password
	 * @param int $user_id
	 *
	 * @return bool
	 */
	private function check_password_native( string $password, int $user_id ): bool {
		$user = get_user_by( 'id', $user_id );
		if ( ! $user ) {
			return false;
		}

		$challenge = get_user_meta( $user_id, 'challenge-response-challenge', true );

		if ( ! $challenge ) {
			return false;
		}

		return password_verify( $user->user_pass . $challenge, $password );
	}

	/**
	 * @param string $password
	 * @param string $hash
	 *
	 * @return bool
	 */
	private function check_password_PHPass( string $password, string $hash ): bool {
		global $wp_hasher;

		if ( empty( $wp_hasher ) ) {
			if ( ! class_exists( 'PasswordHash' ) ) {
				require_once ABSPATH . '/wp-includes/class-phpass.php';
			}
			$wp_hasher = new PasswordHash( 8, true );
		}

		return $wp_hasher->CheckPassword( $password, $hash );
	}
}
