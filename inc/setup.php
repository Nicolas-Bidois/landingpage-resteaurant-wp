<?php
/**
 * Theme setup and configuration
 */

if ( ! defined( 'ABSPATH' ) ) { 
    exit; 
}

/**
 * Theme setup function
 */
function nb_landing_setup() {
    // Add theme support for various features
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'html5', array( 
        'search-form', 
        'comment-form', 
        'comment-list', 
        'gallery', 
        'caption', 
        'style', 
        'script' 
    ) );
    
    // Appearance tools for theme.json
    add_theme_support( 'appearance-tools' );
    
    // Add support for custom logo
    add_theme_support( 'custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ) );

    // Register navigation menus
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'nb-landing' ),
        'footer'  => __( 'Footer Menu', 'nb-landing' ),
    ) );
    
    // Add support for editor styles
    add_theme_support( 'editor-styles' );
    
    // Add support for wide and full alignment
    add_theme_support( 'align-wide' );
}
add_action( 'after_setup_theme', 'nb_landing_setup' );

/**
 * Set content width
 */
if ( ! isset( $content_width ) ) {
    $content_width = 1200;
}
