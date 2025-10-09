import { useState } from '@wordpress/element';
import Paginate from '../components/Paginate';
import { useApi } from '../helper/misc';
import Items from './Items';

const Main = () => {
	const LIMIT = 20;
	const totalRows = useApi( 'records/total' );
	const [ offset, setOffset ] = useState( 0 );

	return (
		<>
			<Items limit={ LIMIT } offset={ offset } />
			<Paginate totalRows={ totalRows } limit={ LIMIT } offset={ offset } setOffset={ setOffset } />
		</>
	);
};

export default Main;