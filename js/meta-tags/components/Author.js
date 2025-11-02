import { useState } from '@wordpress/element';
import { __ } from "@wordpress/i18n";
import Block from "./Fields/Block";
import Checkbox from "./Fields/Checkbox";

const Author = ( { option, social } ) => {
	const [ noindex, setNoIndex ] = useState( option.noindex || false );
	const baseName = `slim_seo[author]`;

	const handleChange = e => setNoIndex( e.target.checked );

	return <>
		<Checkbox
			id={ `${ baseName }[noindex]` }
			std={ option.noindex }
			label={ __( 'Hide from search results', 'slim-seo' ) }
			description={ __( 'This setting will apply noindex robots tag to author pages.', 'slim-seo' ) }
			onChange={ handleChange }
		/>
		{ !noindex &&
			<Block
				baseName={ baseName }
				label={ __( 'Author archive page', 'slim-seo' ) }
				option={ option }
				defaultMetas={ ss.defaultAuthorMetas }
				social={ social }
			/>
		}
	</>;
};

export default Author;