import { RawHTML, render, useEffect, useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { Tab, TabList, TabPanel, Tabs } from "react-tabs";
import PostType from "./components/PostType";
import { request } from "./functions";

const App = () => {
	const [ option, setOption ] = useState( [] );

	useEffect( () => {
		request( 'option' ).then( setOption );
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
							ssPostTypes.unablePostTypes.hasOwnProperty( postTypeId )
								? <UnablePostType id={ postTypeId } postType={ ssPostTypes.unablePostTypes[ postTypeId ] } />
								: <PostType key={ postTypeId } id={ postTypeId } postType={ postType } option={ option[ postTypeId ] || [] } optionArchive={ option[ `${ postTypeId }_archive` ] || [] } />
						}
					</TabPanel>
				) )
			}
		</Tabs>
		<input type="submit" name="submit" className="button button-primary" value={ __( 'Save Changes', 'slim-seo' ) } />
	</>;
};

const UnablePostType = ( { id, postType } ) => {
	const { link, title } = postType;

	return <RawHTML>{
		sprintf(
			__( 'You have a page <a href="%s">%s</a> that has the same slug as the post type archive slug. So WordPress will set it as the archive page for the <code> %s </code> post type.', 'slim-seo' ),
			link,
			title,
			id
		)
	}</RawHTML>;
};

render( <App />, document.getElementById( 'ss-post-types' ) );