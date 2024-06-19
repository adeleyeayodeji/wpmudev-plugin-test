import { createInterpolateElement } from "@wordpress/element";
import { Button, TextControl } from "@wordpress/components";
import { __ } from "@wordpress/i18n";

import AdminFooter from "./AdminFooter";
import Notification from "./Notification";
import React, { useState } from "react";

/**
 * WPMUDEV_Google_Auth
 *
 * @returns {React.ReactElement}
 */
const WPMUDEV_Google_Auth = () => {
	//get init values
	const {
		textDomain,
		initialClientId,
		initialClientSecret,
		nonce,
		restEndpointSave,
	} = window.wpmudevPluginTest;

	//client id
	const [clientId, setClientId] = useState(initialClientId);
	//client secret
	const [clientSecret, setClientSecret] = useState(initialClientSecret);
	//message state
	const [notificationMessage, setMessage] = useState({});

	/**
	 * Set Button to disabled and inputs
	 */
	const setDisabled = (disable = true) => {
		if (disable) {
			//set class client-id-input to readonly
			document.querySelector(".client-id-input").setAttribute("readonly", true);
			//set class client-secret-input to readonly
			document
				.querySelector(".client-secret-input")
				.setAttribute("readonly", true);

			//set class save-button-trigger to disabled
			document
				.querySelector(".save-button-trigger")
				.setAttribute("disabled", true);
		} else {
			//set class client-id-input to readonly
			document.querySelector(".client-id-input").removeAttribute("readonly");
			//set class client-secret-input to readonly
			document
				.querySelector(".client-secret-input")
				.removeAttribute("readonly");

			//set class save-button
			document
				.querySelector(".save-button-trigger")
				.removeAttribute("disabled");
		}
	};

	//handle click
	const handleClick = async (e) => {
		//prevent default
		e.preventDefault();
		//get updated values
		const formData = {
			clientId,
			clientSecret,
		};

		//set button to disabled
		setDisabled(true);

		//send updated values to server
		try {
			const response = await fetch(restEndpointSave, {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
					//jwt
					Authorization: `Bearer ${nonce}`,
				},
				body: JSON.stringify(formData),
			});

			const data = await response.json();

			//set button to enabled
			setDisabled(false);

			//check data status is 200
			if (data.status === 200) {
				//set message
				setMessage({
					type: "success",
					message: data.message,
				});
			} else {
				//set message
				setMessage({
					type: "error",
					message: data.message,
				});
			}

			//log response
			// console.log("log", data); // Handle the response data
		} catch (error) {
			//set button to enabled
			setDisabled(false);
			//log error
			console.error("Error submitting form:", error);
		}
	};

	return (
		<>
			<div className="sui-header">
				<h1 className="sui-header-title">{__("Settings", textDomain)}</h1>
			</div>

			<div className="sui-box">
				<div className="sui-box-header">
					<h2 className="sui-box-title">
						{__("Set Google credentials", textDomain)}
					</h2>
				</div>

				<div className="sui-box-body">
					<div className="wpdev-notification">
						<Notification details={notificationMessage} />
					</div>

					<div className="sui-box-settings-row">
						<TextControl
							className="client-id-input"
							help={createInterpolateElement(
								__("You can get Client ID from <a>here</a>.", textDomain),
								{
									a: (
										<a href="https://developers.google.com/identity/gsi/web/guides/get-google-api-clientid" />
									),
								},
							)}
							label={__("Client ID", textDomain)}
							value={clientId}
							onChange={(value) => setClientId(value)}
						/>
					</div>

					<div className="sui-box-settings-row">
						<TextControl
							className="client-secret-input"
							help={createInterpolateElement(
								__("You can get Client Secret from <a>here</a>.", textDomain),
								{
									a: (
										<a href="https://developers.google.com/identity/gsi/web/guides/get-google-api-clientid" />
									),
								},
							)}
							label={__("Client Secret", textDomain)}
							value={clientSecret}
							onChange={(value) => setClientSecret(value)}
							//set type password
							type="password"
						/>
					</div>

					<div className="sui-box-settings-row">
						<AdminFooter />
					</div>
				</div>

				<div className="sui-box-footer">
					<div className="sui-actions-right">
						<Button
							variant="primary"
							className="button button-primary save-button-trigger"
							onClick={handleClick}
						>
							{__("Save", textDomain)}
						</Button>
					</div>
				</div>
			</div>
		</>
	);
};

export default WPMUDEV_Google_Auth;
