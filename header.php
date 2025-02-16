<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/cart.js"></script>
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/auth.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-cookie/3.0.1/js.cookie.min.js"></script>

    <?php wp_head(); ?> <!-- WordPress hook -->
    <title><?php bloginfo('name'); ?></title>
</head>

<?php
$current_user = wp_get_current_user();
if (!($current_user instanceof WP_User)) {
    return;
}

?>
<body <?php body_class(); ?>>

<header>
    <h1><?php bloginfo('name'); ?></h1>


    <nav class="main-navigation">
        <ul>
            <li><a href="<?php echo home_url(); ?>">Главная</a></li>
            <li><a href="<?php echo get_post_type_archive_link('product'); ?>">Каталог</a></li>
            <?php if (is_user_logged_in()): ?>
                <li><a href="<?php echo site_url('/cab'); ?>">Личный кабинет</a></li>
            <?php else: ?>
                <li><a href="">Войти</a></li>
            <?php endif; ?>
            <li><a id="basket" href="<?php echo site_url('/cart-page'); ?>"></a>
                <span id="basket-icon-quantity"></span>
            </li>
            <li>
                <button onclick="startAuth()">Регистрация/Вход</button>
            </li>
        </ul>
    </nav>
</header>
