<?php
// Подключение стилей и скриптов
function my_custom_theme_scripts() {
    // Подключение Bootstrap
    wp_enqueue_style('bootstrap-css', 'https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap-js', 'https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js', array('jquery'), null, true);

    // Подключение основного файла стилей темы
    wp_enqueue_style('theme-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'my_custom_theme_scripts');

// Регистрация меню
function my_custom_theme_setup() {
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'my-custom-theme'),
    ));
}
add_action('after_setup_theme', 'my_custom_theme_setup');
