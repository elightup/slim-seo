document.querySelector( '#ss-post-type-select' ).addEventListener( 'change', e => {
	document.querySelectorAll( '.ss-post-type-settings' ).forEach( el => el.classList.remove( 'ss-is-active' ) );
	document.querySelector( `.ss-post-type-settings--${ e.target.value }` ).classList.add( 'ss-is-active' );
} );
document.querySelector( '#ss-taxonomy-select' ).addEventListener( 'change', e => {
	document.querySelectorAll( '.ss-taxonomy-settings' ).forEach( el => el.classList.remove( 'ss-is-active' ) );
	document.querySelector( `.ss-taxonomy-settings--${ e.target.value }` ).classList.add( 'ss-is-active' );
} );

( function( window, document, wp, $, ss ) {

	let contentEditor;

	const isBlockEditor = document.body.classList.contains( 'block-editor-page' );
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
		constructor( input, ref, min, max, truncate ) {
			this.input = input;
			this.ref = ref;
			this.min = min;
			this.max = max;
			this.truncate = truncate;

			this.updateCounter = this.updateCounter.bind( this );
			this.updatePreview = this.updatePreview.bind( this );
		}
		get generated() {
			if ( !this.ref ) {
				return '';
			}
			const value = this.ref.value;
			return this.truncate ? value.substring( 0, this.max ) : value;
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
			const isGood = value && value.length >= this.min && value.length <= this.max;
			this.input.el.parentNode.previousElementSibling.classList.remove( 'ss-success', 'ss-warning' );
			this.input.el.parentNode.previousElementSibling.classList.add( isGood ? 'ss-success' : 'ss-warning' );
		}
		addEventListener() {
			this.input.addEventListener( this.updateCounter );
			if ( this.ref ) {
				this.ref.addEventListener( this.updateCounter );
				this.ref.addEventListener( this.updatePreview );
			}
		}
		init() {
			if ( !this.input.el ) {
				return;
			}
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

	if ( document.body.classList.contains( 'settings_page_slim-seo' ) ) {
		const HomeTitleInput = new HomeTitleField( new Input( '#ss-title' ), null, 0, 60 );
		const homeDescription = new Field( new Input( '#ss-description' ), null, 50, 160, true );
		HomeTitleInput.init();
		homeDescription.init();
	}
} )( window, document, wp, jQuery, ss );
