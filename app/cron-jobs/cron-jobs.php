<?php

/**
 * Cron jobs
 *
 * @link          https://wpmudev.com/
 * @since         1.0.0
 *
 * @author        WPMUDEV (https://wpmudev.com)
 * @package       WPMUDEV\PluginTest
 *
 * @copyright (c) 2023, Incsub (http://incsub.com)
 */

namespace WPMUDEV\PluginTest\App\Cron_jobs;

// Abort if called directly.
defined('WPINC') || die;

use WPMUDEV\PluginTest\Base;
use WPMUDEV\PluginTest\App\Admin_Pages\Auth;

class Cron_Jobs extends Base
{
	/**
	 * Initialize the class.
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init()
	{
		// Setup cron job for scanning posts
		add_action('wp_loaded', [$this, 'setup_cron_job']);
		// Clear scheduled hook on plugin deactivation
		register_deactivation_hook(WPMUDEV_PLUGINTEST_PLUGIN_FILE, [$this, 'clear_scheduled_hook']);
	}

	/**
	 * Setup cron job for scanning posts.
	 *
	 * @return void
	 */
	public function setup_cron_job()
	{
		if (!wp_next_scheduled('wpmudev_scan_posts_daily')) {
			// Schedule for 2am daily, adjust the time as needed
			$time = strtotime('tomorrow 2:00 AM');
			// Log the scheduled time for the cron job
			error_log('Cron job scheduled: ' . date('Y-m-d H:i:s', $time));
			// Schedule the event
			wp_schedule_event($time, 'daily', 'wpmudev_scan_posts_daily');
		}
		// Hook to scan posts daily
		add_action('wpmudev_scan_posts_daily', [$this, 'scan_posts_handle']);
	}

	/**
	 * Handle scanning posts.
	 *
	 * @return void
	 */
	public function scan_posts_handle()
	{
		// Initialize the base auth class
		$baseAuth = \WPMUDEV\PluginTest\App\Admin_Pages\Auth::instance();
		//scan posts
		$baseAuth->scan_posts_handle();
	}

	/**
	 * Clear scheduled hook.
	 *
	 * @return void
	 */
	public static function clear_scheduled_hook()
	{
		$timestamp = wp_next_scheduled('wpmudev_scan_posts_daily');
		if ($timestamp) {
			wp_unschedule_event($timestamp, 'wpmudev_scan_posts_daily');
		}
	}
}
