<?php
namespace SlimSEO\MetaTags;

use WP_REST_Server;
use WP_REST_Request;
use SlimSEO\MetaTags\AiProviders\ProviderInterface;
use SlimSEO\MetaTags\AiProviders\OpenAI;
use SlimSEO\MetaTags\AiProviders\Google;
use SlimSEO\MetaTags\AiProviders\Anthropic;
use SlimSEO\MetaTags\AiProviders\OpenRouter;

class AI {
	public function setup(): void {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	public function register_routes(): void {
		register_rest_route( 'slim-seo', 'meta-tags/ai', [
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => [ $this, 'generate' ],
			'permission_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		] );

		register_rest_route( 'slim-seo', 'ai/models', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_models' ],
			'permission_callback' => function () {
				return current_user_can( 'manage_options' );
			},
		] );
	}

	public function get_models( WP_REST_Request $request ): array {
		$provider = $request->get_param( 'provider' ) ?: 'openai';

		$provider_class = $this->get_provider_class( $provider );
		if ( ! $provider_class ) {
			return [];
		}

		$provider_obj = new $provider_class();
		return $provider_obj->get_models();
	}

	public function generate( WP_REST_Request $request ): array {
		$title          = (string) $request->get_param( 'title' );
		$content        = (string) $request->get_param( 'content' );
		$previous_value = (string) $request->get_param( 'previousMetaByAI' );
		$object         = (array) $request->get_param( 'object' );
		$type           = $request->get_param( 'type' ) === 'description' ? 'description' : 'title';

		if ( 'post' === ( $object['type'] ?? '' ) ) {
			$content = Data::get_post_content( $object['ID'] ?? 0, $content );
		}

		// Preprocess content: strip HTML, normalize whitespace, limit length
		$content = wp_strip_all_tags( $content );
		$content = preg_replace( '/\s+/', ' ', $content );
		$content = trim( $content );

		$max_chars = 8000;
		if ( strlen( $content ) > $max_chars ) {
			$content = substr( $content, 0, $max_chars );
		}

		if ( $type === 'description' && empty( $content ) ) {
			return $this->response( __( 'Content is required to generate meta description.', 'slim-seo' ) );
		}

		if ( $type === 'title' && ( empty( $title ) || empty( $content ) ) ) {
			return $this->response( __( 'Title and content are required to generate meta title.', 'slim-seo' ) );
		}

		return $type === 'description'
			? $this->generate_description( $content, $previous_value )
			: $this->generate_title( $content, $previous_value, $title );
	}

	private function generate_title( string $content, string $previous_value, string $title ): array {
		$prompt = <<<'PROMPT'
You are a professional SEO assistant for WordPress websites.

Your task:
- Read the title and content
- Understand the main topic and search intent
- Write exactly ONE SEO-friendly meta title in the SAME language

How to use the provided data:
- Use the title as a topic hint
- Use the content to validate accuracy and intent
- Improve clarity and search appeal when necessary
- Do NOT simply rewrite or paraphrase the title

Meta title requirements:
- Clear, concise, and compelling
- Accurately reflects the content
- Natural wording, not clickbait
- Suitable for Google search results
- Between 50 and 60 characters

Strict rules:
- Do NOT use emojis
- Do NOT use quotation marks
- Do NOT use separators such as |, -, or :
- Do NOT add information not present in the title and content
- Avoid keyword stuffing or unnatural repetition

Output rules:
- Return ONLY the meta title text
- No explanations, no extra lines
PROMPT;

		$content =
			"Title:\n{$title}\n\n" .
			"Content:\n{$content}";

		// Regenerate (rewrite) logic
		if ( $previous_value ) {
			$prompt  .= "\n\n" . $this->get_rewrite_rule();
			$content .= "\n\nPrevious meta title:\n{$previous_value}";
		}

		return $this->request( $prompt, $content );
	}

	private function generate_description( string $content, string $previous_value ): array {
		$prompt = <<<'PROMPT'
You are a professional SEO assistant for WordPress websites.

Your task:
- Read the provided content
- Understand the main topic and search intent
- Write exactly ONE meta description in the SAME language

Meta description requirements:
- Clear, natural, and engaging
- Accurately reflects the content
- Suitable for Google search results
- Active voice
- Between 140 and 160 characters

Strict rules:
- Do NOT use emojis
- Do NOT use quotation marks
- Do NOT add information not present in the content
- Avoid keyword stuffing or unnatural repetition

Output rules:
- Return ONLY the meta description text
- No explanations, no extra lines
PROMPT;

		$content = "Content:\n{$content}";

		// Regenerate (rewrite) logic
		if ( $previous_value ) {
			$prompt  .= "\n\n" . $this->get_rewrite_rule();
			$content .= "\n\nPrevious meta description:\n{$previous_value}";
		}

		return $this->request( $prompt, $content );
	}

	private function get_rewrite_rule(): string {
		return <<<'RULE'
Rewrite rules:
- Keep the same meaning and SEO intent
- Use different wording
- Do NOT repeat wording from the previous value
RULE;
	}

	private function request( string $prompt, string $content ): array {
		$settings = get_option( 'slim_seo' ) ?: [];
		$provider = $settings['ai_provider'] ?? 'openai';
		$model    = $settings['ai_model'] ?? '';
		$api_key  = $settings['ai_api_key'] ?? '';

		// Backward compatibility: check old openai_key
		if ( empty( $api_key ) ) {
			$api_key = $settings['openai_key'] ?? '';
		}

		if ( empty( $api_key ) ) {
			return $this->response( __( 'Please provide an API key in settings.', 'slim-seo' ) );
		}

		$provider_class = $this->get_provider_class( $provider );
		if ( ! $provider_class ) {
			return $this->response( __( 'Invalid provider selected.', 'slim-seo' ) );
		}

		$provider_obj = new $provider_class();

		// Validate model - use default if empty
		if ( empty( $model ) ) {
			$models = $provider_obj->get_models();
			$model  = $models[0]['value'] ?? '';
		}

		if ( empty( $model ) ) {
			return $this->response( __( 'No models available for the selected provider.', 'slim-seo' ) );
		}

		// Apply filter to allow customization
		$model = apply_filters( 'slim_seo_ai_model', $model, $provider );

		// Build request
		$url     = $provider_obj->get_api_url();
		$headers = $provider_obj->get_headers( $api_key );

		// Google needs model in URL
		if ( $provider === 'google' ) {
			$url = str_replace( '{model}', $model, $url );
		}

		$body = $provider_obj->build_request_body( $prompt, $content, $model );

		// Make request
		$response = wp_safe_remote_post( $url, [
			'headers' => $headers,
			'body'    => wp_json_encode( $body ),
			'timeout' => 45,
		] );

		return $this->handle_response( $response, $provider_obj );
	}

	private function get_provider_class( string $provider ): ?string {
		$providers = [
			'openai'     => OpenAI::class,
			'google'     => Google::class,
			'anthropic'  => Anthropic::class,
			'openrouter' => OpenRouter::class,
		];

		return $providers[ $provider ] ?? null;
	}

	private function handle_response( $response, ProviderInterface $provider ): array {
		if ( is_wp_error( $response ) ) {
			// translators: %s: Error message.
			return $this->response( sprintf( __( 'Connection error: %s', 'slim-seo' ), $response->get_error_message() ) );
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		$body        = wp_remote_retrieve_body( $response );
		$result      = json_decode( $body, true );

		// Check for API errors
		if ( $status_code !== 200 ) {
			return $this->handle_api_error( $status_code, $result );
		}

		// Parse response using provider
		$text = $provider->parse_response( $result );
		if ( empty( $text ) ) {
			return $this->response( __( 'Invalid response from AI provider.', 'slim-seo' ) );
		}

		return $this->response( trim( $text ), 'success' );
	}

	private function handle_api_error( int $status_code, array $result ): array {
		$error_message = $result['error']['message'] ?? $result['error'] ?? __( 'Unknown error', 'slim-seo' );

		switch ( $status_code ) {
			case 401:
				return $this->response( __( 'Invalid API Key. Please check your settings.', 'slim-seo' ) );
			case 429:
				return $this->response( __( 'Rate limit exceeded. Please try again later.', 'slim-seo' ) );
			case 500:
			case 502:
			case 503:
				return $this->response( __( 'AI server is currently overloaded. Please wait a moment.', 'slim-seo' ) );
			default:
				// translators: %1$s: HTTP status code, %2$s: Error message.
				return $this->response( sprintf( __( 'API error (%1$s): %2$s', 'slim-seo' ), $status_code, $error_message ) );
		}
	}

	private function response( string $message, string $status = 'error' ): array {
		return compact( 'message', 'status' );
	}
}
