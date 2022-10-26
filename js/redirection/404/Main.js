import { useState } from '@wordpress/element';
import { get } from '../helper/misc';
import Items from './Items';
import Paginate from './Paginate';

const Main = ()  => {
	const LIMIT = 20;
	const totalRows = get( 'total_logs' );
	const [ offset, setOffset ] = useState( 0 );

	return (
		<>
			<Items limit={ LIMIT } offset={ offset } />

			<Paginate totalRows={ totalRows } limit={ LIMIT } setOffset={ setOffset } />
		</>
	);
};

export default Main;