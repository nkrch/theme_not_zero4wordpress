<?php
// Подключение стилей и скриптов
function enqueue_theme_styles_and_scripts() {
    // Подключение основного файла стилей
    wp_enqueue_style('theme-style', get_stylesheet_uri());

    // Подключение дополнительных стилей или скриптов, если нужно
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array('jquery'), null, true);
}

add_action('wp_enqueue_scripts', 'enqueue_theme_styles_and_scripts');

// Регистрация меню
function my_custom_theme_setup() {
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'my-custom-theme'),
    ));
}



add_action('after_setup_theme', 'my_custom_theme_setup');


function register_custom_post_type_product() {
    $labels = array(
        'name'               => 'Продукты',
        'singular_name'      => 'Продукт',
        'menu_name'          => 'Продукты',
        'add_new'            => 'Добавить продукт',
        'add_new_item'       => 'Добавить новый продукт',
        'edit_item'          => 'Редактировать продукт',
        'new_item'           => 'Новый продукт',
        'view_item'          => 'Просмотреть продукт',
        'search_items'       => 'Искать продукты',
        'not_found'          => 'Продукты не найдены',
        'not_found_in_trash' => 'Продукты в корзине не найдены',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'product' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-cart',
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ), // Основные элементы
        'show_in_rest'       => true, // Поддержка редактора Gutenberg
    );

    register_post_type( 'product', $args );
}

add_action( 'init', 'register_custom_post_type_product' );
//questionable function
function include_product_in_archive( $query ) {
    if ( $query->is_main_query() && !is_admin() && is_post_type_archive('product') ) {
        $query->set('posts_per_page', 8);
    }
}
add_action('pre_get_posts', 'include_product_in_archive');




add_theme_support('post-thumbnails');



