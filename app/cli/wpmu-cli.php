<?php

/**
 * WPMU CLI Commands
 *
 * @link          https://wpmudev.com/
 * @since         1.0.0
 *
 * @author        WPMUDEV (https://wpmudev.com)
 * @package       WPMUDEV\PluginTest
 *
 * @copyright (c) 2023, Incsub (http://incsub.com)
 */

namespace WPMUDEV\PluginTest\App\CLI;

// Abort if called directly.
defined('WPINC') || die;

use WPMUDEV\PluginTest\Base;
use WPMUDEV\PluginTest\App\Admin_Pages\Auth;

class WPMU_CLI_Commands extends Base
{
	/**
	 * Init the class.
	 *
	 */
	public function init()
	{
		// Register the CLI command
		add_action('cli_init', [$this, 'register_commands']);
	}

	/**
	 * Register custom WP CLI commands.
	 */
	public function register_commands()
	{
		if (class_exists('WP_CLI')) {
			\WP_CLI::add_command('wpmudev_scan_posts', [$this, 'scan_posts_command']);
		}
	}

	/**
	 * WP CLI command to execute scan_posts_handle method.
	 */
	public function scan_posts_command($args, $assoc_args)
	{
		if (class_exists('WP_CLI')) {
			try {
				//get args
				if (isset($assoc_args['post_type']) && !empty($assoc_args['post_type'])) {
					//pass post type
					$post_type = $assoc_args['post_type'];
				} else {
					//set default post type to empty
					$post_type = '';
				}

				//get auth
				$auth = Auth::instance($post_type);

				// Execute the method
				$auth->scan_posts_handle();

				// Output success message
				\WP_CLI::success('Successfully executed scan_posts_handle.');
			} catch (\Exception $e) {
				// Output error message
				\WP_CLI::error("Error: {$e->getMessage()}");
			}
		}
	}
}
