<?php
/**
 * Schema Admin Class
 *
 * Handles admin functionality including page schema template setting
 */

class Schema_Admin {

    /**
     * Initialize the class
     */
    public static function init() {
        add_action( 'add_meta_boxes', array( __CLASS__, 'add_page_meta_box' ) );
        add_action( 'save_post', array( __CLASS__, 'save_page_meta_box' ) );
    }

    /**
     * Add meta box to page edit screen
     */
    public static function add_page_meta_box() {
        add_meta_box(
            'page_schema_template',
            __( 'Schema Template', 'glint-schema' ),
            array( __CLASS__, 'render_page_meta_box' ),
            'page',
            'side',
            'default'
        );
    }

    /**
     * Render page meta box
     */
    public static function render_page_meta_box( $post ) {
        wp_nonce_field( 'page_schema_template_nonce', 'page_schema_template_nonce' );

        $selected_template = get_post_meta( $post->ID, '_page_schema_template', true );

        // Get all schema settings for pages
        $page_schemas = get_posts( array(
            'post_type' => 'schema_setting',
            'meta_query' => array(
                array(
                    'key' => '_schema_page_type',
                    'value' => 'page',
                    'compare' => '='
                )
            ),
            'posts_per_page' => -1
        ) );

        echo '<select name="page_schema_template" class="widefat">';
        echo '<option value="">' . __( 'Select Schema Template', 'glint-schema' ) . '</option>';
        foreach ( $page_schemas as $schema ) {
            echo '<option value="' . esc_attr( $schema->ID ) . '" ' . selected( $selected_template, $schema->ID, false ) . '>' . esc_html( $schema->post_title ) . '</option>';
        }
        echo '</select>';
    }

    /**
     * Save page meta box
     */
    public static function save_page_meta_box( $post_id ) {
        if ( ! isset( $_POST['page_schema_template_nonce'] ) || ! wp_verify_nonce( $_POST['page_schema_template_nonce'], 'page_schema_template_nonce' ) ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( 'page' !== $_POST['post_type'] ) {
            return;
        }

        if ( isset( $_POST['page_schema_template'] ) ) {
            update_post_meta( $post_id, '_page_schema_template', sanitize_text_field( $_POST['page_schema_template'] ) );
        } else {
            delete_post_meta( $post_id, '_page_schema_template' );
        }
    }
}
