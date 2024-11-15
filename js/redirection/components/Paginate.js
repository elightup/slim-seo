import { __ } from '@wordpress/i18n';
import ReactPaginate from 'react-paginate';

const Paginate = ( { totalRows, limit, offset, setOffset, setIsCheckAll, setCheckedList } ) => {
	const pageCount = Math.ceil( totalRows / limit );

	const handlePageClick = e => {
		const newOffset = ( e.selected * limit ) % totalRows;
		
		setOffset( newOffset );

		if ( 'undefined' !== typeof setIsCheckAll ) {
			setIsCheckAll( false );
		}

		if ( 'undefined' !== typeof setCheckedList ) {
			setCheckedList( [] );
		}		
	};

	return pageCount > 1 && (
		<div className='ss-paginate'>
			<ReactPaginate
				breakLabel='...'
				nextLabel='»'
				onPageChange={ handlePageClick }
				pageRangeDisplayed={ 3 }
				pageCount={ pageCount }
				previousLabel='«'
				renderOnZeroPageCount={ null }
				forcePage={ offset / limit }
				pageLinkClassName="button"
				previousLinkClassName="button"
				nextLinkClassName="button"
				breakLinkClassName="button"
				activeLinkClassName="button-primary"
			/>
		</div>
	);
};

export default Paginate;