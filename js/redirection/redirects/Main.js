import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import Export from '../components/Export';
import Import from '../components/Import';
import { useApi } from '../helper/misc';
import Items from './Items';
import Update from './Update';

const List = () => {
	const [ bulkAction, setBulkAction ] = useState( '' );
	const [ executeBulkAction, setExecuteBulkAction ] = useState( '' );
	const [ redirectType, setRedirectType ] = useState( '' );
	const [ searchKeyword, setSearchKeyword ] = useState( '' );

	const redirects = useApi( 'redirects' );

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
				<Update redirectToEdit={ {} } linkClassName='button button-primary' />
				{
					Array.isArray( redirects ) && redirects.length > 0 ?
						<>
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

							<span className='ss-filters-right'>
								<span className='ss-export-import'>
									<Export /> | <Import />
								</span>

								<span className='ss-search'>
									<input type='text' className='ss-search-input' value={ searchKeyword } placeholder={ __( 'Search..', 'slim-seo' ) } onKeyDown={ onSearchInputKeyDown } onChange={ e => setSearchKeyword( e.target.value.trim() ) } />
								</span>
							</span>
						</>
						:
						<div className="ss-import">
							<Import />
						</div>
				}
			</div>

			<Items searchKeyword={ searchKeyword } redirectType={ redirectType } executeBulkAction={ executeBulkAction } setExecuteBulkAction={ setExecuteBulkAction } />
		</>
	);
};

export default List;