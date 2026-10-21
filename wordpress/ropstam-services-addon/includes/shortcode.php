
<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Render the services calculator.
 *
 * Shortcode: [ropstam_services]
 *
 * @return string
 */
function ropstam_sac_render_services_shortcode() {
    wp_enqueue_style( 'ropstam-sac-style' );
    wp_enqueue_script( 'ropstam-sac-calculator' );

    $services_query = new WP_Query(
        array(
            'post_type'      => 'ropstam_service',
            'post_status'    => 'publish',
            'posts_per_page' => 3,
            'orderby'        => 'modified',
            'order'          => 'DESC',
            'no_found_rows'  => true,
        )
    );

    ob_start();
    ?>
    <section class="ropstam-sac" aria-labelledby="ropstam-sac-heading">
        <div class="ropstam-sac__intro">
            <span class="ropstam-sac__eyebrow">OUR SERVICES</span>
            <h2 id="ropstam-sac-heading">Professional Services, Transparent Pricing</h2>
            <p>Choose a service, customize your options, and see your estimated price instantly.</p>
        </div>

        <?php if ( $services_query->have_posts() ) : ?>
            <div class="ropstam-sac__grid">
                <?php
                while ( $services_query->have_posts() ) :
                    $services_query->the_post();

                    $service_id = get_the_ID();

                    $base_price = get_post_meta( $service_id, 'base_price', true );
                    $delivery_days = get_post_meta( $service_id, 'delivery_time_days', true );
                    $priority_enabled = '1' === (string) get_post_meta( $service_id, 'priority_enabled', true );
                    $priority_price = get_post_meta( $service_id, 'priority_price', true );
                    $priority_days_saved = get_post_meta( $service_id, 'priority_days_saved', true );

                    $base_price = is_numeric( $base_price ) ? max( 0, (float) $base_price ) : 0;
                    $delivery_days = is_numeric( $delivery_days ) ? absint( $delivery_days ) : 0;
                    $priority_price = is_numeric( $priority_price ) ? max( 0, (float) $priority_price ) : 0;
                    $priority_days_saved = is_numeric( $priority_days_saved )
                        ? min( $delivery_days, absint( $priority_days_saved ) )
                        : 0;
                    ?>
                    <article class="ropstam-sac__card"
                        data-service-title="<?php echo esc_attr( get_the_title() ); ?>"
                        data-base-price="<?php echo esc_attr( number_format( $base_price, 2, '.', '' ) ); ?>"
                        data-base-days="<?php echo esc_attr( $delivery_days ); ?>">

                        <?php if ( has_post_thumbnail() ) : ?>
                            <div class="ropstam-sac__image">
                                <?php
                                echo get_the_post_thumbnail(
                                    $service_id,
                                    'large',
                                    array( 'loading' => 'lazy' )
                                );
                                ?>
                            </div>
                        <?php endif; ?>

                        <div class="ropstam-sac__card-content">
                            <h3><?php echo esc_html( get_the_title() ); ?></h3>

                            <div class="ropstam-sac__description">
                                <?php echo wp_kses_post( wpautop( get_the_excerpt() ) ); ?>
                            </div>

                            <p class="ropstam-sac__meta">
                                Estimated delivery:
                                <strong class="ropstam-sac__delivery">
                                    <?php
                                    echo esc_html( $delivery_days );
                                    echo esc_html( 1 === $delivery_days ? ' day' : ' days' );
                                    ?>
                                </strong>
                            </p>

                            <p class="ropstam-sac__base-price">
                                Starting at
                                <strong>$<?php echo esc_html( number_format( $base_price, 2 ) ); ?></strong>
                            </p>

                            <?php if ( $priority_enabled ) : ?>
                                <fieldset class="ropstam-sac__addons">
                                    <legend>Customize your service</legend>

                                    <label class="ropstam-sac__addon">
                                        <input
                                            type="checkbox"
                                            class="ropstam-sac__addon-input"
                                            data-addon-price="<?php echo esc_attr( number_format( $priority_price, 2, '.', '' ) ); ?>"
                                            data-days-saved="<?php echo esc_attr( $priority_days_saved ); ?>"
                                        >
                                        <span>Priority Turnaround</span>
                                        <strong>
                                            +$<?php echo esc_html( number_format( $priority_price, 2 ) ); ?>
                                        </strong>
                                        <small>
                                            Saves <?php echo esc_html( $priority_days_saved ); ?>
                                            <?php echo esc_html( 1 === $priority_days_saved ? 'day' : 'days' ); ?>
                                        </small>
                                    </label>
                                </fieldset>
                            <?php endif; ?>

                            <div class="ropstam-sac__total">
                                <span>Estimated total</span>
                                <strong class="ropstam-sac__total-price" aria-live="polite" aria-atomic="true">
                                    $<?php echo esc_html( number_format( $base_price, 2 ) ); ?>
                                </strong>
                            </div>

                            <button type="button" class="ropstam-sac__button">
                                Request This Service
                            </button>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <p class="ropstam-sac__empty">
                Services are coming soon. Please check back shortly.
            </p>
        <?php endif; ?>

        <?php wp_reset_postdata(); ?>

        <div class="ropstam-sac__modal" hidden>
            <div class="ropstam-sac__modal-backdrop" data-modal-close></div>

            <section
                class="ropstam-sac__modal-dialog"
                role="dialog"
                aria-modal="true"
                aria-labelledby="ropstam-sac-modal-title"
                aria-describedby="ropstam-sac-modal-description"
                tabindex="-1"
            >
                <button type="button" class="ropstam-sac__modal-close"
                    aria-label="Close dialog" data-modal-close>
                    &times;
                </button>

                <span class="ropstam-sac__modal-icon" aria-hidden="true">i</span>
                <h2 id="ropstam-sac-modal-title">Demo Request</h2>

                <p id="ropstam-sac-modal-description">
                    This is a demonstration. No service request or payment will be submitted.
                </p>

                <div class="ropstam-sac__modal-summary">
                    <span>Selected service</span>
                    <strong class="ropstam-sac__modal-service"></strong>

                    <span>Estimated total</span>
                    <strong class="ropstam-sac__modal-price"></strong>

                    <span>Estimated delivery</span>
                    <strong class="ropstam-sac__modal-delivery"></strong>

                    <span>Priority turnaround</span>
                    <strong class="ropstam-sac__modal-priority"></strong>
                </div>

                <button type="button" class="ropstam-sac__button ropstam-sac__modal-ok">
                    Got it
                </button>
            </section>
        </div>
    </section>
    <?php

    return ob_get_clean();
}
add_shortcode( 'ropstam_services', 'ropstam_sac_render_services_shortcode' );
