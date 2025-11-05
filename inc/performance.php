<?php
/**
 * Performance optimizations for NB Landing theme
 */

// Enqueue scripts and styles with performance optimizations
function nb_landing_scripts() {
    // Check if minified versions exist and we're not in development
    $is_production = ! defined( 'WP_DEBUG' ) || ! WP_DEBUG;
    $css_file = $is_production && file_exists( get_template_directory() . '/assets/css/main.min.css' )
        ? 'main.min.css' : 'main.css';
    $js_file = $is_production && file_exists( get_template_directory() . '/assets/js/main.min.js' )
        ? 'main.min.js' : 'main.js';

    // CSS with versioning for cache busting
    $css_version = $is_production && file_exists( get_template_directory() . '/assets/css/' . $css_file )
        ? filemtime( get_template_directory() . '/assets/css/' . $css_file ) : '1.0.0';
    wp_enqueue_style( 'nb-main', get_template_directory_uri() . '/assets/css/' . $css_file, array(), $css_version );

    // JavaScript files with versioning
    $js_version = $is_production && file_exists( get_template_directory() . '/assets/js/' . $js_file )
        ? filemtime( get_template_directory() . '/assets/js/' . $js_file ) : '1.0.0';
    wp_enqueue_script( 'nb-main', get_template_directory_uri() . '/assets/js/' . $js_file, array(), $js_version, true );

    // Localize script for AJAX
    wp_localize_script( 'nb-menu', 'nb_ajax', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'nb_menu_nonce' ),
    ) );

    // Inject custom CSS variables from plugin settings
    $custom_css = nb_generate_custom_css();
    wp_add_inline_style( 'nb-main', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'nb_landing_scripts' );

// Generate custom CSS from plugin settings
function nb_generate_custom_css() {
    $primary_color = get_option('nbcore_primary_color', '#F59E0B');
    $secondary_color = get_option('nbcore_secondary_color', '#EF4444');
    $accent_color = get_option('nbcore_accent_color', '#E94E1B');
    $text_color = get_option('nbcore_text_color', '#F7F8FA');
    $bg_color = get_option('nbcore_bg_color', '#0B1220');
    $font_family = get_option('nbcore_font_family', 'Inter');
    $border_radius = get_option('nbcore_border_radius', '16');
    $logo_height = get_option('nbcore_logo_height', '40');

    $css = "
    :root {
        --nb-primary: {$primary_color};
        --nb-secondary: {$secondary_color};
        --nb-accent: {$accent_color};
        --nb-text: {$text_color};
        --nb-bg: {$bg_color};
        --nb-font-family: '{$font_family}', system-ui, sans-serif;
        --nb-radius: {$border_radius}px;
        --nb-logo-height: {$logo_height}px;
    }

    body {
        background-color: var(--nb-bg);
        color: var(--nb-text);
        font-family: var(--nb-font-family);
    }

    .nb-btn, .btn {
        background: var(--nb-primary);
    }

    .nb-tab.is-active {
        background: var(--nb-accent);
    }

    a {
        color: var(--nb-primary);
    }

    .nb-menu a:hover {
        color: var(--nb-primary);
    }

    .nb-card, .nb-resa, .reservation-form {
        border-radius: var(--nb-radius);
    }

    .nb-grad {
        background: linear-gradient(135deg, var(--nb-primary), var(--nb-secondary));
    }
    ";

    return $css;
}

// Lazy loading disabled temporarily to troubleshoot memory issues
// Will be re-enabled once the root cause is identified
// WordPress 5.5+ has native lazy loading support anyway

// Add support for more image sizes
function nb_add_image_sizes() {
    // Optimize image sizes for performance
    add_image_size( 'nb-hero', 1920, 1080, true );
    add_image_size( 'nb-gallery', 800, 600, true );
    add_image_size( 'nb-thumbnail', 400, 300, true );
}
add_action( 'after_setup_theme', 'nb_add_image_sizes' );

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
        $css_file = file_exists( get_template_directory() . '/assets/css/main.min.css' ) ? 'main.min.css' : 'main.css';
        $js_file = file_exists( get_template_directory() . '/assets/js/main.min.js' ) ? 'main.min.js' : 'main.js';

        header( 'Link: <' . get_template_directory_uri() . '/assets/css/' . $css_file . '>; rel=preload; as=style', false );
        header( 'Link: <' . get_template_directory_uri() . '/assets/js/' . $js_file . '>; rel=preload; as=script', false );
    }
}
add_action( 'send_headers', 'nb_add_preload_headers' );
