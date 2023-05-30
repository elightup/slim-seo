import { Field, Input } from "./common";

const select = document.querySelector( '#ss-post-type-select' );
select.addEventListener( 'change', e => {
	document.querySelectorAll( '.ss-post-type-settings' ).forEach( el => el.classList.remove( 'ss-is-active' ) );
	document.querySelector( `.ss-post-type-settings--${ e.target.value }` ).classList.add( 'ss-is-active' );
} );

// Selected post type when page is loaded.
document.querySelector( `.ss-post-type-settings--${ select.value }` ).classList.add( 'ss-is-active' );

ss.items.forEach( item => {
	const TitleInput = new Field(
		new Input( `#ss-title-${ item }` ),
		new Input( `#ss-title-preview-${ item }` ),
		0,
		60
	);
	const Description = new Field(
		new Input( `#ss-description-${ item }` ),
		new Input( `#ss-description-preview-${ item }` ),
		50,
		160,
		true
	);
	TitleInput.init();
	Description.init();
} );
