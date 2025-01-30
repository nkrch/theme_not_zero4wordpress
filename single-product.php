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
        $description = get_field('description');
        $image = get_field('image');
        $id=get_the_ID();
        ?>
        <?php if ( has_post_thumbnail() ) : ?>
            <!-- Вы можете отобразить миниатюру поста здесь, если необходимо -->
        <?php endif; ?>

        <div class="container">
            <div class="product-card-big">
                <div class="left">
                    <h1 id="h1-title"><?php echo esc_html($title); ?></h1>
                    <p id="p-description" class="description"><?php echo esc_html($description); ?></p>
                    <p id="p-price" class="price"><?php echo esc_html($price); ?> руб.</p>
<button class="action-add-btn" onClick="submitFunc('<?php echo $title; ?>', '<?php echo $price; ?>', '<?php echo $description; ?>', '<?php echo $image; ?>', <?php echo $id; ?>)">В корзину</button>
                    <a class="back" href="<?php echo esc_url(get_post_type_archive_link('product')); ?>">Назад</a>
                </div>
                <img class="right" src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>">
            </div>
        </div>

       

        <!-- Интеграция секции комментариев -->
        <div class="custom-comment-section">
            <?php custom_integrate_comment_section(get_the_ID()); ?>
        </div>
    <?php endwhile; ?>
</div>

<?php
get_footer();
?>