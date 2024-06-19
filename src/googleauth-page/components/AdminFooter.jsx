import { __, sprintf } from "@wordpress/i18n";
import { createInterpolateElement } from "@wordpress/element";

const AdminFooter = () => {
	//get textDomain
	const { textDomain } = window.wpmudevPluginTest;

	//get translated string
	const translatedString = sprintf(
		__(
			"Please use this url <em>%s</em> in your Google API's <strong>Authorized redirect URIs</strong> field",
			textDomain,
		),
		window.wpmudevPluginTest.returnUrl,
	);

	//return
	return (
		<span>
			{createInterpolateElement(translatedString, {
				em: <em />,
				strong: <strong />,
			})}
		</span>
	);
};

export default AdminFooter;
