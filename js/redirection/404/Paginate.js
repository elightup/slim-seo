import { __ } from '@wordpress/i18n';
import ReactPaginate from 'react-paginate';

const Paginate = ( { totalRows, limit, setOffset } ) => {
	const pageCount = Math.ceil( totalRows / limit );

	const handlePageClick = e => {
		const newOffset = ( e.selected * limit ) % totalRows;
		setOffset( newOffset );
	};

	return pageCount > 1 ? (
		<div className='ss-paginate'>
			<ReactPaginate
				breakLabel='...'
				nextLabel={ __( 'Next »', 'slim-seo' ) }
				onPageChange={ handlePageClick }
				pageRangeDisplayed={ 5 }
				pageCount={ pageCount }
				previousLabel={ __( '« Previous', 'slim-seo' ) }
				renderOnZeroPageCount={ null }
			/>
		</div>
	) : '';
};

export default Paginate;