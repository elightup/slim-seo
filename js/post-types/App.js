import { render, useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Tab, TabList, TabPanel, Tabs } from "react-tabs";
import PostType from "./components/PostType";
import Taxonomy from "./components/Taxonomy";
import Block from "./components/Fields/Block";
import { request } from "./functions";

const App = () => {
	const [ option, setOption ] = useState( null );

	useEffect( () => {
		request( 'post-types-option' ).then( setOption );
	}, [] );

	return <>
		<Tabs forceRenderTabPanel={ true } className="ss-vertical-tabs">
			<TabList>
				{ ssPostTypes.hasHomepageSettings && <Tab >{ __( 'Homepage', 'slim-seo' ) }</Tab> }
				{ Object.values( ssPostTypes.postTypes ).map( postType => <Tab>{ postType.label }</Tab> ) }
				{ Object.values( ssPostTypes.taxonomies ).map( taxonomy => <Tab>{ taxonomy.label }</Tab> ) }
			</TabList>
			{ option && ssPostTypes.hasHomepageSettings &&
				<TabPanel>
					<Block
						baseName="slim_seo[home]"
						option={ option[`home`] || [] }
						label="Homepage"
					/>
				</TabPanel>
			}
			{ option &&
				Object.entries( ssPostTypes.postTypes ).map( ( [ postTypeId, postType ] ) => (
					<TabPanel>
						{
							<PostType key={ postTypeId } id={ postTypeId } postType={ postType } option={ option[ postTypeId ] || [] } optionArchive={ option[ `${ postTypeId }_archive` ] || [] } />
						}
					</TabPanel>
				) )
			}
			{ option &&
				Object.entries( ssPostTypes.taxonomies ).map( ( [ taxonomyId, taxonomy ] ) => (
					<TabPanel>
						{
							<Taxonomy key={ taxonomyId } id={ taxonomyId } taxonomy={ taxonomy } option={ option[ taxonomyId ] || [] } />
						}
					</TabPanel>
				) )
			}
		</Tabs>
		<input type="submit" name="submit" className="button button-primary" value={ __( 'Save Changes', 'slim-seo' ) } />
	</>;
};

render( <App />, document.getElementById( 'ss-post-types' ) );