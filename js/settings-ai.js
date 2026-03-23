(function () {
    'use strict';

    const providerSelect = document.getElementById('ss-ai-provider');
    const modelSelect = document.getElementById('ss-ai-model');

    if (!providerSelect || !modelSelect) {
        return;
    }

    function populateModels(models, savedModel) {
        modelSelect.innerHTML = '';

        if (models.length === 0) {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'No models available';
            modelSelect.appendChild(option);
            return;
        }

        models.forEach(function(model) {
            const option = document.createElement('option');
            option.value = model.value;
            option.textContent = model.label;
            modelSelect.appendChild(option);
        });

        if (savedModel) {
            modelSelect.value = savedModel;
        }
    }

    async function fetchModels(provider, savedModel) {
        try {
            const url = new URL('/wp-json/slim-seo/ai/models');
            url.searchParams.set('provider', provider);

            const response = await fetch(url.toString(), {
                headers: {
                    'X-WP-Nonce': window.ssAiSettings.nonce
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch models');
            }

            const models = await response.json();
            populateModels(models, savedModel);
        } catch (error) {
            console.error('Error fetching models:', error);
            populateModels([], savedModel);
        }
    }

    function init() {
        const provider = providerSelect.value || 'openai';
        const savedModel = modelSelect.value;
        fetchModels(provider, savedModel);
    }

    providerSelect.addEventListener('change', function() {
        fetchModels(this.value, '');
    });

    init();
})();
