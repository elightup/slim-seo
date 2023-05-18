import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, PanelRow, TextControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';

export default function Edit( { attributes, setAttributes } ) {
	const {
		separator,
		taxonomy,
		display_current,
		label_home,
		label_search,
		label_404
	} = attributes;

	return (
		<div { ...useBlockProps() }>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'slim-seo' ) }>
					<PanelRow>
						<TextControl
							label={ __( 'Separator', 'slim-seo' ) }
							help={ __( 'The separator between breadcrumb items.', 'slim-seo' ) }
							value={ separator }
							onChange={ separator => setAttributes( { separator } ) }
						/>
					</PanelRow>
					<PanelRow>
						<TextControl
							label={ __( 'Taxonomy', 'slim-seo' ) }
							help={ __( 'The taxonomy that you want to output in the breadcrumb trail when you are on a singular page.', 'slim-seo' ) }
							value={ taxonomy }
							onChange={ taxonomy => setAttributes( { taxonomy } ) }
						/>
					</PanelRow>
					<PanelRow>
						<ToggleControl
							label={ __( 'Display current page', 'slim-seo' ) }
							help={ __( 'Whether or not to display the current page.', 'slim-seo' ) }
							checked={ display_current }
							onChange={ () => setAttributes( { display_current: !display_current } ) }
						/>
					</PanelRow>
					<PanelRow>
						<TextControl
							label={ __( 'Label home', 'slim-seo' ) }
							help={ __( 'Label for the home item.', 'slim-seo' ) }
							value={ label_home }
							onChange={ label_home => setAttributes( { label_home } ) }
						/>
					</PanelRow>
					<PanelRow>
						<TextControl
							label={ __( 'Label search', 'slim-seo' ) }
							help={ __( 'Label for the search results page.', 'slim-seo' ) }
							value={ label_search }
							onChange={ label_search => setAttributes( { label_search } ) }
						/>
					</PanelRow>
					<PanelRow>
						<TextControl
							label={ __( 'Label 404', 'slim-seo' ) }
							help={ __( 'Label for the 404 page.', 'slim-seo' ) }
							value={ label_404 }
							onChange={ label_404 => setAttributes( { label_404 } ) }
						/>
					</PanelRow>
				</PanelBody>
			</InspectorControls>
			<ServerSideRender
				block="slim-seo/breadcrumbs"
				attributes={ attributes }
			/>
		</div>
	);
}
