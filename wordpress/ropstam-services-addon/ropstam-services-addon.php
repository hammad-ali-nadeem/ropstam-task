
<?php
/**
 * Plugin Name: Ropstam Services Add-on Calculator
 * Description: A custom services post type with pricing fields and an interactive add-on calculator.
 * Version: 1.0.0
 * Requires PHP: 7.4
 * Author: Hammad Ali Nadeem
 * Text Domain: ropstam-services-addon
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'ROPSAM_SAC_VERSION', '1.0.0' );
define( 'ROPSAM_SAC_PATH', plugin_dir_path( __FILE__ ) );
define( 'ROPSAM_SAC_URL', plugin_dir_url( __FILE__ ) );

/**
 * Load plugin functionality.
 */
function ropstam_sac_initialize() {
    require_once ROPSAM_SAC_PATH . 'includes/post-type.php';
    require_once ROPSAM_SAC_PATH . 'includes/meta-fields.php';
    require_once ROPSAM_SAC_PATH . 'includes/shortcode.php';
}
add_action( 'plugins_loaded', 'ropstam_sac_initialize' );

/**
 * Register frontend assets.
 */
function ropstam_sac_register_assets() {
    wp_register_style(
        'ropstam-sac-style',
        ROPSAM_SAC_URL . 'assets/css/style.css',
        array(),
        ROPSAM_SAC_VERSION
    );

    wp_register_script(
        'ropstam-sac-calculator',
        ROPSAM_SAC_URL . 'assets/js/calculator.js',
        array(),
        ROPSAM_SAC_VERSION,
        true
    );
}
add_action( 'wp_enqueue_scripts', 'ropstam_sac_register_assets' );

/**
 * Flush rewrite rules on activation.
 */
function ropstam_sac_activate() {
    require_once ROPSAM_SAC_PATH . 'includes/post-type.php';

    ropstam_sac_register_service_post_type();

    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ropstam_sac_activate' );

/**
 * Flush rewrite rules on deactivation.
 */
function ropstam_sac_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'ropstam_sac_deactivate' );
