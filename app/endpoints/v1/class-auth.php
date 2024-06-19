<?php

/**
 * Google Auth Shortcode.
 *
 * @link          https://wpmudev.com/
 * @since         1.0.0
 *
 * @author        WPMUDEV (https://wpmudev.com)
 * @package       WPMUDEV\PluginTest
 *
 * @copyright (c) 2023, Incsub (http://incsub.com)
 */

namespace WPMUDEV\PluginTest\Endpoints\V1;

// Abort if called directly.
defined('WPINC') || die;

use WPMUDEV\PluginTest\Endpoint;
use WP_REST_Server;
use \WPMUDEV\PluginTest\Core\Google_Auth\Auth as WPMU_Auth;

class Auth extends Endpoint
{
	/**
	 * Option name.
	 *
	 * @var string
	 */
	private $option_name = 'wpmudev_plugin_tests_auth';

	/**
	 * API endpoint for the current endpoint.
	 *
	 * @since 1.0.0
	 *
	 * @var string $endpoint
	 */
	protected $endpoint = 'auth/auth-url';

	/**
	 * Register the routes for handling auth functionality.
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function register_routes()
	{
		// TODO
		// Add a new Route to logout.

		// Route to get auth url.
		register_rest_route(
			$this->get_namespace(),
			$this->get_endpoint(),
			array(
				array(
					'methods' => 'POST',
					'callback' => array($this, 'save_credentials'),
					'args'    => array(
						'clientId'     => array(
							'required'    => true,
							'description' => __('The client ID from Google API project.', 'wpmudev-plugin-test'),
							'type'        => 'string',
						),
						'clientSecret' => array(
							'required'    => true,
							'description' => __('The client secret from Google API project.', 'wpmudev-plugin-test'),
							'type'        => 'string',
						),
					),
					'permission_callback' => [$this, 'auth_permission_callback'],
				),
			)
		);

		// Route to get auth confirm
		register_rest_route(
			$this->get_namespace(),
			'auth/confirm',
			array(
				'methods'  => 'GET',
				'callback' => array($this, 'auth_confirm'),
				'permission_callback' => "__return_true",
			),
		);

		//auth-login
		register_rest_route(
			$this->get_namespace(),
			'auth/login',
			array(
				'methods'  => 'GET',
				'callback' => array($this, 'auth_login_with_google'),
				'permission_callback' => "__return_true",
			),
		);

		//scan post endpoint
		register_rest_route(
			$this->get_namespace(),
			'posts/scan',
			array(
				'methods'  => 'POST',
				'callback' => array($this, 'scan_posts'),
				'permission_callback' => [$this, 'auth_permission_callback'],
				//args
				'args' => array(
					'post_type' => array(
						'required'    => true,
						'description' => __('The post type to scan.', 'wpmudev-plugin-test'),
						'type'        => 'string',
					),
				),
			),
		);
	}

	/**
	 * Save the client id and secret.
	 * @since 1.0.0
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function save_credentials($request)
	{
		try {
			$client_id     = $request->get_param('clientId'); //ignore sanitization
			$client_secret = $request->get_param('clientSecret'); //ignore sanitization

			//validate client id and secret
			if (empty($client_id) || empty($client_secret)) {
				throw new \Exception('Client ID and Client Secret are required.');
			}

			//save to settings
			update_option($this->option_name, array(
				'client_id'     => $client_id,
				'client_secret' => $client_secret,
			));

			//return success
			return new \WP_REST_Response(
				array(
					'status' => 200,
					'message' => 'Client ID and Client Secret saved successfully.',
				),
				200
			);
		} catch (\Exception $e) {
			return new \WP_REST_Response([
				'status' => 200,
				'message' => 'Error: ' . $e->getMessage(),
			], 500);
		}
	}

	/**
	 * auth_confirm
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function auth_confirm($request)
	{
		try {
			// Instantiate the Auth class
			$auth =
				WPMU_Auth::instance();

			//set_up
			$auth->set_up();

			//get the code from request
			$code = $request->get_param('code');

			//get user info from google
			$user_info = $auth->get_user_info($code);

			//get user email
			$user_email = $user_info['email'];

			//check email is not empty
			if (empty($user_email)) {
				throw new \Exception('Error: User email not found.');
			}

			//check user exists
			$user = get_user_by('email', $user_email);

			//if user not exists create new user
			if (empty($user)) {
				//get name
				$name = $user_info['name'] ?? $user_email;
				//generate password
				$password = wp_generate_password(12, false);
				//create new user
				$user_id = wp_create_user($name, $password, $user_email);
				//get user
				$user = get_user_by('id', $user_id);
			}

			//login user
			wp_set_current_user($user->ID, $user->user_login);
			//set auth cookie
			wp_set_auth_cookie($user->ID);
			//fire login action
			do_action('wp_login', $user->user_login, $user);

			//redirect to home page
			$url = home_url();
			//add success message
			$url = add_query_arg('notification', 'User logged in successfully.', $url);

			//redirect
			wp_redirect($url);

			//exit
			exit;
		} catch (\Exception $e) {
			error_log($e->getMessage());
			//redirect to home page with error message
			$url = home_url();
			//add error message
			$url = add_query_arg('notification', 'Error: ' . $e->getMessage(), $url);
			//redirect
			wp_redirect($url);

			//exit
			exit;
		}
	}

	/**
	 * auth_login_with_google
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function auth_login_with_google($request)
	{
		try {
			// Instantiate the Auth class
			$auth =
				WPMU_Auth::instance();
			//get_auth_url
			$auth_url = $auth->init();
			//confirm url exist
			if (empty($auth_url)) {
				throw new \Exception('Error: Auth URL not found.');
			}
			//redirect to auth url
			wp_redirect($auth_url);

			//exit
			exit;
		} catch (\Exception $e) {
			error_log($e->getMessage());
			//redirect to home page with error message
			$url = home_url();
			//add error message
			$url = add_query_arg('notification', 'Error: ' . $e->getMessage(), $url);
			//redirect
			wp_redirect($url);

			//exit
			exit;
		}
	}


	/**
	 * Permission callback for the REST API route.
	 *
	 * @return bool
	 */
	public function auth_permission_callback($req)
	{
		//verify jwt token Authorization: `Bearer ${nonce}`,
		$nonce = $req->get_header('Authorization');
		$nonce = str_replace('Bearer ', '', $nonce);
		$shared_key = get_option('shared_key_wpmudev');
		if ($nonce !== $shared_key) {
			return false;
		}
		return true;
	}

	/**
	 * Scan posts
	 *
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function scan_posts($request)
	{
		try {
			//get filter by post type
			$post_type = $request->get_param('post_type');

			//sanitize post type
			$post_type = sanitize_text_field($post_type);

			//set scan active
			update_option('wpmudev_plugin_tests_scan_active', true);

			//init Base Auth
			$baseAuth = \WPMUDEV\PluginTest\App\Admin_Pages\Auth::instance();

			//get scan_posts_handle
			$baseAuth->scan_posts_handle($post_type);

			//return posts
			return new \WP_REST_Response(
				array(
					'status' => 200,
					'message' => 'Scanning posts ....',
				),
				200
			);
		} catch (\Exception $e) {
			return new \WP_REST_Response([
				'status' => 404,
				'message' => 'Info: ' . $e->getMessage(),
			], 500);
		}
	}
}
