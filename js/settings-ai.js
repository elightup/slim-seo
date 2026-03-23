(function () {
    'use strict';

    const providerSelect = document.getElementById('ss-ai-provider');
    const modelSelect = document.getElementById('ss-ai-model');

    if (!providerSelect || !modelSelect) {
        return;
    }

    const providerModels = {
        openai: [
            { value: 'gpt-4.1-mini', label: 'GPT-4.1 Mini' },
            { value: 'gpt-4.1', label: 'GPT-4.1' },
            { value: 'gpt-4o-mini', label: 'GPT-4o Mini' },
            { value: 'gpt-4o', label: 'GPT-4o' },
        ],
        google: [
            { value: 'gemini-2.0-flash-exp', label: 'Gemini 2.0 Flash (Experimental)' },
            { value: 'gemini-2.0-flash', label: 'Gemini 2.0 Flash' },
            { value: 'gemini-1.5-flash-8b', label: 'Gemini 1.5 Flash 8B' },
            { value: 'gemini-1.5-flash', label: 'Gemini 1.5 Flash' },
        ],
        anthropic: [
            { value: 'claude-sonnet-4-20250514', label: 'Claude Sonnet 4 (Latest)' },
            { value: 'claude-3-5-sonnet-20240620', label: 'Claude 3.5 Sonnet' },
            { value: 'claude-3-5-haiku-20240620', label: 'Claude 3.5 Haiku' },
        ],
        openrouter: [
            { value: 'openai/gpt-4.1-mini', label: 'GPT-4.1 Mini' },
            { value: 'openai/gpt-4o-mini', label: 'GPT-4o Mini' },
            { value: 'anthropic/claude-3.5-sonnet-20240620', label: 'Claude 3.5 Sonnet' },
            { value: 'google/gemini-2.0-flash-exp', label: 'Gemini 2.0 Flash' },
            { value: 'meta/llama-3.3-70b-instruct', label: 'Llama 3.3 70B' },
        ],
    };

    function populateModels(provider) {
        const models = providerModels[provider] || [];
        const currentValue = modelSelect.value;

        modelSelect.innerHTML = '';

        if (models.length === 0) {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'Select a provider first';
            modelSelect.appendChild(option);
            return;
        }

        models.forEach(function(model) {
            const option = document.createElement('option');
            option.value = model.value;
            option.textContent = model.label;
            modelSelect.appendChild(option);
        });

        // Try to preserve selection if it exists in new provider
        let matchingOption = null;
        for (let i = 0; i < modelSelect.options.length; i++) {
            if (modelSelect.options[i].value === currentValue) {
                matchingOption = modelSelect.options[i];
                break;
            }
        }
        if (matchingOption) {
            modelSelect.value = currentValue;
        }
    }

    // Populate on page load based on saved provider
    const savedProvider = providerSelect.value || 'openai';
    populateModels(savedProvider);

    // Handle provider change
    providerSelect.addEventListener('change', function() {
        populateModels(this.value);
    });
})();
