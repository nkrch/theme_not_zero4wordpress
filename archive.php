<?php get_header(); ?>

<div class="container">
    <h1><?php the_archive_title(); ?></h1>
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <div class="post">
            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <?php the_excerpt(); ?>
        </div>
    <?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>
