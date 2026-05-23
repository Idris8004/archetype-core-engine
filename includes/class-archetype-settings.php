<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Archetype_Settings {

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_plugin_page' ] );
        add_action( 'admin_init', [ $this, 'page_init' ] );
    }

    public function add_plugin_page() {
        add_options_page(
            'Archetype Core Settings', 
            'Archetype Core', 
            'manage_options', 
            'archetype-core-engine', 
            [ $this, 'create_admin_page' ]
        );
    }

    public function create_admin_page() {
        ?>
        <div class="wrap">
            <h1>Archetype Core Engine Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'archetype_option_group' );
                do_settings_sections( 'archetype-core-engine' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function page_init() {
        register_setting( 'archetype_option_group', 'archetype_gemini_key' );
        register_setting( 'archetype_option_group', 'archetype_openai_key' );
        register_setting( 'archetype_option_group', 'archetype_anthropic_key' );
        register_setting( 'archetype_option_group', 'archetype_active_provider' );
        register_setting( 'archetype_option_group', 'archetype_system_prompt' );

        add_settings_section(
            'archetype_setting_section',
            'API Configuration',
            [ $this, 'section_info' ],
            'archetype-core-engine'
        );

        add_settings_field(
            'archetype_active_provider',
            'Active Provider',
            [ $this, 'active_provider_callback' ],
            'archetype-core-engine',
            'archetype_setting_section'
        );

        add_settings_field(
            'archetype_gemini_key',
            'Gemini API Key (Primary)',
            [ $this, 'gemini_key_callback' ],
            'archetype-core-engine',
            'archetype_setting_section'
        );

        add_settings_field(
            'archetype_openai_key',
            'OpenAI API Key (Fallback 1)',
            [ $this, 'openai_key_callback' ],
            'archetype-core-engine',
            'archetype_setting_section'
        );

        add_settings_field(
            'archetype_anthropic_key',
            'Anthropic API Key (Fallback 2)',
            [ $this, 'anthropic_key_callback' ],
            'archetype-core-engine',
            'archetype_setting_section'
        );

        add_settings_field(
            'archetype_system_prompt',
            'System Prompt',
            [ $this, 'system_prompt_callback' ],
            'archetype-core-engine',
            'archetype_setting_section'
        );
    }

    public function section_info() {
        echo 'Enter your API keys below. The system will use the Active Provider first, and automatically fallback if it fails.';
    }

    public function active_provider_callback() {
        $setting = get_option( 'archetype_active_provider', 'gemini' );
        ?>
        <select name="archetype_active_provider" id="archetype_active_provider">
            <option value="gemini" <?php selected( $setting, 'gemini' ); ?>>Gemini</option>
            <option value="openai" <?php selected( $setting, 'openai' ); ?>>OpenAI</option>
            <option value="anthropic" <?php selected( $setting, 'anthropic' ); ?>>Anthropic</option>
        </select>
        <?php
    }

    public function gemini_key_callback() {
        printf(
            '<input type="text" id="archetype_gemini_key" name="archetype_gemini_key" value="%s" size="50" />',
            esc_attr( get_option( 'archetype_gemini_key' ) )
        );
    }

    public function openai_key_callback() {
        printf(
            '<input type="text" id="archetype_openai_key" name="archetype_openai_key" value="%s" size="50" />',
            esc_attr( get_option( 'archetype_openai_key' ) )
        );
    }

    public function anthropic_key_callback() {
        printf(
            '<input type="text" id="archetype_anthropic_key" name="archetype_anthropic_key" value="%s" size="50" />',
            esc_attr( get_option( 'archetype_anthropic_key' ) )
        );
    }

    public function system_prompt_callback() {
        $default_prompt = "Act as an analytical profiling engine. Analyze the provided data matrix and generate a precise, clinical, and slightly mysterious behavioral synthesis. Avoid typical astrology cliches; use terminology reminiscent of sci-fi diagnostics or architectural blueprints. Maximum 3 sentences.";
        $setting = get_option( 'archetype_system_prompt', $default_prompt );
        printf(
            '<textarea id="archetype_system_prompt" name="archetype_system_prompt" rows="5" cols="80">%s</textarea>',
            esc_textarea( $setting )
        );
    }
}
