import { useEffect, useState } from "@wordpress/element";

const normalizeBool = value => {
	if ( 'true' === value ) {
		return true;
	}
	if ( 'false' === value ) {
		return false;
	}
	return value;
};


export const useToggle = name => {
	const [ handle, setHandle ] = useState( () => () => { } );

	const el = getElement( name );
	const wrapper = el ? el.closest( '.og-field' ) : null;
	const classList = wrapper ? wrapper.classList : '';

	useEffect( () => {
		const h = () => el && toggleDependents( el );
		setHandle( () => h );

		// Kick-off the first time.
		h();
	}, [ name, classList ] ); // Depends on classList in case it's set hidden by another field.

	return handle;
};

const getElement = nameOrElement => typeof nameOrElement === 'string' ? document.getElementById( nameOrElement ) : nameOrElement;

const toggleDependents = el => {
	const inputValue = el.type === 'checkbox' ? el.checked : el.value;
	const wrapper = el.closest( '.og-field' );

	getDependents( el ).forEach( dependent => {
		const dep = dependent.className.match( /dep:([^:]+):([^:\s]+)/ );
		const depValue = normalizeBool( dep[ 2 ] );

		// If current element is hidden, always hide the dependent.

		let isHidden = wrapper.classList.contains( 'og-is-hidden' ) ||
			(
				typeof ( depValue ) === 'string' && depValue.includes( '[' ) && depValue.includes( ']' ) ?
					!depValue.match( /[^[\],]+/g ).includes( inputValue ) :
					depValue !== inputValue
			);

		if ( isHidden ) {
			dependent.classList.add( 'og-is-hidden' );
		} else {
			dependent.classList.remove( 'og-is-hidden' );
		}

		// Toggle sub-dependents.
		dependent.querySelectorAll( '.og-input > input, .og-input > select' ).forEach( toggleDependents );
	} );
};

const getDependents = el => {
	const scope = el.closest( '.og-item' ) || el.closest( '.react-tabs__tab-panel' ) || el.closest( '.og' );
	const shortName = getShortName( el.id );
	return scope ? [ ...scope.querySelectorAll( `[class*="dep:${ shortName }:"]` ) ] : [];
};

const getShortName = name => {
	// Get last `-name` part.
	const match = name.match( /-([^-]*)$/ );
	return match ? match[ 1 ] : name;
};