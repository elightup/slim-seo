import { useDispatch } from '@wordpress/data';
import { useState, useEffect, useRef } from '@wordpress/element';
import { useCheckedTerms, useTermObjects } from './hooks';

const Dropdown = ( { taxonomy, taxonomyData } ) => {
	const { label, metaKey } = taxonomyData;
	const primaryValue = parseInt( taxonomyData.primaryValue || 0, 10 );
	const checkedIds = useCheckedTerms( taxonomy );
	const termObjects = useTermObjects( taxonomy, checkedIds );
	const { editPost } = useDispatch( 'core/editor' );
	const [ primaryId, setPrimaryId ] = useState( primaryValue );
	const initialised = useRef( false );

	const update = value => {
		setPrimaryId( value );
		editPost( { meta: { [ metaKey ]: value } } );
	};

	useEffect( () => {
		if ( checkedIds.length < 1 ) {
			update( 0 );
			return;
		}

		if ( !initialised.current ) {
			initialised.current = true;

			if ( !checkedIds.includes( primaryId ) ) {
				update( checkedIds[ 0 ] );
			}

			return;
		}

		if ( !checkedIds.includes( primaryId ) ) {
			update( checkedIds[ 0 ] );
		}
	}, [ checkedIds.join( ',' ) ] );

	if ( checkedIds.length < 2 ) {
		return null;
	}

	const options = termObjects.length > 0 ? termObjects : checkedIds.map( id => ( { id, name: '#' + id } ) );

	return (
		<div className="ss-primary-term-wrapper">
			<label className="ss-primary-term-label" htmlFor={ `ss-primary-term-select-${ taxonomy }` }>
				{ ssPrimaryTerm.primaryText + ' ' + label }
			</label>
			<select
				id={ `ss-primary-term-select-${ taxonomy }` }
				className="ss-primary-term-select"
				value={ primaryId }
				onChange={ e => update( parseInt( e.target.value, 10 ) ) }
			>
				{ options.map( term => (
					<option key={ term.id } value={ term.id }>
						{ term.name }
					</option>
				) ) }
			</select>
		</div>
	);
};

export default Dropdown;
