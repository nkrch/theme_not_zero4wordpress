<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Dosth
 */
get_header();
?>
<div class="content-container">
    <?php while( have_posts() ): ?>
        <?php the_post(); 
        $title = get_the_title();
        $price = get_field('price');

        
        ?>
        <?php if ( has_post_thumbnail() ) :
           ?>

        <?php endif; ?>
        
        <div class="container">
            <?php echo '<h1>' . esc_html($title) . '</h1>';
            echo '<p>' . esc_html($price) . ' руб.</p>';
            echo '<a class="btn-prime" id="btn-prime-big" href="' . esc_url(get_the_permalink()) . '">Купить</a>';?>       
        </div>
    <?php endwhile; ?>
</div>
<?php get_footer(); ?>