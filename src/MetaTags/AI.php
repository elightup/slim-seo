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
		register_rest_route( 'slim-seo', self::ROUTE_PREFIX . 'generate-meta', [
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => [ $this, 'generate_with_AI' ],
			'permission_callback' => [ $this, 'can_edit_post' ],
		] );

		register_rest_route( 'slim-seo', self::ROUTE_PREFIX . 'term-description', [
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => [ $this, 'render_term_description' ],
			'permission_callback' => [ $this, 'can_edit_term' ],
		] );
	}

	public function can_edit_post(): bool {
		return current_user_can( 'edit_posts' );
	}

	public function generate_with_AI( WP_REST_Request $request ): string {
		$slim_seo   = get_option( 'slim_seo' ) ?: [];
		$openai_key = $slim_seo['openai_key'] ?? '';

		if ( empty( $openai_key ) ) {
			return __( 'You need to provide a ChatGPT API key!', 'slim-seo' );
		}

		$title          = (string) $request->get_param( 'title' ) ?? '';
		$content        = (string) $request->get_param( 'content' ) ?? '';
		$type           = (string) $request->get_param( 'type' ) ?? 'post-description';
		$update_count   = (int) $request->get_param( 'update_count' ) ?? 1;
		$previous_value = (string) $request->get_param( 'previous_value' ) ?? '';
		$site_language  = get_bloginfo( 'language' ); // e.g. en-US, vi, fr-FR

		// Preprocess content: strip HTML, normalize whitespace, limit length
		$ai_content = wp_strip_all_tags( $content );
		$ai_content = preg_replace( '/\s+/', ' ', $ai_content );
		$ai_content = trim( $ai_content );

		$max_chars = 8000;
		if ( strlen( $ai_content ) > $max_chars ) {
			$ai_content = substr( $ai_content, 0, $max_chars );
		}

		return ( strpos( $type, 'description' ) !== false ) ?
			$this->generate_description( $type, $openai_key, $ai_content, $update_count, $previous_value, $site_language )
			: $this->generate_title( $type, $openai_key, $ai_content, $update_count, $previous_value, $site_language, $title );
	}

	private function generate_title( string $type, string $openai_key, string $ai_content, int $update_count, string $previous_value, string $site_language, string $title ): string {
		if ( 'term-title' === $type ) {
			$base_prompt = <<<PROMPT
			You are a professional SEO assistant for WordPress taxonomy archive pages.

			Your task:
			- Read the taxonomy term name and description
			- Understand the topic and scope of this archive
			- Write exactly ONE SEO-friendly meta title for a taxonomy archive page
			- Use the SAME language as the input

			Meta title requirements:
			- Clearly describe the topic of the archive
			- Natural wording, not clickbait
			- Suitable for Google search results
			- Between 50 and 60 characters
			- Maximum 60 characters

			Strict rules:
			- Do NOT use emojis
			- Do NOT use quotation marks
			- Do NOT use separators such as |, -, or :
			- Do NOT mention specific posts, authors, or dates
			- Do NOT add information not implied by the term

			Output rules:
			- Return ONLY the meta title text
			- No explanations, no extra lines

			Site language hint: {$site_language}
			PROMPT;

			if ( $update_count > 1 && $previous_value ) {
				$ai_content =
					"Term name:\n{$title}\n\n" .
					"Term description:\n{$ai_content}\n\n" .
					"Previous meta title:\n{$previous_value}";
			} else {
				$ai_content =
					"Term name:\n{$title}\n\n" .
					"Term description:\n{$ai_content}";
			}
		}

		if ( 'post-title' === $type ) {
			$base_prompt = <<<PROMPT
			You are a professional SEO assistant for WordPress websites.

			- Read the original post title and the page content
			- Understand the main topic and search intent
			- Write exactly ONE SEO-friendly meta title in the SAME language

			How to use the provided data:
			- Use the original post title as a topic hint
			- Use the page content to validate accuracy and intent
			- Improve clarity and search appeal when necessary
			- Do NOT simply rewrite or paraphrase the original post title

			Meta title requirements:
			- Clear, concise, and compelling
			- Accurately reflects the page content
			- Suitable for Google search results
			- Natural wording, not clickbait
			- Between 50 and 60 characters
			- Maximum 60 characters

			Strict rules:
			- Do NOT use emojis
			- Do NOT use quotation marks
			- Do NOT use separators such as |, -, or :
			- Do NOT add information not present in the content
			- Avoid keyword stuffing or unnatural repetition

			Output rules:
			- Return ONLY the meta title text
			- No explanations, no extra lines

			Site language hint: {$site_language}
			PROMPT;

			// Regenerate (rewrite) logic
			if ( $update_count > 1 && $previous_value ) {
				$prompt     = $base_prompt . "\n\nRewrite rules:\n- Keep the same meaning and SEO intent\n- Use different wording\n- Do NOT repeat wording from the previous meta title";
				$ai_content =
					"Original post title:\n{$title}\n\n" .
					"Page content:\n{$ai_content}\n\n" .
					"Previous meta title:\n{$previous_value}";

			} else {
				$prompt     = $base_prompt;
				$ai_content =
					"Original post title:\n{$title}\n\n" .
					"Page content:\n{$ai_content}";

			}
		}

		return $this->call_openai( $openai_key, $prompt, $ai_content );
	}

	private function generate_description( string $type, string $openai_key, string $ai_content, int $update_count, string $previous_value, string $site_language ): string {
		if ( 'term-description' === $type ) {
			$base_prompt = <<<PROMPT
			You are a professional SEO assistant for WordPress taxonomy archive pages.

			Your task:
			- Read the taxonomy term name and description
			- Write exactly ONE meta description for a taxonomy archive page
			- Use the SAME language as the input

			Meta description requirements:
			- Clearly explain what content users can find under this term
			- Informative, natural, and engaging
			- Suitable for Google search results
			- Active voice
			- Between 140 and 160 characters

			Strict rules:
			- Do NOT use emojis
			- Do NOT use quotation marks
			- Do NOT mention specific post titles
			- Do NOT add information not implied by the term
			- Avoid keyword stuffing or unnatural repetition

			Output rules:
			- Return ONLY the meta description text
			- No explanations, no extra lines

			Site language hint: {$site_language}
			PROMPT;

			if ( $update_count > 1 && $previous_value ) {
				$prompt = $base_prompt . "\n\nRewrite rules:\n- Keep the same meaning and SEO intent\n- Use different wording\n- Do NOT repeat sentence structure from the previous meta description\n- Do NOT change the scope of the term";

				$ai_content = "Term description:\n{$ai_content}\n\n" . "Previous meta description:\n{$previous_value}";
			} else {
				$prompt   = $base_prompt;
			}
		}

		if ( 'post-description' === $type ) {
			// Base prompt (used for both generate & rewrite)
			$base_prompt = <<<PROMPT
			You are a professional SEO assistant for WordPress websites.

			Your task:
			- Read the provided page content
			- Detect the primary language used
			- Write exactly ONE meta description in the SAME language

			Meta description requirements:
			- Clear, natural, and engaging
			- Suitable for Google search results
			- Active voice
			- Between 140 and 160 characters
			- Faithful to the actual content

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

			// Regenerate (rewrite) logic
			if ( $update_count > 1 && $previous_value ) {
				$prompt = $base_prompt . "\n\nRewrite rules:\n- Keep the same meaning and SEO intent\n- Use different wording\n- Do NOT repeat sentence structure from the previous meta description";

				$ai_content = "Page content:\n{$ai_content}\n\nPrevious meta description:\n{$previous_value}";
			} else {
				$prompt   = $base_prompt;
			}
		}

		return $this->call_openai( $openai_key, $prompt, $ai_content );
	}

	private function call_openai( string $openai_key, string $prompt, string $ai_content ): string {
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
					'content' => $ai_content,
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
