import { createRoot } from '@wordpress/element';
import { __ } from "@wordpress/i18n";
import Checkbox from "./components/Fields/Checkbox";
import Description from "./components/Fields/Description";
import FacebookImage from './components/Fields/FacebookImage';
import PostTitle from './components/Fields/PostTitle';
import TermTitle from './components/Fields/TermTitle';
import TermDescription from "./components/Fields/TermDescription";
import Text from "./components/Fields/Text";
import TwitterImage from './components/Fields/TwitterImage';
import { request } from "./functions";

const Single = () => {
	const isTermPage = document.querySelector( '#edittag' );
	const facebookImageInstruction = isTermPage ? '' : __( 'Leave empty to use the featured image or the first image in the post content.', 'slim-seo' );

	return <>
		<h2 className="title">{ ss.single.title }</h2>
		{
			isTermPage
				? <TermTitle
					id="slim_seo[title]"
					std={ ss.single.data.title }
				/>
				: <PostTitle
					id="slim_seo[title]"
					std={ ss.single.data.title }
				/>
		}
		{
			isTermPage
				? <TermDescription
					id="slim_seo[description]"
					std={ ss.single.data.description }
					description={ __( 'Leave empty to autogenerate from the term description.', 'slim-seo' ) }
				/>
				: <Description
					id="slim_seo[description]"
					std={ ss.single.data.description }
					description={ __( 'Leave empty to autogenerate from the post exceprt (if available) or the post content.', 'slim-seo' ) }
				/>
		}
		<FacebookImage
			id="slim_seo[facebook_image]"
			std={ ss.single.data.facebook_image }
			description={ facebookImageInstruction }
		/>
		<TwitterImage
			id="slim_seo[twitter_image]"
			std={ ss.single.data.twitter_image }
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