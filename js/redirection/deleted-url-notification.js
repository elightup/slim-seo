document.addEventListener( 'click', ( { target } ) => {
	if ( ! target.classList.contains( 'notice-dismiss' ) ) {
		return;
	}

	const notice = target.closest( '.ss-redirection-deleted-url-notification' );

	if ( ! notice ) {
		return;
	}

	fetch( ajaxurl, {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: new URLSearchParams( {
			action: 'slim_seo_redirection_dismiss_deleted_url_notification',
			nonce: SSRedirectionDeletedURLNotification.nonce,
			index: notice.dataset.index,
		} ),
	} ).catch( () => {} );
} );
