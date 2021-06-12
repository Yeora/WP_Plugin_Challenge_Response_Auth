<?php

namespace MbChallengeResponseAuthentication;

use WP_REST_Request;
use WP_REST_Server;

class Mb_Rest_Endpoint {

	/**
	 * @param string $user
	 *
	 * @return string
	 */
	public function get_fake_salt( string $user ): string {
		$user_individual = substr( sha1( crypt( sha1( $user ), 'something' ) ), 0, 22 );

		return '$2y$10$' . $user_individual;
	}

	/**
	 * @param string $password
	 *
	 * @return string
	 */
	public function get_alg_cost_salt( string $password ): string {

		$info = password_get_info( $password );

		if ( ! $info ) {
			return '*'; // Only if old password hash
		}

		$cost = $info['options']['cost'];

		if ( $info['algo'] == '2y' ) {
			$alg_cost_salt_length = strlen( $cost ) + 27;

			return substr( $password, 0, $alg_cost_salt_length );
		} else {
			return '*';
		}
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return object
	 * @throws Exception
	 */
	public function mb_get_user_salt_and_challenge( WP_REST_Request $request ): object {

		$user_login = (string) $request['user'];

		$bytes     = random_bytes( 32 );
		$challenge = substr( bin2hex( $bytes ), 0, 22 );

		$user = get_user_by( 'login', $user_login );

		if ( $user ) {
			$salt = $this->get_alg_cost_salt( $user->user_pass );
			update_user_meta( $user->ID, 'challenge-response-challenge', $challenge );
		} else {
			$salt = $this->get_fake_salt( $user_login );
		}

		$result = [
			'salt'      => $salt,
			'challenge' => $challenge
		];

		return rest_ensure_response( $result );
	}

	/**
	 *  mb_register_rest_endpoint_route registers api endpoint
	 */
	public function mb_register_rest_endpoint_route(): void {
		register_rest_route( 'mb-challenge', '/get-user-salt-and-challenge/(?P<user>[\w\-_]+)', array(
			'methods'  => WP_REST_Server::READABLE,
			'callback' => [ $this, 'mb_get_user_salt_and_challenge' ],
		) );
	}

}
