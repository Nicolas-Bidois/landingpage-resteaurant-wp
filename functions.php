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
add_filter( 'the_content', 'nb_add_lazy_loading' );
add_filter( 'post_thumbnail_html', 'nb_add_lazy_loading' );

// Add structured data for restaurant
function nb_add_structured_data() {
    if ( is_front_page() ) {
        $structured_data = array(
            '@context' => 'https://schema.org',
            '@type' => 'Restaurant',
            'name' => 'Maison Luma',
            'description' => 'Cuisine de saison • Bistronomie locale',
            'address' => array(
                '@type' => 'PostalAddress',
                'streetAddress' => '12 rue des Gourmets',
                'addressLocality' => 'Auxerre',
                'postalCode' => '89000',
                'addressCountry' => 'FR'
            ),
            'telephone' => '+33386000000',
            'servesCuisine' => 'French',
            'priceRange' => '€€',
            'aggregateRating' => array(
                '@type' => 'AggregateRating',
                'ratingValue' => '4.8',
                'reviewCount' => '1245'
            )
        );
        echo '<script type="application/ld+json">' . wp_json_encode( $structured_data ) . '</script>';
    }
}
add_action( 'wp_head', 'nb_add_structured_data' );



// Optimize performance: Remove query strings from static resources
function nb_remove_query_strings( $src ) {
    if ( strpos( $src, 'ver=' ) ) {
        $src = remove_query_arg( 'ver', $src );
    }
    return $src;
}
add_filter( 'script_loader_src', 'nb_remove_query_strings', 15, 1 );
add_filter( 'style_loader_src', 'nb_remove_query_strings', 15, 1 );

// Add preload for critical resources
function nb_add_preload_headers() {
    if ( ! is_admin() ) {
        header( 'Link: <' . get_template_directory_uri() . '/assets/css/main.css>; rel=preload; as=style', false );
        header( 'Link: <' . get_template_directory_uri() . '/assets/js/navigation.js>; rel=preload; as=script', false );
    }
}
add_action( 'send_headers', 'nb_add_preload_headers' );

// AJAX handler for menu loading
function nb_load_menu_items() {
    check_ajax_referer( 'nb_menu_nonce', 'nonce' );

    $category = sanitize_text_field( $_POST['category'] ?? 'all' );

    // Simulate menu data (replace with actual CPT query)
    $menu_items = array(
        array(
            'title' => 'Salade de saison',
            'price' => '12€',
            'description' => 'Légumes frais du marché, vinaigrette maison',
            'image' => get_template_directory_uri() . '/assets/img/plat1.jpg',
            'category' => 'entrees'
        ),
        // Add more items...
    );

    wp_send_json_success( $menu_items );
}
add_action( 'wp_ajax_load_menu', 'nb_load_menu_items' );
add_action( 'wp_ajax_nopriv_load_menu', 'nb_load_menu_items' );


// Custom excerpt length
function nb_custom_excerpt_length( $length ) {
    return 20;
}
add_filter( 'excerpt_length', 'nb_custom_excerpt_length', 999 );

// Custom excerpt more
function nb_custom_excerpt_more( $more ) {
    return '...';
}
add_filter( 'excerpt_more', 'nb_custom_excerpt_more' );
