import { Field, Input } from "./common";

document.querySelector( '#ss-post-type-select' ).addEventListener( 'change', e => {
	document.querySelectorAll( '.ss-post-type-settings' ).forEach( el => el.classList.remove( 'ss-is-active' ) );
	document.querySelector( `.ss-post-type-settings--${ e.target.value }` ).classList.add( 'ss-is-active' );
} );

ss.items.forEach( item => {
	const TitleInput = new Field( new Input( `#ss-title-${ item }` ), null, 0, 60 );
	const Description = new Field( new Input( `#ss-description-${ item }` ), null, 50, 160, true );
	TitleInput.init();
	Description.init();
} );
