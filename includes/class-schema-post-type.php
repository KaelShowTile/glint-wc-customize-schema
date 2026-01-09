<?php
/**
 * Schema Post Type Class
 * Registers the custom post type for schema settings
 */

class Schema_Post_Type {

    //Initialize the class
    public static function init() {
        add_action( 'init', array( __CLASS__, 'register_post_type' ) );
    }

    //Register the schema_setting post type
    public static function register_post_type() {
        $labels = array(
            'name'               => __( 'Schema Settings', 'glint-schema' ),
            'singular_name'      => __( 'Schema Setting', 'glint-schema' ),
            'menu_name'          => __( 'Schema Settings', 'glint-schema' ),
            'name_admin_bar'     => __( 'Schema Setting', 'glint-schema' ),
            'add_new'            => __( 'Add New', 'glint-schema' ),
            'add_new_item'       => __( 'Add New Schema Setting', 'glint-schema' ),
            'new_item'           => __( 'New Schema Setting', 'glint-schema' ),
            'edit_item'          => __( 'Edit Schema Setting', 'glint-schema' ),
            'view_item'          => __( 'View Schema Setting', 'glint-schema' ),
            'all_items'          => __( 'All Schema Settings', 'glint-schema' ),
            'search_items'       => __( 'Search Schema Settings', 'glint-schema' ),
            'parent_item_colon'  => __( 'Parent Schema Settings:', 'glint-schema' ),
            'not_found'          => __( 'No schema settings found.', 'glint-schema' ),
            'not_found_in_trash' => __( 'No schema settings found in Trash.', 'glint-schema' ),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => false,
            'rewrite'            => false,
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => null,
            'menu_icon'          => 'dashicons-admin-generic',
            'supports'           => array( 'title' ),
        );

        register_post_type( 'schema_setting', $args );
    }
}
