import { __ } from '@wordpress/i18n';
import { Tooltip } from '../helper/misc';

const Header = ( { order, changeOrder } ) => {
	const { orderBy, sort } = order;

	return (
		<tr>
			<th className='ss-log__url'>
				{ __( 'URL', 'slim-seo' ) }
				<Tooltip content={ __( '404 URL', 'slim-seo' ) } />
			</th>
			<th className={ 'ss-log__hit ' + ( 'hit' === orderBy ? `sorted ${sort}` : 'sortable asc' ) }>
				<a href='#' onClick={ changeOrder( { orderBy: 'hit', sort: 'hit' === orderBy ? ( 'desc' === sort ? 'asc' : 'desc' ) : 'desc' } ) }>
					<span>
						{ __( 'Hit', 'slim-seo' ) }
						<Tooltip content={ __( 'Number of 404 URL has been hitted', 'slim-seo' ) } />
					</span>
					<span className='sorting-indicator'></span>
				</a>
			</th>
			<th className={ 'ss-log__created_at ' + ( 'created_at' === orderBy ? `sorted ${sort}` : 'sortable asc' ) }>
				<a href='#' onClick={ changeOrder( { orderBy: 'created_at', sort: 'created_at' === orderBy ? ( 'desc' === sort ? 'asc' : 'desc' ) : 'desc' } ) }>
					<span>
						{ __( 'Created at', 'slim-seo' ) }
						<Tooltip content={ __( 'Created time of 404 URL', 'slim-seo' ) } />
					</span>
					<span className='sorting-indicator'></span>
				</a>
			</th>
			<th className={ 'ss-log__updated_at ' + ( 'updated_at' === orderBy ? `sorted ${sort}` : 'sortable asc' ) }>
				<a href='#' onClick={ changeOrder( { orderBy: 'updated_at', sort: 'updated_at' === orderBy ? ( 'desc' === sort ? 'asc' : 'desc' ) : 'desc' } ) }>
					<span>
						{ __( 'Updated at', 'slim-seo' ) }
						<Tooltip content={ __( 'Last time 404 URL has been hitted', 'slim-seo' ) } />
					</span>
					<span className='sorting-indicator'></span>
				</a>
			</th>
			<th className='ss-log__actions'>{ __( 'Actions', 'slim-seo' ) }</th>
		</tr>
	);
};

export default Header;