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
						{ ssPostTypes.unablePostTypes.hasOwnProperty( postTypeId ) ? 
							<UnablePostType id={ postTypeId } postType={ ssPostTypes.unablePostTypes[ postTypeId ] } /> :
							<PostType key={ postTypeId } id={ postTypeId } postType={ postType } option={ option[ postTypeId ] || [] } optionArchive={ option[ `${ postTypeId }_archive` ] || [] } />
						}
					</TabPanel>
				) )
			}
		</Tabs>
		<input type="submit" name="submit" id="submit" className="button button-primary" value={ __( 'Save Changes', 'slim-seo' ) } />
	</>;
};

const UnablePostType = ( { id, postType } ) => {
	const { link, title } = postType;

	return <>
		<span>{ __( 'You have a page ','slim-seo' ) }</span>
		<a href={ link } target="_blank" rel="noopener noreferrer">{ title } </a>
		<span>{ __( ` that has the same slug as the post type archive slug. So WordPress will set it as the archive page for the `,'slim-seo' ) }</span>
		<code>{ id }</code>
		<span>{ __( ` post type.`,'slim-seo' ) }</span>
	</>;
}

render( <App />, document.getElementById( 'ss-post-types' ) );