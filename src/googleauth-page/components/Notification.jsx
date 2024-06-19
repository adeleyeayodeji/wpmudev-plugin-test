import React, { useState, useEffect } from "react";

const Notification = ({ details }) => {
	const [isVisible, setIsVisible] = useState(false);

	useEffect(() => {
		//check if not empty object
		if (details && Object.keys(details).length) {
			setIsVisible(true);
			const timer = setTimeout(() => setIsVisible(false), 10000);
			return () => clearTimeout(timer); // Cleanup to prevent memory leak
		}
	}, [details]);

	if (!isVisible) return null;

	return (
		<div
			className={`notice notice-${details.type}`}
			style={{
				margin: 0,
				marginBottom: 20,
			}}
		>
			<div dangerouslySetInnerHTML={{ __html: details.message }} />
		</div>
	);
};

export default Notification;
