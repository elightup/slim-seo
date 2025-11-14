import { __ } from '@wordpress/i18n';
import TableHeader from './TableHeader';

const Header = ( { orderBy, setOrderBy, order, setOrder, isCheckAll, checkAll } ) => {
	return (
		<tr>
			<th className='ss-redirect__checkbox'><input type='checkbox' checked={ isCheckAll } onChange={ checkAll } /></th>
			<TableHeader
				className='ss-redirect__type'
				text={ __( 'Type', 'slim-seo' ) }
				type='type'
				orderBy={ orderBy }
				setOrderBy={ setOrderBy }
				order={ order }
				setOrder={ setOrder }
				tooltip={ __( 'Redirect type', 'slim-seo' ) } />
			<TableHeader
				className='ss-redirect__url'
				text={ __( 'From URL', 'slim-seo' ) }
				type='from'
				orderBy={ orderBy }
				setOrderBy={ setOrderBy }
				order={ order }
				setOrder={ setOrder }
				tooltip={ __( 'URL to redirect', 'slim-seo' ) } />
			<TableHeader
				className='ss-redirect__url'
				text={ __( 'To URL', 'slim-seo' ) }
				type='to'
				orderBy={ orderBy }
				setOrderBy={ setOrderBy }
				order={ order }
				setOrder={ setOrder }
				tooltip={ __( 'Destination URL', 'slim-seo' ) } />
			<TableHeader
				className='ss-redirect__note'
				text={ __( 'Note', 'slim-seo' ) }
				tooltip={ __( 'Something to reminds you about the redirects', 'slim-seo' ) } />
			<TableHeader
				className='ss-redirect__enable'
				text={ __( 'Enable', 'slim-seo' ) }
				type='enable'
				orderBy={ orderBy }
				setOrderBy={ setOrderBy }
				order={ order }
				setOrder={ setOrder }
				tooltip={ __( 'Is the redirect enabled?', 'slim-seo' ) } />
			<TableHeader
				className='ss-redirect__actions'
				text={ __( 'Actions', 'slim-seo' ) } />
		</tr>
	);
};

export default Header;