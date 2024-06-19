import React from "react";

// Import components
import WPMUDEV_Google_Auth from "./Google_Auth";
import WPMUDEV_PostMaintenance from "./PostMaintenance";

const WPMUDEV_PluginTest = () => {
	// Get init values
	const { current_page } = window.wpmudevPluginTest;

	// Map page names to components for cleaner code
	const pageComponents = {
		auth: WPMUDEV_Google_Auth,
		posts_maintenance: WPMUDEV_PostMaintenance,
	};

	// Determine which component to render based on current_page
	const ComponentToRender = pageComponents[current_page] || WPMUDEV_Google_Auth;

	return <ComponentToRender />;
};

export default WPMUDEV_PluginTest;
