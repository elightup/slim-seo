# AI Multi-Provider Implementation Plan

> **For agentic workers:** REQUIRED: Use superpowers:subagent-driven-development (if subagents available) or superpowers:executing-plans to implement this plan. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add multi-provider AI support (OpenAI, Google Gemini, Anthropic Claude, OpenRouter) to generate meta title and description, replacing the existing OpenAI-only implementation.

**Architecture:** Use a provider interface pattern with separate classes for each AI provider. Providers differ in API URL format, authentication (headers vs query param), request/response format. Settings store provider + model selection, with JS populating model dropdown based on provider.

**Tech Stack:** PHP (WordPress), Vanilla JS (no React for this feature)

---

## File Structure

```
src/
├── MetaTags/
│   ├── AI.php                    # REST API handler (modify)
│   └── AiProviders/
│       ├── ProviderInterface.php  # New - Interface
│       ├── OpenAI.php              # New - OpenAI implementation
│       ├── OpenRouter.php          # New - OpenRouter implementation
│       ├── Google.php              # New - Google Gemini implementation
│       └── Anthropic.php          # New - Anthropic Claude implementation
├── Settings/
│   ├── Settings.php              # Modify - add setting sanitization
│   ├── MetaTags/RestApi.php       # Modify - hide new API key field
│   └── tabs/tools.php             # Modify - add provider/model dropdowns
├── Upgrade.php                    # Modify - add migration for existing openai_key
└── js/
    └── settings-ai.js             # New - Vanilla JS for provider/model behavior
```

---

## Task 1: Create Provider Interface

**Files:**
- Create: `src/MetaTags/AiProviders/ProviderInterface.php`

- [ ] **Step 1: Create ProviderInterface.php**

```php
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
```

- [ ] **Step 2: Run PHP lint**

Run: `composer phpcs src/MetaTags/AiProviders/ProviderInterface.php`
Expected: No errors

---

## Task 2: Create OpenAI Provider

**Files:**
- Create: `src/MetaTags/AiProviders/OpenAI.php`

- [ ] **Step 1: Create OpenAI.php**

```php
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
            [ 'value' => 'gpt-4.1-mini', 'label' => 'GPT-4.1 Mini' ],
            [ 'value' => 'gpt-4.1', 'label' => 'GPT-4.1' ],
            [ 'value' => 'gpt-4o-mini', 'label' => 'GPT-4o Mini' ],
            [ 'value' => 'gpt-4o', 'label' => 'GPT-4o' ],
        ];
    }
}
```

- [ ] **Step 2: Run PHP lint**

Run: `composer phpcs src/MetaTags/AiProviders/OpenAI.php`
Expected: No errors

---

## Task 3: Create OpenRouter Provider

**Files:**
- Create: `src/MetaTags/AiProviders/OpenRouter.php`

- [ ] **Step 1: Create OpenRouter.php**

```php
<?php
namespace SlimSEO\MetaTags\AiProviders;

class OpenRouter implements ProviderInterface {
    public function get_api_url(): string {
        return 'https://openrouter.ai/api/v1/responses';
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
            [ 'value' => 'openai/gpt-4.1-mini', 'label' => 'GPT-4.1 Mini' ],
            [ 'value' => 'openai/gpt-4o-mini', 'label' => 'GPT-4o Mini' ],
            [ 'value' => 'anthropic/claude-3.5-sonnet-20240620', 'label' => 'Claude 3.5 Sonnet' ],
            [ 'value' => 'google/gemini-2.0-flash-exp', 'label' => 'Gemini 2.0 Flash' ],
            [ 'value' => 'meta/llama-3.3-70b-instruct', 'label' => 'Llama 3.3 70B' ],
        ];
    }
}
```

- [ ] **Step 2: Run PHP lint**

Run: `composer phpcs src/MetaTags/AiProviders/OpenRouter.php`
Expected: No errors

---

## Task 4: Create Google Gemini Provider

**Files:**
- Create: `src/MetaTags/AiProviders/Google.php`

- [ ] **Step 1: Create Google.php**

