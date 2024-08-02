import { useEffect, useState } from '@wordpress/element';
import { __, sprintf } from "@wordpress/i18n";
import PostTypeWithArchivePage from "./Fields/PostTypeWithArchivePage";
import Checkbox from "./Fields/Checkbox";
import Image from "./Fields/Image";
import Text from "./Fields/Text";
import Textarea from "./Fields/Textarea";

const Block = ( { baseName, option, label } ) => <>
	<h3>{ label }</h3>
	<Text
		id={ `${ baseName }[title]` }
		label={ __( 'Meta title', 'slim-seo' ) }
		std={ option.title }
		description={ __( 'Recommended length: ≤ 60 characters.', 'slim-seo' ) }
	/>
	<Textarea
		id={ `${ baseName }[description]` }
		label={ __( 'Meta description', 'slim-seo' ) }
		std={ option.description }
		description={ __( 'Recommended length: 50-160 characters. Leave empty to autogenerate from post exceprt (if available) or post content.', 'slim-seo' ) }
	/>
	<Image
		id={ `${ baseName }[facebook_image]` }
		label={ __( 'Facebook image', 'slim-seo' ) }
		std={ option.facebook_image }
		mediaPopupTitle={ ssPostTypes.mediaPopupTitle }
		description={ __( 'Recommended size: 1200x630 px.', 'slim-seo' ) }
	/>
	<Image
		id={ `${ baseName }[twitter_image]` }
		label={ __( 'Twitter image', 'slim-seo' ) }
		std={ option.twitter_image }
		mediaPopupTitle={ ssPostTypes.mediaPopupTitle }
		description={ __( 'Recommended size: 1200x600 px. Should have aspect ratio 2:1 with minimum width of 300 px and maximum width of 4096 px.', 'slim-seo' ) }
	/>
</>;

const PostType = ( { id, postType, option, optionArchive } ) => {
	const [ noindex, setNoIndex ] = useState( option.noindex || false );
	const baseName = `slim_seo[${ id }]`;
	const baseNameArchive = `slim_seo[${ id }_archive]`;

	const handleChange = e => {
		setNoIndex( e.target.checked );
	}

	return <>
		<Checkbox
			id={ `${ baseName }[noindex]` }
			std={ option.noindex }
			label={ __( 'Hide from search results', 'slim-seo' ) }
			tooltip={ __( 'This setting will apply noindex robots tag to all posts of this post type and exclude the post type from the sitemap.', 'slim-seo' ) }
			onChange={ handleChange }
		/>
		{ ! noindex && 
			<Block
				baseName={ baseName }
				option={ option }
				label={ sprintf( __( 'Singular %s page', 'slim-seo' ), postType.labels.singular_name.toLowerCase() ) }
			/>
		}
		{ ! noindex &&
			(
				ssPostTypes.postTypesWithArchivePage.hasOwnProperty( id )
				?   <PostTypeWithArchivePage
						id={ id }
						postType={ ssPostTypes.postTypesWithArchivePage[ id ] }
						label={ sprintf( __( '%s archive page', 'slim-seo' ), postType.labels.singular_name ) }
					/>
				:   postType.has_archive &&
					<Block
						baseName={ baseNameArchive }
						option={ optionArchive }
						label={ sprintf( __( '%s archive page', 'slim-seo' ), postType.labels.singular_name ) }
						archive={ true }
					/>
			)
		}
	</>;
};

export default PostType;