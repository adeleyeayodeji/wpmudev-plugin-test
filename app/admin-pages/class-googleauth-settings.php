<?php

/**
 * Google Auth block.
 *
 * @link          https://wpmudev.com/
 * @since         1.0.0
 *
 * @author        WPMUDEV (https://wpmudev.com)
 * @package       WPMUDEV\PluginTest
 *
 * @copyright (c) 2023, Incsub (http://incsub.com)
 */

namespace WPMUDEV\PluginTest\App\Admin_Pages;

// Abort if called directly.
defined('WPINC') || die;

use WPMUDEV\PluginTest\Base;
use WPMUDEV\PluginTest\Endpoints\V1\Auth_Confirm;

class Auth extends Base
{
	/**
	 * The page title.
	 *
	 * @var string
	 */
	private $page_title;

	/**
	 * The page slug.
	 *
	 * @var string
	 */
	private $page_slug = 'wpmudev_plugintest_auth';

	/**
	 * Google auth credentials.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $creds = array();

	/**
	 * Option name.
	 *
	 * @var string
	 */
	private $option_name = 'wpmudev_plugin_tests_auth';

	/**
	 * Page Assets.
	 *
	 * @var array
	 */
	private $page_scripts = array();

	/**
	 * Assets version.
	 *
	 * @var string
	 */
	private $assets_version = '';

	/**
	 * A unique string id to be used in markup and jsx.
	 *
	 * @var string
	 */
	private $unique_id = '';

	/**
	 * Post Maintenance Page ID
	 *
	 * @var string
	 */
	private $post_maintenance_page_id = '';

	/**
	 * Initializes the page.
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function init()
	{
		$this->page_title     = __('Google Auth', 'wpmudev-plugin-test');
		$this->creds          = get_option($this->option_name, array());
		$this->assets_version = !empty($this->script_data('version')) ? $this->script_data('version') : WPMUDEV_PLUGINTEST_VERSION;
		$this->unique_id      = "wpmudev_plugintest_auth_main_wrap-{$this->assets_version}";

		//posts_maintenance id
		$this->post_maintenance_page_id = "wpmudev_plugintest_posts_maintenance-{$this->assets_version}";

		add_action('admin_menu', array($this, 'register_admin_page'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
		//add frontend scripts
		add_action('wp_enqueue_scripts', array($this, 'enqueue_assets_frontend'));
		// Add body class to admin pages.
		add_filter('admin_body_class', array($this, 'admin_body_classes'));
		//init_shared_key
		$this->init_shared_key();
		//create shortcode for auth message
		add_shortcode('wpmudev_plugin_test_auth_message', array($this, 'oauth_shortcode'));
	}

	/**
	 * Auth message shortcode
	 *
	 * @param array $atts
	 * @return string
	 */
	public function auth_message_shortcode()
	{
		//get notification
		$notification = isset($_GET['notification']) ? $_GET['notification'] : '';

		//check notification is empty
		if (empty($notification)) {
			return ''; //return empty string
		}

		ob_start();
?>
		<div class="sui-notice sui-notice-<?php echo strpos($notification, 'Error:') !== false ? 'error' : 'success'; ?>">
			<div class="sui-notice-content">
				<p><?php echo esc_html($notification); ?></p>
			</div>
		</div>
<?php
		echo ob_get_clean();
	}

	/**
	 * Auth message shortcode
	 *
	 * @param array $atts
	 * @return string
	 */
	public function oauth_shortcode()
	{
		ob_start();
		echo '<div class="sui-box-login">';
		//add notification listener
		$this->auth_message_shortcode();
		//check if user is logged in
		if (is_user_logged_in()) {
			echo '<p>' . __('You are already logged in.', 'wpmudev-plugin-test') . '</p>';
		} else {
			echo '<p>' . __('Please login with Google to continue.', 'wpmudev-plugin-test') . '</p>';
			//oauth login button
			echo '<p><a href="' . rest_url('wpmudev/v1/auth/login') . '" class="sui-button sui-button-blue">' . __('Login with Google', 'wpmudev-plugin-test') . '</a></p>';
		}
		echo '</div>';
		return ob_get_clean();
	}

