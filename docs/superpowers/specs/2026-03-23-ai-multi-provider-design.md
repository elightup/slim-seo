# AI Multi-Provider Support Design

## Overview

Add support for multiple AI providers (OpenAI, Google, Anthropic, OpenRouter) to generate meta title and meta description, replacing the existing OpenAI-only implementation.

## Requirements

1. User selects a provider from a dropdown
2. Model dropdown updates based on selected provider
3. Single API key field shared across all providers
4. Maintain backward compatibility with existing OpenAI users

## Settings (Backend)

**File:** `src/Settings/tabs/tools.php`

Add provider dropdown and model dropdown to the existing AI Integration section:

```php
// Provider dropdown
<select name="slim_seo[ai_provider]" id="ss-ai-provider">
    <option value="openai">OpenAI</option>
    <option value="google">Google (Gemini)</option>
    <option value="anthropic">Anthropic (Claude)</option>
    <option value="openrouter">OpenRouter</option>
</select>

// Model dropdown (populated by JavaScript based on provider)
<select name="slim_seo[ai_model]" id="ss-ai-model"></select>

// API key input (existing field, now shared)
<input type="password" name="slim_seo[ai_api_key]" ...>
```

**Settings stored in:**
```php
$option['ai_provider'] = sanitize_text_field( $option['ai_provider'] ?? 'openai' );
$option['ai_model']    = sanitize_text_field( $option['ai_model'] ?? '' );
$option['ai_api_key']  = sanitize_text_field( $option['ai_api_key'] ?? '' );
```

**Migrate existing data:** Copy `openai_key` to `ai_api_key` on first load if `ai_api_key` is empty.

## Settings (Frontend/JS)

**File:** `js/admin/src/settings/tabs/tools.js` (or create new)

```javascript
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
```

**Add model filter:** Allow users to modify the model via filter:

```php
apply_filters( 'slim_seo_ai_model', string $model, string $provider ): string
```

Example usage:
```php
add_filter( 'slim_seo_ai_model', function( $model, $provider ) {
    if ( $provider === 'openai' ) {
        return 'gpt-4o'; // Force GPT-4o for OpenAI
    }
    return $model;
}, 10, 2 );
```

Behavior:
- On page load: populate model dropdown based on saved provider
- On provider change: update model dropdown with corresponding models
- Preserve selected model when switching providers (if model exists in new provider)

## Provider Interface

**File:** `src/MetaTags/AiProviders/ProviderInterface.php`

```php
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

## Provider Implementations

### 1. OpenAI

**File:** `src/MetaTags/AiProviders/OpenAI.php`

- **API URL:** `https://api.openai.com/v1/responses`
- **Headers:** `Authorization: Bearer {api_key}`
- **Request format:**
    ```json
    {
        "model": "gpt-4.1-mini",
        "temperature": 0.5,
        "input": [
            { "role": "system", "content": "{prompt}" },
            { "role": "user", "content": "{content}" }
        ]
    }
    ```
- **Response parse:** `$response['output'][0]['content'][0]['text']`

### 2. OpenRouter

**File:** `src/MetaTags/AiProviders/OpenRouter.php`

- **API URL:** `https://openrouter.ai/api/v1/responses`
- **Headers:** `Authorization: Bearer {api_key}`
- **Request format:** Same as OpenAI
- **Response parse:** Same as OpenAI
- **Note:** OpenRouter uses same Responses API format as OpenAI

### 3. Google Gemini

**File:** `src/MetaTags/AiProviders/Google.php`

- **API URL:** `https://generativelanguage.googleapis.com/v1beta/openai/chat/completions?key={api_key}`
- **Headers:** Empty array (API key passed via URL query parameter)
- **Request format (Chat Completions):**
    ```json
    {
        "model": "gemini-2.0-flash",
        "temperature": 0.5,
        "messages": [
            { "role": "system", "content": "{prompt}" },
            { "role": "user", "content": "{content}" }
        ]
    }
    ```
- **Response parse:** `$response['choices'][0]['message']['content']`

> **Note:** Google's OpenAI-compatible endpoint requires API key as a query parameter (`?key=...`), not in the Authorization header.

### 4. Anthropic Claude

**File:** `src/MetaTags/AiProviders/Anthropic.php`

- **API URL:** `https://api.anthropic.com/v1/messages`
- **Headers:** `x-api-key: {api_key}`, `anthropic-version: 2023-06-01`
- **Request format:**
    ```json
    {
        "model": "claude-sonnet-4-20250514",
        "max_tokens": 1024,
        "temperature": 0.5,
        "system": "{prompt}",
        "messages": [
            { "role": "user", "content": "{content}" }
        ]
    }
    ```
- **Response parse:** `$response['content'][0]['text']`

