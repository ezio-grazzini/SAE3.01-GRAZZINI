<?php

// Séparation des produits en fonction des catégories existante sur woocommerce.

function sections_categories_shortcode() {
    $cats = get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'exclude'    => [ get_option('default_product_cat') ],
        'orderby'    => 'menu_order',
    ]);

    ob_start();
    foreach ($cats as $cat) {
        echo '<section class="section-categorie">';
        echo '<div class="eg-separation">';
        echo '<h2 class="separation__title">' . esc_html($cat->name) . '</h2>';
        echo '<p class="separation__description">' . esc_html(wp_strip_all_tags($cat->description)) . '</p>';
        echo '</div>';
        echo do_shortcode('[products category="' . esc_attr($cat->slug) . '" columns="3" limit="12"]');
        echo '</section>';
    }
    return ob_get_clean();
}
add_shortcode('sections_categories', 'sections_categories_shortcode');

// Création du filtre et de l'utilisation du template
add_filter('template_include', function ($template) {
    if (function_exists('is_shop') && is_shop()) {
        $custom = get_stylesheet_directory() . '/sectionboutique.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }
    return $template;
}, 99);

// Champs Personnalisées (Composition, Allergènes, Valeurs Nutritionnelles).

add_action('woocommerce_single_product_summary', function(){
    foreach (['composition' => 'Composition','allergenes' => 'Allergènes','valeurs_nutritionnelles' => "Valeurs nutritionnelles"] as $nom => $titre)
        if ($valeur = get_field($nom)){
            // Si c'est une liste de case à coché, les éléments seront collés entre eux et séparer par une virgule
            if (is_array($valeur)) $valeur = implode (', ', $valeur);      
            echo '<p class="cp__nom">' . esc_html($titre) . '<span class="cp__span">' . ' : ' . nl2br(esc_html($valeur)) . '</span>' . '</p>';
        }
}, 5);

// Affichage des instructions pour le retrait sur la page commande

add_filter( 'render_block', 'show_alert', 10, 2 );
function show_alert( $contenu, $block ) {
    if ( $block['blockName'] !== 'woocommerce/checkout' ) {
        return $contenu;
    }

    $instruction = get_field( 'instructions_pour_le_retrait', 'option' );

    if ( ! $instruction ) {
        return $contenu;
    }

    return 
    '<div class="eg-instructions">' .
    '<h3 class="eg-instructions__title">Instructions pour le retrait</h3>' .
    '<p class="eg-instructions__message">' . esc_html( $instruction ) . '</p>' . '</div>' . $contenu;
}

function obv_get_complementaires( $product_id ) {
    $ids = get_field( 'produits_complementaires', $product_id );
    if ( empty( $ids ) ) {
        return array();
    }
    return array_map( 'intval', $ids );
}

function obv_afficher_suggestions( $ids, $titre ) {
    if ( empty( $ids ) ) {
        return;
    }
    $requete = new WP_Query( array(
        'post_type'      => 'product',
        'post__in'       => $ids,
        'orderby'        => 'post__in',
        'posts_per_page' => 4,
    ) );
    if ( $requete->have_posts() ) {
        echo '<section class="suggestions-complementaires">';
        echo '<h2>' . esc_html( $titre ) . '</h2>';
        woocommerce_product_loop_start();
        while ( $requete->have_posts() ) {
            $requete->the_post();
            wc_get_template_part( 'content', 'product' );
        }
        woocommerce_product_loop_end();
        echo '</section>';
        wp_reset_postdata();
    }
}

add_action( 'woocommerce_after_single_product_summary', function () {
    obv_afficher_suggestions(
        obv_get_complementaires( get_the_ID() ),
        'Pour compléter votre achat'
    );
}, 15 );

add_shortcode( 'suggestions_panier', function () {
    if ( ! WC()->cart || WC()->cart->is_empty() ) {
        return '';
    }
    $ids         = array();
    $dans_panier = array();
    foreach ( WC()->cart->get_cart() as $ligne ) {
        $dans_panier[] = $ligne['product_id'];
        $ids = array_merge( $ids, obv_get_complementaires( $ligne['product_id'] ) );
    }
    $ids = array_values( array_diff( array_unique( $ids ), $dans_panier ) );

    ob_start();
    obv_afficher_suggestions( $ids, 'Vous aimerez aussi' );
    return ob_get_clean();
} );