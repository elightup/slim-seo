import { useEffect, useState } from '@wordpress/element';
import { __, sprintf } from "@wordpress/i18n";
import PostTypeWithArchivePage from "./Fields/PostTypeWithArchivePage";
import Block from "./Fields/Block";
import Checkbox from "./Fields/Checkbox";

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