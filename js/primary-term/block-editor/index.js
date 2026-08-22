import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import Dropdown from './components/dropdown';

const withPrimaryTermDropdown = createHigherOrderComponent( OriginalComponent => {
	return props => {
		const taxonomy = props.slug;
		const taxonomyData = ssPrimaryTerm.taxonomies[ taxonomy ];

		return (
			<>
				<OriginalComponent { ...props } />
				{ taxonomyData && <Dropdown taxonomy={ taxonomy } taxonomyData={ taxonomyData } /> }
			</>
		);
	};
}, 'withPrimaryTermDropdown' );

addFilter(
	'editor.PostTaxonomyType',
	'taxonomy-primary-term/add-dropdown',
	withPrimaryTermDropdown
);
