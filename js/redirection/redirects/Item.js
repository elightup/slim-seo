import { useState } from '@wordpress/element';
import { getFullURL, fetcher } from '../helper/misc';
import { __ } from '@wordpress/i18n';
import Update from './Update';

const Item = ( { redirectItem, checkedList, setCheckedList, deleteRedirects, updateRedirects } ) => {
	const [ redirect, setRedirect ] = useState( redirectItem );

	const checkboxChange = e => {
		if ( !e.target.checked ) {
			setCheckedList( checkedList.filter( item => item !== redirect.id ) );
		} else {
			setCheckedList( [ ...checkedList, redirect.id ] );
		}
	};

	const changeEnable = e => {
		const newRedirect = { ...redirect, enable: !redirect.enable };

		setRedirect( newRedirect );

		fetcher( 'update_redirect', { redirect: newRedirect }, 'POST' ).then( result => updateRedirects( newRedirect ) );
	};

	const updateRedirect = redirect => {
		const newRedirect = { ...redirect };

		setRedirect( newRedirect );

		updateRedirects( newRedirect );
	};

	const deleteRedirect = e => {
		e.preventDefault();

		if ( !confirm( __( 'Delete redirect ', 'slim-seo' ) + `'${ redirect.from }'?` ) ) {
			return;
		}

		deleteRedirects( [ redirect.id ] );
	};

	return (
		<tr>
			<td className='ss-redirect__checkbox'>
				<input type='checkbox' value={ redirect.id } checked={ checkedList.includes( redirect.id ) } onChange={ checkboxChange } />
			</td>
			<td className='ss-redirect__type'>{ redirect.type }</td>
			<td className='ss-redirect__url'>
				{ 'exact-match' === redirect.condition ? <a href={ getFullURL( redirect.from ) } target='_blank'>{ redirect.from }</a> : redirect.from }
				<small>{ SSRedirection.conditionOptions[ redirect.condition ] }</small>
			</td>
			<td className='ss-redirect__url'>
				<a href={ getFullURL( redirect.to ) } target='_blank'>{ redirect.to }</a>
			</td>
			<td className='ss-redirect__note'>{ redirect.note }</td>
			<td className='ss-redirect__enable'>
				<label className='ss-toggle'>
					<input type='checkbox' checked={ 1 == redirect.enable } onChange={ changeEnable } />
					<div className='ss-toggle__switch'></div>
				</label>
			</td>
			<td className='ss-redirect__actions'>
				<Update redirectToEdit={ redirect } callback={ updateRedirect }><span className='dashicons dashicons-edit'></span></Update>
				<a href='#' onClick={ deleteRedirect } title={ __( 'Delete', 'slim-seo' ) }><span className='dashicons dashicons-trash'></span></a>
			</td>
		</tr>
	);
}

export default Item;