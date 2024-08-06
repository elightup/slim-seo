import { render } from '@wordpress/element';
import { CheckboxControl } from '@wordpress/components';
import { __ } from "@wordpress/i18n";
import Text from "./components/Fields/Text";
import Image from "./components/Fields/Image";
import Textarea from "./components/Fields/Textarea";
import Checkbox from "./components/Fields/Checkbox";

const Single = () => {
	return <>
		<h2 className="title">{ ssPostType.title }</h2>
		<Text
			id="slim_seo[title]"
			label={ __( 'Meta title', 'slim-seo' ) }
			std={ ssPostType.data.title }
			description={ __( 'Recommended length: ≤ 60 characters.', 'slim-seo' ) }
			check={ true }
			max={ 60 }
		/>
		<Textarea
			id="slim_seo[description]"
			label={ __( 'Meta description', 'slim-seo' ) }
			std={ ssPostType.data.description }
			description={ __( 'Recommended length: 50-160 characters. Leave empty to autogenerate from post exceprt (if available) or post content.', 'slim-seo' ) }
			check={ true }
			min={ 50 }
			max={ 160 }
			truncate={ true }
		/>
		<Image
			id="slim_seo[facebook_image]"
			label={ __( 'Facebook image', 'slim-seo' ) }
			std={ ssPostType.data.facebook_image }
			description={ __( 'Recommended size: 1200x630 px.', 'slim-seo' ) }
		/>
		<Image
			id="slim_seo[twitter_image]"
			label={ __( 'Twitter image', 'slim-seo' ) }
			std={ ssPostType.data.twitter_image }
			mediaPopupTitle={ ssPostType.mediaPopupTitle }
			description={ __( 'Recommended size: 1200x600 px. Should have aspect ratio 2:1 with minimum width of 300 px and maximum width of 4096 px.', 'slim-seo' ) }
		/>
		<Text
			id="slim_seo[canonical]"
			label={ __( 'Canonical URL', 'slim-seo' ) }
			std={ ssPostType.data.canonical }
		/>
		<Checkbox
			id="slim_seo[noindex]"
			label={ __( ' Hide from search results ', 'slim-seo' ) }
			std={ ssPostType.data.noindex }
			description={ __( 'This setting will apply noindex robots tag to this post of this post type and exclude the post type from the sitemap.', 'slim-seo' ) }
		/>
	</>;
};

render( <Single />, document.getElementById( 'ss-post-type' ) );