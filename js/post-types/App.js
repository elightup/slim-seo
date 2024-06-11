import { render, useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Tab, TabList, TabPanel, Tabs } from 'react-tabs';
import { useApi } from './functions';
import PostType from './PostType';

const App = () => {
	const postTypes = useApi( 'post_types' );

	if ( postTypes && Object.entries( postTypes ).length === 0 ) {
		return  <div className="ss-none">{ __( 'There are no custom post type.', 'slim-seo' ) }</div>;
	}

	return postTypes && (
		<Tabs forceRenderTabPanel={ true } className="ss-vertical-tabs">
			<TabList>
				{
					Object.entries( postTypes ).map( ( [ postTypeId, postType ] ) => (
						<Tab>{ postType.label }</Tab>
					) )
				}
			</TabList>

			{
				Object.entries( postTypes ).map( ( [ postTypeId, postType ] ) => (
					<TabPanel>
						<PostType key={ postTypeId } postType={ postType }/>
					</TabPanel>
				) )
			}

			<TabPanel>
				
			</TabPanel>
			<TabPanel>
				
			</TabPanel>
		</Tabs>

		// Object.entries( postTypes ).map( ( [ postTypeId, postType ] ) => (
		// 	<PostType
		// 		key={ postTypeId }
		// 		postType={ postType }
		// 	/>
		// ) )
	);
};

render( <App />, document.getElementById( 'ss-post-types' ) );