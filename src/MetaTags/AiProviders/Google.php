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

	/**
	 * @link https://ai.google.dev/gemini-api/docs/models
	 */
	public function get_models(): array {
		return [
			'gemini-3.5-flash',
			'gemini-3.1-pro-preview',
			'gemini-3.1-flash-preview',
			'gemini-3.1-flash-lite',
			'gemini-2.5-pro',
			'gemini-2.5-flash',
			'gemini-2.5-flash-lite',
		];
	}
}
