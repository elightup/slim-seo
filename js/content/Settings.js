import { createRoot, useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Tab, TabList, TabPanel, Tabs } from "react-tabs";
import Homepage from "./components/Fields/Homepage";
import PostType from "./components/PostType";
import Taxonomy from "./components/Taxonomy";
import { request } from "./functions";

const App = () => {
	const [ loaded, setLoaded ] = useState( false );
	const [ option, setOption ] = useState( {} );

	useEffect( () => {
		request( 'content/option' ).then( ( res ) => {
			setOption( res );
			setLoaded( true );
		} );
	}, [] );

	const postTypes = Object.entries( ss.postTypes );
	const taxonomies = Object.entries( ss.taxonomies );

	return loaded && <>
		<Tabs forceRenderTabPanel={ true } className="ss-vertical-tabs">
			<TabList>
				<Tab>{ __( 'Homepage', 'slim-seo' ) }</Tab>
				{
					postTypes.length > 1 &&
					<Tab disabled={ true } className="react-tabs__tab ss-tab-heading">
						{ __( 'Post types', 'slim-seo' ) }
						<span className="dashicons dashicons-arrow-down-alt2"></span>
					</Tab>
				}
				{ postTypes.map( ( [ slug, postType ] ) => <Tab key={ slug } className="react-tabs__tab ss-tab-item">{ postType.label }</Tab> ) }
				{
					taxonomies.length > 1 &&
					<Tab disabled={ true } className="react-tabs__tab ss-tab-heading">
						{ __( 'Taxonomies', 'slim-seo' ) }
						<span className="dashicons dashicons-arrow-down-alt2"></span>
					</Tab>
				}
				{ taxonomies.map( ( [ slug, taxonomy ] ) => <Tab key={ slug } className="react-tabs__tab ss-tab-item">{ taxonomy.label }</Tab> ) }
			</TabList>
			<TabPanel>
				<Homepage option={ option.home || {} } />
			</TabPanel>
			{ postTypes.length > 1 && <TabPanel /> }
			{
				postTypes.map( ( [ slug, postType ] ) => (
					<TabPanel key={ slug }>
						{
							<PostType id={ slug } postType={ postType } option={ option[ slug ] || [] } optionArchive={ option[ `${ slug }_archive` ] || [] } />
						}
					</TabPanel>
				) )
			}
			{ taxonomies.length > 1 && <TabPanel /> }
			{
				taxonomies.map( ( [ slug, taxonomy ] ) => (
					<TabPanel key={ slug }>
						{
							<Taxonomy id={ slug } taxonomy={ taxonomy } option={ option[ slug ] || [] } />
						}
					</TabPanel>
				) )
			}
		</Tabs >
		<input type="submit" name="submit" className="button button-primary" value={ __( 'Save Changes', 'slim-seo' ) } />
	</>;
};

const container = document.getElementById( 'ss-content' );
const root = createRoot( container );
root.render( <App /> );