document.querySelector( '#ss-post-type-select' ).addEventListener( 'change', e => {
	document.querySelectorAll( '.ss-post-type-settings' ).forEach( el => el.classList.remove( 'ss-is-active' ) );
	document.querySelector( `.ss-post-type-settings--${ e.target.value }` ).classList.add( 'ss-is-active' );
} );

( function( window, document, wp, $, ss ) {

	let contentEditor;

	const normalize = string => string ? string.replace( /<[^>]+>/gm, '' ).replace( /\s+/gm, ' ' ).trim() : '';
	const formatTitle = ( title = '' ) => {
		const values = {
			site: ss.site.title,
			tagline: ss.site.description,
			title
		};
		return ss.title.parts.map( part => values[ part ] ?? '' ).filter( part => part ).join( ` ${ ss.title.separator } ` );
	};

	function openMediaPopup() {
		let frame;

		function clickHandle( e ) {
			e.preventDefault();

			// Create a frame only if needed.
			if ( !frame ) {
				frame = wp.media( {
					multiple: false,
					title: ss.mediaPopupTitle
				} );
			}

			frame.open();

			// Remove all attached 'select' event.
			frame.off( 'select' );

			// Handle selection.
			frame.on( 'select', () => {
				const url = frame.state().get( 'selection' ).first().toJSON().url;
				e.target.previousElementSibling.value = url;
			} );
		}

		const selectButtons = document.querySelectorAll( '.ss-select-image' );
		selectButtons.forEach( button => button.addEventListener( 'click', clickHandle ) );
	}

	class Input {
		constructor( selector ) {
			this.el = document.querySelector( selector );
		}
		get value() {
			return this.el ? normalize( this.el.value ) : '';
		}
		addEventListener( callback ) {
			this.el && this.el.addEventListener( 'input', callback );
		}
	}

	class Field {
		constructor( input, min, max, truncate ) {
			this.input = input;
			this.min = min;
			this.max = max;
			this.truncate = truncate;

			this.updateCounter = this.updateCounter.bind( this );
			this.updatePreview = this.updatePreview.bind( this );
		}
		get generated() {
			return '';
		}
		updatePreview() {
			this.input.el.placeholder = _.unescape( this.generated );
		}
		updateCounter() {
			let value = this.input.value || this.generated;
			value = _.unescape( value );
			const counter = this.input.el.nextElementSibling.querySelector( '.ss-counter' );
			if ( counter ) {
				counter.textContent = value.length;
			}
			this.updateStatus( value );
		}
		updateStatus( value ) {
			const isGood = value && value.length >= this.min && value.length <= this.max,
				label = this.input.el.parentNode.previousElementSibling;
			label.classList.remove( 'ss-success', 'ss-warning' );
			label.classList.add( isGood ? 'ss-success' : 'ss-warning' );
		}
		addEventListener() {
			this.input.addEventListener( this.updateCounter );
		}
		init() {
			if ( !this.input.el ) {
				return;
			}
			console.log( this.input.el );
			this.updatePreview();
			this.updateCounter();
			this.addEventListener();
		}
	}

	class HomeTitleField extends Field {
		get generated() {
			return formatTitle();
		}
	}

	openMediaPopup();

	ss.items.forEach( item => {
		const TitleInput = new Field( new Input( `#ss-title-${item}-archive` ), 0, 60 );
		const Description = new Field( new Input( `#ss-description-${item}-archive` ), 50, 160, true );
		TitleInput.init();
		Description.init();
	} );
} )( window, document, wp, jQuery, ss );
