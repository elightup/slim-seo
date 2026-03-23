<?php
namespace SlimSEO\MetaTags\AiProviders;

interface ProviderInterface {
	public function get_api_url(): string;

	public function get_headers( string $api_key ): array;

	public function build_request_body(
		string $prompt,
		string $content,
		string $model
	): array;

	public function parse_response( array $response ): string;

	public function get_models(): array;
}