	/**
	 * Init shared_key_wpmudev
	 *
	 */
	public function init_shared_key()
	{
		$shared_key = get_option('shared_key_wpmudev');
		if (empty($shared_key)) {
			$shared_key = wp_generate_password(64, false);
			update_option('shared_key_wpmudev', $shared_key);
		}
	}

	public function register_admin_page()
	{
		$page = add_menu_page(
			'Google Auth setup',
			$this->page_title,
			'manage_options',
			$this->page_slug,
			array($this, 'callback'),
			'dashicons-google',
			6
		);

		add_action('load-' . $page, array($this, 'prepare_assets'));

		//Posts Maintenance Page
		$postMaintenance = add_menu_page(
			__('Posts Maintenance', 'wpmudev-plugin-test'),
			__('Posts Maintenance', 'wpmudev-plugin-test'),
			'manage_options',
			$this->page_slug . '_posts_maintenance', //slug with page query string 'wpmudev_plugintest_posts_maintenance
			array($this, 'posts_maintenance_callback'),
			'dashicons-admin-tools',
			7
		);

		//add scripts for posts maintenance page
		add_action('load-' . $postMaintenance, array($this, 'prepare_assets'));
	}

	/**
	 * The admin page callback method.
	 *
	 * @return void
	 */
	public function callback()
	{
		$this->view($this->unique_id);
	}

	/**
	 * The posts maintenance page callback method.
	 *
	 * @return void
	 */
	public function posts_maintenance_callback()
	{
		$this->view($this->unique_id);
	}

	/**
	 * Get current page for asset id
	 *
	 * @return object
	 */
	public function get_current_page()
	{
		//get current page url
		$current_page = $_SERVER['REQUEST_URI'];
		//check page slug match with current page
		if (strpos($current_page, $this->page_slug) !== false && strpos($current_page, '_posts_maintenance') === false) {
			return (object)[
				'id' => $this->unique_id,
				'page' => 'auth',
			];
		} else if (strpos($current_page, '_posts_maintenance') !== false) {
			return (object)[
				'id' => $this->unique_id,
				'page' => 'posts_maintenance',
			];
		}
		//return default
		return (object)[
			'id' => $this->unique_id,
			'page' => 'auth',
		];
	}

	/**
	 * Prepares assets.
	 *
	 * @return void
	 */
	public function prepare_assets()
	{
		if (!is_array($this->page_scripts)) {
			$this->page_scripts = array();
		}

		$handle       = 'wpmudev_plugintest_authpage';
		$src          = WPMUDEV_PLUGINTEST_ASSETS_URL . '/js/authsettingspage.min.js';
		$style_src    = WPMUDEV_PLUGINTEST_ASSETS_URL . '/css/authsettingspage.min.css';
		$dependencies = !empty($this->script_data('dependencies'))
			? $this->script_data('dependencies')
			: array(
				'react',
				'wp-element',
				'wp-i18n',
				'wp-is-shallow-equal',
				'wp-polyfill',
				'wp-components'
			);

		//get_current_page
		$get_current_page = $this->get_current_page();

		$this->page_scripts[$handle] = array(
			'src'       => $src,
			'style_src' => $style_src,
			'deps'      => $dependencies,
			'ver'       => $this->assets_version,
			'strategy'  => true,
			'localize'  => array(
				'dom_element_id'   => $get_current_page->id,
				'current_page'    => $get_current_page->page,
				'postTypes' => $this->postTypes(),
				'textDomain'       => 'wpmudev-plugin-test',
				'initialClientId'   => $this->creds['client_id'],
				'initialClientSecret' => $this->creds['client_secret'],
				'redirectUrl'      => home_url(),
				'restEndpointSave' => rest_url('wpmudev/v1/auth/auth-url'),
				'returnUrl'        => rest_url('wpmudev/v1/auth/confirm'),
				'nonce'            => get_option('shared_key_wpmudev'),
				'scanPostEndpoint'     => rest_url('wpmudev/v1/posts/scan'),
				'post_scan_status' => $this->get_scan_status(),
			),
		);
	}

	/**
	 * Get posts scan status
	 *
	 */
	public function get_scan_status()
	{
		//get scan status
		$scan_status = get_option('wpmudev_plugin_tests_scan_active', false);
		//return scan status
		return $scan_status;
	}

