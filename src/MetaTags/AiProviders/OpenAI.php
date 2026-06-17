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
			'model' => $model,
			'input' => [
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
		if ( empty( $response['output'] ) || ! is_array( $response['output'] ) ) {
			return '';
		}

		$texts = [];
		foreach ( $response['output'] as $item ) {
			if ( empty( $item['content'] ) || ! is_array( $item['content'] ) ) {
				continue;
			}
			foreach ( $item['content'] as $content ) {
				if ( isset( $content['type'] ) && $content['type'] === 'output_text' ) {
					$texts[] = $content['text'] ?? '';
				}
			}
		}

		return implode( ' ', $texts );
	}

	/**
	 * @link https://developers.openai.com/api/docs/models/all
	 */
	public function get_models(): array {
		return [
			'gpt-5.5',
			'gpt-5.4',
			'gpt-5.4-mini',
			'gpt-5.4-nano',
			'gpt-5',
			'gpt-5-mini',
			'gpt-5-nano',
			'gpt-4.1',
			'gpt-4.1-mini',
			'gpt-4.1-nano',
			'gpt-4o',
			'gpt-4o-mini',
		];
	}
}
