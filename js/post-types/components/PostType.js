import { __ } from "@wordpress/i18n";
import Text from "./Fields/Text";
import Image from "./Fields/Image";
import Textarea from "./Fields/Textarea";
import Checkbox from "./Fields/Checkbox";

const Block = ( { baseName, option, label, archive = false } ) => {
	return <>
		<h3 className="archive-title">{ label } { archive ? 'archive page' : 'pages' }</h3>
		<Text id={ `${baseName}[title]` } label={ __( 'Meta title', 'slim-seo' ) } std={ option.title } description={ __( 'Recommended length: ≤ 60 characters.', 'slim-seo' ) } />
		<Textarea id={ `${baseName}[description]` } label={ __( 'Meta description', 'slim-seo' ) } std={ option.description } description={ __( 'Recommended length: 50-160 characters.', 'slim-seo' ) } />
		<Image id={ `${baseName}[facebook_image]` } label={ __( 'Facebook image', 'slim-seo' ) } std={ option.facebook_image } mediaPopupTitle={ ssPostTypes.mediaPopupTitle } description={ __( 'Recommended size: 1200x600 px. Should have aspect ratio 2:1 with minimum width of 300 px and maximum width of 4096 px.', 'slim-seo' ) } />
		<Image id={ `${baseName}[twitter_image]` } label={ __( 'Twitter image', 'slim-seo' ) } std={ option.twitter_image } mediaPopupTitle={ ssPostTypes.mediaPopupTitle } description={ __( 'Recommended size: 1200x600 px. Should have aspect ratio 2:1 with minimum width of 300 px and maximum width of 4096 px.', 'slim-seo' ) } />
	</>;
}

const PostType = ( { id, postType, option, optionArchive } ) => {
	const baseName = `slim_seo[${ id }]`;
	const baseNameArchive = `slim_seo[${ id }_archive]`;

	return <>
		<Checkbox
			id={ `${baseName}[noindex]` }
			std={ option.noindex }
			label={ __( 'Hide from search results.', 'slim-seo' ) }
			tooltip={ __( 'This setting will apply noindex robots tag to all posts of this post type and exclude the post type from the sitemap.', 'slim-seo' ) }
		/>
		<Block baseName={ baseName } option={ option } label={ postType.labels.singular_name } />
		{ postType.has_archive &&
			<Block baseName={ baseNameArchive } option={ optionArchive } label={ postType.labels.singular_name } archive={ true } />
		}
	</>;
}

export default PostType;