	/**
	 * Get all available post types
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function postTypes()
	{
		try {
			//get all post types
			$post_types = [
				[
					"label" => "All",
					"value" => "all"
				],
				[
					"label" => "Post",
					"value" => "post"
				],
				[
					"label" => "Page",
					"value" => "page"
				]
			];
			//return post types
			return $post_types;
		} catch (\Exception $e) {
			//return empty array
			return array();
		}
	}

	/**
	 * Gets assets data for given key.
	 *
	 * @param string $key
	 *
	 * @return string|array
	 */
	protected function script_data(string $key = '')
	{
		$raw_script_data = $this->raw_script_data();

		return !empty($key) && !empty($raw_script_data[$key]) ? $raw_script_data[$key] : '';
	}

	/**
	 * Gets the script data from assets php file.
	 *
	 * @return array
	 */
	protected function raw_script_data(): array
	{
		static $script_data = null;

		if (is_null($script_data) && file_exists(WPMUDEV_PLUGINTEST_DIR . 'assets/js/authsettingspage.min.asset.php')) {
			$script_data = include WPMUDEV_PLUGINTEST_DIR . 'assets/js/authsettingspage.min.asset.php';
		}

		return (array) $script_data;
	}

	/**
	 * Prepares assets.
	 *
	 * @return void
	 */
	public function enqueue_assets()
	{
		if (!empty($this->page_scripts)) {
			foreach ($this->page_scripts as $handle => $page_script) {
				wp_register_script(
					$handle,
					$page_script['src'],
					$page_script['deps'],
					$page_script['ver'],
					$page_script['strategy']
				);

				if (!empty($page_script['localize'])) {
					wp_localize_script($handle, 'wpmudevPluginTest', $page_script['localize']);
				}

				wp_enqueue_script($handle);

				if (!empty($page_script['style_src'])) {
					wp_enqueue_style($handle, $page_script['style_src'], array(), $this->assets_version);
				}
			}
		}
	}

	/**
	 * Enqueue frontend assets.
	 *
	 * @return void
	 */
	public function enqueue_assets_frontend()
	{
		//add frontend css
		wp_enqueue_style('wpmudev_plugintest_frontend', WPMUDEV_PLUGINTEST_ASSETS_URL . '/css/frontend.min.css', array(), time()); //debugging
	}

	/**
	 * Prints the wrapper element which React will use as root.
	 *
	 * @return void
	 */
	protected function view($id)
	{
		echo '<div id="' . esc_attr($id) . '" class="sui-wrap"></div>';
	}

	/**
	 * Adds the SUI class on markup body.
	 *
	 * @param string $classes
	 *
	 * @return string
	 */
	public function admin_body_classes($classes = '')
	{
		if (!function_exists('get_current_screen')) {
			return $classes;
		}

		$current_screen = get_current_screen();

		if (empty($current_screen->id) || !strpos($current_screen->id, $this->page_slug)) {
			return $classes;
		}

		$classes .= ' sui-' . str_replace('.', '-', WPMUDEV_PLUGINTEST_SUI_VERSION) . ' ';

		return $classes;
	}

	/**
	 * Scan posts handle
	 *
	 */
	public function scan_posts_handle($post_type = '')
	{
		//args
		$args = array(
			'post_type' => ['post', 'page'],
			'posts_per_page' => 10,
			//fields id
			'fields' => 'ids',
		);

		//avoid posts with wpmudev_test_last_scan
		$args['meta_query'] = array(
			'relation' => 'OR',
			array(
				'key' => 'wpmudev_test_last_scan',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key' => 'wpmudev_test_last_scan',
				'value' => '',
				'compare' => '=',
			),
		);

		//if post type is not all
		if (!empty($post_type) && $post_type !== 'all') {
			$args['post_type'] = $post_type;
		}

		//get all posts
		$posts = new \WP_Query($args);

		//check posts found
		if (empty($posts->posts)) {
			throw new \Exception('All posts are scanned.');
		}

		//get posts
		$posts = $posts->posts;

		//loop through and update wpmudev_test_last_scan
		foreach ($posts as $post_id) {
			//get current date
			$date_now = date('Y-m-d H:i:s');
			//update post meta
			update_post_meta($post_id, 'wpmudev_test_last_scan', $date_now);
		}

		return $posts;
	}
}
