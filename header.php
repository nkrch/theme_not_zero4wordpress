<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
    <?php wp_head(); ?>
</head>
<header>

    <nav class="main-navigation">
        <h1><?php bloginfo('name'); ?></h1>

        <ul class="<?php echo join(' ', get_body_class()); ?>">
            <li><a href="index.php">Главная</a></li>
            <li><a href="<?php echo get_permalink("/archive.php")?>">Каталог</a></li>
            <?php if (is_user_logged_in()): ?>
                <li><a href="/?single.php">Личный кабинет</a></li>
            <?php else: ?>
                <li><a href="<?php echo wp_login_url(); ?>">Войти</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<body>
totot


</body></html>