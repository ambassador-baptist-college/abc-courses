<?php
/*
 * Plugin Name: ABC Courses
 * Plugin URI: https://github.com/ambassador-baptist-college/abc-courses/
 * Description: Course Titles and Descriptions
 * Version: 1.0.0
 * Author: AndrewRMinion Design
 * Author URI: https://andrewrminion.com
 * GitHub Plugin URI: https://github.com/ambassador-baptist-college/abc-courses/
 */

if (!defined('ABSPATH')) {
    exit;
}

// Register Custom Post Type
function course_post_type() {

    $labels = array(
        'name'                  => 'Courses',
        'singular_name'         => 'Course',
        'menu_name'             => 'Courses',
        'name_admin_bar'        => 'Course',
        'archives'              => 'Course Archives',
        'parent_item_colon'     => 'Parent Course:',
        'all_items'             => 'All Courses',
        'add_new_item'          => 'Add New Course',
        'add_new'               => 'Add New',
        'new_item'              => 'New Course',
        'edit_item'             => 'Edit Course',
        'update_item'           => 'Update Course',
        'view_item'             => 'View Course',
        'search_items'          => 'Search Course',
        'not_found'             => 'Not found',
        'not_found_in_trash'    => 'Not found in Trash',
        'featured_image'        => 'Featured Image',
        'set_featured_image'    => 'Set featured image',
        'remove_featured_image' => 'Remove featured image',
        'use_featured_image'    => 'Use as featured image',
        'insert_into_item'      => 'Insert into course',
        'uploaded_to_this_item' => 'Uploaded to this course',
        'items_list'            => 'Courses list',
        'items_list_navigation' => 'Courses list navigation',
        'filter_items_list'     => 'Filter courses list',
    );
    $rewrite = array(
        'slug'                  => 'academics/courses/all',
        'with_front'            => true,
        'pages'                 => true,
        'feeds'                 => true,
    );
    $args = array(
        'label'                 => 'Course',
        'description'           => 'Courses',
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes', ),
        'taxonomies'            => array( 'course-category' ),
        'hierarchical'          => true,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 20,
        'menu_icon'             => 'dashicons-welcome-learn-more',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => 'academics/courses/all',
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'rewrite'               => $rewrite,
        'capability_type'       => 'page',
    );
    register_post_type( 'course', $args );

}
add_action( 'init', 'course_post_type', 0 );

// Register Custom Taxonomy
function course_categories() {

    $labels = array(
        'name'                       => 'Course Categories',
        'singular_name'              => 'Course Category',
        'menu_name'                  => 'Course Category',
        'all_items'                  => 'All Course Categories',
        'parent_item'                => 'Parent Course Category',
        'parent_item_colon'          => 'Parent Course Category:',
        'new_item_name'              => 'New Course Category Name',
        'add_new_item'               => 'Add New Course Category',
        'edit_item'                  => 'Edit Course Category',
        'update_item'                => 'Update Course Category',
        'view_item'                  => 'View Course Category',
        'separate_items_with_commas' => 'Separate course categories with commas',
        'add_or_remove_items'        => 'Add or remove course categories',
        'choose_from_most_used'      => 'Choose from the most used',
        'popular_items'              => 'Popular Course Categories',
        'search_items'               => 'Search Course Categories',
        'not_found'                  => 'Not Found',
        'no_terms'                   => 'No course categories',
        'items_list'                 => 'Course Categories list',
        'items_list_navigation'      => 'Course Categories list navigation',
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
    );
    register_taxonomy( 'course-category', array( 'course' ), $args );

}
add_action( 'init', 'course_categories', 0 );

// Use the same slug for post type and taxonomy
function generate_course_taxonomy_rewrite_rules( $wp_rewrite ) {
    $rules = array();
    $post_types = get_post_types( array( 'public' => true, '_builtin' => false ), 'objects' );
    $taxonomies = get_taxonomies( array( 'public' => true, '_builtin' => false ), 'objects' );

    foreach ( $post_types as $post_type ) {
        $post_type_name = $post_type->name; // 'developer'
        $post_type_slug = $post_type->rewrite['slug']; // 'developers'

        foreach ( $taxonomies as $taxonomy ) {
            if ( $taxonomy->object_type[0] == $post_type_name ) {
                $terms = get_categories( array( 'type' => $post_type_name, 'taxonomy' => $taxonomy->name, 'hide_empty' => 0 ) );
                foreach ( $terms as $term ) {
                    $rules[$post_type_slug . '/' . $term->slug . '/?$'] = 'index.php?' . $term->taxonomy . '=' . $term->slug;
                }
            }
        }
    }
    $wp_rewrite->rules = $rules + $wp_rewrite->rules;
}
add_action('generate_rewrite_rules', 'generate_course_taxonomy_rewrite_rules');

// Modify the page title
function filter_course_page_title( $title, $id = NULL ) {
    if ( is_post_type_archive( 'course' ) ) {
          $title = 'Courses';
    }

    return $title;
}
add_filter( 'custom_title', 'filter_course_page_title' );
add_filter( 'get_the_archive_title', 'filter_course_page_title' );

// Add course code to title
function show_course_code( $title, $id ) {
    if ( 'course' == get_post_type( $id ) && is_main_query() && ! is_admin() ) {
        $title = '<span class="course-code">' . get_field( 'course_code' ) . ':</span> ' . $title;

        if ( get_field( 'credit_hours' ) ) {
            $title .= ' <span class="credit-hours">' . get_field( 'credit_hours' ) . ' credit hour';
            if ( '1' != get_field( 'credit_hours' ) ) {
                $title .= 's';
            }
            $title .= '</span>';
        }
    }

    return $title;
}
add_filter( 'the_title', 'show_course_code', 10, 2 );

// Sort archive
function sort_courses_by_codes( $query ) {
    if ( is_post_type_archive( 'course' ) && ! is_admin() ) {
        $query->set( 'orderby',     'meta_value' );
        $query->set( 'order',       'ASC' );
        $query->set( 'meta_key',    'course_code' );
    }

    return $query;
}
add_filter( 'pre_get_posts', 'sort_courses_by_codes' );