## AI Class Updates

**File:** `src/MetaTags/AI.php`

1. Update `request()` method to use provider-based approach:

```php
private function request( string $prompt, string $content ): array {
    $settings   = get_option( 'slim_seo' ) ?: [];
    $provider   = $settings['ai_provider'] ?? 'openai';
    $model      = $settings['ai_model'] ?? '';
    $api_key    = $settings['ai_api_key'] ?? '';

    if ( empty( $api_key ) ) {
        return $this->response( __( 'Please provide an API key in settings.', 'slim-seo' ) );
    }

    // Get provider instance
    $provider_class = $this->get_provider_class( $provider );
    if ( ! $provider_class ) {
        return $this->response( __( 'Invalid provider selected.', 'slim-seo' ) );
    }

    $provider = new $provider_class();

    // Validate model - use default if empty
    if ( empty( $model ) ) {
        $models   = $provider->get_models();
        $model    = $models[0]['value'] ?? '';
    }

    if ( empty( $model ) ) {
        return $this->response( __( 'No models available for the selected provider.', 'slim-seo' ) );
    }

    // Build request
    $url     = $provider->get_api_url();
    $headers = $provider->get_headers( $api_key );
    $body    = $provider->build_request_body( $prompt, $content, $model );

    // Make request
    $response = wp_safe_remote_post( $url, [
        'headers' => $headers,
        'body'    => wp_json_encode( $body ),
        'timeout' => 45,
    ] );

    // Handle response...
    return $this->handle_response( $response, $provider );
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
```

2. Update error handling to handle provider-specific errors:

```php
private function handle_response( $response, ProviderInterface $provider ): array {
    if ( is_wp_error( $response ) ) {
        return $this->response( sprintf( __( 'Connection error: %s', 'slim-seo' ), $response->get_error_message() ) );
    }

    $status_code = wp_remote_retrieve_response_code( $response );
    $body        = wp_remote_retrieve_body( $response );
    $result      = json_decode( $body, true );

    // Check for API errors (provider-specific handling)
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
```

3. Handle common HTTP errors across all providers:

```php
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
```

## Data Migration

On plugin upgrade, migrate existing settings:

```php
// In Settings.php or a migration handler
public function migrate_ai_settings(): void {
    $settings = get_option( 'slim_seo' ) ?: [];

    // If old openai_key exists and new ai_api_key doesn't
    if ( ! empty( $settings['openai_key'] ) && empty( $settings['ai_api_key'] ) ) {
        $settings['ai_api_key'] = $settings['openai_key'];
        $settings['ai_provider'] = 'openai';
        $settings['ai_model']    = 'gpt-4.1-mini';

        update_option( 'slim_seo', $settings );
    }
}
```

## Settings.php Updates

**File:** `src/Settings/Settings.php`

Update the existing sanitization at line ~96 to include new fields:

```php
$option['openai_key']   = empty( $option['openai_key'] ) ? '' : sanitize_text_field( $option['openai_key'] );
// Add these lines:
$option['ai_provider']  = in_array( $option['ai_provider'], [ 'openai', 'google', 'anthropic', 'openrouter' ], true ) ? $option['ai_provider'] : 'openai';
$option['ai_model']     = sanitize_text_field( $option['ai_model'] ?? '' );
$option['ai_api_key']   = sanitize_text_field( $option['ai_api_key'] ?? '' );
```

Also update `RestApi.php` to hide the new API key field from the client (same as existing `openai_key` handling):

## File Structure

```
src/
├── MetaTags/
│   ├── AI.php                    # REST API handler (modified)
│   └── AiProviders/
│       ├── ProviderInterface.php  # New - Interface
│       ├── OpenAI.php              # New - OpenAI implementation
│       ├── OpenRouter.php          # New - OpenRouter implementation
│       ├── Google.php              # New - Google Gemini implementation
│       └── Anthropic.php          # New - Claude implementation
├── Settings/
│   ├── Settings.php              # Modified - add setting sanitization
│   └── tabs/
│       └── tools.php              # Modified - add provider/model dropdowns
└── js/
    └── admin/
        └── src/
            └── settings/
                └── tabs/
                    └── tools.js    # Modified - provider/model logic
```

## Testing

1. **OpenAI:** Test with various GPT-4 models
2. **Google:** Test with Gemini models
3. **Anthropic:** Test with Claude models
4. **OpenRouter:** Test with different provider models
5. **Migration:** Verify existing users' API key is preserved
6. **Error handling:** Test invalid keys, rate limits, server errors

## Backward Compatibility

- Existing users with `openai_key` setting will have it migrated to `ai_api_key`
- Default provider: OpenAI
- Default model: `gpt-4.1-mini` (same as current)