import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { fetcher, useApi } from '../helper/misc';
import Header from './Header';
import Item from './Item';

const Items = ( { limit, offset } ) => {
	const [ order, setOrder ] = useState( { orderBy: 'updated_at', sort: 'desc' } );
	const { result: logs, mutate } = useApi( 'logs', { orderBy: order.orderBy, sort: order.sort, limit, offset }, { returnMutate: true, options: { revalidateIfStale: false } } );

	const changeOrder = by => e => {
		e.preventDefault();

		const sort = by === order.orderBy ? ( 'desc' === order.sort ? 'asc' : 'desc' ) : 'desc';
		setOrder( { orderBy: by, sort } );
	};

	const deleteLog = log => {
		fetcher( 'delete_log', { id: log.id } ).then( result => {
			mutate( logs.filter( l => {
				return l.id != log.id;
			} ) );
		} );
	};

	const deleteAllLogs = e => {
		e.preventDefault();

		if ( !confirm( __( 'Delete all logs?', 'slim-seo' ) ) ) {
			return;
		}

		fetcher( 'delete_logs', {} ).then( result => {
			mutate( {} );
		} );
	}

	if ( undefined === logs ) {
		return <div className='ss-loader'></div>;
	} else if ( 0 === Object.keys( logs ).length ) {
		return <span>{ __( 'No data', 'slim-seo' ) }</span>;
	}

	return (
		<>
			<div className='ss-filters'>
				<button className='button button-primary ss-delete-all-logs' onClick={ deleteAllLogs }>{ __( 'Delete all logs', 'slim-seo' ) }</button>
			</div>
			
			<table className='ss-table'>
				<thead>
					<Header order={ order } changeOrder={ changeOrder } />
				</thead>

				<tbody>
					{ logs.map( log => <Item key={ log.id } log={ log } deleteLog={ deleteLog } /> ) }
				</tbody>

				<tfoot>
					<Header order={ order } changeOrder={ changeOrder } />
				</tfoot>
			</table>
		</>
	);
};

export default Items;