```php
<?php
namespace SlimSEO\MetaTags\AiProviders;

class Google implements ProviderInterface {
    public function get_api_url(): string {
        // API key is passed via query parameter, not header
        return 'https://generativelanguage.googleapis.com/v1beta/openai/chat/completions';
    }

    public function get_headers( string $api_key ): array {
        // Google uses API key in URL query param, not header
        return [
            'Content-Type' => 'application/json',
        ];
    }

    public function build_request_body( string $prompt, string $content, string $model ): array {
        return [
            'model'       => $model,
            'temperature' => 0.5,
            'messages'    => [
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
        return $response['choices'][0]['message']['content'] ?? '';
    }

    public function get_models(): array {
        return [
            [ 'value' => 'gemini-2.0-flash-exp', 'label' => 'Gemini 2.0 Flash (Experimental)' ],
            [ 'value' => 'gemini-2.0-flash', 'label' => 'Gemini 2.0 Flash' ],
            [ 'value' => 'gemini-1.5-flash-8b', 'label' => 'Gemini 1.5 Flash 8B' ],
            [ 'value' => 'gemini-1.5-flash', 'label' => 'Gemini 1.5 Flash' ],
        ];
    }

    public function get_api_url_with_key( string $api_key ): string {
        return $this->get_api_url() . "?key=$api_key";
    }
}
```

- [ ] **Step 2: Run PHP lint**

Run: `composer phpcs src/MetaTags/AiProviders/Google.php`
Expected: No errors

---

## Task 5: Create Anthropic Claude Provider

**Files:**
- Create: `src/MetaTags/AiProviders/Anthropic.php`

- [ ] **Step 1: Create Anthropic.php**

```php
<?php
namespace SlimSEO\MetaTags\AiProviders;

class Anthropic implements ProviderInterface {
    public function get_api_url(): string {
        return 'https://api.anthropic.com/v1/messages';
    }

    public function get_headers( string $api_key ): array {
        return [
            'Content-Type'    => 'application/json',
            'x-api-key'       => $api_key,
            'anthropic-version' => '2023-06-01',
        ];
    }

    public function build_request_body( string $prompt, string $content, string $model ): array {
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
            [ 'value' => 'claude-sonnet-4-20250514', 'label' => 'Claude Sonnet 4 (Latest)' ],
            [ 'value' => 'claude-3-5-sonnet-20240620', 'label' => 'Claude 3.5 Sonnet' ],
            [ 'value' => 'claude-3-5-haiku-20240620', 'label' => 'Claude 3.5 Haiku' ],
        ];
    }
}
```

- [ ] **Step 2: Run PHP lint**

Run: `composer phpcs src/MetaTags/AiProviders/Anthropic.php`
Expected: No errors

---

## Task 6: Refactor AI.php to Use Providers

**Files:**
- Modify: `src/MetaTags/AI.php`

- [ ] **Step 1: Add imports and update the class**

Replace the contents of AI.php with the refactored version that uses providers.

```php
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

        // Google needs API key in URL
        if ( $provider === 'google' ) {
            $url = $url . '?key=' . $api_key;
            $headers = []; // No auth header for Google
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
            'openai'    => OpenAI::class,
            'google'    => Google::class,
            'anthropic' => Anthropic::class,
            'openrouter'=> OpenRouter::class,
        ];

        return $providers[ $provider ] ?? null;
    }

    private function handle_response( $response, ProviderInterface $provider ): array {
        if ( is_wp_error( $response ) ) {
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
                return $this->response( sprintf( __( 'API error (%1$s): %2$s', 'slim-seo' ), $status_code, $error_message ) );
        }
    }

    private function response( string $message, string $status = 'error' ): array {
        return compact( 'message', 'status' );
    }
}
```

- [ ] **Step 2: Run PHP lint**

Run: `composer phpcs src/MetaTags/AI.php`
Expected: No errors

---

## Task 7: Update Settings.php Sanitization

**Files:**
- Modify: `src/Settings/Settings.php` (lines 91-99)

- [ ] **Step 1: Update sanitize method**

Add sanitization for new fields after line 96:

```php
// Existing line 96:
$option['openai_key'] = empty( $option['openai_key'] ) ? '' : sanitize_text_field( $option['openai_key'] );

// Add these lines after:
$option['ai_provider'] = in_array( $option['ai_provider'], [ 'openai', 'google', 'anthropic', 'openrouter' ], true ) ? $option['ai_provider'] : 'openai';
$option['ai_model']    = sanitize_text_field( $option['ai_model'] ?? '' );
$option['ai_api_key']  = sanitize_text_field( $option['ai_api_key'] ?? '' );
```

