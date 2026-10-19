<?php
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

add_filter('template_include', function ($template) {
    if (function_exists('is_shop') && is_shop()) {
        $custom = get_stylesheet_directory() . '/sectionboutique.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }
    return $template;
}, 99);
?>