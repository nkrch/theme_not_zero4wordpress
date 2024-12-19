<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
    <?php wp_head(); ?>
</head>
<nav class="main-navigation">
    <ul class="<?php echo join(' ', get_body_class()); ?>">
        <li><a href="<?php echo home_url(); ?>">Главная</a></li>
        <li><a href="<?php echo home_url('/catalog/'); ?>">Каталог</a></li>
        <?php if (is_user_logged_in()): ?>
            <li><a href="<?php echo home_url('/account/'); ?>">Личный кабинет</a></li>
        <?php else: ?>
            <li><a href="<?php echo wp_login_url(); ?>">Войти</a></li>
        <?php endif; ?>
    </ul>
</nav>
<body>

    <?php body_class(); ?>>
</body>
</html>
