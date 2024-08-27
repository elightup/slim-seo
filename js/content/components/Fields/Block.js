import { __, sprintf } from "@wordpress/i18n";
import Image from "./Image";
import Title from "./Title";
import Description from "./Description";

export default Block = ( { type, baseName, option, optionPlaceholder = [], label, onFocus, onBlur } ) => {
	const description = type === 'taxonomy' ? __( 'term description', 'slim-seo' ) : __( 'post exceprt (if available) or post content', 'slim-seo' );

	return <>
		<h3>{ label }</h3>
		<Title
			id={ `${ baseName }[title]` }
			isPost={ false }
			label={ __( 'Meta title', 'slim-seo' ) }
			std={ option.title || '' }
			placeholder={ optionPlaceholder.title || '' }
			description={ __( 'Recommended length: ≤ 60 characters.', 'slim-seo' ) }
		/>
		<Description
			id={ `${ baseName }[description]` }
			isPost={ false }
			label={ __( 'Meta description', 'slim-seo' ) }
			std={ option.description || '' }
			placeholder={ optionPlaceholder.description || '' }
			// Translators: %s - the source to generate meta description.
			description={ sprintf( __( 'Recommended length: 50-160 characters. Leave empty to autogenerate from %s.', 'slim-seo' ), description ) }
		/>
		<Image
			id={ `${ baseName }[facebook_image]` }
			label={ __( 'Facebook image', 'slim-seo' ) }
			std={ option.facebook_image || '' }
			mediaPopupTitle={ ss.mediaPopupTitle }
			description={ __( 'Recommended size: 1200x630 px.', 'slim-seo' ) }
		/>
		<Image
			id={ `${ baseName }[twitter_image]` }
			label={ __( 'Twitter image', 'slim-seo' ) }
			std={ option.twitter_image || '' }
			mediaPopupTitle={ ss.mediaPopupTitle }
			description={ __( 'Recommended size: 1200x600 px. Should have aspect ratio 2:1 with minimum width of 300 px and maximum width of 4096 px.', 'slim-seo' ) }
		/>
	</>;
};