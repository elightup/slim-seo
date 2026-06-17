( function () {
    'use strict';

    const { useSelect, useDispatch } = wp.data;
    const { useState, useEffect, useRef } = wp.element;
    const { addFilter } = wp.hooks;
    const { createHigherOrderComponent } = wp.compose;
    const el = wp.element.createElement;

    const getRestBase = taxonomy => {
        const taxObj = wp.data.select( 'core' ).getTaxonomy( taxonomy );
		
        return taxObj && taxObj.rest_base ? taxObj.rest_base : taxonomy;
    }

    const useCheckedTerms = taxonomy => {
        const restBase = getRestBase( taxonomy );
        
		return useSelect(
            select => select( 'core/editor' ).getEditedPostAttribute( restBase ) || [],
            [ restBase ]
        );
    }

    const useTermObjects = ( taxonomy, ids ) => {
        return useSelect( select => {
            if ( !ids || 0 === ids.length ) {
				return [];
			}

			return ids.map( id => select( 'core' ).getEntityRecord( 'taxonomy', taxonomy, id ) ).filter( Boolean );
        }, [ taxonomy, ids.join( ',' ) ] );
    }

    const PrimaryTermDropdown = ( { taxonomy, taxonomyData } ) => {
        let { label, primaryValue, metaKey } = taxonomyData;

        primaryValue = parseInt( primaryValue || 0, 10 );

        const checkedIds = useCheckedTerms( taxonomy );
        const termObjects = useTermObjects( taxonomy, checkedIds );
        const { editPost } = useDispatch( 'core/editor' );
        const [ primaryId, setPrimaryId ] = useState( primaryValue );
        const initialised = useRef( false );

        const update = value => {
            setPrimaryId( value );
            editPost( { meta: { [ metaKey ]: value } } );
        };
        
        useEffect( () => {
            if ( checkedIds.length < 1 ) {
                update( 0 );

                return;
            }

            if ( !initialised.current ) {
                initialised.current = true;
                
                if ( !checkedIds.includes( primaryId ) ) {
                    update( checkedIds[ 0 ] );
                }
                
                return;
            }

            if ( !checkedIds.includes( primaryId ) ) {
                update( checkedIds[ 0 ] );
            }
        }, [ checkedIds.join( ',' ) ] );

        const handleChange = e => {
            update( parseInt( e.target.value, 10 ) );
        }

        if ( checkedIds.length < 2 ) {
			return null;
		}

        const options = termObjects.length > 0 ? termObjects : checkedIds.map( id => { return { id: id, name: '#' + id }; } );

        return el(
			'div',
			{
				className: 'ss-primary-term-wrapper'
			},
            el(
				'label',
				{
					className: 'ss-primary-term-label',
					htmlFor: 'ss-primary-term-select-' + taxonomy
				},
                ssPrimaryTerm.primaryText + ' ' + label
            ),
            el(
				'select',
				{
					id: 'ss-primary-term-select-' + taxonomy,
					className: 'ss-primary-term-select',
					value: primaryId,
					onChange: handleChange,
				},
                options.map( term => {
                    return el(
						'option',
						{
							key: term.id,
							value: term.id 
						},
						term.name
					);
                } )
            )
        );
    }

    const withPrimaryTermDropdown = createHigherOrderComponent( OriginalComponent => {
        return props => {
            const taxonomy = props.slug;
            const taxonomyData = ssPrimaryTerm.taxonomies[ taxonomy ];

            return el(
                wp.element.Fragment,
                null,
                el(
					OriginalComponent,
					props
				),
                taxonomyData ? el( PrimaryTermDropdown, { taxonomy: taxonomy, taxonomyData: taxonomyData } ) : null
            );
        };
    }, 'withPrimaryTermDropdown' );

    addFilter(
        'editor.PostTaxonomyType',
        'taxonomy-primary-term/add-dropdown',
        withPrimaryTermDropdown
    );

} )();