<?php
// Подключение стилей и скриптов
function enqueue_theme_styles_and_scripts() {
    // Подключение основного файла стилей
    wp_enqueue_style('theme-style', get_stylesheet_uri());

    // Подключение Bootstrap JS (убедитесь, что используете корректную версию)
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

// Регистрация пользовательского типа записи "Продукт"
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

// Включение поддержки миниатюр и комментариев
add_theme_support('post-thumbnails');
add_theme_support('comments');

// Функция для включения товаров в архив
function include_product_in_archive( $query ) {
    if ( $query->is_main_query() && !is_admin() && is_post_type_archive('product') ) {
        $query->set('posts_per_page', 8);
    }
}
add_action('pre_get_posts', 'include_product_in_archive');

/**
 * ================================
 * Custom Comment Functions Start
 * ================================
 */

// Обработка отправки формы комментария
/*function custom_handle_comment_submission($post_id) {
    // Проверка nonce для безопасности
    if ( ! isset($_POST['custom_comment_nonce']) || ! wp_verify_nonce($_POST['custom_comment_nonce'], 'custom_comment_action') ) {
        echo '<p style="color:red;">Security check failed. Please try again.</p>';
        return;
    }

    // Санитизация и валидация введенных данных
    $email = sanitize_email($_POST['email']);
    $rating = intval($_POST['rating']);
    $comment_content = sanitize_textarea_field($_POST['comment']);

    $errors = array();

    if ( ! is_email($email) ) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ( $rating < 1 || $rating > 5 ) {
        $errors[] = 'Rating must be between 1 and 5.';
    }

    if ( empty($comment_content) ) {
        $errors[] = 'Please enter a comment.';
    }

    if ( ! empty($errors) ) {
        foreach ( $errors as $error ) {
            echo '<p style="color:red;">' . esc_html($error) . '</p>';
        }
        return;
    }

    // Подготовка данных комментария
    $current_user = wp_get_current_user();

    $commentdata = array(
        'comment_post_ID'      => $post_id,
        'comment_author'       => $current_user->exists() ? $current_user->display_name : 'Anonymous',
        'comment_author_email' => $email,
        'comment_content'      => $comment_content,
        'comment_type'         => '', // '' для обычных комментариев
        'comment_parent'       => 0,
        'user_id'              => $current_user->ID,
        'comment_approved'     => 1, // Автоматическое одобрение; установить 0 для ручного одобрения
    );

    // Вставка комментария в базу данных
    $comment_id = wp_insert_comment($commentdata);

    if ( $comment_id ) {
        // Добавление рейтинга как мета данных комментария
        add_comment_meta($comment_id, 'rating', $rating);
        echo '<p style="color:green;">Thank you for your comment!</p>';
    } else {
        echo '<p style="color:red;">There was an error submitting your comment. Please try again.</p>';
    }
}*/

// Отображение формы комментария
/**
 * Отображение формы комментария с обработкой сообщений
 */
function custom_display_comment_form() {
    global $post;
    $post_id = get_the_ID();
    $success = false;
    $error_message = '';

    // Проверка параметров запроса для отображения сообщений
    if ( isset($_GET['comment']) && $_GET['comment'] === 'success' ) {
        $success = true;
    } elseif ( isset($_GET['comment']) && $_GET['comment'] === 'error' && isset($_GET['message']) ) {
        $error_message = urldecode($_GET['message']);
    }

    // Отображение сообщений
    if ( $success ) {
        echo '<p style="color:green;">Спасибо за ваш комментарий!</p>';
    } elseif ( $error_message ) {
        echo '<p style="color:red;">' . esc_html($error_message) . '</p>';
    }

    ?>
    <form method="post" action="" class="custom-comment-form">
        <?php wp_nonce_field('custom_comment_action', 'custom_comment_nonce'); ?>
        <input type="hidden" name="post_id" value="<?php echo esc_attr($post_id); ?>">
        <p>
            <label for="email">Email:</label><br>
            <input type="email" name="email" id="email" required>
        </p>
        <p>
            <label for="rating">Рейтинг (1-5):</label><br>
            <select name="rating" id="rating" required>
                <option value="">Выберите Рейтинг</option>
                <?php
                for ($i = 1; $i <= 5; $i++) {
                    echo '<option value="' . $i . '">' . $i . '</option>';
                }
                ?>
            </select>
        </p>
        <p>
            <label for="comment">Комментарий:</label><br>
            <textarea name="comment" id="comment" rows="5" required></textarea>
        </p>
        <p>
            <input type="submit" name="custom_comment_submit" value="Отправить комментарий">
        </p>
    </form>
    <?php

}

// Отображение существующих комментариев с рейтингом
function custom_display_comments($post_id) {
    $args = array(
        'post_id' => $post_id,
        'status'  => 'approve',
        'orderby' => 'comment_date',
        'order'   => 'DESC',
    );

    $comments = get_comments($args);

    if ( $comments ) {
        echo '<h3>Comments:</h3>';
        echo '<ul class="custom-comments">';
        foreach ( $comments as $comment ) {
            $rating = get_comment_meta($comment->comment_ID, 'rating', true);
            ?>
            <li style="margin-bottom:20px;">
                <p class="custom-comment-author"><strong><?php echo esc_html($comment->comment_author_email); ?></strong> <?php echo esc_html(get_comment_date('', $comment)); ?></p>
                <p>Rating: <?php echo esc_html($rating); ?>/5</p>
                <p><?php echo nl2br(esc_html($comment->comment_content)); ?></p>
            </li>
            <?php
        }
        echo '</ul>';
    } else {
        echo '<p>No comments yet.</p>';
    }
}

// Интеграция формы комментария и отображения комментариев
function custom_integrate_comment_section($post_id) {
    // Отображение формы комментария
    custom_display_comment_form();

    // Отображение существующих комментариев
    custom_display_comments($post_id);
}


// ... существующий код в functions.php ...

/**
 * Обработка отправки формы комментария и перенаправление (PRG)
 */
function custom_handle_comment_submission_init() {
    if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_comment_submit']) ) {
        // Получение ID текущего поста
        if ( isset($_POST['post_id']) ) {
            $post_id = intval($_POST['post_id']);
            
            // Обработка отправки комментария
            $result = custom_handle_comment_submission($post_id);

            // Формирование URL для перенаправления
            if ( $result === true ) {
                // Успешная отправка
                $redirect_url = add_query_arg( 'comment', 'success', get_permalink($post_id) );
            } else {
                // Ошибка при отправке
                $redirect_url = add_query_arg( array( 'comment' => 'error', 'message' => urlencode($result) ), get_permalink($post_id) );
            }

            // Безопасное перенаправление
            wp_safe_redirect( $redirect_url );
            exit;
        }
    }
}
add_action( 'template_redirect', 'custom_handle_comment_submission_init' );

