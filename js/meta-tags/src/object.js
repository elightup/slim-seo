import { Field, Input, normalize } from "./common";

let contentEditor;
const $ = jQuery;

const isBlockEditor = document.body.classList.contains( 'block-editor-page' );
const formatTitle = ( title = '' ) => {
	const values = {
		site: ss.site.title,
		tagline: ss.site.description,
		title
	};
	return ss.title.parts.map( part => values[ part ] ?? '' ).filter( part => part ).join( ` ${ ss.title.separator } ` );
};

class PostTitleInput extends Input {
	get value() {
		if ( ss.isHome ) {
			return formatTitle();
		}
		const value = isBlockEditor ? normalize( wp.data.select( 'core/editor' ).getEditedPostAttribute( 'title' ) ) : super.value;
		return formatTitle( value );
	}
	onChange( callback ) {
		isBlockEditor ? wp.data.subscribe( callback ) : super.onChange( callback );
	}
}

class PostExcerptInput extends Input {
	get value() {
		return isBlockEditor ? normalize( wp.data.select( 'core/editor' ).getEditedPostAttribute( 'excerpt' ) ) : super.value;
	}
	onChange( callback ) {
		isBlockEditor ? wp.data.subscribe( callback ) : super.onChange( callback );
	}
}

class PostContentInput extends Input {
	get value() {
		if ( isBlockEditor ) {
			return normalize( wp.data.select( 'core/editor' ).getEditedPostContent() );
		}
		return contentEditor && !contentEditor.isHidden() ? normalize( contentEditor.getContent() ) : super.value;
	}
	onChange( callback ) {
		if ( isBlockEditor ) {
			wp.data.subscribe( callback );
			return;
		}
		super.onChange( callback );

		$( document ).on( 'tinymce-editor-init', ( event, editor ) => {
			if ( editor.id !== 'content' ) {
				return;
			}
			contentEditor = editor;
			editor.on( 'input keyup', callback );
		} );
	}
}

class TermTitleInput extends Input {
	get value() {
		return formatTitle( super.value );
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
		this.ref2.onChange( this.updateCounter );
		this.ref2.onChange( this.updatePreview );
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
