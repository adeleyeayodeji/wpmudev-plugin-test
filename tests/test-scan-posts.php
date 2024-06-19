<?php

class Scan_Posts_Test extends WP_UnitTestCase
{
	/**
	 * Create test posts
	 *
	 */
	public function setUp(): void
	{
		parent::setUp();

		// Create a post
		$post_id = $this->factory->post->create(array(
			'post_title' => 'Test Post 1',
			'post_content' => 'This is a test post content',
			'post_status' => 'publish',
			'post_type' => 'post'
		));

		// Create another post
		$post_id = $this->factory->post->create(array(
			'post_title' => 'Test Post 2',
			'post_content' => 'This is another test post content',
			'post_status' => 'publish',
			'post_type' => 'post'
		));
	}

	/**
	 * Test if posts are created
	 *
	 */
	public function test_posts_created(): void
	{
		// Get all posts
		$posts = $this->scan_posts();

		//log
		fwrite(STDOUT, "\n\033[32mUser REST_API_VERSION: " . print_r($posts, true) . " \033[0m\n");

		// Check if posts are created
		$this->assertEquals(2, count($posts));
	}

	/**
	 * Scan for posts
	 *
	 * @return array
	 */
	public function scan_posts()
	{
		try {
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
		} catch (\Exception $e) {
			//error log
			fwrite(STDOUT, "\n\033[32mUser REST_API_VERSION: " . $e->getMessage() . " \033[0m\n");
			return [];
		}
	}
}
