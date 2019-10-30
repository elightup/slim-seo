( function ( window, document, wp, $, ss ) {
	let contentEditor;

	function isBlockEditor() {
		return document.body.classList.contains( 'block-editor-page' );
	}

	function normalize( string ) {
		return string ? string.replace( /<[^>]+>/gm, '' ).replace( /\s+/gm, ' ' ).trim() : '';
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

	class PostTitleInput extends Input {
		get value() {
			const value = isBlockEditor() ? normalize( wp.data.select( 'core/editor' ).getEditedPostAttribute( 'title' ) ) : super.value;
			return value + ' - ' + ss.site.title;
		}
		addEventListener( callback ) {
			isBlockEditor() ? wp.data.subscribe( callback ) : super.addEventListener( callback );
		}
	}

	class PostExcerptInput extends Input {
		get value() {
			return isBlockEditor() ? normalize( wp.data.select( 'core/editor' ).getEditedPostAttribute( 'excerpt' ) ) : super.value;
		}
		addEventListener( callback ) {
			isBlockEditor() ? wp.data.subscribe( callback ) : super.addEventListener( callback );
		}
	}

	class PostContentInput extends Input {
		get value() {
			if ( isBlockEditor() ) {
				return normalize( wp.data.select( 'core/editor' ).getEditedPostContent() );
			}
			return contentEditor && ! contentEditor.isHidden() ? normalize( contentEditor.getContent() ) : super.value;
		}
		addEventListener( callback ) {
			if ( isBlockEditor() ) {
				wp.data.subscribe( callback );
				return;
			}
			super.addEventListener( callback );

			$( document ).on( 'tinymce-editor-init', function( event, editor ) {
				if ( editor.id !== 'content' ) {
					return;
				}
				contentEditor = editor;
				editor.on( 'input', callback );
			} );
		}
	}

	class TermTitleInput extends Input {
		get value() {
			return super.value + ' - ' + ss.site.title;
		}
	}

	class Field {
		constructor( input, ref, min, max, truncate ) {
			this.input    = input;
			this.ref      = ref;
			this.min      = min;
			this.max      = max;
			this.truncate = truncate;

			this.updateCounter = this.updateCounter.bind( this );
			this.updatePreview = this.updatePreview.bind( this );
		}
		get generated() {
			const value = this.ref.value;
			return this.truncate ? value.substring( 0, this.max ) : value;
		}
		updatePreview() {
			this.input.el.placeholder = this.generated;
		}
		updateCounter() {
			const value = this.input.value || this.generated;
			this.input.el.nextElementSibling.querySelector( '.ss-number' ).textContent = value.length;
			this.updateStatus( value );
		}
		updateStatus( value ) {
			const isGood = value && value.length >= this.min && value.length <= this.max;
			this.input.el.parentNode.previousElementSibling.classList.remove( 'ss-success', 'ss-warning' );
			this.input.el.parentNode.previousElementSibling.classList.add( isGood ? 'ss-success' : 'ss-warning' );
		}
		addEventListener() {
			this.input.addEventListener( this.updateCounter );
			this.ref.addEventListener( this.updateCounter );
			this.ref.addEventListener( this.updatePreview );
		}
		init() {
			this.updatePreview();
			this.updateCounter();
			this.addEventListener();
		}
	}

	class PostDescriptionField extends Field {
		constructor( input, ref, ref2, min, max, truncate ) {
			super( input, ref, min, max, truncate );
			this.ref2 = ref2;
		}
		get generated() {
			const value = this.ref.value || this.ref2.value;
			return this.truncate ? value.substring( 0, this.max ) : value;
		}
		addEventListener() {
			super.addEventListener();
			this.ref2.addEventListener( this.updateCounter );
			this.ref2.addEventListener( this.updatePreview );
		}
	}

	// Post.
	if ( document.body.classList.contains( 'post-new-php' ) || document.body.classList.contains( 'post-php' ) ) {
		const postTitle = new Field( new Input( '#ss-title' ), new PostTitleInput( '#title' ), 0, 60 );
		const postDescription = new PostDescriptionField( new Input( '#ss-description' ), new PostExcerptInput( '#excerpt' ), new PostContentInput( '#content' ), 50, 160, true );
		postTitle.init();
		postDescription.init();
	}

	// Term.
	if ( document.body.classList.contains( 'term-php' ) ) {
		const termTitle = new Field( new Input( '#ss-title' ), new TermTitleInput( '#name' ), 0, 60 );
		const termDescription = new Field( new Input( '#ss-description' ), new Input( '#description' ), 50, 160, true );
		termTitle.init();
		termDescription.init();
	}
} )( window, document, wp, jQuery, ss );
