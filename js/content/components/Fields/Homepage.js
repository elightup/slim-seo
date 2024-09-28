import { RawHTML } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { useEffect, useState } from '@wordpress/element';
import { request } from "../../functions";
import Description from "./Description";
import FacebookImage from "./FacebookImage";
import Title from "./Title";
import TwitterImage from "./TwitterImage";

const HasHomepage = () => (
	<>
		<h3>{ __( 'Homepage', 'slim-seo' ) }</h3>
		<RawHTML>
			{ sprintf(
				__( '<p>You have a page <a href="%s">%s</a> that is set as the homepage.</p><p>To set the meta tags for the page, please <a href="%s">set on the edit page</a>.</p>', 'slim-seo' ),
				ss.homepage.link,
				ss.homepage.name,
				ss.homepage.edit
			) }
		</RawHTML>
	</>
);

const NoneHomepage = ( { option } ) => {
	const baseName = 'slim_seo[home]';
	let [ titlePreview, setTitlePreview ] = useState( option.title || ss.homepage.title );
	let [ descriptionPreview, setDescriptionPreview ] = useState( option.description || ss.homepage.description );
	const placeholders = {
		title: ss.homepage.title,
		description: ss.homepage.description,
	};

	const handleTitleChange = value => {
		refreshPreview( 'title', value );
	};

	const handleDescriptionChange = value => {
		refreshPreview( 'description', value );
	};

	const refreshPreview = ( type, value ) => {
		if ( !value ) {
			defaultPreview();
			return;
		}

		const timer = setTimeout( () =>
			request( 'content/render_text', { text: value } ).then( res => {
				type === 'title' ? setTitlePreview( res ) : setDescriptionPreview( res );
			} )
			, 1000 );
		return () => clearTimeout( timer );
	};

	const defaultPreview = ( type, value ) => {
		setTitlePreview( ss.homepage.title );
		setDescriptionPreview( ss.homepage.description );
	};

	useEffect( () => {
		request( 'content/render_text', { text: option.title || ss.homepage.title } ).then( res => {
			res && setTitlePreview( res );
		} );
		request( 'content/render_text', { text: option.description || ss.homepage.description } ).then( res => {
			res && setDescriptionPreview( res );
		} );
	}, [] );

	return <>
		<h3>{ __( 'Homepage', 'slim-seo' ) }</h3>
		<Title
			id={ `${ baseName }[title]` }
			isSettings={ true }
			std={ option.title || '' }
			preview={ titlePreview || '' }
			placeholder={ placeholders.title || '' }
			onChange={ handleTitleChange }
		/>
		<Description
			id={ `${ baseName }[description]` }
			isSettings={ true }
			std={ option.description || '' }
			preview={ descriptionPreview || '' }
			placeholder={ placeholders.description || '' }
			onChange={ handleDescriptionChange }
		/>
		<FacebookImage
			id={ `${ baseName }[facebook_image]` }
			std={ option.facebook_image || '' }
		/>
		<TwitterImage
			id={ `${ baseName }[twitter_image]` }
			std={ option.twitter_image || '' }
		/>
	</>;
};

export default ( { option } ) => ss.hasHomepageSettings ? <NoneHomepage option={ option } /> : <HasHomepage />;