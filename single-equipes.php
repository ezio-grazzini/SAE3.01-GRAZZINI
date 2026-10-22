<?php get_header(); ?>

<h1 class="entry-title"><?php the_title(); ?></h1>

<div class="eg-membre">
    <?php while(have_posts()) : the_post(); ?>
        <?php the_post_thumbnail('large')?>
        <div class="eg-membre__info">
            <p class="info__poste"><?php the_field('poste')?></p>
            <p class="info__citation"><?php the_field('citation')?></p>
            <p class="info_description"><?php the_field('description')?></p>
        </div>
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>
