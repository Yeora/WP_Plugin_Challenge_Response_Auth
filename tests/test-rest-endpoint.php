<?php
/**
 * Class RestEndPointTest
 *
 * @package Mb_Challenge_Response_Authentication
 */

use MbChallengeResponseAuthentication\Mb_Rest_Endpoint;

/**
 * RestEndPointTest test case.
 */
class Rest_EndPoint_Test extends WP_UnitTestCase {

	/**
	 * Test if the Mb_Rest_Endpoint exists
	 */
	public function test_rest_endpoint_exists(): void {
		$this->assertInstanceOf( Mb_Rest_Endpoint::class, $this->get_mb_rest_endpoint() );
	}

	public function test_get_fake_salt(): void {
		$mb_rest_endpoint = $this->get_mb_rest_endpoint();

		$fake_salt_one   = $mb_rest_endpoint->get_fake_salt( 'fakeuser' );
		$fake_salt_two   = $mb_rest_endpoint->get_fake_salt( 'fakeuser' );
		$fake_salt_three = $mb_rest_endpoint->get_fake_salt( 'fakeuser123456' );

		$this->assertEquals( $fake_salt_one, $fake_salt_two );
		$this->assertNotEquals( $fake_salt_one, $fake_salt_three );
	}

	public function test_mb_get_user_salt_and_challenge_fake_user(): void {
		$mb_rest_endpoint = $this->get_mb_rest_endpoint();
		$request          = new WP_REST_Request();
		$request->set_param( 'user', 'user_doesnt_exists' );
		$salt = $mb_rest_endpoint->get_fake_salt('user_doesnt_exists');
		$result = $mb_rest_endpoint->mb_get_user_salt_and_challenge( $request );
		$this->assertArrayHasKey( 'salt', $result->data );
		$this->assertArrayHasKey( 'challenge', $result->data );
		$this->assertEquals( $salt,$result->data['salt'] );
	}

	public function test_mb_get_user_salt_and_challenge_real_user(): void {
		$mb_rest_endpoint = $this->get_mb_rest_endpoint();
		$user['id']       = 1000;
		$user['username'] = 'test_user';
		$user['password'] = '$2y$10$LVVlL6WHwabThHqT6o1It.z9c710bDO2XTJtTA7KloPt1L9wwCEMi';
		$this->create_user( $user );
		$request = new WP_REST_Request();
		$request->set_param( 'user', $user['username'] );
		$result = $mb_rest_endpoint->mb_get_user_salt_and_challenge( $request );
		$this->assertArrayHasKey( 'salt', $result->data );
		$this->assertArrayHasKey( 'challenge', $result->data );
		$this->assertEquals( '$2y$10$LVVlL6WHwabThHqT6o1It.', $result->data['salt'] );
	}

	public function test_mb_get_user_salt_and_challenge_two_calls(): void {
		$mb_rest_endpoint = $this->get_mb_rest_endpoint();
		$request          = new WP_REST_Request();
		$request->set_param( 'user', 'user_name' );
		$result_one = $mb_rest_endpoint->mb_get_user_salt_and_challenge( $request )->data;
		$result_two = $mb_rest_endpoint->mb_get_user_salt_and_challenge( $request )->data;
		$this->assertEquals( $result_one['salt'], $result_two['salt'] );
		$this->assertNotEquals( $result_one['challenge'], $result_two['challenge'] );
	}

	private function create_user( array $user ): void {
		global $wpdb;
		$wpdb->query( $wpdb->prepare( 'INSERT INTO `wptests_users` (`ID`,`user_login`, `user_pass`) VALUES
		(%s,%s,%s)', $user ) );
	}

	private function get_mb_rest_endpoint(): Mb_Rest_Endpoint {
		return new Mb_Rest_Endpoint();
	}
}