/**
 * Обработка данных комментария
 * Возвращает true при успехе или строку с сообщениями об ошибках
 */
function custom_handle_comment_submission($post_id) {
    // Проверка nonce для безопасности
    if ( ! isset($_POST['custom_comment_nonce']) || ! wp_verify_nonce($_POST['custom_comment_nonce'], 'custom_comment_action') ) {
        return 'Проверка безопасности не пройдена. Пожалуйста, попробуйте снова.';
    }

    // Санитизация и валидация введенных данных
    $email = sanitize_email($_POST['email']);
    $rating = intval($_POST['rating']);
    $comment_content = sanitize_textarea_field($_POST['comment']);

    $errors = array();

    if ( ! is_email($email) ) {
        $errors[] = 'Пожалуйста, введите действительный адрес электронной почты.';
    }

    if ( $rating < 1 || $rating > 5 ) {
        $errors[] = 'Рейтинг должен быть от 1 до 5.';
    }

    if ( empty($comment_content) ) {
        $errors[] = 'Пожалуйста, оставьте комментарий.';
    }

    if ( ! empty($errors) ) {
        return implode(' ', $errors);
    }

    // Подготовка данных комментария
    $current_user = wp_get_current_user();

    $commentdata = array(
        'comment_post_ID'      => $post_id,
        'comment_author'       => $current_user->exists() ? $current_user->display_name : 'Anonymous',
        'comment_author_email' => $email,
        'comment_content'      => $comment_content,
        'comment_type'         => '', // '' для обычных комментариев
        'comment_parent'       => 0,
        'user_id'              => $current_user->ID,
        'comment_approved'     => 1, // Автодобро пожаловать; установите 0 для ручного одобрения
    );

    // Вставка комментария в базу данных
    $comment_id = wp_insert_comment($commentdata);

    if ( $comment_id ) {
        // Добавление рейтинга как мета данных комментария
        add_comment_meta($comment_id, 'rating', $rating);
        return true;
    } else {
        return 'Произошла ошибка при отправке вашего комментария. Пожалуйста, попробуйте снова.';
    }
}

/**
 * ==============================
 * Custom Comment Functions End
 * ==============================
 */