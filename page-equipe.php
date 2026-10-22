<?php get_header(); ?>

<div class="eg-equipe">
    <h1 class="entry-title"><?php the_title(); ?></h1>
    <ul class="eg-equipe__list">
        <?php 
        $membres = new WP_Query(array(
            'post_type' => 'equipes',
            'posts_per_page' => '-1',
            'orderby' => 'title',
            'order' => 'ASC',
        ));
        while ($membres->have_posts()): $membres->the_post(); ?>
            <li class="list__item">
                <a class="item__link" href="<?php the_permalink();?>">
                    <?php the_post_thumbnail('medium') ?>
                    <h2 class="link__name"><?php the_title(); ?></h2>
                    <p class="link__poste"><?php the_field('poste'); ?></p>
                </a>
            </li>
        <?php endwhile; wp_reset_postdata(); ?>
    </ul>
</div>

<?php get_footer(); ?>