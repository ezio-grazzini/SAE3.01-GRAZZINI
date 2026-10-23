<?php
/**
 * Modèle d'affichage pour la taxonomie Gamme
 */

get_header();

// Récupérer le terme de taxonomie actuellement affiché
$current_term = get_queried_object();
?>

<div class="woocommerce container my-5">
    <header class="woocommerce-products-header">
        <h1 class="woocommerce-products-header__title page-title">
            <?php echo esc_html( $current_term->name ); ?>
        </h1>
        <?php if ( ! empty( $current_term->description ) ) : ?>
            <div class="term-description">
                <?php echo wp_kses_post( $current_term->description ); ?>
            </div>
        <?php endif; ?>
    </header>

    <?php
    // Configuration de la requête pour récupérer les produits associés
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => 12,
        'paged'          => get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1,
        'tax_query'      => array(
            array(
                'taxonomy' => 'gamme', // Slug de la taxonomie
                'field'    => 'slug',
                'terms'    => $current_term->slug,
            ),
        ),
    );

    $products_query = new WP_Query( $args );

    if ( $products_query->have_posts() ) : ?>

        <?php woocommerce_product_loop_start(); ?>

            <?php while ( $products_query->have_posts() ) : $products_query->the_post(); ?>
                <?php wc_get_template_part( 'content', 'product' ); ?>
            <?php endwhile; ?>

        <?php woocommerce_product_loop_end(); ?>

        <?php
        // Pagination WooCommerce
        echo paginate_links( array(
            'total' => $products_query->max_num_pages,
        ) );
        ?>

    <?php else : ?>
        <p class="woocommerce-info"><?php esc_html_e( 'Aucun produit trouvé dans cette gamme.', 'woocommerce' ); ?></p>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>
</div>

<?php
get_footer();