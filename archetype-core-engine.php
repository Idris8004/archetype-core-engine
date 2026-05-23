<?php
/**
 * Plugin Name: Archetype Core Engine
 * Description: Interactive behavioral profiling engine with three calculation tools.
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: archetype-core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define( 'ARCHETYPE_CORE_VERSION', '1.0.0' );
define( 'ARCHETYPE_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'ARCHETYPE_CORE_URL', plugin_dir_url( __FILE__ ) );

// Include required classes
require_once ARCHETYPE_CORE_DIR . 'includes/class-archetype-settings.php';
require_once ARCHETYPE_CORE_DIR . 'includes/class-archetype-ajax.php';

// Initialize Plugin Update Checker
require_once ARCHETYPE_CORE_DIR . 'includes/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/Idris8004/archetype-core-engine/',
	__FILE__,
	'archetype-core-engine'
);
$myUpdateChecker->setBranch('main');

class Archetype_Core_Engine {

    public function __construct() {
        // Initialize Admin Settings
        if ( is_admin() ) {
            new Archetype_Settings();
        }

        // Initialize AJAX handlers
        new Archetype_Ajax();

        // Register shortcodes
        add_shortcode( 'archetype_matrix', [ $this, 'render_matrix_shortcode' ] );
        add_shortcode( 'archetype_bazi', [ $this, 'render_bazi_shortcode' ] );
        add_shortcode( 'archetype_psych', [ $this, 'render_psych_shortcode' ] );

        // Enqueue scripts and styles
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_global_assets' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    public function enqueue_global_assets() {
        // Enqueue fonts globally so the theme can use them
        wp_enqueue_style( 'google-fonts-geist', 'https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap', [], null );
        wp_enqueue_style( 'google-symbols', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap', [], null );
        
        // Enqueue global overrides
        wp_enqueue_style( 'archetype-global-css', ARCHETYPE_CORE_URL . 'assets/css/global-theme-overrides.css', [], ARCHETYPE_CORE_VERSION );
    }

    public function enqueue_assets() {
        // Only enqueue on pages that have our shortcodes.
        global $post;
        if ( ! is_a( $post, 'WP_Post' ) ) {
            return;
        }

        if ( has_shortcode( $post->post_content, 'archetype_matrix' ) ||
             has_shortcode( $post->post_content, 'archetype_bazi' ) ||
             has_shortcode( $post->post_content, 'archetype_psych' ) ) {

            // Custom Scoped CSS
            wp_enqueue_style( 'archetype-core-css', ARCHETYPE_CORE_URL . 'assets/css/archetype-frontend.css', [], ARCHETYPE_CORE_VERSION );

            // Chart.js (for Radar chart)
            wp_enqueue_script( 'chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', [], null, true );

            // Frontend JS
            wp_enqueue_script( 'archetype-core-js', ARCHETYPE_CORE_URL . 'assets/js/archetype-frontend.js', [ 'jquery', 'chart-js' ], ARCHETYPE_CORE_VERSION, true );
            
            // Pass localized data to JS
            wp_localize_script( 'archetype-core-js', 'archetypeCoreVars', [
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'archetype_synthesis_nonce' ),
            ] );
        }
    }

    public function render_matrix_shortcode( $atts ) {
        ob_start();
        include ARCHETYPE_CORE_DIR . 'templates/shortcode-matrix.php';
        return ob_get_clean();
    }

    public function render_bazi_shortcode( $atts ) {
        ob_start();
        include ARCHETYPE_CORE_DIR . 'templates/shortcode-bazi.php';
        return ob_get_clean();
    }

    public function render_psych_shortcode( $atts ) {
        ob_start();
        include ARCHETYPE_CORE_DIR . 'templates/shortcode-psych.php';
        return ob_get_clean();
    }
}

// Initialize the plugin
new Archetype_Core_Engine();
