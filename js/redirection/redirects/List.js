import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Tooltip } from '../helper/misc';
import request from '../helper/request';
import Paginate from './Paginate';
import Update from './Update';

const List = () => {
	const LIMIT = 20;
	const [ offset, setOffset ] = useState( 0 );
	const [ redirects, setRedirects ] = useState( [] );
	const [ isLoadingData, setIsLoadingData ] = useState( true );
	const [ bulkAction, setBulkAction ] = useState( '' );
	const [ redirectType, setRedirectType ] = useState( '' );
	const [ searchKeyword, setSearchKeyword ] = useState( '' );
	const [ checkedList, setCheckedList ] = useState( [] );
	const [ isCheckAll, setIsCheckAll ] = useState( false );
	const [ showUpdateRedirectModal, setShowUpdateRedirectModal ] = useState( false );
	const [ redirectToEdit, setRedirectToEdit ] = useState( SSRedirection.defaultRedirect );

	const getRedirects = async () => {
		setIsLoadingData( true );

		await request( 'redirects', {} ).then( result => {
			setIsLoadingData( false );
			setRedirects( [ ...result ] );
		} );
	};

	const checkboxChange = ( e, id ) => {
		if ( !e.target.checked ) {
			setCheckedList( checkedList.filter( item => item !== id ) );
		} else {
			setCheckedList( [ ...checkedList, id ] );
		}
	};

	const checkAll = () => {
		setIsCheckAll( !isCheckAll );

		if ( isCheckAll ) {
			setCheckedList( [] );
		} else {
			setCheckedList( redirects.map( redirect => redirect.id ) );
		}
	};

	const addRedirect = e => {
		e.preventDefault();

		setShowUpdateRedirectModal( true );
		setRedirectToEdit( SSRedirection.defaultRedirect );
	};

	const editRedirect = redirect => {
		return e => {
			e.preventDefault();

			setShowUpdateRedirectModal( true );
			setRedirectToEdit( redirect );
		};
	};

	const deleteRedirects = ids => {
		reset();

		request( 'delete_redirects', { ids }, 'POST' ).then( result => {
			getRedirects();
		} );
	};

	const deleteRedirect = redirect => {
		return e => {
			e.preventDefault();

			if ( !confirm( __( 'Delete redirect ', 'slim-seo' ) + `'${ redirect.from }'?` ) ) {
				return;
			}

			deleteRedirects( [ redirect.id ] );
		};
	};

	const applyBulkAction = e => {
		e.preventDefault();

		if ( 'delete' === bulkAction && checkedList.length ) {
			deleteRedirects( checkedList );
		}
	};

	const filterRedirectType = e => {
		e.preventDefault();

		reset( 'redirect-type' );

		getRedirects().then( result => {
			if ( '' !== redirectType ) {
				setRedirects( prev => prev.filter( redirect => redirect.type == redirectType ) );
			}
		} );
	};

	const search = () => {
		reset( 'search' );

		getRedirects().then( result => {
			if ( '' !== searchKeyword ) {
				setRedirects( prev => prev.filter( redirect => redirect.from.includes( searchKeyword ) || redirect.to.includes( searchKeyword ) ) );
			}
		} );
	};

	const onSearchInputKeyUp = e => {
		if ( 'Enter' === e.code ) {
			search();
		}
	};

	const searchButtonClicked = e => {
		e.preventDefault();

		search();
	};

	const changeEnable = ( e, redirect ) => {
		setRedirects( prev => prev.map( r => {
			if ( r.id == redirect.id ) {
				r.enable = !r.enable;

				request( 'update_redirect', { redirect: r }, 'POST' );
			}

			return r;
		} ) );
	};

	const afterUpdateRedirect = () => {
		getRedirects();
		reset();
	};

	const reset = ( ignore = '' ) => {
		if ( 'bulk-action' !== ignore ) {
			setBulkAction( '' );
		}

		if ( 'redirect-type' !== ignore ) {
			setRedirectType( '' );
		}

		if ( 'search' !== ignore ) {
			setSearchKeyword( '' );
		}

		setIsCheckAll( false );

		setOffset( 0 );

		setCheckedList( [] );
	};

	useEffect( () => {
		getRedirects();
	}, [] );

	return (
		<>
			<div className='ss-filters'>
				<button className='button button-primary' onClick={ addRedirect }>{ __( 'Add Redirect', 'slim-seo' ) }</button>

				<span className='ss-bulk-actions'>
					<select name='ssr_bulk_actions' value={ bulkAction } onChange={ e => setBulkAction( e.target.value ) }>
						<option value=''>{ __( 'Bulk actions', 'slim-seo' ) }</option>
						<option value='delete'>{ __( 'Delete', 'slim-seo' ) }</option>
					</select>
					<button className='button button-secondary' onClick={ applyBulkAction }>{ __( 'Apply', 'slim-seo' ) }</button>
				</span>

				<span className='ss-filter'>
					<select name='ssr_redirect_type' value={ redirectType } onChange={ e => setRedirectType( e.target.value ) }>
						<option value=''>{ __( 'All redirect types', 'slim-seo' ) }</option>
						{ Object.entries( SSRedirection.redirectTypes ).map( ( [ value, label ] ) => <option key={ value } value={ value }>{ label }</option> ) }
					</select>
					<button className='button button-secondary' onClick={ filterRedirectType }>{ __( 'Filter', 'slim-seo' ) }</button>
				</span>

				<span className='ss-search'>
					<input type='text' className='ss-search-input' value={ searchKeyword } placeholder={ __( 'Search..', 'slim-seo' ) } onKeyUp={ onSearchInputKeyUp } onChange={ e => setSearchKeyword( e.target.value.trim() ) } />
					<button className='button button-secondary' onClick={ searchButtonClicked }>{ __( 'Search', 'slim-seo' ) }</button>
				</span>
			</div>

			<table className='ss-table'>
				<thead>
					<tr>
						<th className='ss-redirect__checkbox'><input type='checkbox' checked={ isCheckAll } onChange={ checkAll } /></th>
						<th className='ss-redirect__type'>
							{ __( 'Type', 'slim-seo' ) }
							<Tooltip content={ __( 'Redirect type', 'slim-seo' ) } />
						</th>
						<th className='ss-redirect__url'>
							{ __( 'From URL', 'slim-seo' ) }
							<Tooltip content={ __( 'URL to redirect', 'slim-seo' ) } />
						</th>
						<th className='ss-redirect__url'>
							{ __( 'To URL', 'slim-seo' ) }
							<Tooltip content={ __( 'Destination URL', 'slim-seo' ) } />
						</th>
						<th className='ss-redirect__note'>
							{ __( 'Note', 'slim-seo' ) }
							<Tooltip content={ __( 'Something to reminds you about the redirects', 'slim-seo' ) } />
						</th>
						<th className='ss-redirect__enable'>
							{ __( 'Enable', 'slim-seo' ) }
							<Tooltip content={ __( 'Is the redirect enabled?', 'slim-seo' ) } />
						</th>
						<th className='ss-redirect__actions'>{ __( 'Actions', 'slim-seo' ) }</th>
					</tr>
				</thead>

				<tbody>
					{
						isLoadingData
							? <tr><td colSpan='7'><div className='ss-loader'></div></td></tr>
							: redirects.length > 0
								? redirects.slice( offset, offset + LIMIT ).map( redirect => (
									<tr key={ redirect.id }>
										<td className='ss-redirect__checkbox'>
											<input type='checkbox' value={ redirect.id } checked={ checkedList.includes( redirect.id ) } onChange={ e => checkboxChange( e, redirect.id ) } />
										</td>
										<td className='ss-redirect__type'>{ redirect.type }</td>
										<td className='ss-redirect__url'>
											{ redirect.from }
											<small>{ SSRedirection.conditionOptions[ redirect.condition ] }</small>
										</td>
										<td className='ss-redirect__url'>
											{ redirect.to }
										</td>
										<td className='ss-redirect__note'>{ redirect.note }</td>
										<td className='ss-redirect__enable'>
											<label className='ss-toggle'>
												<input className='ss-toggle__checkbox' type='checkbox' defaultChecked={ 1 == redirect.enable } onChange={ e => changeEnable( e, redirect ) } />
												<div className='ss-toggle__switch'></div>
											</label>
										</td>
										<td className='ss-redirect__actions'>
											<a href='#' onClick={ editRedirect( redirect ) } title={ __( 'Edit', 'slim-seo' ) }><span className='dashicons dashicons-edit'></span></a>
											<a href='#' onClick={ deleteRedirect( redirect ) } title={ __( 'Delete', 'slim-seo' ) }><span className='dashicons dashicons-trash'></span></a>
										</td>
									</tr>
								) )
								: <tr><td colSpan='7'><p>{ __( 'No redirects found.', 'slim-seo' ) }</p></td></tr>
					}
				</tbody>

				<tfoot>
					<tr>
						<th className='ss-redirect__checkbox'><input type='checkbox' checked={ isCheckAll } onChange={ checkAll } /></th>
						<th className='ss-redirect__type'>
							{ __( 'Type', 'slim-seo' ) }
							<Tooltip content={ __( 'Redirect type', 'slim-seo' ) } />
						</th>
						<th className='ss-redirect__url'>
							{ __( 'From URL', 'slim-seo' ) }
							<Tooltip content={ __( 'URL to redirect', 'slim-seo' ) } />
						</th>
						<th className='ss-redirect__url'>
							{ __( 'To URL', 'slim-seo' ) }
							<Tooltip content={ __( 'Destination URL', 'slim-seo' ) } />
						</th>
						<th className='ss-redirect__note'>
							{ __( 'Note', 'slim-seo' ) }
							<Tooltip content={ __( 'Something to reminds you about the redirects', 'slim-seo' ) } />
						</th>
						<th className='ss-redirect__enable'>
							{ __( 'Enable', 'slim-seo' ) }
							<Tooltip content={ __( 'Is the redirect enabled?', 'slim-seo' ) } />
						</th>
						<th className='ss-redirect__actions'>{ __( 'Actions', 'slim-seo' ) }</th>
					</tr>
				</tfoot>
			</table>

			{ isLoadingData ? '' : <Paginate totalRows={ redirects.length } limit={ LIMIT } setOffset={ setOffset } /> }

			{ showUpdateRedirectModal && <Update redirectToEdit={ redirectToEdit } callback={ afterUpdateRedirect } setShowUpdateRedirectModal={ setShowUpdateRedirectModal } /> }
		</>
	);
};

export default List;