import React, { useState, useEffect } from "react";
import { Button } from "@wordpress/components";
import { __ } from "@wordpress/i18n";

import Notification from "./Notification";

const WPMUDEV_PostMaintenance = () => {
	//get init values
	const { textDomain, postTypes, scanPostEndpoint, nonce } =
		window.wpmudevPluginTest;

	//set post types
	const [postTypesData, setPostTypes] = useState([]);

	//message state
	const [notificationMessage, setMessage] = useState({});

	//disabled state
	const [disabled, setDisabled] = useState(false);

	//post types state
	const [selectedPostType, setSelectedPostType] = useState("");

	/**
	 * ComponentDidMount
	 *
	 * Set postTypesData
	 */
	useEffect(() => {
		//set postTypesData
		setPostTypes(postTypes);
	}, []);

	/**
	 * Scan posts
	 */
	const scanPosts = () => {
		try {
			//set button to disabled
			setDisabled(true);
			//set message
			setMessage({
				type: "info",
				message: __("Scanning posts...", textDomain),
			});

			//set button to disabled
			setDisabled(false);

			//send request to server
			fetch(scanPostEndpoint, {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
					//jwt
					Authorization: `Bearer ${nonce}`,
				},
				body: JSON.stringify({
					post_type: selectedPostType,
				}),
			})
				.then((response) => response.json())
				.then((data) => {
					//set message
					setMessage({
						type: data.status === 200 ? "success" : "error",
						message: data.message,
					});
					//set button to disabled
					setDisabled(false);
				});
		} catch (error) {
			//set message
			setMessage({
				type: "error",
				message: __("An error occurred", textDomain),
			});
			//set button to disabled
			setDisabled(false);
		}
	};

	/**
	 * Handle post type change
	 * @param {Object} event
	 * @return {void}
	 */
	const handlePostTypeChange = (event) => {
		setSelectedPostType(event.target.value);
	};

	//return
	return (
		<>
			<div className="sui-header">
				<h1 className="sui-header-title">
					{__("Post Maintenance", textDomain)}
				</h1>
			</div>

			<div className="sui-box">
				<div className="sui-box-header">
					<h2 className="sui-box-title">{__("Scan posts", textDomain)}</h2>
				</div>

				<div className="sui-box-body">
					<div className="wpdev-notification">
						<Notification details={notificationMessage} />
					</div>

					{/* Post Types Dropdown */}
					<div
						className="sui-box-settings-row"
						style={{
							display: "flex",
							gap: "10px",
							flexDirection: "column",
						}}
					>
						<label htmlFor="post-type">
							{__("Select post type", textDomain)}
						</label>
						<select
							value={selectedPostType}
							onChange={handlePostTypeChange}
							id="post-type"
						>
							{postTypesData.map((v, k) => (
								<option key={k} value={v.value}>
									{v.label}
								</option>
							))}
						</select>
					</div>

					<div className="sui-box-settings-row">
						<Button
							variant="primary"
							className="button button-primary"
							onClick={scanPosts}
							disabled={disabled}
						>
							{__("Scan posts", textDomain)}
						</Button>
					</div>
				</div>
			</div>
		</>
	);
};

export default WPMUDEV_PostMaintenance;
