import {
	createRoot,
	render,
	StrictMode,
	createInterpolateElement,
} from "@wordpress/element";
import "./scss/style.scss";
import WPMUDEV_PluginTest from "./components/Home";

const domElement = document.getElementById(
	window.wpmudevPluginTest.dom_element_id,
);

if (createRoot) {
	createRoot(domElement).render(
		<StrictMode>
			<WPMUDEV_PluginTest />
		</StrictMode>,
	);
} else {
	render(
		<StrictMode>
			<WPMUDEV_PluginTest />
		</StrictMode>,
		domElement,
	);
}
