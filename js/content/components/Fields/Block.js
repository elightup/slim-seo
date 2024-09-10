import { __ } from "@wordpress/i18n";
import Description from "./Description";
import Image from "./Image";
import Title from "./Title";

export default Block = ( { baseName, option, optionPlaceholder = [], label, descriptionInstruction = '', onFocus, onBlur } ) => (
	<>
		<h3>{ label }</h3>
		<Title
			id={ `${ baseName }[title]` }
			isSettings={ true }
			label={ __( 'Meta title', 'slim-seo' ) }
			std={ option.title || '' }
			placeholder={ optionPlaceholder.title || '' }
			description={ __( 'Recommended length: ≤ 60 characters.', 'slim-seo' ) }
		/>
		<Description
			id={ `${ baseName }[description]` }
			isSettings={ true }
			label={ __( 'Meta description', 'slim-seo' ) }
			std={ option.description || '' }
			placeholder={ optionPlaceholder.description || '' }
			description={ descriptionInstruction }
		/>
		<Image
			id={ `${ baseName }[facebook_image]` }
			label={ __( 'Facebook image', 'slim-seo' ) }
			std={ option.facebook_image || '' }
			mediaPopupTitle={ ss.mediaPopupTitle }
			description={ __( 'Recommended size: 1200x630 px. Should have 1.91:1 aspect ratio with width ≥ 600 px.', 'slim-seo' ) }
		/>
		<Image
			id={ `${ baseName }[twitter_image]` }
			label={ __( 'Twitter image', 'slim-seo' ) }
			std={ option.twitter_image || '' }
			mediaPopupTitle={ ss.mediaPopupTitle }
			description={ __( 'Recommended size: 1200x600 px. Should have 2:1 aspect ratio with width ≥ 300 px and ≤ 4096 px.', 'slim-seo' ) }
		/>
	</>
);