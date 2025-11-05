<?php
/**
 * Security enhancements for NB Landing theme
 */

// Add security headers
function nb_add_security_headers() {
    if ( ! is_admin() ) {
        header( 'X-Content-Type-Options: nosniff' );
        header( 'X-Frame-Options: SAMEORIGIN' );
        header( 'X-XSS-Protection: 1; mode=block' );
        header( 'Referrer-Policy: strict-origin-when-cross-origin' );
    }
}
add_action( 'send_headers', 'nb_add_security_headers' );

// Security: Sanitize form inputs
function nb_sanitize_form_input( $input ) {
    return sanitize_text_field( wp_strip_all_tags( $input ) );
}

// AJAX handler for menu loading with enhanced security
function nb_load_menu_items() {
    check_ajax_referer( 'nb_menu_nonce', 'nonce' );

    $category = isset( $_POST['category'] ) ? nb_sanitize_form_input( $_POST['category'] ) : 'all';

    // Validate category
    $allowed_categories = array( 'all', 'entrees', 'plats', 'desserts', 'boissons' );
    if ( ! in_array( $category, $allowed_categories, true ) ) {
        wp_send_json_error( array( 'message' => 'Invalid category' ) );
        return;
    }

    // Query menu items from CPT
    $args = array(
        'post_type' => 'plat',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    );

    if ( $category !== 'all' ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'categorie_plat',
                'field' => 'slug',
                'terms' => $category,
            ),
        );
    }

    $query = new WP_Query( $args );
    $menu_items = array();

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $menu_items[] = array(
                'title' => get_the_title(),
                'price' => get_post_meta( get_the_ID(), 'prix', true ),
                'description' => get_the_excerpt(),
                'image' => get_the_post_thumbnail_url( get_the_ID(), 'medium' ),
                'category' => $category
            );
        }
        wp_reset_postdata();
    }

    wp_send_json_success( $menu_items );
}
add_action( 'wp_ajax_load_menu', 'nb_load_menu_items' );
add_action( 'wp_ajax_nopriv_load_menu', 'nb_load_menu_items' );

// Remove WordPress version from head
remove_action( 'wp_head', 'wp_generator' );

// Disable XML-RPC if not needed
add_filter( 'xmlrpc_enabled', '__return_false' );

// Disable file editing from admin
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
    define( 'DISALLOW_FILE_EDIT', true );
}

// Limit login attempts (basic implementation)
function nb_check_login_attempts( $user, $username, $password ) {
    $max_attempts = 5;
    $lockout_duration = 15 * MINUTE_IN_SECONDS;
    
    $attempts_key = 'nb_login_attempts_' . sanitize_user( $username );
    $lockout_key = 'nb_login_lockout_' . sanitize_user( $username );
    
    // Check if user is locked out
    if ( get_transient( $lockout_key ) ) {
        return new WP_Error( 'too_many_attempts', __( 'Too many failed login attempts. Please try again later.' ) );
    }
    
    // If login failed, increment attempts
    if ( is_wp_error( $user ) ) {
        $attempts = (int) get_transient( $attempts_key );
        $attempts++;
        
        set_transient( $attempts_key, $attempts, $lockout_duration );
        
        if ( $attempts >= $max_attempts ) {
            set_transient( $lockout_key, true, $lockout_duration );
            return new WP_Error( 'too_many_attempts', __( 'Too many failed login attempts. Account locked for 15 minutes.' ) );
        }
    } else {
        // Clear attempts on successful login
        delete_transient( $attempts_key );
        delete_transient( $lockout_key );
    }
    
    return $user;
}
add_filter( 'authenticate', 'nb_check_login_attempts', 30, 3 );

// Sanitize file uploads
function nb_sanitize_file_name( $filename ) {
    $filename = remove_accents( $filename );
    $filename = sanitize_file_name( $filename );
    return $filename;
}
add_filter( 'sanitize_file_name', 'nb_sanitize_file_name', 10 );

// Add Content Security Policy (basic)
function nb_add_csp_header() {
    if ( ! is_admin() ) {
        header( "Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.googletagmanager.com https://www.google-analytics.com; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:; connect-src 'self' https://www.google-analytics.com;" );
    }
}
// Uncomment when ready to implement strict CSP
// add_action( 'send_headers', 'nb_add_csp_header' );