- [ ] **Step 2: Run PHP lint**

Run: `composer phpcs src/Settings/Settings.php`
Expected: No errors

---

## Task 8: Update RestApi.php to Hide API Key

**Files:**
- Modify: `src/Settings/MetaTags/RestApi.php` (line 61)

- [ ] **Step 1: Add hiding for new ai_api_key field**

After line 61:
```php
// Don't expose the OpenAI key to the client.
$option['openai_key'] = ! empty( $option['openai_key'] );
// Add:
$option['ai_api_key'] = ! empty( $option['ai_api_key'] );
```

- [ ] **Step 2: Run PHP lint**

Run: `composer phpcs src/Settings/MetaTags/RestApi.php`
Expected: No errors

---

## Task 9: Add Migration in Upgrade.php

**Files:**
- Modify: `src/Upgrade.php`

- [ ] **Step 1: Add upgrade_to_v2 method**

Add new upgrade method to migrate existing `openai_key` to new format:

```php
private function upgrade_to_v2(): void {
    $option = get_option( 'slim_seo' ) ?: [];

    // If old openai_key exists and new ai_api_key doesn't
    if ( ! empty( $option['openai_key'] ) && empty( $option['ai_api_key'] ) ) {
        $option['ai_api_key'] = $option['openai_key'];
        $option['ai_provider'] = 'openai';
        $option['ai_model']    = 'gpt-4.1-mini';

        update_option( 'slim_seo', $option );
    }
}
```

- [ ] **Step 2: Update SLIM_SEO_DB_VER constant**

Check what the current DB version is in the plugin and increment it.

- [ ] **Step 3: Run PHP lint**

Run: `composer phpcs src/Upgrade.php`
Expected: No errors

---

## Task 10: Update Tools Settings UI

**Files:**
- Modify: `src/Settings/tabs/tools.php`

- [ ] **Step 1: Replace AI Integration section**

Replace the existing "OpenAI Integration" section (lines 9-25) with:

```php
<h3><?php esc_attr_e( 'AI Integration', 'slim-seo' ); ?></h3>
<p>
    <?php
    echo wp_kses_post( sprintf( __( 'Select an AI provider and add your API key to unlock AI features in Slim SEO. <a href="%s" target="_blank">Get API keys</a>.', 'slim-seo' ), 'https://slimseo/docs/ai-settings/' ) );
    ?>
</p>
<div class="ef-control">
    <div class="ef-control__label">
        <label for="ss-ai-provider"><?php esc_html_e( 'Provider:', 'slim-seo' ); ?></label>
    </div>
    <div class="ef-control__input">
        <select name="slim_seo[ai_provider]" id="ss-ai-provider">
            <option value="openai" <?php selected( $data['ai_provider'] ?? 'openai', 'openai' ); ?>><?php esc_html_e( 'OpenAI', 'slim-seo' ); ?></option>
            <option value="google" <?php selected( $data['ai_provider'] ?? '', 'google' ); ?>><?php esc_html_e( 'Google (Gemini)', 'slim-seo' ); ?></option>
            <option value="anthropic" <?php selected( $data['ai_provider'] ?? '', 'anthropic' ); ?>><?php esc_html_e( 'Anthropic (Claude)', 'slim-seo' ); ?></option>
            <option value="openrouter" <?php selected( $data['ai_provider'] ?? '', 'openrouter' ); ?>><?php esc_html_e( 'OpenRouter', 'slim-seo' ); ?></option>
        </select>
    </div>
</div>
<div class="ef-control">
    <div class="ef-control__label">
        <label for="ss-ai-model"><?php esc_html_e( 'Model:', 'slim-seo' ); ?></label>
    </div>
    <div class="ef-control__input">
        <select name="slim_seo[ai_model]" id="ss-ai-model">
            <option value=""><?php esc_html_e( 'Select a provider first', 'slim-seo' ); ?></option>
        </select>
    </div>
</div>
<div class="ef-control">
    <div class="ef-control__label">
        <label for="ss-ai-api-key"><?php esc_html_e( 'API key:', 'slim-seo' ); ?></label>
    </div>
    <div class="ef-control__input">
        <div class="ss-input-wrapper">
            <input type="<?php echo esc_attr( empty( $data['ai_api_key'] ) ? 'text' : 'password' ); ?>" name="slim_seo[ai_api_key]" id="ss-ai-api-key" value="<?php echo esc_attr( $data['ai_api_key'] ?? '' ); ?>">
        </div>
    </div>
</div>
```

