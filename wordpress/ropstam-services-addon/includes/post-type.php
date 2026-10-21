
<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register the Services content type.
 */
function ropstam_sac_register_service_post_type() {
    $labels = array(
        'name'                  => __( 'Services', 'ropstam-services-addon' ),
        'singular_name'         => __( 'Service', 'ropstam-services-addon' ),
        'menu_name'             => __( 'Services', 'ropstam-services-addon' ),
        'add_new'               => __( 'Add New', 'ropstam-services-addon' ),
        'add_new_item'          => __( 'Add New Service', 'ropstam-services-addon' ),
        'edit_item'             => __( 'Edit Service', 'ropstam-services-addon' ),
        'new_item'              => __( 'New Service', 'ropstam-services-addon' ),
        'view_item'             => __( 'View Service', 'ropstam-services-addon' ),
        'search_items'          => __( 'Search Services', 'ropstam-services-addon' ),
        'not_found'             => __( 'No services found.', 'ropstam-services-addon' ),
        'not_found_in_trash'    => __( 'No services found in Trash.', 'ropstam-services-addon' ),
    );

    register_post_type(
        'ropstam_service',
        array(
            'labels'          => $labels,
            'public'          => true,
            'show_in_rest'    => true,
            'menu_icon'       => 'dashicons-admin-tools',
            'has_archive'     => false,
            'rewrite'         => array(
                'slug' => 'services',
            ),
            'supports'        => array( 'title', 'editor', 'thumbnail', 'revisions' ),
        )
    );
}
add_action( 'init', 'ropstam_sac_register_service_post_type' );
