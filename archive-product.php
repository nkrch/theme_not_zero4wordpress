<?php get_header(); ?>


<div class="container" id="container-of-posts">
    <h1>Архив продуктов</h1>

    <?php
    // Настроим WP_Query для извлечения продуктов
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1; // Текущая страница пагинации
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 8, // Количество постов на странице
        'paged' => $paged, // Пагинация
    );
    $product_query = new WP_Query($args);

    if ($product_query->have_posts()) :
        echo '<div class="product-list">';
        while ($product_query->have_posts()) : $product_query->the_post();
            $name = get_field('name');
            $price = get_field('price');
            $image = get_field('image'); // Получаем поле
 $title = get_the_title();
 $link=get_the_permalink();
echo '<div class="product-card">';
            
if ($image) {
    echo '<img  src="' . $image . '" alt="">';
}
            if (!empty($name)) {
                echo '<h5>' . esc_html($name) . '</h5>';
            } else {
                echo '<h5>' . esc_html($title) . '</h5>';
            }
            echo '<p>' . esc_html($price) . ' руб.</p>';
            echo '<a class="btn-prime" href="' . esc_url(get_the_permalink()) . '">Купить</a>';
            echo '</div>';
        endwhile;
        echo '</div>';

        // Пагинация
        echo '<div class="pagination">';
        echo paginate_links(array(
            'total' => $product_query->max_num_pages,
            'current' => $paged,
        ));
        echo '</div>';

        wp_reset_postdata();
    else :
        echo '<p>Продукты не найдены</p>';
    endif;
    ?>
</div>

<?php get_footer(); ?>
