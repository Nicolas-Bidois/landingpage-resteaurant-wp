<?php
/**
 * Accessibility improvements for NB Landing theme
 */

// Add skip links for keyboard navigation
function nb_add_skip_links() {
    ?>
    <a href="#main-content" class="skip-link screen-reader-text">
        <?php esc_html_e( 'Skip to main content', 'nb-landing' ); ?>
    </a>
    <a href="#primary-navigation" class="skip-link screen-reader-text">
        <?php esc_html_e( 'Skip to navigation', 'nb-landing' ); ?>
    </a>
    <?php
}
add_action( 'wp_body_open', 'nb_add_skip_links' );

// Add ARIA labels to navigation
function nb_add_nav_aria_labels( $args ) {
    if ( 'primary' === $args['theme_location'] ) {
        $args['container_aria_label'] = __( 'Primary Navigation', 'nb-landing' );
    }
    return $args;
}
add_filter( 'wp_nav_menu_args', 'nb_add_nav_aria_labels' );

// Ensure images have alt text
function nb_check_image_alt( $attr, $attachment ) {
    if ( empty( $attr['alt'] ) ) {
        $attr['alt'] = get_the_title( $attachment->ID );
    }
    return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'nb_check_image_alt', 10, 2 );

// Add ARIA attributes to search form
function nb_search_form_aria( $form ) {
    $form = str_replace( '<form', '<form role="search" aria-label="' . esc_attr__( 'Search', 'nb-landing' ) . '"', $form );
    $form = str_replace( 'type="search"', 'type="search" aria-label="' . esc_attr__( 'Search input', 'nb-landing' ) . '"', $form );
    return $form;
}
add_filter( 'get_search_form', 'nb_search_form_aria' );

// Add focus styles CSS
function nb_add_accessibility_styles() {
    $css = "
    /* Skip links */
    .skip-link.screen-reader-text {
        position: absolute;
        left: -9999px;
        top: 0;
        z-index: 999999;
        padding: 1em;
        background: var(--nb-primary, #F59E0B);
        color: #fff;
        text-decoration: none;
    }
    
    .skip-link.screen-reader-text:focus {
        left: 0;
        clip: auto;
        height: auto;
        width: auto;
        display: block;
    }
    
    /* Focus styles */
    a:focus,
    button:focus,
    input:focus,
    textarea:focus,
    select:focus {
        outline: 2px solid var(--nb-primary, #F59E0B);
        outline-offset: 2px;
    }
    
    /* Screen reader only text */
    .screen-reader-text {
        clip: rect(1px, 1px, 1px, 1px);
        position: absolute !important;
        height: 1px;
        width: 1px;
        overflow: hidden;
        word-wrap: normal !important;
    }
    
    .screen-reader-text:focus {
        background-color: #f1f1f1;
        border-radius: 3px;
        box-shadow: 0 0 2px 2px rgba(0, 0, 0, 0.6);
        clip: auto !important;
        color: #21759b;
        display: block;
        font-size: 14px;
        font-weight: bold;
        height: auto;
        left: 5px;
        line-height: normal;
        padding: 15px 23px 14px;
        text-decoration: none;
        top: 5px;
        width: auto;
        z-index: 100000;
    }
    
    /* High contrast mode support */
    @media (prefers-contrast: high) {
        :root {
            --nb-primary: #0066cc;
            --nb-text: #000000;
            --nb-bg: #ffffff;
        }
    }
    
    /* Reduced motion support */
    @media (prefers-reduced-motion: reduce) {
        *,
        *::before,
        *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
            scroll-behavior: auto !important;
        }
    }
    
    /* Focus visible for keyboard navigation */
    :focus:not(:focus-visible) {
        outline: none;
    }
    
    :focus-visible {
        outline: 2px solid var(--nb-primary, #F59E0B);
        outline-offset: 2px;
    }
    ";
    
    wp_add_inline_style( 'nb-main', $css );
}
add_action( 'wp_enqueue_scripts', 'nb_add_accessibility_styles', 20 );

// Add language attribute to HTML tag
function nb_add_language_attributes( $output ) {
    return $output . ' lang="' . esc_attr( get_bloginfo( 'language' ) ) . '"';
}
add_filter( 'language_attributes', 'nb_add_language_attributes' );

// Ensure proper heading hierarchy
function nb_check_heading_hierarchy( $content ) {
    // This is a placeholder for more complex heading hierarchy checking
    // In production, you might want to use a more sophisticated approach
    return $content;
}
add_filter( 'the_content', 'nb_check_heading_hierarchy', 20 );

// Add ARIA live regions for dynamic content
function nb_add_aria_live_regions() {
    ?>
    <div id="nb-announcements" class="screen-reader-text" aria-live="polite" aria-atomic="true"></div>
    <?php
}
add_action( 'wp_footer', 'nb_add_aria_live_regions' );

// Custom excerpt length with proper ending
function nb_custom_excerpt_length( $length ) {
    return 20;
}
add_filter( 'excerpt_length', 'nb_custom_excerpt_length', 999 );

// Custom excerpt more with accessibility
function nb_custom_excerpt_more( $more ) {
    if ( is_admin() ) {
        return $more;
    }
    
    global $post;
    return '... <a href="' . esc_url( get_permalink( $post->ID ) ) . '" class="read-more" aria-label="' . 
           sprintf( esc_attr__( 'Continue reading %s', 'nb-landing' ), get_the_title( $post->ID ) ) . '">' . 
           esc_html__( 'Read more', 'nb-landing' ) . '</a>';
}
add_filter( 'excerpt_more', 'nb_custom_excerpt_more' );

// Add role attributes to main content areas
function nb_add_content_roles( $content ) {
    if ( is_main_query() && in_the_loop() ) {
        return '<article role="article" id="post-' . get_the_ID() . '" class="' . implode( ' ', get_post_class() ) . '">' . 
               $content . '</article>';
    }
    return $content;
}
// Note: This filter might need adjustment based on theme structure
// add_filter( 'the_content', 'nb_add_content_roles', 5 );
