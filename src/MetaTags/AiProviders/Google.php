<?php
namespace SlimSEO\MetaTags\AiProviders;

class Google implements ProviderInterface {
	public function get_api_url(): string {
		return 'https://generativelanguage.googleapis.com/v1beta/models/{model}:generateContent';
	}

	public function get_headers( string $api_key ): array {
		return [
			'x-goog-api-key' => $api_key,
			'Content-Type'   => 'application/json',
		];
	}

	public function build_request_body( string $prompt, string $content, string $model ): array {
		return [
			'generationConfig'   => [
				'temperature' => 0.5,
			],
			'system_instruction' => [
				'parts' => [
					[
						'text' => $prompt,
					],
				],
			],
			'contents'           => [
				'parts' => [
					[
						'text' => $content,
					],
				],
			],
		];
	}

	public function parse_response( array $response ): string {
		return $response['candidates'][0]['content']['parts'][0]['text'] ?? '';
	}

	public function get_models(): array {
		return [
			[
				'value' => 'gemini-3.1-pro-preview',
				'label' => 'Gemini 3.1 Pro Preview',
			],
			[
				'value' => 'gemini-3.1-flash-preview',
				'label' => 'Gemini 3.1 Flash Preview',
			],
			[
				'value' => 'gemini-3.1-flash-lite-preview',
				'label' => 'Gemini 3.1 Flash Lite Preview',
			],
			[
				'value' => 'gemini-2.5-pro',
				'label' => 'Gemini 2.5 Pro',
			],
			[
				'value' => 'gemini-2.5-flash',
				'label' => 'Gemini 2.5 Flash',
			],
			[
				'value' => 'gemini-2.5-flash-lite',
				'label' => 'Gemini 2.5 Flash Lite',
			],
		];
	}
}
