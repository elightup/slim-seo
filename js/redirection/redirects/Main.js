import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { fetcher } from '../helper/misc';
import Add from './Update';
import Items from './Items';

const List = () => {
	const [ bulkAction, setBulkAction ] = useState( '' );
	const [ executeBulkAction, setExecuteBulkAction ] = useState( '' );
	const [ redirectType, setRedirectType ] = useState( '' );
	const [ searchKeyword, setSearchKeyword ] = useState( '' );

	const applyBulkAction = e => {
		e.preventDefault();

		setExecuteBulkAction( bulkAction );
	};

	const onSearchInputKeyDown = e => {
		if ( 'Enter' === e.code ) {
			e.preventDefault();
		}
	};

	return (
		<>
			<div className='ss-filters'>
				<Add linkClassName='button button-primary' />

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
				</span>

				<span className='ss-search'>
					<input type='text' className='ss-search-input' value={ searchKeyword } placeholder={ __( 'Search..', 'slim-seo' ) } onKeyDown={ onSearchInputKeyDown } onChange={ e => setSearchKeyword( e.target.value.trim() ) } />
				</span>
			</div>

			<Items searchKeyword={ searchKeyword } redirectType={ redirectType } executeBulkAction={ executeBulkAction } setExecuteBulkAction={ setExecuteBulkAction }  />
		</>
	);
};

export default List;