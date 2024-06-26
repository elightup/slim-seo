import { __, _n, sprintf } from '@wordpress/i18n';

const Limit = ( { limit, setLimit, total, setOffset, setIsCheckAll, setCheckedList } ) => {
	const handleChange = e => {
		setLimit( parseInt( e.target.value ) );

		setOffset( 0 );
		setIsCheckAll( false );
		setCheckedList( [] );
	};

	return (
		<div className='ss-limit'>
			<select value={ limit } onChange={ handleChange }>
				<option value={ 20 }>20</option>
				<option value={ 50 }>50</option>
				<option value={ 100 }>100</option>
				<option value={ 200 }>200</option>
				<option value={ total }>{ __( 'All', 'slim-seo' ) }</option>
			</select>
			&nbsp;
			{ sprintf( _n( 'items per page. Total %d item.', 'items per page. Total %d items.', 'slim-seo' ), Number( total ) ) }
		</div>
	);
};

export default Limit;