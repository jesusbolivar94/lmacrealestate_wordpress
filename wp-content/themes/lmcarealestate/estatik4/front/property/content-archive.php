<?php $target_blank = ! empty( $target_blank ) ? $target_blank : '';

if ( empty( $ignore_wrapper ) ) : ?>
    <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php endif; ?>
    <div class="js-es-listing es-listing es-listing--<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>">
        <?php es_load_template( 'front/property/content-archive-image.php', array(
            'target_blank' => $target_blank,
            'wishlist_confirm' => ! empty( $wishlist_confirm ) ? $wishlist_confirm : null,
        ) ); ?>
        <div class="es-listing__content">
            <div class="es-listing__content__inner">
                <div class="es-listing__content__left">
                    <?php es_the_title( '<h3 class="es-listing__title">
                        <a href="' . es_get_the_permalink() . '" ' . $target_blank . '>', '</a></h3>' ); ?>
                    <div class='es-badges es-listing--hide-on-list'>
                        <?php es_the_field( 'referencia', '<div class="es-referencia">Referência: ', '</div>' ); ?>
                        <?php es_the_price('<div class="es-price">', '</div>'); ?>
                    </div>
                    <?php do_action( 'es_property_meta', array( 'use_icons' => true ) ); ?>
                </div>
                <a class="es-listing-link" <?php echo $target_blank; ?>
                   href="<?php echo es_get_the_permalink(); ?>">
                    VER MAIS
                </a>
            </div>
        </div>
    </div>
<?php if ( empty( $ignore_wrapper ) ) : ?>
    </div>
<?php endif;
