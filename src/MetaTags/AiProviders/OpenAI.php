<?php
namespace SlimSEO\MetaTags\AiProviders;

class OpenAI implements ProviderInterface {
	public function get_api_url(): string {
		return 'https://api.openai.com/v1/responses';
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
				'value' => 'gpt-5.4',
				'label' => 'GPT-5.4',
			],
			[
				'value' => 'gpt-5.4-mini',
				'label' => 'GPT-5.4 Mini',
			],
			[
				'value' => 'gpt-5.4-nano',
				'label' => 'GPT-5.4 Nano',
			],
			[
				'value' => 'gpt-5',
				'label' => 'GPT-5',
			],
			[
				'value' => 'gpt-5-mini',
				'label' => 'GPT-5 Mini',
			],
			[
				'value' => 'gpt-5-nano',
				'label' => 'GPT-5 Nano',
			],
			[
				'value' => 'gpt-4.1',
				'label' => 'GPT-4.1',
			],
			[
				'value' => 'gpt-4.1-mini',
				'label' => 'GPT-4.1 Mini',
			],
			[
				'value' => 'gpt-4.1-nano',
				'label' => 'GPT-4.1 Nano',
			],
			[
				'value' => 'gpt-4o',
				'label' => 'GPT-4o',
			],
			[
				'value' => 'gpt-4o-mini',
				'label' => 'GPT-4o Mini',
			],
		];
	}
}
