<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-migrate-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>


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
                <li><a href="<?php echo home_url('/cab'); ?>">Личный кабинет</a></li>
            <?php else: ?>
                <li><a href="<?php echo home_url('/enter'); ?>">Войти</a></li>
            <?php endif; ?>

        </ul>
    </nav>
</header>

<?php
// Убираем использование $_GET['page'] и оставляем логику стандартной маршрутизации WordPress.
if (is_home() || is_front_page()) {
// Главная страница
require_once 'index.php';
} elseif (is_post_type_archive('product')) {
// Архив продуктов
require_once 'archive-product.php';
} elseif (is_page('cab')) {
// Личный кабинет
require_once ('cabinet.php');
} elseif (is_page('enter')) {
// Вход
require_once 'auth.php';
} 
elseif (is_singular('product')) {
   require_once 'single.php';
}
else {
// Для всех других случаев (404)
require_once '404.php';
}

?>

</body>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

</html>