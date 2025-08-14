<?php
/**
 * Enqueue scripts and styles for the theme
 */
function splice_test_theme_scripts() {
    // Enqueue theme styles
    wp_enqueue_style('splice-test-theme-style', get_stylesheet_uri());
    
    // Enqueue theme scripts
    wp_enqueue_script(
        'splice-test-theme-navigation',
        get_template_directory_uri() . '/js/navigation.js',
        array(),
        '1.0.0',
        true
    );

    // Localize script with nonce and translations
    wp_localize_script(
        'splice-test-theme-navigation',
        'spliceNavigation',
        array(
            'nonce' => wp_create_nonce('splice_nav_nonce'),
            'ajaxurl' => admin_url('admin-ajax.php')
        )
    );
}
add_action('wp_enqueue_scripts', 'splice_test_theme_scripts');

/**
 * Handle menu toggle AJAX action
 */
function splice_handle_menu_toggle() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'splice_nav_nonce')) {
        wp_send_json_error('Invalid security token');
    }

    // Sanitize and validate any incoming data
    $menu_id = isset($_POST['menu_id']) ? absint($_POST['menu_id']) : 0;
    $is_active = isset($_POST['is_active']) ? rest_sanitize_boolean($_POST['is_active']) : false;

    // Process the menu toggle
    $response = array(
        'success' => true,
        'menu_id' => $menu_id,
        'is_active' => $is_active
    );

    wp_send_json_success($response);
}
add_action('wp_ajax_splice_menu_toggle', 'splice_handle_menu_toggle');
add_action('wp_ajax_nopriv_splice_menu_toggle', 'splice_handle_menu_toggle');

/**
 * Sanitize menu items before display
 */
function splice_sanitize_menu_items($items, $args) {
    if (isset($args->theme_location) && $args->theme_location === 'primary') {
        foreach ($items as $item) {
            // Sanitize menu item attributes
            $item->title = wp_kses_post($item->title);
            $item->url = esc_url($item->url);
            $item->attr_title = esc_attr($item->attr_title);
            $item->description = wp_kses_post($item->description);
        }
    }
    return $items;
}
add_filter('wp_nav_menu_objects', 'splice_sanitize_menu_items', 10, 2);

/**
 * Splice Test Theme functions and definitions
 */

if (!defined('ABSPATH')) {
    exit; // Security: Prevent direct access
}

// Include necessary WordPress core files
if (!class_exists('WP_Site_Health')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-site-health.php';
}

// Register navigation menus
function splice_test_register_menus() {
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'splice-test'),
        'footer'  => __('Footer Menu', 'splice-test'),
        'projects' => __('Projects Archive', 'splice-test')
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
        'has_archive' => true, // Ensure archive functionality is enabled
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
        // 'project_name' => __('Project Name', 'splice-test'),
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
    // $description = get_post_meta($post->ID, 'project_description', true);
    // echo '<p>';
    // echo '<label for="project_description">' . __('Description', 'splice-test') . '</label>';
    // echo '<textarea id="project_description" name="project_description" ';
    // echo 'class="widefat" rows="5">' . esc_textarea($description) . '</textarea>';
    // echo '</p>';
}

// Save meta data

register_rest_route(
    'splice/v1',
    '/projects',
    array(
        'methods' => 'GET',
        'callback' => 'splice_get_projects',
        'permission_callback' => function () {
            return true;
        }
    )
);

function splice_get_projects() {
    $args = array(
        'post_type' => 'project',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    );

    $projects = get_posts($args);
    
    $project_list = array();
    
    foreach ($projects as $project) {
        $project_list[] = array(
            'title' => get_the_title($project->ID),
            'url' => get_permalink($project->ID),
            'start_date' => get_post_meta($project->ID, 'project_start_date', true),
            'end_date' => get_post_meta($project->ID, 'project_end_date', true)
        );
    }

    if (empty($project_list)) {
        return array();
    }

    return $project_list;
}
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


// function remove_post_type_title() {

//     remove_post_type_support( 'project', 'title' );
//     remove_post_type_support( 'project', 'editor' );

// }

// Debug template selection
  function splice_test_debug_template() {
      if (is_post_type_archive('project')) {
          echo '<!-- Using template: ' . get_query_template('archive-project') . ' -->';
      }
  }
  add_action('wp_head', 'splice_test_debug_template');
  
  // add_action( 'init', 'remove_post_type_title' );