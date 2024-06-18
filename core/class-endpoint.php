<?php
/**
 * Base class for all endpoint classes.
 *
 * @link          https://wpmudev.com/
 * @since         1.0.0
 *
 * @author        WPMUDEV (https://wpmudev.com)
 * @package       WPMUDEV\PluginTest
 *
 * @copyright (c) 2023, Incsub (http://incsub.com)
 */

namespace WPMUDEV\PluginTest;

use WPMUDEV\PluginTest\Base;
use WP_REST_Response;
use WP_REST_Controller;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

class Endpoint extends WP_REST_Controller {
	/**
	 * API endpoint version.
	 *
	 * @since 1.0.0
	 *
	 * @var int $version
	 */
	protected $version = 1;

	/**
	 * API endpoint namespace.
	 *
	 * @since 1.0.0
	 *
	 * @var string $namespace
	 */
	protected $namespace;

	/**
	 * API endpoint for the current endpoint.
	 *
	 * @since 1.0.0
	 *
	 * @var string $endpoint
	 */
	protected $endpoint = '';

	/**
	 * Endpoint constructor.
	 *
	 * We need to register the routes here.
	 *
	 * @since 1.0.0
	 */
	protected function __construct() {
		// Setup namespace of the endpoint.
		$this->namespace = 'wpmudev/v' . $this->version;

		// If the single instance hasn't been set, set it now.
		$this->register_hooks();
	}

	/**
	 * Instance obtaining method.
	 *
	 * @return static Called class instance.
	 * @since 1.0.0
	 */
	public static function instance() {
		static $instances = array();

		// @codingStandardsIgnoreLine Plugin-backported
		$called_class_name = get_called_class();

		if ( ! isset( $instances[ $called_class_name ] ) ) {
			$instances[ $called_class_name ] = new $called_class_name();
		}

		return $instances[ $called_class_name ];
	}

	/**
	 * Set up WordPress hooks and filters
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function register_hooks() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Check if a given request has access to manage settings.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return bool
	 * @since 1.0.0
	 *
	 */
	public function edit_permission( $request ) {
		$capable = current_user_can( 'manage_options' );

		/**
		 * Filter to modify settings rest capability.
		 *
		 * @param WP_REST_Request $request Request object.
		 *
		 * @param bool            $capable Is user capable?.
		 *
		 * @since 1.0.0
		 *
		 */
		return apply_filters( 'wpmudev_plugintest_rest_settings_permission', $capable, $request );
	}

	/**
	 * Get formatted response for the current request.
	 *
	 * @param array $data    Response data.
	 * @param bool  $success Is request success.
	 *
	 * @return WP_REST_Response
	 * @since 1.0.0
	 *
	 */
	public function get_response( $data = array(), $success = true ) {
		// Response status.
		$status = $success ? 200 : 400;

		return new WP_REST_Response(
			array(
				'success' => $success,
				'data'    => $data,
			),
			$status
		);
	}

	/**
	 * Get the Endpoint's namespace
	 *
	 * @return string
	 */
	public function get_namespace() {
		return $this->namespace;
	}

	/**
	 * Get the Endpoint's endpoint part
	 *
	 * @return string
	 */
	public function get_endpoint() {
		return $this->endpoint;
	}

	public function get_endpoint_url() {
		return trailingslashit( rest_url() ) . trailingslashit( $this->get_namespace() ) . $this->get_endpoint();
	}

	/**
	 * Register the routes for the objects of the controller.
	 *
	 * This should be defined in extending class.
	 *
	 * @since 1.0.0
	 */
	public function register_routes() {
	}
}
