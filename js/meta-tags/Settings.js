import { createRoot, useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Tab, TabList, TabPanel, Tabs } from "react-tabs";
import Author from "./components/Author";
import Homepage from "./components/Homepage";
import PostType from "./components/PostType";
import Taxonomy from "./components/Taxonomy";
import { request } from "./functions";

const App = () => {
	const [ loaded, setLoaded ] = useState( false );
	const [ option, setOption ] = useState( {} );
	const [ social, setSocial ] = useState( {
		facebook: false,
		twitter: false,
	} );

	useEffect( () => {
		request( 'meta-tags/option' ).then( response => {
			setOption( response );

			let features = [ 'open_graph', 'twitter_cards' ];
			if ( response?.features && Array.isArray( response.features ) ) {
				features = response.features;
			}
			setSocial( {
				facebook: features.includes( 'open_graph' ),
				twitter: features.includes( 'twitter_cards' ),
			} );
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
				{ postTypes.map( ( [ slug, postType ] ) => <Tab key={ slug } className="react-tabs__tab ss-tab-item" title={ `${ postType.labels.singular_name } (${ postType.name })` }>{ postType.labels.singular_name } ({ postType.name })</Tab> ) }
				{
					taxonomies.length > 1 &&
					<Tab disabled={ true } className="react-tabs__tab ss-tab-heading">
						{ __( 'Taxonomies', 'slim-seo' ) }
						<span className="dashicons dashicons-arrow-down-alt2"></span>
					</Tab>
				}
				{ taxonomies.map( ( [ slug, taxonomy ] ) => <Tab key={ slug } className="react-tabs__tab ss-tab-item" title={ `${ taxonomy.labels.singular_name } (${ taxonomy.name })` }>{ taxonomy.labels.singular_name } ({ taxonomy.name })</Tab> ) }
				<Tab disabled={ true } className="react-tabs__tab ss-tab-heading">
					{ __( 'Other pages', 'slim-seo' ) }
					<span className="dashicons dashicons-arrow-down-alt2"></span>
				</Tab>
				<Tab className="react-tabs__tab ss-tab-item">{ __( 'Author', 'slim-seo' ) }</Tab>
			</TabList>
			<TabPanel>
				<Homepage option={ option.home || {} } social={ social } />
			</TabPanel>
			{ postTypes.length > 1 && <TabPanel /> }
			{
				postTypes.map( ( [ slug, postType ] ) => (
					<TabPanel key={ slug }>
						{
							<PostType id={ slug } postType={ postType } option={ option[ slug ] || [] } optionArchive={ option[ `${ slug }_archive` ] || [] } social={ social } />
						}
					</TabPanel>
				) )
			}
			{ taxonomies.length > 1 && <TabPanel /> }
			{
				taxonomies.map( ( [ slug, taxonomy ] ) => (
					<TabPanel key={ slug }>
						{
							<Taxonomy id={ slug } taxonomy={ taxonomy } option={ option[ slug ] || [] } social={ social } />
						}
					</TabPanel>
				) )
			}
			<TabPanel />
			<TabPanel>
				<Author option={ option.author || {} } social={ social } />
			</TabPanel>
		</Tabs >
		<input type="submit" name="submit" className="button button-primary" value={ __( 'Save Changes', 'slim-seo' ) } />
	</>;
};

const container = document.getElementById( 'ss-meta-tags' );
const root = createRoot( container );
root.render( <App /> );