import { render } from '@wordpress/element';
import { CheckboxControl } from '@wordpress/components';
import { __ } from "@wordpress/i18n";
import Text from "./components/Fields/Text";
import Image from "./components/Fields/Image";
import Textarea from "./components/Fields/Textarea";
import Checkbox from "./components/Fields/Checkbox";

const Single = () => {
	return <>
		<h2 className="title">{ ssPostTypes.title }</h2>
		<Text
			id="slim_seo[title]"
			label={ __( 'Meta title', 'slim-seo' ) }
			std={ ssPostTypes.metadata.title }
			tooltip={ __( 'Recommended length: ≤ 60 characters.', 'slim-seo' ) }
		/>
		<Textarea
			id="slim_seo[description]"
			label={ __( 'Meta description', 'slim-seo' ) }
			std={ ssPostTypes.metadata.description }
			tooltip={ __( 'Recommended length: 50-160 characters.', 'slim-seo' ) }
		/>
		<Image
			id="slim_seo[twitter_image]"
			label={ __( 'Twitter image', 'slim-seo' ) }
			std={ ssPostTypes.metadata.twitter_image }
			mediaPopupTitle={ ssPostTypes.mediaPopupTitle }
			tooltip={ __( 'Recommended size: 1200x600 px. Should have aspect ratio 2:1 with minimum width of 300 px and maximum width of 4096 px.', 'slim-seo' ) }
		/>
		<Image
			id="slim_seo[facebook_image]"
			label={ __( 'Facebook image', 'slim-seo' ) }
			std={ ssPostTypes.metadata.facebook_image }
			tooltip={ __( 'Recommended size: 1200x600 px. Should have aspect ratio 2:1 with minimum width of 300 px and maximum width of 4096 px.', 'slim-seo' ) }
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
			tooltip={ __( 'This setting will apply noindex robots tag to all posts of this post type and exclude the post type from the sitemap.', 'slim-seo' ) }
		/>
	</>;
};

render( <Single />, document.getElementById( 'ss-post-type' ) );