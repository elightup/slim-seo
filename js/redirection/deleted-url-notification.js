document.addEventListener( 'click', ( { target } ) => {
	const notice = target.closest( '.ss-redirection-deleted-url-notification' );

	if ( target.classList.contains( 'notice-dismiss' ) && notice ) {
		fetch( ajaxurl, {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: new URLSearchParams( {
				action: 'slim_seo_redirection_dismiss_deleted_url_notification',
				nonce: SSRedirectionDeletedURLNotification.nonce,
				index: notice.dataset.index,
			} ),
		} ).catch( () => {} );
	}
} );
