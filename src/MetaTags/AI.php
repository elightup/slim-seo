<?php
namespace SlimSEO\MetaTags;

use WP_REST_Server;
use WP_REST_Request;

class AI {
	const ROUTE_PREFIX = 'meta-tags/ai/';

	public function setup(): void {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	public function register_routes(): void {
		register_rest_route( 'slim-seo', self::ROUTE_PREFIX . 'meta', [
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => [ $this, 'generate' ],
			'permission_callback' => [ $this, 'can_edit_post' ],
		] );
	}

	public function can_edit_post(): bool {
		return current_user_can( 'edit_posts' );
	}

	public function generate( WP_REST_Request $request ): string {
		$title          = (string) $request->get_param( 'title' );
		$content        = (string) $request->get_param( 'content' );
		$type           = (string) ( $request->get_param( 'type' ) ?? 'post-description' );
		$update_count   = (int) ( $request->get_param( 'update_count' ) ?? 1 );
		$previous_value = (string) $request->get_param( 'previous_value' );
		$site_language  = get_bloginfo( 'language' ); // e.g. en-US, vi, fr-FR

		// Preprocess content: strip HTML, normalize whitespace, limit length
		$content = wp_strip_all_tags( $content );
		$content = preg_replace( '/\s+/', ' ', $content );
		$content = trim( $content );

		$max_chars = 8000;
		if ( strlen( $content ) > $max_chars ) {
			$content = substr( $content, 0, $max_chars );
		}

		return str_contains( $type, 'description' )
			? $this->generate_description( $content, $update_count, $previous_value, $site_language )
			: $this->generate_title( $content, $update_count, $previous_value, $site_language, $title );
	}

	private function generate_title( string $content, int $update_count, string $previous_value, string $site_language, string $title ): string {
		$prompt = <<<PROMPT
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

			Site language hint: {$site_language}
		PROMPT;

		$content =
			"Title:\n{$title}\n\n" .
			"Content:\n{$content}";

		// Regenerate (rewrite) logic
		if ( $update_count > 1 && $previous_value ) {
			$prompt  .= "\n\n" . $this->get_rewrite_rule();
			$content .= "\n\nPrevious meta title:\n{$previous_value}";
		}

		return $this->call_openai( $prompt, $content );
	}

	private function generate_description( string $content, int $update_count, string $previous_value, string $site_language ): string {
		$prompt = <<<PROMPT
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

			Site language hint: {$site_language}
		PROMPT;

		$content = "Content:\n{$content}";

		// Regenerate (rewrite) logic
		if ( $update_count > 1 && $previous_value ) {
			$prompt  .= "\n\n" . $this->get_rewrite_rule();
			$content .= "\n\nPrevious meta description:\n{$previous_value}";
		}

		return $this->call_openai( $prompt, $content );
	}

	private function get_rewrite_rule(): string {
		return <<<'RULE'
		Rewrite rules:
		- Keep the same meaning and SEO intent
		- Use different wording
		- Do NOT repeat wording from the previous value
		RULE;
	}

	private function call_openai( string $prompt, string $content ): string {
		$slim_seo   = get_option( 'slim_seo' ) ?: [];
		$openai_key = $slim_seo['openai_key'] ?? '';

		if ( empty( $openai_key ) ) {
			return __( 'You need to provide a ChatGPT API key!', 'slim-seo' );
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
				'Authorization' => 'Bearer ' . $openai_key,
			],
			'body'    => $body,
			'timeout' => 45,
		] );

		if ( is_wp_error( $response ) ) {
			return __( 'API request failed.', 'slim-seo' );
		}

		$code = wp_remote_retrieve_response_code( $response );
		if ( $code < 200 || $code >= 300 ) {
			return sprintf( __( 'API error (HTTP %d)', 'slim-seo' ), $code );
		}

		$result = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! is_array( $result ) ) {
			return __( 'Invalid response from the AI service.', 'slim-seo' );
		}

		if ( isset( $result['error'] ) ) {
			return sprintf( __( 'API error: %s', 'slim-seo' ), $result['error']['message'] );
		}

		if ( isset( $result['status'], $result['output'][0]['content'][0]['text'] ) && $result['status'] === 'completed' ) {
			$description = trim( $result['output'][0]['content'][0]['text'] );

			// Final safety truncation
			return $description;
		}

		return __( 'Could not retrieve content from the AI service.', 'slim-seo' );
	}
}
