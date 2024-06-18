<?php

class TestAPIAuth extends WP_Test_REST_TestCase {

	public function test_get_auth_url() {
		$request  = new WP_REST_Request( 'GET', '/wpmudev/v1/auth/auth-url' );
		$response = rest_get_server()->dispatch( $request );
		$error    = $response->as_error();

		// by default it will be error, at least it needs params
		$this->assertWPError( $error );

		// but, make sure its not "not found" -- as we  already registers it
		$this->assertNotSame( 'rest_no_route', $error->get_error_code() );
	}
}
