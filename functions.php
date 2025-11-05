<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Enqueue scripts and styles
function nb_landing_scripts() {
    // CSS
    wp_enqueue_style( 'nb-main', get_template_directory_uri() . '/assets/css/main.css', array(), '1.0.0' );

    // JavaScript files
    wp_enqueue_script( 'nb-utils', get_template_directory_uri() . '/assets/js/utils.js', array(), '1.0.0', true );
    wp_enqueue_script( 'nb-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array(), '1.0.0', true );
    wp_enqueue_script( 'nb-menu', get_template_directory_uri() . '/assets/js/menu.js', array(), '1.0.0', true );
    wp_enqueue_script( 'nb-testimonials', get_template_directory_uri() . '/assets/js/testimonials.js', array(), '1.0.0', true );

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

    $css = "
    :root {
        --nb-primary: {$primary_color};
        --nb-secondary: {$secondary_color};
        --nb-accent: {$accent_color};
        --nb-text: {$text_color};
        --nb-bg: {$bg_color};
        --nb-font-family: '{$font_family}', system-ui, sans-serif;
        --nb-radius: {$border_radius}px;
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

// Theme setup
function nb_landing_setup() {
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );

    // Register navigation menu
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'nb-landing' ),
    ) );
}
add_action( 'after_setup_theme', 'nb_landing_setup' );

// Add lazy loading to images
function nb_add_lazy_loading( $content ) {
    if ( ! is_admin() ) {
        $content = preg_replace( '/<img(.*?)src=/', '<img$1loading="lazy" src=', $content );
    }
    return $content;
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

// Security: Sanitize form inputs
function nb_sanitize_form_input( $input ) {
    return sanitize_text_field( wp_strip_all_tags( $input ) );
}

// Add security headers
function nb_add_security_headers() {
    if ( ! is_admin() ) {
        header( 'X-Content-Type-Options: nosniff' );
        header( 'X-Frame-Options: SAMEORIGIN' );
        header( 'X-XSS-Protection: 1; mode=block' );
    }
}
add_action( 'send_headers', 'nb_add_security_headers' );

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

// Add accessibility improvements
function nb_add_accessibility_attrs( $content ) {
    // Add skip links
    if ( is_front_page() ) {
        $content = '<a href="#main-content" class="sr-only focus:not-sr-only">Aller au contenu principal</a>' . $content;
    }
    return $content;
}
add_filter( 'the_content', 'nb_add_accessibility_attrs' );

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
