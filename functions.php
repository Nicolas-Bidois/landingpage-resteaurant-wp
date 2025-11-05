<?php
/**
 * NB Landing Theme - Main Functions File
 * 
 * @package NB_Landing
 * @version 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) { 
    exit; 
}

// Theme version
define( 'NB_THEME_VERSION', '1.1.0' );

// Include modular functionality
require_once get_template_directory() . '/inc/setup.php';
require_once get_template_directory() . '/inc/performance.php';
require_once get_template_directory() . '/inc/seo.php';
require_once get_template_directory() . '/inc/security.php';
require_once get_template_directory() . '/inc/accessibility.php';
require_once get_template_directory() . '/inc/patterns.php';

/**
 * Theme activation hook
 * Clear transients and flush rewrite rules
 */
function nb_theme_activation() {
    // Clear all theme-related transients
    delete_transient( 'nb_structured_data_restaurant' );
    
    // Flush rewrite rules
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'nb_theme_activation' );

/**
 * Theme deactivation hook
 * Clean up transients
 */
function nb_theme_deactivation() {
    delete_transient( 'nb_structured_data_restaurant' );
}
add_action( 'switch_theme', 'nb_theme_deactivation' );
