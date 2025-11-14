import { TextTooltip } from '../../helper/Tooltip';

const TableHeader = ( { className, text, tooltip, type, orderBy, setOrderBy, order, setOrder } ) => {
	const sorting = e => {
		e.preventDefault();

		setOrderBy( prev => {
			if ( prev === type ) {
				setOrder( prevOrder => 'DESC' === prevOrder ? 'ASC' : 'DESC' );
			} else {
				setOrder( 'DESC' );
			}
	
			return type;
		} );
	};

	className += type ? ( type === orderBy ? ' sorted ' + ( 'DESC' === order ? 'desc' : 'asc' ) : ' sortable' ) : '';

	return (
		<th className={ className }>
			{
				type
					? (						
						<a href="#" onClick={ sorting }>
							<TextTooltip content={ tooltip }>
								<span>{ text }</span>
							</TextTooltip>
							<span className="sorting-indicators">
								<span className="sorting-indicator asc" aria-hidden="true"></span>
								<span className="sorting-indicator desc" aria-hidden="true"></span>
							</span>
						</a>						
					)
					: (
						<TextTooltip content={ tooltip }>
							<span>{ text }</span>
						</TextTooltip>
					)
			}
			
		</th>
	);
};

export default TableHeader;