<?php
namespace SlimSEO\MetaTags;

use WP_REST_Server;
use WP_REST_Request;

class AI {
	public function setup(): void {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	public function register_routes(): void {
		register_rest_route( 'slim-seo', 'meta-tags/ai', [
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => [ $this, 'generate' ],
			'permission_callback' => [ $this, 'can_edit_post' ],
		] );
	}

	public function can_edit_post(): bool {
		return current_user_can( 'edit_posts' );
	}

	public function generate( WP_REST_Request $request ): array {
		$title          = (string) $request->get_param( 'title' );
		$content        = (string) $request->get_param( 'content' );
		$previous_value = (string) $request->get_param( 'previousMetaByAI' );
		$type           = $request->get_param( 'type' ) === 'description' ? 'description' : 'title';

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
		$slim_seo   = get_option( 'slim_seo' ) ?: [];
		$openai_key = $slim_seo['openai_key'] ?? '';

		if ( empty( $openai_key ) ) {
			return $this->response( __( 'You need to provide an OpenAI API key!', 'slim-seo' ) );
		}

		$body = wp_json_encode( [
			'model'       => 'gpt-4.1-mini',
			'temperature' => 0.5, // Stable & SEO-safe
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
		] );

		$response = wp_safe_remote_post( 'https://api.openai.com/v1/responses', [
			'headers' => [
				'Content-Type'  => 'application/json',
				'Authorization' => "Bearer $openai_key",
			],
			'body'    => $body,
			'timeout' => 45,
		] );

		// 1. Network error
		if ( is_wp_error( $response ) ) {
			return $this->response( sprintf( __( 'Connection error: %s', 'slim-seo' ), $response->get_error_message() ) );
		}

		$status_code = wp_remote_retrieve_response_code( $response );
		$body        = wp_remote_retrieve_body( $response );
		$result      = json_decode( $body, true );

		// 2. OpenAI API error
		if ( $status_code !== 200 ) {
			$error_message = $result['error']['message'] ?? __( 'Unknown.', 'slim-seo' );

			switch ( $status_code ) {
				case 401:
					return $this->response( __( 'Invalid API Key. Please check your settings.', 'slim-seo' ) );
				case 429:
					return $this->response( __( 'Rate limit exceeded or insufficient quota. Please try again later.', 'slim-seo' ) );
				case 500:
				case 503:
					return $this->response( __( 'OpenAI server is currently overloaded. Please wait a moment.', 'slim-seo' ) );
				default:
					return $this->response( sprintf( __( 'OpenAI API error (%1$s): %2$s', 'slim-seo' ), $status_code, $error_message ) );
			}
		}

		// 3. Invalid response
		if ( empty( $result['output'][0]['content'][0]['text'] ) ) {
			return $this->response( __( 'Invalid response from OpenAI API.', 'slim-seo' ) );
		}

		// 4. Success
		return $this->response( trim( $result['output'][0]['content'][0]['text'] ), 'success' );
	}

	private function response( string $message, string $status = 'error' ): array {
		return compact( 'message', 'status' );
	}
}
