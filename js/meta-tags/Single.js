import { createRoot, useEffect, useState } from '@wordpress/element';
import { __ } from "@wordpress/i18n";
import Checkbox from "./components/Fields/Checkbox";
import FacebookImage from './components/Fields/FacebookImage';
import PostDescription from "./components/Fields/PostDescription";
import PostTitle from './components/Fields/PostTitle';
import TermDescription from "./components/Fields/TermDescription";
import TermTitle from './components/Fields/TermTitle';
import Text from "./components/Fields/Text";
import TwitterImage from './components/Fields/TwitterImage';
import { request } from "./functions";

const Single = () => {
	const [ social, setSocial ] = useState( {
		facebook: false,
		twitter: false,
	} );

	useEffect( () => {
		request( 'meta-tags/option' ).then( response => {
			let features = [ 'open_graph', 'twitter_cards' ];
			if ( response?.features && Array.isArray( response.features ) ) {
				features = response.features;
			}

			setSocial( {
				facebook: features.includes( 'open_graph' ),
				twitter: features.includes( 'twitter_cards' ),
			} );
		} );
	}, [] );

	return document.querySelector( '#edittag' ) ? <Term social={ social } /> : <Post social={ social } />;
};

const Post = ( { social } ) => (
	<>
		<PostTitle id="slim_seo[title]" std={ ss.data.title } />
		<PostDescription
			id="slim_seo[description]"
			std={ ss.data.description }
		/>
		{
			social.facebook &&
			<FacebookImage
				id="slim_seo[facebook_image]"
				std={ ss.data.facebook_image }
				description={ __( 'Leave empty to use the featured image or the first image in the post content.', 'slim-seo' ) }
			/>
		}
		{
			social.twitter && <TwitterImage id="slim_seo[twitter_image]" std={ ss.data.twitter_image } />
		}
		<Text id="slim_seo[canonical]" label={ __( 'Canonical URL', 'slim-seo' ) } std={ ss.data.canonical } />
		<Checkbox
			id="slim_seo[noindex]"
			label={ __( ' Hide from search results ', 'slim-seo' ) }
			std={ ss.data.noindex }
			description={ __( 'This setting will apply noindex robots tag to this post and exclude it from the sitemap.', 'slim-seo' ) }
		/>
	</>
);

const Term = ( { social } ) => (
	<>
		<h2>{ __( 'Search Engine Optimization', 'slim-seo' ) }</h2>
		<TermTitle id="slim_seo[title]" std={ ss.data.title } />
		<TermDescription
			id="slim_seo[description]"
			std={ ss.data.description }
		/>
		{
			social.facebook && <FacebookImage id="slim_seo[facebook_image]" std={ ss.data.facebook_image } />
		}
		{
			social.twitter && <TwitterImage id="slim_seo[twitter_image]" std={ ss.data.twitter_image } />
		}
		<Text id="slim_seo[canonical]" label={ __( 'Canonical URL', 'slim-seo' ) } std={ ss.data.canonical } />
		<Checkbox
			id="slim_seo[noindex]"
			label={ __( ' Hide from search results ', 'slim-seo' ) }
			std={ ss.data.noindex }
			description={ __( 'This setting will apply noindex robots tag to this term and exclude it from the sitemap.', 'slim-seo' ) }
		/>
	</>
);

const container = document.getElementById( 'ss-single' );
const root = createRoot( container );
root.render( <Single /> );