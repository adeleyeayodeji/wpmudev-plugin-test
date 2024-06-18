import { createRoot, render, StrictMode, createInterpolateElement } from '@wordpress/element';
import { Button, TextControl } from '@wordpress/components';

import "./scss/style.scss"

const domElement = document.getElementById( window.wpmudevPluginTest.dom_element_id );

const WPMUDEV_PluginTest = () => {

    const handleClick = () => {
    }

    return (
    <>
        <div class="sui-header">
            <h1 class="sui-header-title">
                Settings
            </h1>
      </div>

        <div className="sui-box">

            <div className="sui-box-header">
                <h2 className="sui-box-title">Set Google credentials</h2>
            </div>

            <div className="sui-box-body">
                <div className="sui-box-settings-row">
                    <TextControl
                        help={createInterpolateElement(
                            'You can get Client ID from <a>here</a>.',
                            {
                              a: <a href="https://developers.google.com/identity/gsi/web/guides/get-google-api-clientid"/>,
                            }
                          )}
                        label="Client ID"
                        onChange={()=>{}}
                    />
                </div>

                <div className="sui-box-settings-row">
                    <TextControl
                        help={createInterpolateElement(
                            'You can get Client Secret from <a>here</a>.',
                            {
                              a: <a href="https://developers.google.com/identity/gsi/web/guides/get-google-api-clientid"/>,
                            }
                          )}
                        label="Client Secret"
						onChange={()=>{}}
                    />
                </div>

                <div className="sui-box-settings-row">
                    <span>Please use this url <em>{window.wpmudevPluginTest.returnUrl}</em> in your Google API's <strong>Authorized redirect URIs</strong> field</span>
                </div>
            </div>

            <div className="sui-box-footer">
                <div className="sui-actions-right">
                    <Button
                        variant="primary"
                        onClick={ handleClick }
                    >
                        Save
                    </Button>

                </div>
            </div>

        </div>

    </>
    );
}

if ( createRoot ) {
    createRoot( domElement ).render(<StrictMode><WPMUDEV_PluginTest/></StrictMode>);
} else {
    render( <StrictMode><WPMUDEV_PluginTest/></StrictMode>, domElement );
}
