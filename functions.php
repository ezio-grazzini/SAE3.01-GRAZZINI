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

// Système de cancel rajouté avec le plugin WC Cancel Order

// Système de promocode rajouté avec woocommerce