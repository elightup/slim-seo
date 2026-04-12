( function( i18n ) {
	'use strict';

	const providerSelect = document.getElementById( 'ss-ai-provider' );
	const modelSelect = document.getElementById( 'ss-ai-model' );

	if ( !providerSelect || !modelSelect ) {
		return;
	}

	/**
	 * Renders model options into the model select and restores the saved value when present.
	 *
	 * @param {Array<{value: string, label: string}>} models     Options returned from the REST API.
	 * @param {string}                                 savedModel Model id to select after populate; empty skips.
	 */
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

	/**
	 * Loads models for a provider and updates the model dropdown.
	 *
	 * @param {string} provider Provider slug (e.g. openai).
	 * @param {string} savedModel Model id to preserve after fetch; may be empty.
	 */
	function fetchModels( provider, savedModel ) {
		wp.apiFetch( {
			path: `/slim-seo/ai/models?provider=${ encodeURIComponent( provider ) }`,
		} ).then( models => {
			populateModels( models, savedModel );
		} ).catch( () => {
			populateModels( [] );
		} );
	}

	/**
	 * Initializes the model list from the current provider on page load.
	 */
	function init() {
		const provider = providerSelect.value || 'openai';
		fetchModels( provider, i18n.model );
	}

	providerSelect.addEventListener( 'change', e => fetchModels( e.target.value, '' ) );

	init();

	// Bulk AI Generation.
	const bulkButton = document.getElementById( 'ss-bulk-ai-start' );
	const progressEl = document.getElementById( 'ss-bulk-ai-progress' );
	const logBody = document.getElementById( 'ss-bulk-ai-log-body' );
	const showLogBtn = document.getElementById( 'ss-bulk-ai-show-log' );
	const logModal = document.getElementById( 'ss-bulk-ai-log-modal' );
	const logOverlay = document.getElementById( 'ss-bulk-ai-log-overlay' );
	const closeLogBtn = document.getElementById( 'ss-bulk-ai-close-log' );

	if ( ! bulkButton ) {
		return;
	}

	let state = { phase: 'posts', offset: 0, running: false, totalAi: 0, totalErr: 0 };
	const buttonLabel = bulkButton.textContent;
	const bulk = i18n.bulk;

	/**
	 * Writes a single paragraph of HTML into a bulk-generation status element.
	 *
	 * @param {HTMLElement} element Target container (e.g. progress area).
	 * @param {string}      text    Message to show.
	 */
	const print = ( element, text ) => element.innerHTML = `<p>${ text }</p>`;

	/**
	 * Shows the bulk generation log modal and overlay.
	 */
	function openLogModal() {
		logModal.style.display = '';
		logOverlay.style.display = '';
		document.body.classList.add( 'modal-open' );
	}

	/**
	 * Hides the bulk generation log modal and overlay.
	 */
	function closeLogModal() {
		logModal.style.display = 'none';
		logOverlay.style.display = 'none';
		document.body.classList.remove( 'modal-open' );
	}

	closeLogBtn.addEventListener( 'click', closeLogModal );
	logOverlay.addEventListener( 'click', closeLogModal );
	showLogBtn.addEventListener( 'click', openLogModal );

	/**
	 * Appends one log row to the bulk generation log table.
	 *
	 * @param {{time?: string, level?: string, ref?: string, message?: string}} entry Log line fields from the REST response or client.
	 */
	function appendLog( entry ) {
		const level = ( entry.level || 'INFO' ).toUpperCase();
		const rowClassName = level === 'ERROR' ? 'ss-danger' : level === 'OK' ? 'ss-success' : level === 'SKIP' ? 'ss-warning' : '';
		const tableRow = document.createElement( 'tr' );
		tableRow.className = rowClassName;
		[ entry.time, level, entry.ref, entry.message ].forEach( columnValue => {
			const tableCell = document.createElement( 'td' );
			tableCell.textContent = columnValue || '';
			tableRow.appendChild( tableCell );
		} );
		logBody.appendChild( tableRow );
	}

	/**
	 * Reads bulk tool form fields into the request body shape expected by the chunk endpoint.
	 *
	 * @returns {{post_types: string[], taxonomies: string[], skip_title: boolean, skip_description: boolean}} Payload for one chunk request.
	 */
	function readPayload() {
		const selectedPostTypes = [];
		document.querySelectorAll( "input[name='ss_bulk_post_types[]']:checked" ).forEach( input => selectedPostTypes.push( input.value ) );
		const selectedTaxonomies = [];
		document.querySelectorAll( "input[name='ss_bulk_taxonomies[]']:checked" ).forEach( input => selectedTaxonomies.push( input.value ) );

		return {
			post_types: selectedPostTypes,
			taxonomies: selectedTaxonomies,
			skip_title: !! document.querySelector( "input[name='ss_bulk_skip_title']" )?.checked,
			skip_description: !! document.querySelector( "input[name='ss_bulk_skip_description']" )?.checked,
		};
	}

	/**
	 * Posts one bulk-AI chunk, updates state and log UI, then recurses until the run is done or errors.
	 */
	async function runStep() {
		if ( ! state.running ) {
			return;
		}

		const requestBody = Object.assign( {}, readPayload(), { phase: state.phase, offset: state.offset } );

		try {
			const response = await wp.apiFetch( {
				path: '/slim-seo/bulk-ai/chunk',
				method: 'POST',
				data: requestBody,
			} );

			( response.log_entries || [] ).forEach( appendLog );

			if ( response.batch_stats ) {
				state.totalAi += response.batch_stats.ai_calls || 0;
				state.totalErr += response.batch_stats.errors || 0;
			}

			state.phase = response.next_phase;
			state.offset = response.next_offset;

			if ( response.done ) {
				print( progressEl, bulk.done + ' ' + bulk.generated + ': ' + state.totalAi + ( state.totalErr ? ', ' + bulk.errors + ': ' + state.totalErr : '' ) );
				stop();
			} else {
				await runStep();
			}
		} catch {
			appendLog( { level: 'ERROR', message: bulk.restFail } );
			stop();
		}
	}

	/**
	 * Ends a bulk generation run and restores the start button.
	 */
	function stop() {
		state.running = false;
		bulkButton.disabled = false;
		bulkButton.textContent = buttonLabel;
	}

	bulkButton.addEventListener( 'click', () => {
		if ( state.running ) {
			return;
		}

		const checkedPostTypeCount = document.querySelectorAll( "input[name='ss_bulk_post_types[]']:checked" ).length;
		state.phase = checkedPostTypeCount ? 'posts' : 'terms';
		state.offset = 0;
		state.running = true;
		state.totalAi = 0;
		state.totalErr = 0;

		logBody.innerHTML = '';
		progressEl.innerHTML = '';
		bulkButton.disabled = true;
		bulkButton.textContent = bulk.running;
		showLogBtn.style.display = '';
		appendLog( { time: new Date().toLocaleTimeString( [], { hour12: false } ), level: 'INFO', ref: 'System', message: bulk.started } );

		runStep();
	} );
} )( ssAiSettings );