Note: The new fields need defaults in Settings.php to ensure they're always available in $data array.

- [ ] **Step 2: Add defaults in Settings.php**

Add defaults for new fields in the defaults array in Settings.php:

```php
private $defaults = [
    // ... existing ...
    'ai_provider' => 'openai',
    'ai_model'    => 'gpt-4.1-mini',
    'ai_api_key'  => '',
];
```

- [ ] **Step 3: Run PHP lint**

Run: `composer phpcs src/Settings/tabs/tools.php`
Expected: No errors

---

## Task 11: Add JavaScript for Provider/Model Behavior

**Files:**
- Create: `js/settings-ai.js`
- Modify: `apps.webpack.config.js` (add entry point)

- [ ] **Step 1: Create settings-ai.js**

```javascript
(function () {
    'use strict';

    const providerSelect = document.getElementById('ss-ai-provider');
    const modelSelect = document.getElementById('ss-ai-model');

    if (!providerSelect || !modelSelect) {
        return;
    }

    const providerModels = {
        openai: [
            { value: 'gpt-4.1-mini', label: 'GPT-4.1 Mini' },
            { value: 'gpt-4.1', label: 'GPT-4.1' },
            { value: 'gpt-4o-mini', label: 'GPT-4o Mini' },
            { value: 'gpt-4o', label: 'GPT-4o' },
        ],
        google: [
            { value: 'gemini-2.0-flash-exp', label: 'Gemini 2.0 Flash (Experimental)' },
            { value: 'gemini-2.0-flash', label: 'Gemini 2.0 Flash' },
            { value: 'gemini-1.5-flash-8b', label: 'Gemini 1.5 Flash 8B' },
            { value: 'gemini-1.5-flash', label: 'Gemini 1.5 Flash' },
        ],
        anthropic: [
            { value: 'claude-sonnet-4-20250514', label: 'Claude Sonnet 4 (Latest)' },
            { value: 'claude-3-5-sonnet-20240620', label: 'Claude 3.5 Sonnet' },
            { value: 'claude-3-5-haiku-20240620', label: 'Claude 3.5 Haiku' },
        ],
        openrouter: [
            { value: 'openai/gpt-4.1-mini', label: 'GPT-4.1 Mini' },
            { value: 'openai/gpt-4o-mini', label: 'GPT-4o Mini' },
            { value: 'anthropic/claude-3.5-sonnet-20240620', label: 'Claude 3.5 Sonnet' },
            { value: 'google/gemini-2.0-flash-exp', label: 'Gemini 2.0 Flash' },
            { value: 'meta/llama-3.3-70b-instruct', label: 'Llama 3.3 70B' },
        ],
    };

    function populateModels(provider) {
        const models = providerModels[provider] || [];
        const currentValue = modelSelect.value;

        modelSelect.innerHTML = '';

        if (models.length === 0) {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'Select a provider first';
            modelSelect.appendChild(option);
            return;
        }

        models.forEach(model => {
            const option = document.createElement('option');
            option.value = model.value;
            option.textContent = model.label;
            modelSelect.appendChild(option);
        });

        // Try to preserve selection if it exists in new provider
        const matchingOption = Array.from(modelSelect.options).find(opt => opt.value === currentValue);
        if (matchingOption) {
            modelSelect.value = currentValue;
        }
    }

    // Populate on page load
    const initialProvider = providerSelect.value || 'openai';
    populateModels(initialProvider);
    if (modelSelect.value) {
        modelSelect.value = '<?php echo esc_js( $data['ai_model'] ?? '' ); ?>';
    }

    // Handle provider change
    providerSelect.addEventListener('change', function () {
        populateModels(this.value);
    });
})();
```

Actually, since we're using vanilla JS and not React, let me simplify this to work without PHP echo in JS:

