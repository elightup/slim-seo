import { useState } from '@wordpress/element';
import { __, sprintf } from "@wordpress/i18n";
import Block from "./Fields/Block";
import Checkbox from "./Fields/Checkbox";

const Taxonomy = ( { id, taxonomy, option } ) => {
	const [ noindex, setNoIndex ] = useState( option.noindex || false );
	const baseName = `slim_seo[${ id }]`;

	const handleChange = e => {
		setNoIndex( e.target.checked );
	}

	return <>
		<Checkbox
			id={ `${ baseName }[noindex]` }
			std={ option.noindex }
			label={ __( 'Hide from search results', 'slim-seo' ) }
			tooltip={ __( 'This setting will apply noindex robots tag to all posts of this taxonomy and exclude the taxonomy from the sitemap.', 'slim-seo' ) }
			onChange={ handleChange }
		/>
		{ ! noindex && 
			<Block
				baseName={ baseName }
				option={ option }
				label={ taxonomy.labels.singular_name }
			/>
		}
	</>;
};

export default Taxonomy;