( function ( window, document, ss ) {
	function isBlockEditor() {
		return document.body.classList.contains( 'block-editor-page' );
	}

	function stripHtml( string ) {
		return string.replace( /<[^>]*>?/gm, '' );
	}

	class Field {
		constructor( selector, ref, pattern ) {
			this.el = document.querySelector( selector );
			this.ref = document.querySelector( ref );
			this.pattern = pattern;

			this.init();
		}

		getGeneratedValue() {
			var value = '';
			if ( ! isBlockEditor() ) {
				value = this.ref.value;
			}

			return this.normalize( value );
		}

		normalize( value ) {
			value = stripHtml( value );
			value = this.pattern.replace( '{#}', value ).replace( '{site.title}', ss.site.title );
			return value;
		}

		updatePreview() {
			this.el.placeholder = this.getGeneratedValue();
		}

		updateCounter() {
			var value = this.el.value ? this.el.value : this.getGeneratedValue();
			this.el.nextElementSibling.querySelector( '.ss-number' ).textContent = value.length;
		}

		listenToChange() {
			this.el.addEventListener( 'input', this.updateCounter.bind( this ) );
			if ( ! isBlockEditor() ) {
				this.ref.addEventListener( 'input', this.updatePreview.bind( this ) );
				this.ref.addEventListener( 'input', this.updateCounter.bind( this ) );
			}
		}

		init() {
			this.updatePreview();
			this.updateCounter();
			this.listenToChange();
		}
	}

	class PostDescription extends Field {
		constructor( selector, ref, ref2, pattern ) {
			super( selector, ref, pattern );
			this.ref2 = document.querySelector( ref2 );
		}

		getGeneratedValue() {
			if ( isBlockEditor() ) {
				return '';
			}
			var value = stripHtml( this.ref.value );
			if ( ! value ) {
				console.log( this.ref2 );
				value = this.normalize( this.ref2.value );
			}

			return value;
		}

		normalize( value ) {
			value = stripHtml( value );
			value = this.pattern.replace( '{#}', value );
			value = value.replace( /\s{2,}/, ' ' ); // Remove extra white spaces.
			value = value.substring( 0, 160 );

			return value;
		}

		listenToChange() {
			this.el.addEventListener( 'input', this.updateCounter.bind( this ) );
			if ( isBlockEditor() ) {
				return;
			}
			this.ref.addEventListener( 'input', this.updatePreview.bind( this ) );
			this.ref.addEventListener( 'input', this.updateCounter.bind( this ) );
			this.ref2.addEventListener( 'input', this.updatePreview.bind( this ) );
			this.ref2.addEventListener( 'input', this.updateCounter.bind( this ) );
		}

		init() {
			super.init();

			if ( ! window.tinymce ) {
				console.log( 'No editor' );
				return;
			}

			var editor = tinymce.get( 'content' );
			console.info( editor );

			editor.on( 'keyup change', function() {
				editor.save();
			} );
		}
	}

	// Post.
	if ( document.body.classList.contains( 'post-new-php' ) || document.body.classList.contains( 'post-php' ) ) {
		new Field( '#ss-title', '#title', '{#} - {site.title}' );
		new PostDescription( '#ss-description', '#excerpt', '#content', '{#}' );
	}

	// Term.
	if ( document.body.classList.contains( 'term-php' ) ) {
		new Field( '#ss-title', '#name', '{#} - {site.title}' );
		new Field( '#ss-description', '#description', '{#}' );
	}
} )( window, document, ss );
