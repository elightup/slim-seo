import { createRoot, useEffect, useState } from '@wordpress/element';
import { __ } from "@wordpress/i18n";
import { request } from "./functions";
import Checkbox from "./components/Fields/Checkbox";
import FacebookImage from './components/Fields/FacebookImage';
import PostDescription from "./components/Fields/PostDescription";
import PostTitle from './components/Fields/PostTitle';
import TermDescription from "./components/Fields/TermDescription";
import TermTitle from './components/Fields/TermTitle';
import Text from "./components/Fields/Text";
import TwitterImage from './components/Fields/TwitterImage';

const Single = () => {
	const [ social, setSocial ] = useState( false );

	useEffect( () => {
		request( 'meta-tags/option' ).then( ( res ) => {
			setSocial( [ 'open_graph', 'twitter_cards' ].some( key => res.features.includes( key ) ) );
		} );
	}, [] );

	return document.querySelector( '#edittag' ) ? <Term social={ social } /> : <Post social={ social } />;
}

const Post = ( { social } ) => (
	<>
		<PostTitle id="slim_seo[title]" std={ ss.data.title } />
		<PostDescription
			id="slim_seo[description]"
			std={ ss.data.description }
		/>
		{
			social && (
				<>
					<FacebookImage
						id="slim_seo[facebook_image]"
						std={ ss.data.facebook_image }
						description={ __( 'Leave empty to use the featured image or the first image in the post content.', 'slim-seo' ) }
					/>
					<TwitterImage id="slim_seo[twitter_image]" std={ ss.data.twitter_image } />
				</>
			)
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
			social && (
				<>
					<FacebookImage id="slim_seo[facebook_image]" std={ ss.data.facebook_image } />
					<TwitterImage id="slim_seo[twitter_image]" std={ ss.data.twitter_image } />
				</>
			)
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