# Coding Task Questions

# 1. Reducing npm Build File Size:

While executing npm’s build command, you will notice that the resulting zipped file is considerably large. Any suggestions on how to optimize and reduce its size?

- **Optimizing Composer Dependencies for Production:**

  When deploying your application to a production environment, it's crucial to minimize the footprint of your `vendor` directory to improve performance and reduce deployment size. Achieve this by configuring Composer to exclude unnecessary files and optimize the autoloader. Follow these steps:

  1.  **Configure Composer to Exclude Development Files**: Edit your `composer.json` to exclude non-essential files (like tests and documentation) from packages. Add the following configuration:

      ```json
      "config": {
      	 "archive": {
      		 "exclude": ["Tests", "tests", "docs", "*.md", ".*"]
      	 }
      }
      ```

  2.  **Install Dependencies**: Use the following command to install your Composer dependencies. This command ensures that only the necessary production files are included, excludes development dependencies, and optimizes the autoloader for better performance:

      ```bash
      composer install --no-dev --prefer-dist --optimize-autoloader
      ```

  By applying these configurations and installation options, you significantly reduce the size of your `vendor` directory, making your production environment leaner and faster.

# 2. Enhancing Google Auth Plugin

The plugin introduces a new admin menu named **Google Auth**, featuring fields for Client ID and Client Secret. To enhance this functionality:

1. Ensure the page is translatable. ✅
2. Set the Client Secret field as a password input for enhanced security. ✅
3. Add functionality to the save button, directing inputs to the `wp-json/wpmudev/v1/auth/auth-url` REST endpoint. ✅
4. Implement notifications for successful storage or error responses. ✅
5. Secure the existing endpoint. ✅
6. Complete the endpoint's callback for storing inputs in the `wpmudev_plugin_test_settings` option. ✅
7. Verify correct retrieval using the mentioned methods.

# 3. Google oAuth Return URL Setup

To implement Google’s oAuth, establish a return URL endpoint at `/wp-json/wpmudev/v1/auth/confirm`, providing functionality to:

1. Retrieve user email. ✅
2. If the email exists and the user is not logged in, log in the user. ✅
3. If the email doesn’t exist, create a new user with a generated password, and log them in. Redirect to the admin or home page accordingly. ✅
4. Create a shortcode to display a personalized message if the user is logged in or a link for Google oAuth login if not. ✅

# 4. Admin Menu for Posts Maintenance

Introduce a new admin menu page titled **Posts Maintenance** featuring a **Scan Posts** button. When clicked, this button should scan all public posts and pages (with customizable post type filters) and update the `wpmudev_test_last_scan` post_meta with the current timestamp. Ensure that operation will keep running if the user leaves that page. This operation should be repeated daily to ensure ongoing maintenance. ✅

# 5. WP-CLI Command for Terminal

For system administrators' convenience, create a WP-CLI command to execute the **Scan Posts** action (which you created in Task #4 above) from the terminal. Include clear instructions for usage and customization. ✅

# 6. Composer Package Version Control

Prevent conflicts associated with using common composer packages in WordPress. Implement measures to ensure compatibility and prevent version conflicts with other plugins or themes. ✅

````json

	We can prevent conflicts associated with using common composer packages in WordPress by implementing the following measures:

	1. **Composer Package Version Control**: Specify the exact version of each package in your `composer.json` file to prevent conflicts with other plugins or themes. For example:

	```json
	"require": {
		"google/apiclient": "2.16"
	}

	2. **Composer Classmap Autoloading**: Use classmap autoloading to prevent conflicts with other plugins or themes that may use the same package. Add the following configuration to your `composer.json` file:

	```json
"autoload": {
		"classmap": [
			"core/",
			"app/"
		]
	}

````

# 7. Unit Testing for Scan Posts

Prioritize software testing by initiating unit tests. Specifically, design a unit test to validate the 'Scan Posts' functionality, ensuring it runs without errors and effectively scans post content or any specified criteria.

**Please be sure to adhere to WPCS rules in your code for all tasks in this test. Following these rules for consistency and best practices is a priority and of crucial importance.**

We wish you good luck!
