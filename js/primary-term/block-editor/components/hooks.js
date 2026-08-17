import { useSelect } from '@wordpress/data';

const getRestBase = taxonomy => {
	const taxObj = wp.data.select( 'core' ).getTaxonomy( taxonomy );

	return taxObj?.rest_base ?? taxonomy;
};

export const useCheckedTerms = taxonomy => {
	const restBase = getRestBase( taxonomy );

	return useSelect(
		select => select( 'core/editor' ).getEditedPostAttribute( restBase ) || [],
		[ restBase ]
	);
};

export const useTermObjects = ( taxonomy, ids ) => {
	return useSelect( select => {
		if ( !ids?.length ) {
			return [];
		}
		
		return select( 'core' ).getEntityRecords( 'taxonomy', taxonomy, { include: ids, per_page: ids.length } ) ?? [];
	}, [ taxonomy, ids.join( ',' ) ] );
};
