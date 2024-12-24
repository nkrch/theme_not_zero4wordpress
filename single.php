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
        $description=get_field('description');
        $image=get_field('image');
        ?>
        <?php if ( has_post_thumbnail() ) :
           ?>

        <?php endif; ?>
        
        <div class="container">
            <?php echo '<div class="product-card-big"><div class="left">';
            echo '<h1>' . esc_html($title) . '</h1>';
            
            echo '<p class="description">' . esc_html($description) . '</p>';
            echo '<p class="price">' . esc_html($price) . ' руб.</p>';
            echo '<a class="btn-prime" id="btn-prime-big" href="' . esc_url(get_the_permalink()) . '">Купить</a>';
            echo '<a class="back" href="' . esc_url(get_post_type_archive_link('product')) . '">Назад</a>';
            echo '</div>
            <img class="right" src="' . $image . '" alt=""></div></div>';


            
            ?>       
        </div>
    <?php endwhile; ?>

    
</div>
<?php get_footer(); ?>