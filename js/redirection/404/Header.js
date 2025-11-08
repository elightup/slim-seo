import { __ } from '@wordpress/i18n';
import { Tooltip } from '../../helper/Tooltip';

const Header = ( { order, changeOrder } ) => {
	const { orderBy, sort } = order;

	return (
		<tr>
			<th className='ss-log__url'>
				{ __( 'URL', 'slim-seo' ) }
				<Tooltip content={ __( '404 URL', 'slim-seo' ) } />
			</th>
			<th className={ 'ss-log__hit ' + ( 'hit' === orderBy ? `sorted ${sort}` : 'sortable asc' ) }>
				<a href='#' onClick={ changeOrder( 'hit' ) }>
					<span>
						{ __( 'Hit', 'slim-seo' ) }
						<Tooltip content={ __( 'The number of times the URL was hitted', 'slim-seo' ) } />
					</span>
					<span className='sorting-indicator'></span>
				</a>
			</th>
			<th className={ 'ss-log__created_at ' + ( 'created_at' === orderBy ? `sorted ${sort}` : 'sortable asc' ) }>
				<a href='#' onClick={ changeOrder( 'created_at' ) }>
					<span>
						{ __( 'Created at', 'slim-seo' ) }
						<Tooltip content={ __( 'First time the URL was hitted', 'slim-seo' ) } />
					</span>
					<span className='sorting-indicator'></span>
				</a>
			</th>
			<th className={ 'ss-log__updated_at ' + ( 'updated_at' === orderBy ? `sorted ${sort}` : 'sortable asc' ) }>
				<a href='#' onClick={ changeOrder( 'updated_at' ) }>
					<span>
						{ __( 'Updated at', 'slim-seo' ) }
						<Tooltip content={ __( 'Last time the URL was hitted', 'slim-seo' ) } />
					</span>
					<span className='sorting-indicator'></span>
				</a>
			</th>
			<th className='ss-log__actions'>{ __( 'Actions', 'slim-seo' ) }</th>
		</tr>
	);
};

export default Header;