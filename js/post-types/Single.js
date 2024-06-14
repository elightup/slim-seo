import { render } from '@wordpress/element';
import { CheckboxControl } from '@wordpress/components';
import { __ } from "@wordpress/i18n";
import Text from "./components/Fields/Text";
import Image from "./components/Fields/Image";
import Textarea from "./components/Fields/Textarea";
import Checkbox from "./components/Fields/Checkbox";

const Single = () => {
	return <>
		<Text
			id="slim_seo[title]"
			label={ __( 'Meta title', 'slim-seo' ) }
			std={ ssPostTypes.metadata.title }
		/>
		<Textarea
			id="slim_seo[description]"
			label={ __( 'Meta description', 'slim-seo' ) }
			std={ ssPostTypes.metadata.description }
		/>
		<Image
			id="slim_seo[twitter_image]"
			label={ __( 'Twitter image', 'slim-seo' ) }
			std={ ssPostTypes.metadata.twitter_image }
			mediaPopupTitle={ ssPostTypes.mediaPopupTitle }
		/>
		<Image
			id="slim_seo[facebook_image]"
			label={ __( 'Facebook image', 'slim-seo' ) }
			std={ ssPostTypes.metadata.facebook_image }
		/>
		<Text
			id="slim_seo[canonical]"
			label={ __( 'Canonical URL', 'slim-seo' ) }
			std={ ssPostTypes.metadata.canonical }
		/>
		<Checkbox
			id="slim_seo[noindex]"
			label={ __( ' Hide from search results ', 'slim-seo' ) }
			std={ ssPostTypes.metadata.noindex }
		/>
	</>;
};

render( <Single />, document.getElementById( 'ss-post-type' ) );