import { __, sprintf } from "@wordpress/i18n";
import Checkbox from "./Checkbox";
import Image from "./Image";
import Text from "./Text";
import Textarea from "./Textarea";

export default  Block = ( { type, baseName, option, label } ) => <>
	<h3>{ label }</h3>
	<Text
		id={ `${ baseName }[title]` }
		label={ __( 'Meta title', 'slim-seo' ) }
		std={ option.title || '' }
		description={ __( 'Recommended length: ≤ 60 characters.', 'slim-seo' ) }
	/>
	<Textarea
		id={ `${ baseName }[description]` }
		label={ __( 'Meta description', 'slim-seo' ) }
		std={ option.description || '' }
		description={ __( 'Recommended length: 50-160 characters. Leave empty to autogenerate from post exceprt (if available) or post content or term description.', 'slim-seo' ) }
	/>
	<Image
		id={ `${ baseName }[facebook_image]` }
		label={ __( 'Facebook image', 'slim-seo' ) }
		std={ option.facebook_image || '' }
		mediaPopupTitle={ ssPostTypes.mediaPopupTitle }
		description={ __( 'Recommended size: 1200x630 px.', 'slim-seo' ) }
	/>
	<Image
		id={ `${ baseName }[twitter_image]` }
		label={ __( 'Twitter image', 'slim-seo' ) }
		std={ option.twitter_image || '' }
		mediaPopupTitle={ ssPostTypes.mediaPopupTitle }
		description={ __( 'Recommended size: 1200x600 px. Should have aspect ratio 2:1 with minimum width of 300 px and maximum width of 4096 px.', 'slim-seo' ) }
	/>
</>;