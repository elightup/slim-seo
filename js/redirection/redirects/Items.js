import { useEffect, useRef, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { DndContext, closestCenter,	KeyboardSensor,	PointerSensor, useSensor, useSensors } from '@dnd-kit/core';
import { SortableContext, sortableKeyboardCoordinates, verticalListSortingStrategy, arrayMove } from '@dnd-kit/sortable';
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
	const [ orderBy, setOrderBy ] = useState( '' );
	const [ order, setOrder ] = useState( 'DESC' );
	const [ remountPaginate, setRemountPaginate ] = useState( 0 );
	const { result: redirects, mutate } = useApi( 'redirects', {}, { returnMutate: true } );
	const prevRedirectsCount = useRef( 0 );
	const isDragEnabled = ! orderBy;

	useEffect( () => {
		const count = redirects?.length ?? 0;

		if ( count > prevRedirectsCount.current ) {
			setOffset( 0 );
			setRemountPaginate( Date.now() );
		}

		prevRedirectsCount.current = count;
	}, [ redirects?.length ] );

	const sensors = useSensors(
		useSensor( PointerSensor ),
		useSensor( KeyboardSensor, { coordinateGetter: sortableKeyboardCoordinates } )
	);

	const deleteRedirects = ( ids = [] ) => {
		fetcher( 'delete_redirects', { ids }, 'POST' ).then( () => {
			mutate(
				redirects.filter( r => ! ids.includes( r.id ) ),
				{ revalidate: false }
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
			{ revalidate: false }
		);
	};

	const handleDragEnd = event => {
		const { active, over } = event;

		if ( ! over || active.id === over.id ) {
			return;
		}

		const oldIndex = redirects.findIndex( r => r.id === active.id );
		const newIndex = redirects.findIndex( r => r.id === over.id );
		const reordered = arrayMove( redirects, oldIndex, newIndex );

		mutate( reordered, { revalidate: false } );

		fetcher( 'reorder_redirects', { ids: reordered.map( r => r.id ) }, 'POST' );
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

	if ( orderBy ) {
		filteredRedirects.sort( ( redirect1, redirect2 ) => {
			const val1 = redirect1[ orderBy ];
			const val2 = redirect2[ orderBy ];

			if ( val1 == null ) return order === 'DESC' ? 1 : -1;
			if ( val2 == null ) return order === 'DESC' ? -1 : 1;

			let result;

			if ( typeof val1 === 'string' ) {
				result = val1.localeCompare( val2 );
			} else {
				result = val1 - val2;
			}

			return order === 'DESC' ? -result : result;
		} );
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

	const displayedRedirects = filteredRedirects.slice( offset, offset + limit );
	const sortableIds = isDragEnabled ? displayedRedirects.map( r => r.id ) : [];

	return (
		<>
			<DndContext sensors={ sensors } collisionDetection={ closestCenter } onDragEnd={ isDragEnabled ? handleDragEnd : undefined }>
				<table className='ss-table'>
					<thead>
						<Header
							orderBy={ orderBy }
							setOrderBy={ setOrderBy }
							order={ order }
							setOrder={ setOrder }
							isCheckAll={ isCheckAll }
							checkAll={ checkAll }
							isDragEnabled={ isDragEnabled } />
					</thead>

					<SortableContext items={ sortableIds } strategy={ verticalListSortingStrategy }>
						<tbody>
							{
								displayedRedirects.map( redirect => (
									<Item
										key={ redirect.id }
										redirectItem={ redirect }
										checkedList={ checkedList }
										setCheckedList={ setCheckedList }
										deleteRedirects={ deleteRedirects }
										updateRedirects={ updateRedirects }
										isDragEnabled={ isDragEnabled } />
								) )
							}
						</tbody>
					</SortableContext>

					<tfoot>
						<Header
							orderBy={ orderBy }
							setOrderBy={ setOrderBy }
							order={ order }
							setOrder={ setOrder }
							isCheckAll={ isCheckAll }
							checkAll={ checkAll }
							isDragEnabled={ isDragEnabled } />
					</tfoot>
				</table>
			</DndContext>

			<div className='ss-redirects-footer'>
				<Limit limit={ limit } setLimit={ setLimit } total={ filteredRedirects.length } setOffset={ setOffset } setIsCheckAll={ setIsCheckAll } setCheckedList={ setCheckedList } />
				<Paginate key={ remountPaginate } totalRows={ filteredRedirects.length } limit={ limit } offset={ offset } setOffset={ setOffset } setIsCheckAll={ setIsCheckAll } setCheckedList={ setCheckedList } />
			</div>
		</>
	);
};

export default Items;