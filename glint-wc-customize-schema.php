<?php
/**
 * Plugin Name: CHT Customize Schema
 * Description: Customize schema format
 * Version: 1.0.0
 * Author: Kael
 */

defined( 'ABSPATH' ) || exit;

// Define plugin constants
define( 'GLINT_SCHEMA_VERSION', '1.0.0' );
define( 'GLINT_SCHEMA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GLINT_SCHEMA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include required files
require_once GLINT_SCHEMA_PLUGIN_DIR . 'includes/class-schema-post-type.php';
require_once GLINT_SCHEMA_PLUGIN_DIR . 'includes/class-schema-meta-boxes.php';
require_once GLINT_SCHEMA_PLUGIN_DIR . 'includes/class-schema-admin.php';
require_once GLINT_SCHEMA_PLUGIN_DIR . 'includes/class-schema-frontend.php';

// Initialize the plugin
function glint_schema_init() {
    Schema_Post_Type::init();
    Schema_Meta_Boxes::init();
    Schema_Admin::init();
    Schema_Frontend::init();
}
add_action( 'plugins_loaded', 'glint_schema_init' );

// Enqueue admin scripts and styles
function glint_schema_admin_enqueue_scripts( $hook ) {
    if ( 'post.php' === $hook || 'post-new.php' === $hook ) {
        global $post;
        if ( 'schema_setting' === $post->post_type ) {
            wp_enqueue_script( 'glint-schema-admin-js', GLINT_SCHEMA_PLUGIN_URL . 'assets/js/admin-script.js', array( 'jquery' ), GLINT_SCHEMA_VERSION, true );
            wp_enqueue_style( 'glint-schema-admin-css', GLINT_SCHEMA_PLUGIN_URL . 'assets/css/admin-style.css', array(), GLINT_SCHEMA_VERSION );
        }
    }
    if ( 'page' === $hook ) {
        wp_enqueue_script( 'glint-schema-page-js', GLINT_SCHEMA_PLUGIN_URL . 'assets/js/admin-script.js', array( 'jquery' ), GLINT_SCHEMA_VERSION, true );
    }
}
add_action( 'admin_enqueue_scripts', 'glint_schema_admin_enqueue_scripts' );
