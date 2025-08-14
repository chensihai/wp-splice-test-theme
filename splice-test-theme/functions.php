<?php
/**
 * Splice Test Theme functions and definitions
 */

if (!defined('ABSPATH')) {
    exit; // Security: Prevent direct access
}


// Register navigation menus
function splice_test_register_menus() {
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'splice-test'),
        'footer'  => __('Footer Menu', 'splice-test')
    ));
}
add_action('init', 'splice_test_register_menus');

// Theme setup
function splice_test_theme_setup() {
    load_theme_textdomain('splice-test', get_template_directory() . '/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption'
    ));
    add_theme_support('custom-logo');
}
add_action('after_setup_theme', 'splice_test_theme_setup');

// ... remaining meta box and REST API code from previous steps ...
