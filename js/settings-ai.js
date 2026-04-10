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

	const print = ( el, text ) => el.innerHTML = `<p>${ text }</p>`;

	function openLogModal() {
		logModal.style.display = '';
		logOverlay.style.display = '';
		document.body.classList.add( 'modal-open' );
	}

	function closeLogModal() {
		logModal.style.display = 'none';
		logOverlay.style.display = 'none';
		document.body.classList.remove( 'modal-open' );
	}

	closeLogBtn.addEventListener( 'click', closeLogModal );
	logOverlay.addEventListener( 'click', closeLogModal );
	showLogBtn.addEventListener( 'click', openLogModal );

	function appendLog( entry ) {
		const lvl = ( entry.level || 'INFO' ).toUpperCase();
		const cls = lvl === 'ERROR' ? 'ss-danger' : lvl === 'OK' ? 'ss-success' : lvl === 'SKIP' ? 'ss-warning' : '';
		const tr = document.createElement( 'tr' );
		tr.className = cls;
		[ entry.time, lvl, entry.ref, entry.message ].forEach( val => {
			const td = document.createElement( 'td' );
			td.textContent = val || '';
			tr.appendChild( td );
		} );
		logBody.appendChild( tr );
	}

	function readPayload() {
		const pt = [];
		document.querySelectorAll( "input[name='ss_bulk_post_types[]']:checked" ).forEach( el => pt.push( el.value ) );
		const tx = [];
		document.querySelectorAll( "input[name='ss_bulk_taxonomies[]']:checked" ).forEach( el => tx.push( el.value ) );
		let batch = parseInt( document.querySelector( "input[name='ss_bulk_batch_size']" )?.value || '3', 10 );
		batch = Math.max( 1, Math.min( 10, batch ) );

		return {
			post_types: pt,
			taxonomies: tx,
			batch_size: batch,
			skip_title: !! document.querySelector( "input[name='ss_bulk_skip_title']" )?.checked,
			skip_description: !! document.querySelector( "input[name='ss_bulk_skip_description']" )?.checked,
		};
	}

	async function runStep() {
		if ( ! state.running ) {
			return;
		}

		const body = Object.assign( {}, readPayload(), { phase: state.phase, offset: state.offset } );

		try {
			const d = await wp.apiFetch( {
				path: '/slim-seo/bulk-ai/chunk',
				method: 'POST',
				data: body,
			} );

			( d.log_entries || [] ).forEach( appendLog );

			if ( d.batch_stats ) {
				state.totalAi += d.batch_stats.ai_calls || 0;
				state.totalErr += d.batch_stats.errors || 0;
			}

			state.phase = d.next_phase;
			state.offset = d.next_offset;

			if ( d.done ) {
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

	function stop() {
		state.running = false;
		bulkButton.disabled = false;
		bulkButton.textContent = buttonLabel;
	}

	bulkButton.addEventListener( 'click', () => {
		if ( state.running ) {
			return;
		}

		const pts = document.querySelectorAll( "input[name='ss_bulk_post_types[]']:checked" ).length;
		state.phase = pts ? 'posts' : 'terms';
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
