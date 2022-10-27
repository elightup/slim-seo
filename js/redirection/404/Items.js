import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { useApi } from '../helper/misc';
import Header from './Header';
import Item from './Item';

const Items = ( { limit, offset } ) => {
	const [ order, setOrder ] = useState( { orderBy: 'updated_at', sort: 'desc' } );
	const logs = useApi( 'logs', { order, limit, offset }, 'POST' );

	const changeOrder = by => e => {
		e.preventDefault();

		const sort = by === order.orderBy ? ( 'desc' === order.sort ? 'asc' : 'desc' ) : 'desc';
		setOrder( { orderBy: by, sort } );
	};

	if ( undefined === logs ) {
		return <div className='ss-loader'></div>;
	} else if ( 0 === Object.keys( logs ).length ) {
		return <span>{ __( 'No data', 'slim-seo' ) }</span>;
	}

	return (
		<table className='ss-table'>
			<thead>
				<Header order={ order } changeOrder={ changeOrder } />
			</thead>

			<tbody>
				{ logs.map( log => <Item key={ log.id } log={ log } /> ) }
			</tbody>

			<tfoot>
				<Header order={ order } changeOrder={ changeOrder } />
			</tfoot>
		</table>
	);
};

export default Items;