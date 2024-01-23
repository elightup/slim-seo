import { useReducer } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { fetcher, exportCSV } from "../helper/misc";

const Export = () => {
	const [ exporting, toggleExporting ] = useReducer( exporting => !exporting, false );
	const text = exporting ? __( 'Exporting', 'slim-seo' ) : __( 'Export', 'slim-seo' );

	const submit = e => {
		e.preventDefault();

		toggleExporting();
	
		fetcher( 'export' ).then( result => {
			if ( result ) {
				exportCSV( result.filename, result.data );
			} else {
				alert( __( 'No redirect found!. Cannot export', 'slim-seo' ) );
			}

			toggleExporting();
		} );
	};

	return (
		<a href='#' title={ text } onClick={ submit } disabled={ exporting }>
			{ text }
		</a>
	);
};

export default Export;
