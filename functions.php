<?php
if ( ! defined('ABSPATH') ) { exit; }

add_action('wp_enqueue_scripts', function(){
  $v = wp_get_theme()->get('Version');
  wp_enqueue_style('nb-main', get_template_directory_uri().'/assets/css/main.css', [], $v);

  // Enqueue split JavaScript files for better performance
  wp_enqueue_script('nb-utils', get_template_directory_uri().'/assets/js/utils.js', [], $v, true);
  wp_enqueue_script('nb-navigation', get_template_directory_uri().'/assets/js/navigation.js', [], $v, true);
  wp_enqueue_script('nb-menu', get_template_directory_uri().'/assets/js/menu.js', [], $v, true);
  wp_enqueue_script('nb-testimonials', get_template_directory_uri().'/assets/js/testimonials.js', [], $v, true);
});

register_nav_menus(['primary' => __('Menu principal','nb-landing')]);

add_action('after_setup_theme', function(){
  add_theme_support('post-thumbnails');
  add_theme_support('html5', ['search-form','gallery','caption']);
});

// Emplacement du menu principal
add_action('after_setup_theme', function () {
  register_nav_menus([
    'primary' => 'Navigation principale (landing)',
  ]);
});
