import { __, _n, sprintf } from '@wordpress/i18n';
import { linksFromText } from '../helper/text';
import Base from './Base';

const LinksInternal = ( { postContent } ) => {
	const links = linksFromText( postContent );
	const internalOutboundLinks = links.filter( link => {
		return 0 === link.indexOf( SSContentAnalysis.homeURL ) || 0 === link.indexOf( '/' ) || 0 === link.indexOf( '#' );
	} );
	const internalOutboundLinksLength = internalOutboundLinks.length;

	return (
		<Base title={ __( 'Links', 'slim-seo' ) } success={ internalOutboundLinksLength > 0 }>
			<h4>{ __( 'Outbound', 'slim-seo' ) }</h4>
			{
				internalOutboundLinksLength > 0
				? <p>{ sprintf( _n( '%d link internal.', '%d links internal.', internalOutboundLinksLength, 'slim-seo' ), internalOutboundLinksLength ) }</p>
				: <p>{ __( 'No link found.', 'slim-seo' ) }</p>
			}
			<h4>{ __( 'Inbound', 'slim-seo' ) }</h4>
			{
				SSContentAnalysis.SSLMActivated
				? <p>{ __( 'Check Internal Inbound tab in Link Manager to get more detail.', 'slim-seo' ) }</p>
				: <p>{ __( 'Please install.', 'slim-seo' ) } <a href="https://wpslimseo.com/products/slim-seo-link-manager/">{ __( 'Link Manager plugin.', 'slim-seo' ) }</a></p>
			}
		</Base>
	);
};

export default LinksInternal;