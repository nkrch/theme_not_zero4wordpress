<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-cookie/3.0.5/js.cookie.min.js"></script>
    <script src="<?php get_template_directory_uri() ?>/js/cart.js" defer></script>
    <?php wp_head(); ?>
    <title><?php bloginfo('name'); ?></title>
</head>
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

        <?php wp_reset_postdata(); ?>
    </nav>
</header>


