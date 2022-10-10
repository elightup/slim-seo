import { useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import request from '../helper/request';
import Paginate from './Paginate';
import Header from './Header';
import AddRedirect from '../redirects/Update';

const List = ()  => {
	const LIMIT = 20;
	const [ totalRows, setTotalRows ] = useState( 0 );
	const [ offset, setOffset ] = useState( 0 );
	const [ logs, setLogs ] = useState( [] );
	const [ isLoadingData, setIsLoadingData ] = useState( true );
	const [ order, setOrder ] = useState( { orderBy: 'updated_at', sort: 'desc' } );
	const [ showAddRedirectModal, setShowAddRedirectModal ] = useState( false );
	const [ redirectToAdd, setRedirectToAdd ] = useState( SSRedirection.defaultRedirect );

	const addRedirect = log => {
		return e => {
			e.preventDefault();

			setShowAddRedirectModal( true );
			setRedirectToAdd( { ...SSRedirection.defaultRedirect, from: log.url } );
		};
	};

	const afterAddRedirect = () => {
		window.location.reload();
	};

	const changeOrder = _order => {
		return e => {
			e.preventDefault();

			setOrder( { ..._order } );

			setIsLoadingData( true );
		};
	};

	useEffect( () => {
		request( 'total_404_logs', {} ).then( setTotalRows );
	}, [] );

	useEffect( () => {
		request( 'get_404_logs', { order, limit: LIMIT, offset }, 'POST' ).then( result => {
			setIsLoadingData( false );
			setLogs( result );
		} );
	}, [ order, offset ] );

	return (
		<>
			<table className='ss-table'>
				<thead>
					<Header order={ order } changeOrder={ changeOrder } />
				</thead>

				<tbody>
					{
						isLoadingData
						? <tr><td colSpan='5'><div className='ss-loader'></div></td></tr>
						: logs.length
							? logs.map( log => (
								<tr key={ log.id }>
									<td className='ss-log__url'>{ log.url }</td>
									<td className='ss-log__hit'>{ log.hit }</td>
									<td className='ss-log__created_at'>{ log.created_at }</td>
									<td className='ss-log__updated_at'>{ log.updated_at }</td>
									<td className='ss-log__actions'><a href='#' onClick={ addRedirect( log ) }>{ __( 'Add Redirect', 'slim-seo' ) }</a></td>
								</tr>
							) )
							: <tr><td colSpan='5'>{ __( 'No data', 'slim-seo' ) }</td></tr>
					}
				</tbody>

				<tfoot>
					<Header order={ order } changeOrder={ changeOrder } />
				</tfoot>
			</table>

			<Paginate totalRows={ totalRows } limit={ LIMIT } setIsLoadingData={ setIsLoadingData } setOffset={ setOffset } />

			{ showAddRedirectModal && <AddRedirect redirectToEdit={ redirectToAdd } callback={ afterAddRedirect } setShowUpdateRedirectModal={ setShowAddRedirectModal } /> }
		</>
	);
};

export default List;