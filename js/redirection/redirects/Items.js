import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import Paginate from '../components/Paginate';
import { fetcher, useApi } from '../helper/misc';
import Header from './Header';
import Item from './Item';

const Items = ( { searchKeyword, redirectType, executeBulkAction, setExecuteBulkAction } ) => {
	const LIMIT = 20;
	const [ offset, setOffset ] = useState( 0 );
	const [ checkedList, setCheckedList ] = useState( [] );
	const [ isCheckAll, setIsCheckAll ] = useState( false );
	const [ remountPaginate, setRemountPaginate ] = useState( 0 );
	const { result: redirects, mutate } = useApi( 'redirects', {}, { returnMutate: true } );

	const checkAll = () => {
		setIsCheckAll( !isCheckAll );

		if ( isCheckAll ) {
			setCheckedList( [] );
		} else {
			setCheckedList( redirects.map( redirect => redirect.id ) );
		}
	};

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
		return <span>{ __( 'No redirects found.', 'slim-seo' ) }</span>;
	}

	let filteredRedirects = [ ...redirects ];

	if ( searchKeyword ) {
		filteredRedirects = filteredRedirects.filter( redirect => redirect.from.includes( searchKeyword ) || redirect.to.includes( searchKeyword ) );
	}

	if ( redirectType ) {
		filteredRedirects = filteredRedirects.filter( redirect => redirect.type == redirectType );
	}

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
					{ filteredRedirects.slice( offset, offset + LIMIT ).map( redirect => <Item key={ redirect.id } redirectItem={ redirect } checkedList={ checkedList } setCheckedList={ setCheckedList } deleteRedirects={ deleteRedirects } updateRedirects={ updateRedirects } /> ) }
				</tbody>

				<tfoot>
					<Header isCheckAll={ isCheckAll } checkAll={ checkAll } />
				</tfoot>
			</table>

			<Paginate key={ remountPaginate } totalRows={ filteredRedirects.length } limit={ LIMIT } setOffset={ setOffset } />
		</>
	);
};

export default Items;