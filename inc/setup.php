<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }


add_action('after_setup_theme', function(){
// Images responsives & HTML5
add_theme_support('post-thumbnails');
add_theme_support('responsive-embeds');
add_theme_support('html5', ['search-form','gallery','caption','style','script']);
// Gestion via theme.json
add_theme_support('appearance-tools');
});