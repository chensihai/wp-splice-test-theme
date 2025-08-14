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

// Register Projects custom post type
function splice_test_register_project_cpt() {
    $labels = array(
        'name' => __('Projects', 'splice-test'),
        'singular_name' => __('Project', 'splice-test'),
        'menu_name' => __('Projects', 'splice-test'),
        'add_new_item' => __('Add New Project', 'splice-test'),
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
        'rewrite' => array('slug' => 'projects'),
        'menu_icon' => 'dashicons-portfolio',
    );

    register_post_type('project', $args);
}
add_action('init', 'splice_test_register_project_cpt');

// Add project meta boxes
function splice_test_add_project_meta_boxes() {
    add_meta_box(
        'project_details',
        __('Project Details', 'splice-test'),
        'splice_test_project_meta_callback',
        'project'
    );
}
add_action('add_meta_boxes', 'splice_test_add_project_meta_boxes');

// Meta box callback
function splice_test_project_meta_callback($post) {
    wp_nonce_field('splice_test_save_project_meta', 'project_meta_nonce');

    $fields = array(
        'project_name' => __('Project Name', 'splice-test'),
        'project_start_date' => __('Start Date', 'splice-test'),
        'project_end_date' => __('End Date', 'splice-test'),
        'project_url' => __('Project URL', 'splice-test'),
    );

    foreach ($fields as $key => $label) {
        $value = get_post_meta($post->ID, $key, true);
        echo '<p>';
        echo '<label for="' . esc_attr($key) . '">' . $label . '</label>';
        echo '<input type="' . ($key === 'project_url' ? 'url' : 'text') . '" ';
        echo 'id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" ';
        echo 'value="' . esc_attr($value) . '" class="widefat">';
        echo '</p>';
    }

    // Project Description (textarea)
    $description = get_post_meta($post->ID, 'project_description', true);
    echo '<p>';
    echo '<label for="project_description">' . __('Description', 'splice-test') . '</label>';
    echo '<textarea id="project_description" name="project_description" ';
    echo 'class="widefat" rows="5">' . esc_textarea($description) . '</textarea>';
    echo '</p>';
}

// Save meta data
function splice_test_save_project_meta($post_id) {
    if (!isset($_POST['project_meta_nonce']) ||
        !wp_verify_nonce($_POST['project_meta_nonce'], 'splice_test_save_project_meta')) {
        return;
    }

    $fields = array(
        'project_start_date',
        'project_end_date',
        'project_url'
    );

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta(
                $post_id,
                $field,
                sanitize_text_field($_POST[$field])
            );
        }
    }
}
add_action('save_post_project', 'splice_test_save_project_meta');

// ... remaining meta box and REST API code from previous steps ...
