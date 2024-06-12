import { useState } from "@wordpress/element";
import { CheckboxControl } from '@wordpress/components';
import { __ } from "@wordpress/i18n";
import Text from "./Fields/Text";
import Image from "./Fields/Image";
import Textarea from "./Fields/Textarea";

const Block = ( { baseName, option, label, archive = false } ) => {
	return <>
		<h3 className="archive-title">{ label } { archive ? 'archive page' : 'pages' }</h3>
		<Text id={ `${baseName}[title]` } label={ __( 'Meta title', 'slim-seo' ) } std={ option.title } />
		<Textarea id={ `${baseName}[description]` } label={ __( 'Meta description', 'slim-seo' ) } std={ option.description } />
		<Image id={ `${baseName}[facebook_image]` } label={ __( 'Facebook image', 'slim-seo' ) } std={ option.facebook_image } />
		<Image id={ `${baseName}[twitter_image]` } label={ __( 'Twitter image', 'slim-seo' ) } std={ option.twitter_image } />
	</>;
}

const PostType = ( { id, postType, option, optionArchive } ) => {
	const [ isChecked, setChecked ] = useState( option.noindex || false );
	const baseName = `slim_seo[${ id }]`;
	const baseNameArchive = `slim_seo[${ id }_archive]`;

	return <>
		<CheckboxControl
			label={ __( 'Hide from search results.', 'slim-seo' ) }
			help={ __( 'This setting will apply noindex robots tag to all posts of this post type and exclude the post type from the sitemap.', 'slim-seo' ) }
			name={ `${ baseName }[noindex]` }             
			checked={ isChecked }
			onChange={ setChecked }
		/>
		<Block baseName={ baseName } option={ option } label={ postType.labels.singular_name } />
		{ postType.has_archive && <Block baseName={ baseNameArchive } option={ optionArchive } label={ postType.labels.singular_name } archive={ true } /> }
	</>;
}

export default PostType;