( function ( window, document, wp, $, _, ss ) {
	var contentEditor;

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
		getValue() {
			return this.el ? normalize( this.el.value ) : '';
		}
		addEventListener( callback ) {
			this.el && this.el.addEventListener( 'input', callback );
		}
	}

	class PostTitleInput extends Input {
		getValue() {
			var value = isBlockEditor() ? normalize( wp.data.select( 'core/editor' ).getEditedPostAttribute( 'title' ) ) : super.getValue();
			return value + ' - ' + ss.site.title;
		}
		addEventListener( callback ) {
			isBlockEditor() ? wp.data.subscribe( callback ) : super.addEventListener( callback );
		}
	}

	class PostExcerptInput extends Input {
		getValue() {
			return isBlockEditor() ? normalize( wp.data.select( 'core/editor' ).getEditedPostAttribute( 'excerpt' ) ) : super.getValue();
		}
		addEventListener( callback ) {
			isBlockEditor() ? wp.data.subscribe( callback ) : super.addEventListener( callback );
		}
	}

	class PostContentInput extends Input {
		getValue() {
			if ( isBlockEditor() ) {
				return normalize( wp.data.select( 'core/editor' ).getEditedPostContent() );
			}
			return contentEditor && ! contentEditor.isHidden() ? normalize( contentEditor.getContent() ) : super.getValue();
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

	class Field {
		constructor( input, ref ) {
			this.input = input;
			this.ref   = ref;

			this.updateCounter = _.debounce( this.updateCounter.bind( this ), 200 );
			this.updatePreview = _.debounce( this.updatePreview.bind( this ), 200 );
		}
		updatePreview() {
			this.input.el.placeholder = this.ref.getValue();
		}
		updateCounter() {
			var value = this.input.getValue();
			if ( ! value ) {
				value = this.ref.getValue();
			}
			this.input.el.nextElementSibling.querySelector( '.ss-number' ).textContent = value.length;
		}
		listenToChange() {
			this.input.addEventListener( this.updateCounter );
			this.ref.addEventListener( this.updateCounter );
			this.ref.addEventListener( this.updatePreview );
		}
		init() {
			this.updatePreview();
			this.updateCounter();
			this.listenToChange();
		}
	}

	class PostDescriptionField extends Field {
		constructor( input, ref, ref2 ) {
			super( input, ref );
			this.ref2 = ref2;
		}
		updatePreview() {
			var value = this.ref.getValue();
			if ( ! value ) {
				value = this.ref2.getValue().substring( 0, 160 ); // Only truncate for post content.
			}
			this.input.el.placeholder = value;
		}
		updateCounter() {
			var value = this.input.getValue();
			if ( ! value ) {
				value = this.ref.getValue();
			}
			if ( ! value ) {
				value = this.ref2.getValue().substring( 0, 160 ); // Only truncate for post content.
			}
			this.input.el.nextElementSibling.querySelector( '.ss-number' ).textContent = value.length;
		}
		listenToChange() {
			this.input.addEventListener( this.updateCounter );
			this.ref.addEventListener( this.updateCounter );
			this.ref.addEventListener( this.updatePreview );
			this.ref2.addEventListener( this.updateCounter );
			this.ref2.addEventListener( this.updatePreview );
		}
	}

	// Post.
	if ( document.body.classList.contains( 'post-new-php' ) || document.body.classList.contains( 'post-php' ) ) {
		var postTitle = new Field( new Input( '#ss-title' ), new PostTitleInput( '#title' ) );
		var postDescription = new PostDescriptionField( new Input( '#ss-description' ), new PostExcerptInput( '#excerpt' ), new PostContentInput( '#content' ) );
		postTitle.init();
		postDescription.init();
	}

	// Term.
	if ( document.body.classList.contains( 'term-php' ) ) {
		var termTitle = new Field( new Input( '#ss-title' ), new Input( '#name' ) );
		var termDescription = new Field( new Input( '#ss-description' ), new Input( '#description' ) );
		termTitle.init();
		termDescription.init();
	}
} )( window, document, wp, jQuery, _, ss );
