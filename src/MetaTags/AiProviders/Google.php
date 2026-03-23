<?php
namespace SlimSEO\MetaTags\AiProviders;

class Google implements ProviderInterface {
	public function get_api_url(): string {
		return 'https://generativelanguage.googleapis.com/v1beta/openai/chat/completions';
	}

	public function get_headers( string $api_key ): array {
		return [
			'Content-Type' => 'application/json',
		];
	}

	public function build_request_body( string $prompt, string $content, string $model ): array {
		return [
			'model'       => $model,
			'temperature' => 0.5,
			'messages'    => [
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
		return $response['choices'][0]['message']['content'] ?? '';
	}

	public function get_models(): array {
		return [
			[
				'value' => 'gemini-2.0-flash-exp',
				'label' => 'Gemini 2.0 Flash (Experimental)',
			],
			[
				'value' => 'gemini-2.0-flash',
				'label' => 'Gemini 2.0 Flash',
			],
			[
				'value' => 'gemini-1.5-flash-8b',
				'label' => 'Gemini 1.5 Flash 8B',
			],
			[
				'value' => 'gemini-1.5-flash',
				'label' => 'Gemini 1.5 Flash',
			],
		];
	}
}
