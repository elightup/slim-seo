import { __ } from '@wordpress/i18n';
import { getFullURL } from '../helper/misc';
import Update from '../redirects/Update';

const Item = ( { log, deleteLog } ) => {
	const deleteLogClicked = e => {
		e.preventDefault();

		if ( !confirm( __( 'Delete log ', 'slim-seo' ) + `'${ log.url }'?` ) ) {
			return;
		}

		deleteLog( log );
	};

	return (
		<tr>
			<td className='ss-log__url'><a href={ getFullURL( log.url ) } target='_blank'>{ log.url }</a></td>
			<td className='ss-log__hit'>{ log.hit }</td>
			<td className='ss-log__created_at'>{ log.created_at }</td>
			<td className='ss-log__updated_at'>{ log.updated_at }</td>
			<td className='ss-log__actions'>
				<Update redirectToEdit={ { from: log.url } }><span className='dashicons dashicons-welcome-add-page'></span></Update>
				<a href='#' onClick={ deleteLogClicked } title={ __( 'Delete', 'slim-seo' ) }><span className='dashicons dashicons-trash'></span></a>
			</td>
		</tr>
	);
};

export default Item;