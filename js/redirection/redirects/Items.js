import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { fetcher, useApi } from '../helper/misc';
import Header from './Header';
import Item from './Item';
import Limit from '../components/Limit';
import Paginate from '../components/Paginate';

const Items = ( { searchKeyword, redirectType, executeBulkAction, setExecuteBulkAction } ) => {
	const [ limit, setLimit ] = useState( 20 );
	const [ offset, setOffset ] = useState( 0 );
	const [ checkedList, setCheckedList ] = useState( [] );
	const [ isCheckAll, setIsCheckAll ] = useState( false );
	const [ remountPaginate, setRemountPaginate ] = useState( 0 );
	const { result: redirects, mutate } = useApi( 'redirects', {}, { returnMutate: true } );

	const deleteRedirects = ( ids = [] ) => {
		fetcher( 'delete_redirects', { ids }, 'POST' ).then( result => {
			mutate(
				redirects.filter( r => {
					return ! ids.includes( r.id );
				} ),
				{
					revalidate: false
				}
			);
		} );
	};

	const updateRedirects = redirect => {
		mutate(
			redirects.map( r => {
				if ( r.id == redirect.id ) {
					r = redirect;
				}

				return r;
			} ),
			{
				revalidate: false
			}
		);
	};

	useEffect( () => {
		if ( 'delete' === executeBulkAction ) {
			deleteRedirects( checkedList );

			setExecuteBulkAction( '' );
		}
	}, [ executeBulkAction ] );

	useEffect( () => {
		setOffset( 0 );
		setRemountPaginate( Date.now() );
	}, [ searchKeyword, redirectType ] );

	if ( undefined === redirects ) {
		return <div className='ss-loader'></div>;
	} else if ( 0 === Object.keys( redirects ).length ) {
		return;
	}

	let filteredRedirects = [ ...redirects ];

	if ( searchKeyword ) {
		filteredRedirects = filteredRedirects.filter( redirect => redirect.from.includes( searchKeyword ) || redirect.to.includes( searchKeyword ) );
	}

	if ( redirectType ) {
		filteredRedirects = filteredRedirects.filter( redirect => redirect.type == redirectType );
	}

	const checkAll = () => {
		setIsCheckAll( !isCheckAll );

		if ( isCheckAll ) {
			setCheckedList( [] );
		} else {
			setCheckedList( filteredRedirects.slice( offset, offset + limit ).map( redirect => redirect.id ) );
		}
	};

	if ( !filteredRedirects.length ) {
		return <span>{ __( 'No redirects found.', 'slim-seo' ) }</span>;
	}
	
	return (
		<>
			<table className='ss-table'>
				<thead>
					<Header isCheckAll={ isCheckAll } checkAll={ checkAll } />
				</thead>

				<tbody>
					{ filteredRedirects.slice( offset, offset + limit ).map( redirect => <Item key={ redirect.id } redirectItem={ redirect } checkedList={ checkedList } setCheckedList={ setCheckedList } deleteRedirects={ deleteRedirects } updateRedirects={ updateRedirects } /> ) }
				</tbody>

				<tfoot>
					<Header isCheckAll={ isCheckAll } checkAll={ checkAll } />
				</tfoot>
			</table>

			<div className='ss-redirects-footer'>
				<Limit limit={ limit } setLimit={ setLimit } total={ filteredRedirects.length } setOffset={ setOffset } setIsCheckAll={ setIsCheckAll } setCheckedList={ setCheckedList } />
				<Paginate key={ remountPaginate } totalRows={ filteredRedirects.length } limit={ limit } offset={ offset } setOffset={ setOffset } setIsCheckAll={ setIsCheckAll } setCheckedList={ setCheckedList } />
			</div>			
		</>
	);
};

export default Items;