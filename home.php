<?php get_header(); ?>

<main>
    <?php
    if (!is_user_logged_in()) :
        ?>
        <h3>Вам недоступны корзина и личный кабинет :( <br>
            Зарегистрируйтесь или войдите в аккаунт</h3>
    <?php else : ?>
        <h3>Вы </h3>
    <?php endif; ?>
</main>

