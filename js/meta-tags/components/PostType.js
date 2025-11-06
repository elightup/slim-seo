import { useState } from '@wordpress/element';
import { __, sprintf } from "@wordpress/i18n";
import Block from "./Fields/Block";
import Checkbox from "./Fields/Checkbox";
import PostTypeWithArchivePage from "./Fields/PostTypeWithArchivePage";

const PostType = ( { id, postType, option, optionArchive, social } ) => {
	const [ noindex, setNoIndex ] = useState( option.noindex || false );
	const baseName = `slim_seo[${ id }]`;
	const baseNameArchive = `slim_seo[${ id }_archive]`;

	const handleChange = e => setNoIndex( e.target.checked );

	return <>
		<Checkbox
			id={ `${ baseName }[noindex]` }
			std={ option.noindex }
			label={ __( 'Hide from search results', 'slim-seo' ) }
			description={ __( 'This setting will apply noindex robots tag to all posts of this post type and exclude the post type from the sitemap.', 'slim-seo' ) }
			onChange={ handleChange }
		/>
		{ !noindex &&
			<Block
				baseName={ baseName }
				option={ option }
				label={ sprintf( __( 'Singular %s', 'slim-seo' ), postType.labels.singular_name.toLowerCase() ) }
				defaultMetas={ ss.defaultPostMetas.single }
				social={ social }
				facebookImageInstruction={ __( 'Leave empty to use the featured image or the first image in the post content.', 'slim-seo' ) }
			/>
		}
		{ !noindex &&
			(
				ss.postTypesWithArchivePage.hasOwnProperty( id )
					? <PostTypeWithArchivePage
						id={ id }
						postType={ ss.postTypesWithArchivePage[ id ] }
						label={ sprintf( __( '%s archive', 'slim-seo' ), postType.labels.singular_name ) }
					/>
					: postType.has_archive &&
					<Block
						baseName={ baseNameArchive }
						option={ optionArchive }
						defaultMetas={ ss.defaultPostMetas.archive }
						label={ sprintf( __( '%s archive', 'slim-seo' ), postType.labels.singular_name ) }
						archive={ true }
						social={ social }
					/>
			)
		}
	</>;
};

export default PostType;