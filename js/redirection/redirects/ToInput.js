import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { fetcher } from '../helper/misc';

const ToInput = ( { value, setRedirect } ) => {
	const [ url, setUrl ] = useState( value );
	const [ suggestions, setSuggestions ] = useState( [] );
	const [ isDropdownVisible, setDropdownVisible ] = useState( false );
	const [ loading, setLoading ] = useState( true );

	const handleInputChange = e => {
		const value = e.target.value;

		if ( value ) {
			setDropdownVisible( true );
			setLoading( true );

			fetcher( 'posts', { search: value } ).then( result => {
				setSuggestions( prev => result );

				setLoading( false );

				if ( 0 === result.length ) {
					setDropdownVisible( false );
				}
			} );
		} else {
			setDropdownVisible( false );
		}

		setUrl( value );
		setRedirect( prev => ( { ...prev, to: value } ) );
	};

	const handleSuggestionClick = suggestion => {
		setDropdownVisible( false );
		setUrl( suggestion.url );
		setRedirect( prev => ( { ...prev, to: suggestion.url } ) );
	};

	const handleKeyDown = e => {
		if ( url && 'Enter' === e.key ) {
			setDropdownVisible( false );
		}
	};

	return (
		<div className='ss-to-wrapper'>
			<input
				id='ss-to'
				type='text'
				value={ url }
				onChange={ handleInputChange }
				onKeyDown={ handleKeyDown }
				onBlur={ () => setDropdownVisible( false ) }
				placeholder={ __( 'Enter URL or Search page', 'slim-seo' ) }
			/>

			{
				isDropdownVisible &&
				<ul>
					{
						loading
							? <li>{ __( 'Loading...', 'slim-seo' ) }</li>
							: suggestions.map( suggestion => (
								<li key={ suggestion.url } onClick={ () => handleSuggestionClick( suggestion ) } onMouseDown={ e => e.preventDefault() }>
									{ suggestion.title } - { suggestion.url }
								</li>
							) )
					}
				</ul>
			}
		</div>
	);
};

export default ToInput;