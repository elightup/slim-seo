import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { fetcher } from '../helper/misc';

const ToInput = ( { value, setRedirect } ) => {
	const [ url, setUrl ] = useState( value );
	const [ suggestions, setSuggestions ] = useState( [] );
	const [ filteredSuggestions, setFilteredSuggestions ] = useState( [] );
	const [ isDropdownVisible, setDropdownVisible ] = useState( false );
	const [ loading, setLoading ] = useState( true );

	const handleInputChange = e => {
		const value = e.target.value;

		setUrl( value );

		if ( value ) {
			const lowerCaseValue = value.toLowerCase();
			const filtered = suggestions.filter( item => item.title.toLowerCase().includes( lowerCaseValue ) || item.url.toLowerCase().includes( lowerCaseValue ) );

			setFilteredSuggestions( prev => filtered );
			setDropdownVisible( true );
		} else {
			setDropdownVisible( false );
		}

		setRedirect( prev => ( { ...prev, to: value } ) );
	};

	const handleSuggestionClick = suggestion => {
		setUrl( suggestion.url );
		setDropdownVisible( false );
		setRedirect( prev => ( { ...prev, to: suggestion.url } ) );
	};

	const handleKeyDown = e => {
		if ( url && 'Enter' === e.key ) {
			setDropdownVisible( false );
		}
	};

	useEffect( () => {
        setLoading( true );

        fetcher( 'posts' ).then( result => {
            setSuggestions( prev => result );

            setLoading( false );
        } );
    }, [] );

	return (
		<div className='ss-to-wrapper'>
			<input
				id='ss-to'
				type='text'
				value={ url }
				onChange={ handleInputChange }
				onKeyDown={ handleKeyDown }
				onFocus={ () => url && setDropdownVisible( true ) }
				onBlur={ () => setDropdownVisible( false ) }
				placeholder={ __( 'Enter URL or select a page', 'slim-seo' ) }
			/>

			{
				isDropdownVisible &&
				(
					<ul>
						{
							loading
								? <li>{ __( 'Loading...', 'slim-seo' ) }</li>
								: filteredSuggestions.length > 0
									? (
										filteredSuggestions.map( ( suggestion, index ) => (
											<li key={ index } onClick={ () => handleSuggestionClick( suggestion ) } onMouseDown={ e => e.preventDefault() }>
												{ suggestion.title } - { suggestion.url }
											</li>
										) )
									)
									: ''
						}
					</ul>
				)
			}
		</div>
	);
}

export default ToInput;