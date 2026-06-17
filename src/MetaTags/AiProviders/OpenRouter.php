<?php
namespace SlimSEO\MetaTags\AiProviders;

/**
 * OpenRouter uses the same API as OpenAI!
 */
class OpenRouter extends OpenAI {
	public function get_api_url(): string {
		return 'https://openrouter.ai/api/v1/responses';
	}

	/**
	 * @link https://openrouter.ai/models?input_modalities=text&categories=marketing/seo
	 */
	public function get_models(): array {
		return [
			'openai/gpt-5.5',
			'openai/gpt-5.4',
			'openai/gpt-5.4-mini',
			'openai/gpt-5.4-nano',
			'openai/gpt-5',
			'openai/gpt-5-mini',
			'openai/gpt-5-nano',
			'openai/gpt-4.1',
			'openai/gpt-4.1-mini',
			'openai/gpt-4.1-nano',
			'openai/gpt-4o',
			'openai/gpt-4o-mini',
			'anthropic/claude-opus-4-6',
			'anthropic/claude-sonnet-4.6',
			'anthropic/claude-haiku-4-5',
			'google/gemini-3.1-pro-preview',
			'google/gemini-3.1-flash-preview',
			'google/gemini-3.1-flash-lite-preview',
			'google/gemini-2.5-pro',
			'google/gemini-2.5-flash',
			'google/gemini-2.5-flash-lite',
			'deepseek/deepseek-v3.2',
		];
	}
}