```javascript
(function () {
    'use strict';

    const providerSelect = document.getElementById('ss-ai-provider');
    const modelSelect = document.getElementById('ss-ai-model');

    if (!providerSelect || !modelSelect) {
        return;
    }

    const providerModels = {
        openai: [
            { value: 'gpt-4.1-mini', label: 'GPT-4.1 Mini' },
            { value: 'gpt-4.1', label: 'GPT-4.1' },
            { value: 'gpt-4o-mini', label: 'GPT-4o Mini' },
            { value: 'gpt-4o', label: 'GPT-4o' },
        ],
        google: [
            { value: 'gemini-2.0-flash-exp', label: 'Gemini 2.0 Flash (Experimental)' },
            { value: 'gemini-2.0-flash', label: 'Gemini 2.0 Flash' },
            { value: 'gemini-1.5-flash-8b', label: 'Gemini 1.5 Flash 8B' },
            { value: 'gemini-1.5-flash', label: 'Gemini 1.5 Flash' },
        ],
        anthropic: [
            { value: 'claude-sonnet-4-20250514', label: 'Claude Sonnet 4 (Latest)' },
            { value: 'claude-3-5-sonnet-20240620', label: 'Claude 3.5 Sonnet' },
            { value: 'claude-3-5-haiku-20240620', label: 'Claude 3.5 Haiku' },
        ],
        openrouter: [
            { value: 'openai/gpt-4.1-mini', label: 'GPT-4.1 Mini' },
            { value: 'openai/gpt-4o-mini', label: 'GPT-4o Mini' },
            { value: 'anthropic/claude-3.5-sonnet-20240620', label: 'Claude 3.5 Sonnet' },
            { value: 'google/gemini-2.0-flash-exp', label: 'Gemini 2.0 Flash' },
            { value: 'meta/llama-3.3-70b-instruct', label: 'Llama 3.3 70B' },
        ],
    };

    function populateModels(provider) {
        const models = providerModels[provider] || [];
        const currentValue = modelSelect.value;

        modelSelect.innerHTML = '';

        if (models.length === 0) {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'Select a provider first';
            modelSelect.appendChild(option);
            return;
        }

        models.forEach(function(model) {
            const option = document.createElement('option');
            option.value = model.value;
            option.textContent = model.label;
            modelSelect.appendChild(option);
        });

        // Try to preserve selection if it exists in new provider
        let matchingOption = null;
        for (let i = 0; i < modelSelect.options.length; i++) {
            if (modelSelect.options[i].value === currentValue) {
                matchingOption = modelSelect.options[i];
                break;
            }
        }
        if (matchingOption) {
            modelSelect.value = currentValue;
        }
    }

    // Populate on page load based on saved provider
    const savedProvider = providerSelect.value || 'openai';
    populateModels(savedProvider);

    // Handle provider change
    providerSelect.addEventListener('change', function() {
        populateModels(this.value);
    });
})();
```

- [ ] **Step 2: Update webpack config**

Add entry point in apps.webpack.config.js:

```javascript
entry: {
    // ... existing ...
    'settings-ai': './js/settings-ai.js',
},
```

- [ ] **Step 3: Enqueue the script**

Add script enqueue in Settings.php or in tools.php template.

In tools.php, add at the bottom (after submit_button):
```php
wp_enqueue_script( 'slim-seo-settings-ai' );
```

- [ ] **Step 4: Build JS**

Run: `npm run build:js`
Expected: Build succeeds

---

## Task 12: Verify and Test

- [ ] **Step 1: Run all PHP lint**

Run: `composer phpcs src/MetaTags/AiProviders/ src/MetaTags/AI.php src/Settings/Settings.php src/Settings/MetaTags/RestApi.php src/Upgrade.php src/Settings/tabs/tools.php`
Expected: No errors

- [ ] **Step 2: Run static analysis**

Run: `composer phpstan analyse src/MetaTags/AiProviders/ src/MetaTags/AI.php`
Expected: No critical errors

- [ ] **Step 3: Build assets**

Run: `npm run build`
Expected: Build succeeds

- [ ] **Step 4: Test manually**
- Go to Settings > Tools
- Verify AI Integration section shows provider/model/API key fields
- Change provider and verify model dropdown updates
- Save settings and verify they persist