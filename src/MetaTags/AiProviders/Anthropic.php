<?php
namespace SlimSEO\MetaTags\AiProviders;

class Anthropic implements ProviderInterface {
	public function get_api_url(): string {
		return 'https://api.anthropic.com/v1/messages';
	}

	public function get_headers( string $api_key ): array {
		return [
			'Content-Type'      => 'application/json',
			'x-api-key'         => $api_key,
			'anthropic-version' => '2023-06-01',
		];
	}

	public function build_request_body(
		string $prompt,
		string $content,
		string $model
	): array {
		return [
			'model'       => $model,
			'max_tokens'  => 1024,
			'temperature' => 0.5,
			'system'      => $prompt,
			'messages'    => [
				[
					'role'    => 'user',
					'content' => $content,
				],
			],
		];
	}

	public function parse_response( array $response ): string {
		return $response['content'][0]['text'] ?? '';
	}

	public function get_models(): array {
		return [
			[
				'value' => 'claude-sonnet-4-20250514',
				'label' => 'Claude Sonnet 4 (Latest)',
			],
			[
				'value' => 'claude-3-5-sonnet-20240620',
				'label' => 'Claude 3.5 Sonnet',
			],
			[
				'value' => 'claude-3-5-haiku-20240620',
				'label' => 'Claude 3.5 Haiku',
			],
		];
	}
}
