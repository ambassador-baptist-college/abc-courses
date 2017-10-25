<?php
/*
 * Plugin Name: ABC Courses
 * Plugin URI: https://github.com/ambassador-baptist-college/abc-courses/
 * Description: Course Titles and Descriptions
 * Version: 1.1.0
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
    if ( 'course' == get_post_type( $id ) && ! is_admin() && is_main_query() ) {
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

// Register searchform assets
function register_course_search() {
    wp_register_script( 'course-search', plugins_url( 'js/course-search.min.js', __FILE__ ), array( 'jquery' ), '1.1.0', true );
    wp_register_style( 'course-search', plugins_url( 'css/course-search.min.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'register_course_search' );

// Join custom fields to course search or ajax query
function include_course_code_join( $join ) {
    global $wpdb, $wp_query;

    $join .= ' LEFT JOIN ' . $wpdb->postmeta . ' ON ' . $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id';

    return $join;
}
function include_course_code_where( $where ) {
    global $pagenow, $wpdb;

    $where = preg_replace(
        "/\(\s*" . $wpdb->posts . ".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
        "(" . $wpdb->posts . ".post_title LIKE $1) OR (" . $wpdb->postmeta . ".meta_value LIKE $1)",
        $where
    );

    return $where;
}
function include_course_code_distinct( $where ) {
    global $wpdb;

    return "DISTINCT";
}
function include_course_code_if_course( $query ) {
    // use posts_join filter only if querying course CPT
    if ( array_key_exists( 'post_type', $query->query ) && 'course' === $query->query['post_type'] && ( is_search() || $query->query['s'] ) ) {

        add_filter( 'posts_join', 'include_course_code_join' );
        add_filter( 'posts_where', 'include_course_code_where' );
        add_filter( 'posts_distinct', 'include_course_code_distinct' );
    }
}
add_action( 'pre_get_posts', 'include_course_code_if_course' );

// Add shortcode
function display_all_courses( $atts ) {
    $args = shortcode_atts(
        array(),
        $atts
    );
    $shortcode_output = '<section class="courses-shortcode">';

    // set category options
    $category_options = array(
        'taxonomy'  => 'course-category',
        'echo'      => false,
        'title_li'  => '',
    );
    $course_categories = get_terms( $category_options );

    $shortcode_output .= '<h2>Categories</h2><p class="course-categories courses-container">
    <a href="' . home_url() . '/course-category/all/" class="cat-filter clear-filters" data-course-category="clear">All</a>';
    foreach ( $course_categories as $course ) {
        $shortcode_output .= '<a href="' . home_url() . '/course-category/' . $course->slug . '/" class="cat-filter ' . $course->taxonomy . '-' . $course->slug . '" data-course-category="' . $course->taxonomy . '-' . $course->slug . '">' . $course->name . '</a> ';
    }
    $shortcode_output .= '</p>
    <h2>Search</h2>
    <form class="search" name="courses" action="' . home_url( '/' ) . '">
        <input type="search" name="s" placeholder="Live search&hellip;" />
        <input type="hidden" name="post_type" value="course" />
        <input type="submit" value="Search" class="screen-reader-text" />
    </form>';

    // include script
    wp_enqueue_style( 'course-search' );
    wp_enqueue_script( 'course-search' );
    wp_localize_script( 'course-search', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

    // show main content area
    $shortcode_output .= '<section class="courses-container site-main">';

    // WP_Query arguments
    $args = array (
        'post_type'              => array( 'course' ),
        'post_status'            => array( 'publish' ),
        'posts_per_page'         => '-1',
        'orderby'                => 'meta_value',
        'order'                  => 'ASC',
        'meta_key'               => 'course_code',
        'cache_results'          => true,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => true,
    );

    // The Query
    $all_courses_query = new WP_Query( $args );

    // The Loop
    if ( $all_courses_query->have_posts() ) {
        ob_start();
        while ( $all_courses_query->have_posts() ) {
            $all_courses_query->the_post();
            get_template_part( 'template-parts/content', 'single' );
        }
        $shortcode_output .= ob_get_clean();
    }

    // Restore original Post Data
    wp_reset_postdata();
    $shortcode_output .= '</section>
    </section>';

    // return content
    return $shortcode_output;
}
add_shortcode( 'all_courses', 'display_all_courses' );
