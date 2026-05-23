<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Archetype_Ajax {

    public function __construct() {
        add_action( 'wp_ajax_archetype_generate_synthesis', [ $this, 'generate_synthesis' ] );
        add_action( 'wp_ajax_nopriv_archetype_generate_synthesis', [ $this, 'generate_synthesis' ] );
    }

    public function generate_synthesis() {
        check_ajax_referer( 'archetype_synthesis_nonce', 'nonce' );

        $data = isset( $_POST['data'] ) ? sanitize_text_field( wp_unslash( $_POST['data'] ) ) : '';
        $type = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';

        if ( empty( $data ) || empty( $type ) ) {
            wp_send_json_error( 'Invalid data provided.' );
        }

        $system_prompt = "You are an elite organizational psychologist and behavioral profiler. Synthesize the provided user data into a highly practical, clinical assessment. Do not use overly robotic, sci-fi, or mystical language. Translate the metrics into real-world insights regarding their work habits, potential bottlenecks, and optimal environments. Format your response exactly like this: First line should be a 3-5 word punchy, professional title wrapped in **. The second line should be a single, concise paragraph (under 100 words) explaining the practical implications.";
        $prompt = "Tool Type: {$type}. User Data: {$data}. Please provide the synthesis.";

        $active_provider = get_option( 'archetype_active_provider', 'gemini' );
        
        $response_text = '';
        $debug_log = [];

        try {
            if ( $active_provider === 'gemini' ) {
                $response_text = $this->call_gemini( $system_prompt, $prompt, $debug_log );
            } elseif ( $active_provider === 'openai' ) {
                $response_text = $this->call_openai( $system_prompt, $prompt, $debug_log );
            } elseif ( $active_provider === 'anthropic' ) {
                $response_text = $this->call_anthropic( $system_prompt, $prompt, $debug_log );
            }

            // Fallbacks
            if ( empty( $response_text ) && $active_provider !== 'gemini' ) {
                $response_text = $this->call_gemini( $system_prompt, $prompt, $debug_log );
            }
            if ( empty( $response_text ) && $active_provider !== 'openai' ) {
                $response_text = $this->call_openai( $system_prompt, $prompt, $debug_log );
            }
            if ( empty( $response_text ) && $active_provider !== 'anthropic' ) {
                $response_text = $this->call_anthropic( $system_prompt, $prompt, $debug_log );
            }

        } catch ( Exception $e ) {
            $debug_log[] = 'Exception: ' . $e->getMessage();
        }

        if ( ! empty( $response_text ) ) {
            wp_send_json_success( $response_text );
        } else {
            $error_msg = 'All API providers failed. Debug Info: ' . implode(' | ', $debug_log);
            wp_send_json_error( $error_msg );
        }
    }

    private function call_gemini( $system_prompt, $user_prompt, &$debug_log ) {
        $api_key = get_option( 'archetype_gemini_key' );
        if ( empty( $api_key ) ) {
            $debug_log[] = 'Gemini Key Empty';
            return false;
        }

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $api_key;
        
        $body = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [ [ 'text' => $system_prompt . "\n\n" . $user_prompt ] ]
                ]
            ]
        ];

        $response = wp_remote_post( $url, [
            'headers' => [ 'Content-Type' => 'application/json' ],
            'body'    => wp_json_encode( $body ),
            'timeout' => 15,
            'sslverify' => false, // often needed in local environments
        ] );

        if ( is_wp_error( $response ) ) {
            $debug_log[] = 'Gemini WP_Error: ' . $response->get_error_message();
            return false;
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( isset( $data['candidates'][0]['content']['parts'][0]['text'] ) ) {
            return $data['candidates'][0]['content']['parts'][0]['text'];
        }

        $debug_log[] = 'Gemini Response Error: ' . wp_trim_words($body, 10);
        return false;
    }

    private function call_openai( $system_prompt, $user_prompt, &$debug_log ) {
        $api_key = get_option( 'archetype_openai_key' );
        if ( empty( $api_key ) ) {
            $debug_log[] = 'OpenAI Key Empty';
            return false;
        }

        $url = 'https://api.openai.com/v1/chat/completions';
        
        $body = [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [ 'role' => 'system', 'content' => $system_prompt ],
                [ 'role' => 'user', 'content' => $user_prompt ]
            ]
        ];

        $response = wp_remote_post( $url, [
            'headers' => [ 
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $api_key
            ],
            'body'    => wp_json_encode( $body ),
            'timeout' => 15,
            'sslverify' => false,
        ] );

        if ( is_wp_error( $response ) ) {
            $debug_log[] = 'OpenAI WP_Error: ' . $response->get_error_message();
            return false;
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( isset( $data['choices'][0]['message']['content'] ) ) {
            return $data['choices'][0]['message']['content'];
        }

        $debug_log[] = 'OpenAI Response Error: ' . wp_trim_words($body, 10);
        return false;
    }

    private function call_anthropic( $system_prompt, $user_prompt, &$debug_log ) {
        $api_key = get_option( 'archetype_anthropic_key' );
        if ( empty( $api_key ) ) {
            $debug_log[] = 'Anthropic Key Empty';
            return false;
        }

        $url = 'https://api.anthropic.com/v1/messages';
        
        $body = [
            'model' => 'claude-3-haiku-20240307',
            'max_tokens' => 300,
            'system' => $system_prompt,
            'messages' => [
                [ 'role' => 'user', 'content' => $user_prompt ]
            ]
        ];

        $response = wp_remote_post( $url, [
            'headers' => [ 
                'Content-Type'      => 'application/json',
                'x-api-key'         => $api_key,
                'anthropic-version' => '2023-06-01'
            ],
            'body'    => wp_json_encode( $body ),
            'timeout' => 15,
            'sslverify' => false,
        ] );

        if ( is_wp_error( $response ) ) {
            $debug_log[] = 'Anthropic WP_Error: ' . $response->get_error_message();
            return false;
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( isset( $data['content'][0]['text'] ) ) {
            return $data['content'][0]['text'];
        }

        $debug_log[] = 'Anthropic Response Error: ' . wp_trim_words($body, 10);
        return false;
    }
}
