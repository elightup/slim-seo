import { render, useEffect ,useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Tab, TabList, TabPanel, Tabs } from "react-tabs";
import { request } from "./functions";
import PostType from "./components/PostType";

const App = () => {
	const [ option, setOption ] = useState( [] );

	useEffect( () => {
		request( 'option' ).then( setOption );
	}, [] );

	if ( Object.entries( ssPostTypes.postTypes ).length === 0 ) {
		return  <div className="ss-none">{ __( 'There are no custom post type.', 'slim-seo' ) }</div>;
	}

	return <>
		<Tabs forceRenderTabPanel={ true } className="ss-vertical-tabs">
			<TabList>
				{
					Object.entries( ssPostTypes.postTypes ).map( ( [ postTypeId, postType ] ) => (
						<Tab>{ postType.label }</Tab>
					) )
				}
			</TabList>
			{
				Object.entries( ssPostTypes.postTypes ).map( ( [ postTypeId, postType ] ) => (
					<TabPanel>
						<PostType key={ postTypeId } id={ postTypeId } postType={ postType } option={ option[ postTypeId ] || [] } optionArchive={ option[ `${ postTypeId }_archive` ] || [] } />
					</TabPanel>
				) )
			}
		</Tabs>
		<input type="submit" name="submit" id="submit" className="button button-primary" value={ __( 'Save Changes', 'slim-seo' ) } />
	</>;
};

render( <App />, document.getElementById( 'ss-post-types' ) );