( function( i18n ) {
	'use strict';

	const providerSelect = document.getElementById( 'ss-ai-provider' );
	const modelSelect = document.getElementById( 'ss-ai-model' );

	if ( !providerSelect || !modelSelect ) {
		return;
	}

	function populateModels( models, savedModel ) {
		modelSelect.innerHTML = '';

		if ( models.length === 0 ) {
			const option = document.createElement( 'option' );
			option.value = '';
			option.textContent = i18n.text.noModelsAvailable;
			modelSelect.appendChild( option );
			return;
		}

		models.forEach( function( model ) {
			const option = document.createElement( 'option' );
			option.value = model.value;
			option.textContent = model.label;
			modelSelect.appendChild( option );
		} );

		if ( savedModel ) {
			modelSelect.value = savedModel;
		}
	}

	function fetchModels( provider, savedModel ) {
		wp.apiFetch( {
			path: `/slim-seo/ai/models?provider=${ encodeURIComponent( provider ) }`,
		} ).then( models => {
			populateModels( models, savedModel );
		} ).catch( () => {
			populateModels( [] );
		} );
	}

	function init() {
		const provider = providerSelect.value || 'openai';
		fetchModels( provider, i18n.model );
	}

	providerSelect.addEventListener( 'change', e => fetchModels( e.target.value, '' ) );

	init();
} )( ssAiSettings );
