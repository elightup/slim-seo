import { useState } from '@wordpress/element';
import { __ } from "@wordpress/i18n";
import Block from "./Fields/Block";
import Checkbox from "./Fields/Checkbox";

const Taxonomy = ( { id, taxonomy, option, social } ) => {
	const [ noindex, setNoIndex ] = useState( option.noindex || false );
	const baseName = `slim_seo[${ id }]`;

	const handleChange = e => setNoIndex( e.target.checked );

	return <>
		<Checkbox
			id={ `${ baseName }[noindex]` }
			std={ option.noindex }
			label={ __( 'Hide from search results', 'slim-seo' ) }
			description={ __( 'This setting will apply noindex robots tag to all terms of this taxonomy and exclude the taxonomy from the sitemap.', 'slim-seo' ) }
			onChange={ handleChange }
		/>
		{ !noindex &&
			<Block
				baseName={ baseName }
				option={ option }
				label={ taxonomy.labels.singular_name }
				defaultMetas={ ss.defaultTermMetas }
				social={ social }
			/>
		}
	</>;
};

export default Taxonomy;