const openMediaPopup = () => {
	let frame;

	const clickHandle = e => {
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
	};

	const selectButtons = document.querySelectorAll( '.ss-select-image' );
	selectButtons.forEach( button => button.addEventListener( 'click', clickHandle ) );
};

export const normalize = html => !html ? '' : html
	.replace( /<(script|style)[^>]*?>.*?<\/\1>/gm, '' ) // Remove <style> & <script>
	.replace( /<[^>]*?>/gm, '' )                        // Remove other HTML tags.
	.replace( /\s+/gm, ' ' )                            // Remove duplicated white spaces.
	.trim();

export class Input {
	constructor( selector ) {
		this.el = document.querySelector( selector );
	}
	get value() {
		return this.el ? normalize( this.el.value ) : '';
	}
	onChange( callback ) {
		this.el && this.el.addEventListener( 'input', callback );
	}
}

export class Field {
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
		const label = this.input.el.parentNode.previousElementSibling;
		label.classList.remove( 'ss-success', 'ss-warning' );
		label.classList.add( isGood ? 'ss-success' : 'ss-warning' );
	}
	addEventListener() {
		this.input.onChange( this.updateCounter );
		if ( this.ref ) {
			this.ref.onChange( this.updateCounter );
			this.ref.onChange( this.updatePreview );
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

openMediaPopup();
