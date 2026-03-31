<?php
namespace SlimSEO\MetaTags\AiProviders;

/**
 * OpenRouter uses the same API as OpenAI!
 */
class OpenRouter extends OpenAI {
	public function get_api_url(): string {
		return 'https://openrouter.ai/api/v1/responses';
	}

	public function get_models(): array {
		return [
			[
				'value' => 'openai/gpt-5.4',
				'label' => 'OpenAI: GPT-5.4',
			],
			[
				'value' => 'openai/gpt-5.4-mini',
				'label' => 'OpenAI: GPT-5.4 Mini',
			],
			[
				'value' => 'openai/gpt-5.4-nano',
				'label' => 'OpenAI: GPT-5.4 Nano',
			],
			[
				'value' => 'openai/gpt-5',
				'label' => 'OpenAI: GPT-5',
			],
			[
				'value' => 'openai/gpt-5-mini',
				'label' => 'OpenAI: GPT-5 Mini',
			],
			[
				'value' => 'openai/gpt-5-nano',
				'label' => 'OpenAI: GPT-5 Nano',
			],
			[
				'value' => 'openai/gpt-4.1',
				'label' => 'OpenAI: GPT-4.1',
			],
			[
				'value' => 'openai/gpt-4.1-mini',
				'label' => 'OpenAI: GPT-4.1 Mini',
			],
			[
				'value' => 'openai/gpt-4.1-nano',
				'label' => 'OpenAI: GPT-4.1 Nano',
			],
			[
				'value' => 'openai/gpt-4o',
				'label' => 'OpenAI: GPT-4o',
			],
			[
				'value' => 'openai/gpt-4o-mini',
				'label' => 'OpenAI: GPT-4o Mini',
			],
			[
				'value' => 'anthropic/claude-opus-4-6',
				'label' => 'Anthropic: Claude Opus 4.6',
			],
			[
				'value' => 'anthropic/claude-sonnet-4.6',
				'label' => 'Anthropic: Claude Sonnet 4.6',
			],
			[
				'value' => 'anthropic/claude-haiku-4-5',
				'label' => 'Anthropic: Claude Haiku 4.5',
			],
			[
				'value' => 'google/gemini-3.1-pro-preview',
				'label' => 'Google: Gemini 3.1 Pro Preview',
			],
			[
				'value' => 'google/gemini-3.1-flash-preview',
				'label' => 'Google: Gemini 3.1 Flash Preview',
			],
			[
				'value' => 'google/gemini-3.1-flash-lite-preview',
				'label' => 'Google: Gemini 3.1 Flash Lite Preview',
			],
			[
				'value' => 'google/gemini-2.5-pro',
				'label' => 'Google: Gemini 2.5 Pro',
			],
			[
				'value' => 'google/gemini-2.5-flash',
				'label' => 'Google: Gemini 2.5 Flash',
			],
			[
				'value' => 'google/gemini-2.5-flash-lite',
				'label' => 'Google: Gemini 2.5 Flash Lite',
			],
			[
				'value' => 'deepseek/deepseek-v3.2',
				'label' => 'DeepSeek: DeepSeek V3.2',
			],
		];
	}
}
