import { RawHTML } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';

export default ( { id, label, postType } ) => {
	const { link, title, edit } = postType;

	return <>
		<h3>{ label }</h3>
		<RawHTML>
			{ sprintf(
				__( '<p>You have a page <a href="%s">%s</a> that has the same slug as the post type archive slug. So WordPress will set it as the archive page for the <code>%s</code> post type.</p><p>To set the meta tags for the page, please <a href="%s">set on the edit page</a>.</p>', 'slim-seo' ),
				link,
				title,
				id,
				edit
			) }
		</RawHTML>
	</>;
};