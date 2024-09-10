import { createRoot } from '@wordpress/element';
import { __ } from "@wordpress/i18n";
import Checkbox from "./components/Fields/Checkbox";
import Description from "./components/Fields/Description";
import Image from "./components/Fields/Image";
import TermDescription from "./components/Fields/TermDescription";
import Text from "./components/Fields/Text";
import Title from "./components/Fields/Title";

const Single = () => {
	isTermPage = document.querySelector( '#edittag' );

	return <>
		<h2 className="title">{ ss.single.title }</h2>
		<Title
			id="slim_seo[title]"
			label={ __( 'Meta title', 'slim-seo' ) }
			std={ ss.single.data.title }
			description={ __( 'Recommended length: ≤ 60 characters.', 'slim-seo' ) }
		/>
		{
			isTermPage
				? <TermDescription
					id="slim_seo[description]"
					label={ __( 'Meta description', 'slim-seo' ) }
					std={ ss.single.data.description }
					description={ __( 'Recommended length: 50-160 characters. Leave empty to autogenerate from the term description.', 'slim-seo' ) }
				/>
				: <Description
					id="slim_seo[description]"
					label={ __( 'Meta description', 'slim-seo' ) }
					std={ ss.single.data.description }
					description={ __( 'Recommended length: 50-160 characters. Leave empty to autogenerate from the post exceprt (if available) or the post content.', 'slim-seo' ) }
				/>
		}
		<Image
			id="slim_seo[facebook_image]"
			label={ __( 'Facebook image', 'slim-seo' ) }
			std={ ss.single.data.facebook_image }
			description={ __( 'Recommended size: 1200x630 px. Should have 1.91:1 aspect ratio with width ≥ 600 px.', 'slim-seo' ) }
		/>
		<Image
			id="slim_seo[twitter_image]"
			label={ __( 'Twitter image', 'slim-seo' ) }
			std={ ss.single.data.twitter_image }
			mediaPopupTitle={ ss.mediaPopupTitle }
			description={ __( 'Recommended size: 1200x600 px. Should have 2:1 aspect ratio with width ≥ 300 px and ≤ 4096 px.', 'slim-seo' ) }
		/>
		<Text
			id="slim_seo[canonical]"
			label={ __( 'Canonical URL', 'slim-seo' ) }
			std={ ss.single.data.canonical }
		/>
		<Checkbox
			id="slim_seo[noindex]"
			label={ __( ' Hide from search results ', 'slim-seo' ) }
			std={ ss.single.data.noindex }
			description={ __( 'This setting will apply noindex robots tag to this post and exclude it from the sitemap.', 'slim-seo' ) }
		/>
	</>;
};

const container = document.getElementById( 'ss-single' );
const root = createRoot( container );
root.render( <Single /> );