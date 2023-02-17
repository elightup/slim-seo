import { __ } from '@wordpress/i18n';
import { Tooltip } from '../helper/misc';

const Header = ( { isCheckAll, checkAll } ) => {
	return (
		<tr>
			<th className='ss-redirect__checkbox'><input type='checkbox' checked={ isCheckAll } onChange={ checkAll } /></th>
			<th className='ss-redirect__type'>
				{ __( 'Type', 'slim-seo' ) }
				<Tooltip content={ __( 'Redirect type', 'slim-seo' ) } />
			</th>
			<th className='ss-redirect__url'>
				{ __( 'From URL', 'slim-seo' ) }
				<Tooltip content={ __( 'URL to redirect', 'slim-seo' ) } />
			</th>
			<th className='ss-redirect__url'>
				{ __( 'To URL', 'slim-seo' ) }
				<Tooltip content={ __( 'Destination URL', 'slim-seo' ) } />
			</th>
			<th className='ss-redirect__note'>
				{ __( 'Note', 'slim-seo' ) }
				<Tooltip content={ __( 'Something to reminds you about the redirects', 'slim-seo' ) } />
			</th>
			<th className='ss-redirect__enable'>
				{ __( 'Enable', 'slim-seo' ) }
				<Tooltip content={ __( 'Is the redirect enabled?', 'slim-seo' ) } />
			</th>
			<th className='ss-redirect__actions'>{ __( 'Actions', 'slim-seo' ) }</th>
		</tr>
	);
};

export default Header;