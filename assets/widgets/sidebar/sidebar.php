<?php

function register_product_sidebar() {
    register_sidebar([
        'name'          => 'Сайдбар архива товаров',
        'id'            => 'product_archive_sidebar',
        'before_widget' => '<div class="widget-product-rating-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);
}

add_action('widgets_init', 'register_product_sidebar');
?>