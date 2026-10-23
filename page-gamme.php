<?php
/**
 * Template Name: Page Gammes (Tous les produits)
 *
 * Ce modèle affiche tous les produits qui possèdent au moins un terme dans la taxonomie 'gamme'.
 */

get_header();
?>

<div class="woocommerce container my-5">
    <header class="woocommerce-products-header">
        <h1 class="woocommerce-products-header__title page-title">
            <?php the_title(); ?>
        </h1>
        <div class="page-description">
            <?php the_content(); ?>
        </div>
    </header>

    <?php
    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : ( ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1 );

    // Requête pour récupérer les produits ayant au moins une gamme attribuée
    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 12,
        'paged'          => $paged,
        'tax_query'      => array(
            array(
                'taxonomy' => 'gamme',
                'operator' => 'EXISTS', // Récupère tous les produits associés à la taxonomie gamme
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

        <nav class="woocommerce-pagination my-4">
            <?php
            echo paginate_links( array(
                'total'     => $products_query->max_num_pages,
                'current'   => $paged,
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;',
            ) );
            ?>
        </nav>

    <?php else : ?>
        <p class="woocommerce-info"><?php esc_html_e( 'Aucun produit trouvé.', 'woocommerce' ); ?></p>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>
</div>

<?php
get_footer();