import { Modal } from '@wordpress/components';
import { useReducer, useState } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { fetcher } from "../helper/misc";
import Sample from './Sample';

const Import = () => {
	const [ upload, toggleUpload ] = useReducer( upload => !upload, false );
	const [ file, setFile ] = useState();
	const [ loading, toggleLoading ] = useReducer( loading => !loading, false );
	const text = __( 'Import', 'slim-seo' );

	const handleChange = e => setFile( e.target.files[ 0 ] );

	const submit = () => {
		toggleLoading();

		const reader = new FileReader();

		reader.readAsText( file );

		reader.onload = e => {
			fetcher( 'import', { text: reader.result }, 'POST' ).then( result => {
				if ( result ) {
					location.reload();
				} else {
					alert( __( 'Invalid data format. Please try again.', 'slim-seo' ) );

					toggleLoading();
				}
			} );
		}		
	};

	return (
		<>
			<a href='#' title={ text } onClick={ toggleUpload }>{ text }</a>
			
			{
				upload
				&& <Modal title={ __( 'Upload CSV', 'slim-seo' ) } overlayClassName='ss-modal ss-upload' onRequestClose={ toggleUpload }>
					<input type="file" accept="*.csv" onChange={ handleChange } />
					<button type="button" className="button-primary" onClick={ submit } disabled={ !file || loading }>
						{
							loading ? __( 'Submitting...', 'slim-seo' ) : __( 'Submit', 'slim-seo' )
						}
					</button>
					<Sample />			
				</Modal>
			}
		</>
	);
};

export default Import;