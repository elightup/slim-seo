<?php
namespace SlimSEO\MetaTags\AiProviders;

class OpenRouter implements ProviderInterface {
	public function get_api_url(): string {
		return 'https://openrouter.ai/api/v1/responses';
	}

	public function get_headers( string $api_key ): array {
		return [
			'Content-Type'  => 'application/json',
			'Authorization' => "Bearer $api_key",
		];
	}

	public function build_request_body( string $prompt, string $content, string $model ): array {
		return [
			'model'       => $model,
			'temperature' => 0.5,
			'input'       => [
				[
					'role'    => 'system',
					'content' => $prompt,
				],
				[
					'role'    => 'user',
					'content' => $content,
				],
			],
		];
	}

	public function parse_response( array $response ): string {
		return $response['output'][0]['content'][0]['text'] ?? '';
	}

	public function get_models(): array {
		return [
			[
				'value' => 'openai/gpt-4.1-mini',
				'label' => 'GPT-4.1 Mini',
			],
			[
				'value' => 'openai/gpt-4o-mini',
				'label' => 'GPT-4o Mini',
			],
			[
				'value' => 'anthropic/claude-3.5-sonnet-20240620',
				'label' => 'Claude 3.5 Sonnet',
			],
			[
				'value' => 'google/gemini-2.0-flash-exp',
				'label' => 'Gemini 2.0 Flash',
			],
			[
				'value' => 'meta/llama-3.3-70b-instruct',
				'label' => 'Llama 3.3 70B',
			],
		];
	}
}
