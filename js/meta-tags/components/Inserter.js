import { RawHTML, useState } from "@wordpress/element";
import { __ } from "@wordpress/i18n";

const Group = ( { group, searchTerm } ) => {
	const s = searchTerm.toLowerCase();
	const items = Object.entries( group.options ).filter( ( [ value, label ] ) => !s || label.toLowerCase().includes( s ) );

	return items.length > 0 &&
		<>
			<div className="ss-dropdown__title">{ group.label }</div>
			<div className="ss-dropdown__items">
				{
					items.map( ( [ value, label ] ) => (
						<RawHTML key={ value } className="ss-dropdown__item" data-value={ value }>
							{ label }
						</RawHTML>
					) )
				}
			</div>
		</>;
};

const Items = ( { items } ) => items.map( property => (
	<RawHTML key={ property.id } className="ss-dropdown__item" data-type={ property.type } data-id={ property.id }>
		{ getLabel( property ) }
	</RawHTML>
) );

const Search = ( { handleSearch } ) => (
	<div className="ss-dropdown__search">
		<input onInput={ handleSearch } type="text" placeholder={ __( 'Search...', 'slim-seo' ) } />
	</div>
);

// Get label, including nested groups (where outer group has no label).
const getLabel = property => {
	let label = property?.label || '';
	if ( label ) {
		return label;
	}

	let fields = property?.fields || [];
	fields.forEach( field => {
		if ( !label ) {
			label = getLabel( field );
		}
	} );

	return label;
};

export default function Inserter( { items = [], group = false, hasSearch = false, onSelect } ) {
	const [ searchTerm, setSearchTerm ] = useState( '' );

	const handleClick = e => e.target.matches( '.ss-dropdown__item' ) && onSelect( e );
	const handleSearch = e => setSearchTerm( e.target.value );

	return (
		<div onClick={ handleClick }>
			{ hasSearch && <Search handleSearch={ handleSearch } /> }
			{
				group
					? items.map( ( group, index ) => <Group key={ index } group={ group } searchTerm={ searchTerm } /> )
					: <Items items={ items } />
			}
		</div>
	);
}
