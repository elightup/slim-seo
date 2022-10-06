import { useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import request from '../helper/request';
import { Tooltip } from '../helper/misc';
import Paginate from './Paginate';
import AddRedirect from '../redirects/Update';

const List = ()  => {
	const LIMIT = 20;
	const [ totalRows, setTotalRows ] = useState( 0 );
	const [ offset, setOffset ] = useState( 0 );
	const [ logs, setLogs ] = useState( [] );
	const [ isLoadingData, setIsLoadingData ] = useState( true );
	const [ orderBy, setOrderBy ] = useState( 'updated_at-DESC' );
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

	const changeOrder = order => {
		return e => {
			e.preventDefault();

			setIsLoadingData( true );

			setOrderBy( order );
		};
	};

	useEffect( () => {
		request( 'total_404_logs', {} ).then( setTotalRows );
	}, [] );

	useEffect( () => {
		request( 'get_404_logs', { orderBy, limit: LIMIT, offset } ).then( result => {
			setIsLoadingData( false );
			setLogs( result );
		} );
	}, [ orderBy, offset ] );

	return (
		<>
			<table className='ss-table'>
				<thead>
					<tr>
						<th className='ss-log__url'>
							{ __( 'URL', 'slim-seo' ) }
							<Tooltip content={ __( '404 URL', 'slim-seo' ) } />
						</th>
						<th className='ss-log__hit'>
							{ __( 'Hit', 'slim-seo' ) }
							<Tooltip content={ __( 'Number of 404 URL has been hitted', 'slim-seo' ) } />
							<span className='ss-orderby'>
								<a href='#' onClick={ changeOrder( 'hit-ASC' ) }><i className={ `ss-orderby__arrow up` + ( 'hit-ASC' === orderBy ? ' active' : '' ) }></i></a>
								<a href='#' onClick={ changeOrder( 'hit-DESC' ) }><i className={ `ss-orderby__arrow down` + ( 'hit-DESC' === orderBy ? ' active' : '' ) }></i></a>
							</span>
						</th>
						<th className='ss-log__created_at'>
							{ __( 'Created at', 'slim-seo' ) }
							<Tooltip content={ __( 'Created time of 404 URL', 'slim-seo' ) } />
							<span className='ss-orderby'>
								<a href='#' onClick={ changeOrder( 'created_at-ASC' ) }><i className={ `ss-orderby__arrow up` + ( 'created_at-ASC' === orderBy ? ' active' : '' ) }></i></a>
								<a href='#' onClick={ changeOrder( 'created_at-DESC' ) }><i className={ `ss-orderby__arrow down` + ( 'created_at-DESC' === orderBy ? ' active' : '' ) }></i></a>
							</span>
						</th>
						<th className='ss-log__updated_at'>
							{ __( 'Updated at', 'slim-seo' ) }
							<Tooltip content={ __( 'Last time 404 URL has been hitted', 'slim-seo' ) } />
							<span className='ss-orderby'>
								<a href='#' onClick={ changeOrder( 'updated_at-ASC' ) }><i className={ `ss-orderby__arrow up` + ( 'updated_at-ASC' === orderBy ? ' active' : '' ) }></i></a>
								<a href='#' onClick={ changeOrder( 'updated_at-DESC' ) }><i className={ `ss-orderby__arrow down` + ( 'updated_at-DESC' === orderBy ? ' active' : '' ) }></i></a>
							</span>
						</th>
						<th className='ss-log__actions'>{ __( 'Actions', 'slim-seo' ) }</th>
					</tr>
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
					<tr>
						<td className='ss-log__url'>{ __( 'URL', 'slim-seo' ) }</td>
						<td className='ss-log__hit'>{ __( 'Hit', 'slim-seo' ) }</td>
						<td className='ss-log__created_at'>{ __( 'Created at', 'slim-seo' ) }</td>
						<td className='ss-log__updated_at'>{ __( 'Updated at', 'slim-seo' ) }</td>
						<td className='ss-log__actions'>{ __( 'Actions', 'slim-seo' ) }</td>
					</tr>
				</tfoot>
			</table>

			<Paginate totalRows={ totalRows } limit={ LIMIT } setIsLoadingData={ setIsLoadingData } setOffset={ setOffset } />

			{ showAddRedirectModal && <AddRedirect redirectToEdit={ redirectToAdd } callback={ afterAddRedirect } setShowUpdateRedirectModal={ setShowAddRedirectModal } /> }
		</>
	);
};

export default List;