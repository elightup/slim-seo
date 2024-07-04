import { render, useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Tab, TabList, TabPanel, Tabs } from "react-tabs";
import PostType from "./components/PostType";
import { request } from "./functions";

const App = () => {
	const [ option, setOption ] = useState( [] );

	useEffect( () => {
		request( 'post-types-option' ).then( setOption );
	}, [] );

	return <>
		<Tabs forceRenderTabPanel={ true } className="ss-vertical-tabs">
			<TabList>
				{ Object.values( ssPostTypes.postTypes ).map( postType => <Tab>{ postType.label }</Tab> ) }
			</TabList>
			{
				Object.entries( ssPostTypes.postTypes ).map( ( [ postTypeId, postType ] ) => (
					<TabPanel>
						{
							<PostType key={ postTypeId } id={ postTypeId } postType={ postType } option={ option[ postTypeId ] || [] } optionArchive={ option[ `${ postTypeId }_archive` ] || [] } />
						}
					</TabPanel>
				) )
			}
		</Tabs>
		<input type="submit" name="submit" className="button button-primary" value={ __( 'Save Changes', 'slim-seo' ) } />
	</>;
};

render( <App />, document.getElementById( 'ss-post-types' ) );