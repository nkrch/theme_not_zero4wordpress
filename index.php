<?php get_header(); ?>

<div class="container" id="container-index">

    <p><?php bloginfo('description'); ?></p>

    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <div class="post">
            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <?php the_excerpt(); ?>
        </div>
    <?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>
