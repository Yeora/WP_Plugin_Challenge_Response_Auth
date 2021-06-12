<?php
/**
 * Class Mb_Password_Hasher_Test
 *
 * @package Mb_Challenge_Response_Authentication
 */

use MbChallengeResponseAuthentication\Mb_Password_Hasher;

/**
 * Mb_Password_Hasher_Test test case.
 */
class Mb_Password_Hasher_Test extends WP_UnitTestCase {

	/**
	 * Test if the Mb_Password_Hasher exists
	 */
	public function test_mb_password_hasher_exists(): void {
		$this->assertInstanceOf( Mb_Password_Hasher::class, $this->get_mb_password_hasher() );
	}

	public function test_get_hash(): void {
		$mb_password_hasher = $this->get_mb_password_hasher();
		$hash               = $mb_password_hasher->get_hash( 'test' );

		$this->assertIsString( $hash );
		$this->assertTrue( password_verify( 'test', $hash ) );
	}

	public function test_check_password_native_forced(): void {

		[ $user, $auth, $client_hash ] = $this->get_check_password_data();

		$this->check_password_prepare( $user, $auth );

		$mb_password_hasher = $this->get_mb_password_hasher();
		$result             = $mb_password_hasher->check_password( $client_hash, $auth['server_hash'], $user['id'] );

		$this->assertTrue( $result );
	}


	public function test_check_password_native_forced_fail(): void {

		[ $user, $auth, $client_hash ] = $this->get_check_password_data();
		$client_hash = 'demo';

		$this->check_password_prepare( $user, $auth );

		$mb_password_hasher = $this->get_mb_password_hasher();
		$result             = $mb_password_hasher->check_password( $client_hash, $auth['server_hash'], $user['id'] );

		$this->assertFalse( $result );
	}

	public function test_check_password_default_not_forced(): void {

		[ $user, $auth, $client_hash ] = $this->get_check_password_data();
		$auth['force'] = false;
		$client_hash   = 'demo';

		$this->check_password_prepare( $user, $auth );

		$mb_password_hasher = $this->get_mb_password_hasher();
		$result             = $mb_password_hasher->check_password( $client_hash, $auth['server_hash'], $user['id'] );

		$this->assertTrue( $result );
	}

	public function test_check_password_no_user(): void {

		[ $user, $auth, $client_hash ] = $this->get_check_password_data();
		unset( $user['id'] );

		$this->check_password_prepare( $user, $auth );

		$user['id'] = - 1000;

		$mb_password_hasher = $this->get_mb_password_hasher();
		$result             = $mb_password_hasher->check_password( $client_hash, $auth['server_hash'], $user['id'] );

		$this->assertFalse( $result );
	}

	public function test_check_password_no_challenge(): void {

		[ $user, $auth, $client_hash ] = $this->get_check_password_data();
		unset( $auth['challenge'] );

		$this->check_password_prepare( $user, $auth );

		$mb_password_hasher = $this->get_mb_password_hasher();
		$result             = $mb_password_hasher->check_password( $client_hash, $auth['server_hash'], $user['id'] );

		$this->assertFalse( $result );
	}

	public function test_update_hash(): void {
		$mb_password_hasher = $this->get_mb_password_hasher();

		$this->create_user( 1000, 'test_user', '123' );

		$user_hash = $mb_password_hasher->update_hash( 'passwort', 1000 );

		$user = get_user_by( 'login', 'test_user' );

		$this->assertEquals( $user_hash, $user->user_pass );
		$this->assertTrue( password_verify( 'passwort', $user->user_pass ) );
	}

	private function get_check_password_data(): array {
		$user['id']          = 1000;
		$user['name']        = 'demo';
		$auth['server_hash'] = '$2y$10$LVVlL6WHwabThHqT6o1It.z9c710bDO2XTJtTA7KloPt1L9wwCEMi'; // hash for "demo"
		$auth['challenge']   = '30fd4978f3ea5ca0ef612c';
		$auth['force']       = true;
		$client_hash         = '$2a$10$c5DrNaLbSujCmEsxuVBKFuERbCLyxz64GHgkmf1JCJaj0fIn2Pmfi';

		return [ $user, $auth, $client_hash ];
	}

	private function check_password_prepare( array $user, array $auth ): void {
		if ( isset( $user['id'] ) ) {
			$this->create_user( $user['id'], $user['name'], $auth['server_hash'] );
			if ( isset( $auth['challenge'] ) ) {
				$this->set_user_challenge( $user['id'], $auth['challenge'] );
			}
		}
		$this->set_challenge_response_force_option( $auth['force'] );
	}

	private function set_challenge_response_force_option( bool $value = false ): void {
		update_option( 'mb_challenge_response_options', [ 'mb_challenge_response_field_force_cr' => $value ] );
	}

	private function create_user( int $id, string $username, string $password ): void {
		global $wpdb;
		$wpdb->query( 'INSERT INTO `wptests_users` (`ID`,`user_login`, `user_pass`) 
		VALUES (\'' . $id . '\',\'' . $username . '\',\'' . $password . '\')' );
	}

	private function set_user_challenge( int $user_id, string $challenge ): void {
		update_user_meta( $user_id, 'challenge-response-challenge', $challenge );
	}

	private function get_mb_password_hasher(): Mb_Password_Hasher {
		global $wpdb;

		return new Mb_Password_Hasher( $wpdb );
	}
}
