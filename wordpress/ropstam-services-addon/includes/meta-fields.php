
<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register service metadata.
 */
function ropstam_sac_register_meta() {
    $fields = array(
        'base_price' => array(
            'type'              => 'number',
            'sanitize_callback' => 'ropstam_sac_sanitize_price',
        ),
        'delivery_time_days' => array(
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
        ),
        'priority_enabled' => array(
            'type'              => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
        ),
        'priority_price' => array(
            'type'              => 'number',
            'sanitize_callback' => 'ropstam_sac_sanitize_price',
        ),
        'priority_days_saved' => array(
            'type'              => 'integer',
            'sanitize_callback' => 'absint',
        ),
    );

    foreach ( $fields as $key => $settings ) {
        register_post_meta(
            'ropstam_service',
            $key,
            array(
                'type'              => $settings['type'],
                'single'            => true,
                'show_in_rest'      => true,
                'sanitize_callback' => $settings['sanitize_callback'],
                'auth_callback'     => function( $allowed, $meta_key, $post_id ) {
                    return current_user_can( 'edit_post', $post_id );
                },
            )
        );
    }
}
add_action( 'init', 'ropstam_sac_register_meta' );

/**
 * Sanitize a non-negative price.
 *
 * @param mixed $value Submitted price.
 * @return float
 */
function ropstam_sac_sanitize_price( $value ) {
    if ( ! is_numeric( $value ) ) {
        return 0;
    }

    return max( 0, round( (float) $value, 2 ) );
}

/**
 * Add the service settings meta box.
 */
function ropstam_sac_add_meta_box() {
    add_meta_box(
        'ropstam-sac-service-details',
        __( 'Service Pricing & Priority Turnaround', 'ropstam-services-addon' ),
        'ropstam_sac_render_meta_box',
        'ropstam_service',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes_ropstam_service', 'ropstam_sac_add_meta_box' );

/**
 * Render service settings.
 *
 * @param WP_Post $post Current service.
 */
function ropstam_sac_render_meta_box( $post ) {
    wp_nonce_field( 'ropstam_sac_save_service', 'ropstam_sac_nonce' );

    $base_price = get_post_meta( $post->ID, 'base_price', true );
    $delivery_days = get_post_meta( $post->ID, 'delivery_time_days', true );
    $priority_enabled = get_post_meta( $post->ID, 'priority_enabled', true );
    $priority_price = get_post_meta( $post->ID, 'priority_price', true );
    $priority_days_saved = get_post_meta( $post->ID, 'priority_days_saved', true );
    ?>
    <p>
        <label for="ropstam_sac_base_price">
            <strong><?php esc_html_e( 'Base Price ($)', 'ropstam-services-addon' ); ?></strong>
        </label>
        <input type="number" id="ropstam_sac_base_price"
            name="ropstam_sac_base_price" min="0" step="0.01"
            value="<?php echo esc_attr( $base_price ); ?>" class="widefat">
    </p>

    <p>
        <label for="ropstam_sac_delivery_days">
            <strong><?php esc_html_e( 'Standard Delivery (days)', 'ropstam-services-addon' ); ?></strong>
        </label>
        <input type="number" id="ropstam_sac_delivery_days"
            name="ropstam_sac_delivery_days" min="0" step="1"
            value="<?php echo esc_attr( $delivery_days ); ?>" class="widefat">
    </p>

    <hr>

    <p>
        <label>
            <input type="checkbox" name="ropstam_sac_priority_enabled"
                value="1" <?php checked( (bool) $priority_enabled ); ?>>
            <strong><?php esc_html_e( 'Enable Priority Turnaround', 'ropstam-services-addon' ); ?></strong>
        </label>
    </p>

    <p>
        <label for="ropstam_sac_priority_price">
            <?php esc_html_e( 'Priority Add-on Price ($)', 'ropstam-services-addon' ); ?>
        </label>
        <input type="number" id="ropstam_sac_priority_price"
            name="ropstam_sac_priority_price" min="0" step="0.01"
            value="<?php echo esc_attr( $priority_price ); ?>" class="widefat">
    </p>

    <p>
        <label for="ropstam_sac_priority_days_saved">
            <?php esc_html_e( 'Days Saved With Priority', 'ropstam-services-addon' ); ?>
        </label>
        <input type="number" id="ropstam_sac_priority_days_saved"
            name="ropstam_sac_priority_days_saved" min="0" step="1"
            value="<?php echo esc_attr( $priority_days_saved ); ?>" class="widefat">
        <span class="description">
            <?php esc_html_e( 'The estimated delivery time will decrease by this number of days, but never below zero.', 'ropstam-services-addon' ); ?>
        </span>
    </p>
    <?php
}

/**
 * Save service metadata securely.
 *
 * @param int $post_id Service ID.
 */
function ropstam_sac_save_service_meta( $post_id ) {
    if (
        ! isset( $_POST['ropstam_sac_nonce'] ) ||
        ! wp_verify_nonce(
            sanitize_text_field( wp_unslash( $_POST['ropstam_sac_nonce'] ) ),
            'ropstam_sac_save_service'
        )
    ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( wp_is_post_revision( $post_id ) ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $price_fields = array(
        'ropstam_sac_base_price'     => 'base_price',
        'ropstam_sac_priority_price' => 'priority_price',
    );

    foreach ( $price_fields as $input => $meta_key ) {
        if ( ! isset( $_POST[ $input ] ) ) {
            continue;
        }

        $value = sanitize_text_field( wp_unslash( $_POST[ $input ] ) );

        if ( '' !== $value && is_numeric( $value ) && (float) $value >= 0 ) {
            update_post_meta( $post_id, $meta_key, round( (float) $value, 2 ) );
        } else {
            delete_post_meta( $post_id, $meta_key );
        }
    }

    $day_fields = array(
        'ropstam_sac_delivery_days'       => 'delivery_time_days',
        'ropstam_sac_priority_days_saved' => 'priority_days_saved',
    );

    foreach ( $day_fields as $input => $meta_key ) {
        if ( ! isset( $_POST[ $input ] ) ) {
            continue;
        }

        $value = sanitize_text_field( wp_unslash( $_POST[ $input ] ) );

        if ( '' !== $value && ctype_digit( $value ) ) {
            update_post_meta( $post_id, $meta_key, absint( $value ) );
        } else {
            delete_post_meta( $post_id, $meta_key );
        }
    }

    $priority_enabled = isset( $_POST['ropstam_sac_priority_enabled'] )
        && '1' === $_POST['ropstam_sac_priority_enabled'];

    update_post_meta(
        $post_id,
        'priority_enabled',
        $priority_enabled ? '1' : '0'
    );
}
add_action( 'save_post_ropstam_service', 'ropstam_sac_save_service_meta' );
