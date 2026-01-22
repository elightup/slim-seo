<?php
namespace SlimSEO\MetaTags\Settings;

use WP_REST_Server;
use WP_REST_Request;
use WP_Term;
use SlimSEO\Helpers\Option;
use SlimSEO\MetaTags\Helper;
use SlimSEO\MetaTags\Description;
use SlimSEO\MetaTags\Title;
use SlimSEO\MetaTags\Data;

class Preview {
	const ROUTE_PREFIX = 'meta-tags/preview/';

	public function setup(): void {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	public function register_routes(): void {
		register_rest_route( 'slim-seo', self::ROUTE_PREFIX . 'post-title', [
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => [ $this, 'render_post_title' ],
			'permission_callback' => [ $this, 'can_edit_post' ],
		] );

		register_rest_route( 'slim-seo', self::ROUTE_PREFIX . 'term-title', [
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => [ $this, 'render_term_title' ],
			'permission_callback' => [ $this, 'can_edit_term' ],
		] );

		register_rest_route( 'slim-seo', self::ROUTE_PREFIX . 'post-description', [
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => [ $this, 'render_post_description' ],
			'permission_callback' => [ $this, 'can_edit_post' ],
		] );

		register_rest_route( 'slim-seo', self::ROUTE_PREFIX . 'term-description', [
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => [ $this, 'render_term_description' ],
			'permission_callback' => [ $this, 'can_edit_term' ],
		] );

		// Render text for homepage title and description.
		register_rest_route( 'slim-seo', self::ROUTE_PREFIX . 'homepage', [
			'methods'             => WP_REST_Server::EDITABLE,
			'callback'            => [ $this, 'render_homepage_text' ],
			'permission_callback' => [ $this, 'can_edit_homepage' ],
		] );
	}

	public function can_edit_post( WP_REST_Request $request ): bool {
		$post_id = (int) $request->get_param( 'ID' );
		return $post_id && current_user_can( 'edit_posts' ) && current_user_can( 'read_post', $post_id );
	}

	public function can_edit_homepage(): bool {
		return current_user_can( 'manage_options' );
	}

	public function can_edit_term(): bool {
		return current_user_can( 'edit_posts' );
	}

	public function render_post_title( WP_REST_Request $request ): array {
		return $this->render_title( $request, 'post' );
	}

	public function render_term_title( WP_REST_Request $request ): array {
		return $this->render_title( $request, 'term' );
	}

	private function render_title( WP_REST_Request $request, string $object_type = 'post' ): array {
		$id = (int) $request->get_param( 'ID' );
		if ( ! $id ) {
			return [
				'preview' => '',
				'default' => '',
			];
		}

		$text  = (string) $request->get_param( 'text' ); // Manual entered meta title
		$title = (string) $request->get_param( 'title' ); // Live title

		$data = [];
		if ( $object_type === 'post' ) {
			$data = [
				'post' => [
					'title' => $title,
				],
			];
		} else {
			$data = [
				'term' => [
					'name' => $title,
				],
			];
		}

		$default = $object_type === 'post' ? $this->get_default_post_title( $id ) : $this->get_default_term_title( $id );
		if ( $object_type === 'post' ) {
			$preview = Helper::render( $text ?: $default, $id, 0, $data );
		} else {
			$preview = Helper::render( $text ?: $default, 0, $id, $data );
		}

		return compact( 'preview', 'default' );
	}

	private function get_default_post_title( int $post_id ): string {
		// For static frontpage: don't use page's settings, use WordPress default instead.
		$is_static_frontpage = 'page' === get_option( 'show_on_front' ) && $post_id === (int) get_option( 'page_on_front' );
		if ( $is_static_frontpage ) {
			return Title::DEFAULTS['home'];
		}

		$default = Title::DEFAULTS['post'];
		$key     = get_post_type( $post_id );

		return $key ? Option::get( "$key.title", $default ) : $default;
	}

	private function get_default_term_title( int $term_id ): string {
		$default = Title::DEFAULTS['term'];
		$term    = get_term( $term_id );
		if ( ! ( $term instanceof WP_Term ) ) {
			return $default;
		}
		return Option::get( "{$term->taxonomy}.title", $default );
	}

	public function render_term_description( WP_REST_Request $request ): array {
		$id = (int) $request->get_param( 'ID' );
		if ( ! $id ) {
			return [
				'preview' => '',
				'default' => '',
			];
		}

		$text        = (string) $request->get_param( 'text' ); // Manual entered meta description
		$description = (string) $request->get_param( 'description' ); // Live description
		$data        = [
			'term' => [
				'description'      => $description,
				'auto_description' => Helper::truncate( $description ),
			],
		];

		$default = $this->get_default_term_description( $id );
		$preview = Helper::render( $text ?: $default, 0, $id, $data );

		return compact( 'preview', 'default' );
	}

	private function get_default_term_description( int $term_id ): string {
		$default = Description::DEFAULTS['term'];
		$term    = get_term( $term_id );
		if ( ! ( $term instanceof WP_Term ) ) {
			return $default;
		}
		return Option::get( "{$term->taxonomy}.description", $default );
	}

	public function render_post_description( WP_REST_Request $request ): array {
		$id = (int) $request->get_param( 'ID' );
		if ( ! $id ) {
			return [
				'preview' => '',
				'default' => '',
			];
		}

		$content = (string) $request->get_param( 'content' ); // Live content
		$content = Data::get_post_content( $id, $content );

		if ( $request->get_param( 'AI' ) && ! empty( $content ) ) {
			$preview = $this->generate_with_AI(
				$content,
				(int) $request->get_param( 'update_count' ),
				(string) $request->get_param( 'previous_value' )
			);
			return [
				'preview' => $preview,
				'default' => '',
			];
		}

		$text    = (string) $request->get_param( 'text' ); // Manual entered meta description
		$excerpt = (string) $request->get_param( 'excerpt' ); // Live excerpt

		$data         = [];
		$data['post'] = array_filter( [
			'excerpt'          => $excerpt,
			'content'          => $content,
			'auto_description' => Helper::truncate( $excerpt ?: $content ),
		] );
		$data         = array_filter( $data );
		$default      = $this->get_default_post_description( $id );
		$preview      = Helper::render( $text ?: $default, $id, 0, $data );

		return compact( 'preview', 'default' );
	}

	private function generate_with_AI( string $content, int $update_count, string $previous_value ): string {
		$slim_seo    = get_option( 'slim_seo' ) ?: [];
		$openai_key = $slim_seo['openai_key'] ?? '';

		if ( empty( $openai_key ) ) {
			return __( 'You need to provide a ChatGPT API key!', 'slim-seo' );
		}

		// Preprocess content: strip HTML, normalize whitespace, limit length
		$ai_content = wp_strip_all_tags( $content );
		$ai_content = preg_replace( '/\s+/', ' ', $ai_content );
		$ai_content = trim( $ai_content );

		$max_chars = 8000;
		if ( strlen( $ai_content ) > $max_chars ) {
			$ai_content = substr( $ai_content, 0, $max_chars );
		}

		$site_language = get_bloginfo( 'language' ); // e.g. en-US, vi, fr-FR

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

		$api_url = 'https://api.openai.com/v1/responses';
		$body    = wp_json_encode( [
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

		$headers = [
			'Content-Type'  => 'application/json',
			'Authorization' => 'Bearer ' . $openai_key,
		];

		$response = wp_safe_remote_post( $api_url, [
			'headers'   => $headers,
			'body'      => $body,
			'method'    => 'POST',
			'timeout'   => 45,
			'sslverify' => true,
		] );

		if ( is_wp_error( $response ) ) {
			return sprintf( __( 'API error: %s', 'slim-seo' ), $response->get_error_message() );
		}

		$code = wp_remote_retrieve_response_code( $response );
		if ( $code < 200 || $code >= 300 ) {
			return sprintf( __( 'API error (HTTP %d)', 'slim-seo' ), $code );
		}

		$response = wp_remote_retrieve_body( $response );
		$result   = json_decode( $response, true );

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

		return __( 'Could not retrieve content from the API.', 'slim-seo' );
	}

	private function get_default_post_description( int $post_id ): string {
		$default = Description::DEFAULTS['post'];
		$key     = get_post_type( $post_id );

		return $key ? Option::get( "$key.description", $default ) : $default;
	}

	public function render_homepage_text( WP_REST_Request $request ): string {
		$text = (string) $request->get_param( 'text' );
		return Helper::render( $text );
	}
}
