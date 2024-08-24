<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}


// Enqueue parent theme styles
function astra_child_enqueue_styles()
{
    wp_enqueue_style('astra-parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('astra-child-style', get_stylesheet_directory_uri() . '/style.css', array('astra-parent-style'), wp_get_theme()->get('Version'));
}

add_action('wp_enqueue_scripts', 'astra_child_enqueue_styles');


remove_action( 'es_after_listings', 'es_powered_by' );
remove_action( 'es_after_single_content', 'es_powered_by' );
remove_action( 'es_after_authentication', 'es_powered_by' );
remove_action( 'es_after_profile', 'es_powered_by' );
