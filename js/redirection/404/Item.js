import { getFullURL } from '../helper/misc';
import AddRedirect from '../redirects/Update';

const Item = ( { log } ) => {
	return (
		<tr>
			<td className='ss-log__url'><a href={ getFullURL( log.url ) } target='_blank'>{ log.url }</a></td>
			<td className='ss-log__hit'>{ log.hit }</td>
			<td className='ss-log__created_at'>{ log.created_at }</td>
			<td className='ss-log__updated_at'>{ log.updated_at }</td>
			<td className='ss-log__actions'>
				<AddRedirect redirectToEdit={ { ...SSRedirection.defaultRedirect, from: log.url } } />
			</td>
		</tr>
	);
};

export default Item;