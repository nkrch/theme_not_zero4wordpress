<?php
// Подключение стилей и скриптов
function enqueue_theme_styles_and_scripts()
{
    // Enqueue main stylesheet
    wp_enqueue_style('theme-style', get_stylesheet_uri());

    // Enqueue custom CSS files
    wp_enqueue_style('base-style', get_template_directory_uri() . '/assets/css/base.css');
    wp_enqueue_style('layout-style', get_template_directory_uri() . '/assets/css/layout.css');
    wp_enqueue_style('products-style', get_template_directory_uri() . '/assets/css/product.css');
    wp_enqueue_style('buttons-forms-style', get_template_directory_uri() . '/assets/css/button-forms.css');
    wp_enqueue_style('cart-table-style', get_template_directory_uri() . '/assets/css/cart-table.css');
    wp_enqueue_style('responsive-style', get_template_directory_uri() . '/assets/css/responsive.css');
    wp_enqueue_style('misc-style', get_template_directory_uri() . '/assets/css/misc.css');
    wp_enqueue_style('widget-style', get_template_directory_uri() . '/assets/css/widget.scss');
    wp_enqueue_style('switcher-style', get_template_directory_uri() . '/assets/css/switcher.scss');

    // Enqueue jQuery, as it's a dependency for your scripts


    // Enqueue Bootstrap JS after jQuery and your scripts
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array('jquery'), null, true);
}

add_action('wp_enqueue_scripts', 'enqueue_theme_styles_and_scripts');

function enqueue_theme_scripts()
{
    // Enqueue WordPress jQuery
    wp_enqueue_script('jquery');
// Enqueue js-cookie library (from CDN)
    wp_enqueue_script('js-cookie', 'https://cdnjs.cloudflare.com/ajax/libs/js-cookie/3.0.1/js.cookie.min.js', array(), '3.0.1', true);

    // Enqueue custom scripts with versioning for cache busting
    wp_enqueue_script('cart-script', get_template_directory_uri() . '/assets/js/cart.js', array('jquery'), '1.0', true);
    wp_enqueue_script('auth-script', get_template_directory_uri() . '/assets/js/auth.js', array('jquery'), '1.0', true);  // Ensure jQuery is a dependency
}

add_action('wp_enqueue_scripts', 'enqueue_theme_scripts');


// Регистрация меню
function my_custom_theme_setup()
{
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'my-custom-theme'),
    ));
}

add_action('after_setup_theme', 'my_custom_theme_setup');

// Регистрация пользовательского типа записи "Продукт"
function register_custom_post_type_product()
{
    $labels = array(
        'name' => 'Продукты',
        'singular_name' => 'Продукт',
        'menu_name' => 'Продукты',
        'add_new' => 'Добавить продукт',
        'add_new_item' => 'Добавить новый продукт',
        'edit_item' => 'Редактировать продукт',
        'new_item' => 'Новый продукт',
        'view_item' => 'Просмотреть продукт',
        'search_items' => 'Искать продукты',
        'not_found' => 'Продукты не найдены',
        'not_found_in_trash' => 'Продукты в корзине не найдены',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'product'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-cart',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'), // Основные элементы
        'show_in_rest' => true, // Поддержка редактора Gutenberg
    );

    register_post_type('product', $args);
}

add_action('init', 'register_custom_post_type_product');

// Включение поддержки миниатюр и комментариев
add_theme_support('post-thumbnails');
add_theme_support('comments');

// Функция для включения товаров в архив
function include_product_in_archive($query)
{
    if ($query->is_main_query() && !is_admin() && is_post_type_archive('product')) {
        $query->set('posts_per_page', 8);
    }
}

add_action('pre_get_posts', 'include_product_in_archive');

function custom_display_comment_form()
{
    global $post;
    $post_id = get_the_ID();
    $success = false;
    $error_message = '';

    // Проверка параметров запроса для отображения сообщений
    if (isset($_GET['comment']) && $_GET['comment'] === 'success') {
        $success = true;
    } elseif (isset($_GET['comment']) && $_GET['comment'] === 'error' && isset($_GET['message'])) {
        $error_message = urldecode($_GET['message']);
    }

    // Отображение сообщений
    if ($success) {
        echo '<p style="color:green;">Спасибо за ваш комментарий!</p>';
    } elseif ($error_message) {
        echo '<p style="color:red;">' . esc_html($error_message) . '</p>';
    }

    ?>
    <form method="post" action="" class="custom-comment-form">
        <?php wp_nonce_field('custom_comment_action', 'custom_comment_nonce'); ?>

        <input type="hidden" name="post_id" value="<?php echo esc_attr($post_id); ?>">
        <div>
            <input placeholder="Email" type="email" name="email" id="email" required>


            <select name="rating" id="rating" required>
                <option value="">Выберите Рейтинг</option>
                <?php
                for ($i = 1; $i <= 5; $i++) {
                    echo '<option value="' . $i . '">' . $i . '</option>';
                }
                ?>
            </select>
        </div>


        <textarea placeholder="Комментарий" name="comment" id="comment" rows="5" required></textarea>


        <input id="submit-btn-comment" type="submit" name="custom_comment_submit" value="Отправить комментарий">

    </form>
    <?php

}

// Отображение существующих комментариев с рейтингом
function custom_display_comments($post_id)
{
    $args = array(
        'post_id' => $post_id,
        'status' => 'approve',
        'orderby' => 'comment_date',
        'order' => 'DESC',
    );

    $comments = get_comments($args);

    if ($comments) {
        echo '<h3>Comments:</h3>';
        echo '<ul class="custom-comments">';
        foreach ($comments as $comment) {
            $rating = get_comment_meta($comment->comment_ID, 'rating', true);
            ?>
            <li style="margin-bottom:20px;">
                <p class="custom-comment-author">
                    <strong><?php echo esc_html($comment->comment_author_email); ?></strong> <?php echo esc_html(get_comment_date('', $comment)); ?>
                </p>
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
function custom_integrate_comment_section($post_id)
{
    // Отображение формы комментария
    custom_display_comment_form();

    // Отображение существующих комментариев
    custom_display_comments($post_id);
}


// ... существующий код в functions.php ...

/**
 * Обработка отправки формы комментария и перенаправление (PRG)
 */
function custom_handle_comment_submission_init()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_comment_submit'])) {
        // Получение ID текущего поста
        if (isset($_POST['post_id'])) {
            $post_id = intval($_POST['post_id']);

            // Обработка отправки комментария
            $result = custom_handle_comment_submission($post_id);

            // Формирование URL для перенаправления
            if ($result === true) {
                // Успешная отправка
                $redirect_url = add_query_arg('comment', 'success', get_permalink($post_id));
            } else {
                // Ошибка при отправке
                $redirect_url = add_query_arg(array('comment' => 'error', 'message' => urlencode($result)), get_permalink($post_id));
            }

            // Безопасное перенаправление
            wp_safe_redirect($redirect_url);
            exit;
        }
    }
}

add_action('template_redirect', 'custom_handle_comment_submission_init');

/**
 * Обработка данных комментария
 * Возвращает true при успехе или строку с сообщениями об ошибках
 */
function custom_handle_comment_submission($post_id)
{
    // Проверка nonce для безопасности
    if (!isset($_POST['custom_comment_nonce']) || !wp_verify_nonce($_POST['custom_comment_nonce'], 'custom_comment_action')) {
        return 'Проверка безопасности не пройдена. Пожалуйста, попробуйте снова.';
    }

    // Санитизация и валидация введенных данных
    $email = sanitize_email($_POST['email']);
    $rating = intval($_POST['rating']);
    $comment_content = sanitize_textarea_field($_POST['comment']);

    $errors = array();

    if (!is_email($email)) {
        $errors[] = 'Пожалуйста, введите действительный адрес электронной почты.';
    }

    if ($rating < 1 || $rating > 5) {
        $errors[] = 'Рейтинг должен быть от 1 до 5.';
    }

    if (empty($comment_content)) {
        $errors[] = 'Пожалуйста, оставьте комментарий.';
    }

    if (!empty($errors)) {
        return implode(' ', $errors);
    }

    // Подготовка данных комментария
    $current_user = wp_get_current_user();

    $commentdata = array(
        'comment_post_ID' => $post_id,
        'comment_author' => $current_user->exists() ? $current_user->display_name : 'Anonymous',
        'comment_author_email' => $email,
        'comment_content' => $comment_content,
        'comment_type' => '', // '' для обычных комментариев
        'comment_parent' => 0,
        'user_id' => $current_user->ID,
        'comment_approved' => 1, // Автодобро пожаловать; установите 0 для ручного одобрения
    );

    // Вставка комментария в базу данных
    $comment_id = wp_insert_comment($commentdata);

    if ($comment_id) {
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


$wp_file_descriptions = array(
    'functions.php' => 'Функции темы',
    'header.php' => 'Шапка темы',
    'footer.php' => 'Подвал темы',
    'style.css' => 'Стили темы',
    'index.php' => 'Главная страница', 'cart.php' => 'Корзина'
);

//cart

add_action('wp_ajax_add_to_cart', 'ajax_add_to_cart');
add_action('wp_ajax_nopriv_add_to_cart', 'ajax_add_to_cart');
function ajax_add_to_cart()
{
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);

    if ($product_id && $quantity) {
        WC()->cart->add_to_cart($product_id, $quantity);
        wp_send_json_success(['cart_count' => WC()->cart->get_cart_contents_count()]);
    } else {
        wp_send_json_error(['message' => 'Invalid product ID or quantity.']);
    }
}

// AJAX: Удаление товара из корзины
add_action('wp_ajax_remove_from_cart', 'ajax_remove_from_cart');
add_action('wp_ajax_nopriv_remove_from_cart', 'ajax_remove_from_cart');
function ajax_remove_from_cart()
{
    $cart_item_key = sanitize_text_field($_POST['cart_item_key']);

    if ($cart_item_key) {
        WC()->cart->remove_cart_item($cart_item_key);
        wp_send_json_success(['cart_count' => WC()->cart->get_cart_contents_count()]);
    } else {
        wp_send_json_error(['message' => 'Invalid cart item key.']);
    }
}

// AJAX: Обновление количества товара в корзине
add_action('wp_ajax_update_cart_quantity', 'ajax_update_cart_quantity');
add_action('wp_ajax_nopriv_update_cart_quantity', 'ajax_update_cart_quantity');
function ajax_update_cart_quantity()
{
    $cart_item_key = sanitize_text_field($_POST['cart_item_key']);
    $quantity = intval($_POST['quantity']);

    if ($cart_item_key && $quantity >= 0) {
        WC()->cart->set_quantity($cart_item_key, $quantity);
        wp_send_json_success(['cart_count' => WC()->cart->get_cart_contents_count()]);
    } else {
        wp_send_json_error(['message' => 'Invalid cart item key or quantity.']);
    }
}


function register_custom_post_type_cart()
{
    $labels = array(
        'name' => 'Заказы',
        'singular_name' => 'Заказ',
        'menu_name' => 'Заказы',
        'add_new' => 'Добавить заказ',
        'add_new_item' => 'Добавить новый заказ',
        'edit_item' => 'Редактировать заказ',
        'new_item' => 'Новый заказ',
        'view_item' => 'Просмотреть заказ',
        'search_items' => 'Искать заказы',
        'not_found' => 'Заказы не найдены',
        'not_found_in_trash' => 'Заказы в корзине не найдены',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'cart'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 6,
        'menu_icon' => 'dashicons-cart',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'), // Основные элементы
        'show_in_rest' => true, // Поддержка редактора Gutenberg
    );

    register_post_type('cart', $args);
}

add_action('init', 'register_custom_post_type_cart');


//AJAX 
add_action('wp_ajax_create_order', 'handle_order');
add_action('wp_ajax_nopriv_create_order', 'handle_order');


function handle_order()
{
    // Получаем данные из POST-запроса
    $data = json_decode(stripslashes($_POST['data']), true);

    // Проверяем обязательные поля
    if (empty($data['name'])) {
        wp_send_json_error(['message' => 'Необходимо заполнить все обязательные поля!', 'data' => $data]);
        exit;
    }
    if (empty($data['cart']) || !is_array($data['cart'])) {
        wp_send_json_error(['message' => 'Не переданы заказы!']);
        exit;
    }

    // Собираем данные из POST-запроса
    $customer_name = $data['name'];
    $customer_email = $data['email'];
    $cart = $data['cart']; // Массив товаров
    $alertif = $data['alertif'];
    $workif = $data['workif'];
    $datenow = $data['datenow'];;
    $datetill = $data['datetill'];

    // Генерируем уникальный идентификатор заказа
    $id_order = uniqid();

    // Создаем новую запись типа order
    $order_id = wp_insert_post([
        'post_type' => 'cart',
        'post_title' => 'Order for ' . $customer_name . ' № ' . $id_order,
        'post_status' => 'publish',
    ]);

    // Проверяем, что запись создана успешно
    if (!$order_id || is_wp_error($order_id)) {
        wp_send_json_error(['message' => 'Ошибка создания записи!']);
        exit;
    }


    // Сохраняем обработанный массив товаров в метаполе
    if (!empty($products)) {
        update_post_meta($order_id, 'products', $products);
    }

    // Сброс постданных
    wp_reset_postdata();

    // Устанавливаем значения для ACF полей (если используется ACF)
    update_field('fio_user', $customer_name, $order_id);
    update_field('email', $customer_email, $order_id);
    update_field('id', $id_order, $order_id);
    update_field('alertif', $alertif, $order_id);
    update_field('workif', $workif, $order_id);
    update_field('datenow', $datenow, $order_id);
    update_field('datetill', $datetill, $order_id);

    // Возвращаем успешный ответ
    wp_send_json_success(['message' => 'Заказ успешно создан!', 'order_id' => $order_id]);
    wp_die(); // Завершаем выполнение
}

//META TRY 5

function save_cart_items_meta($post_id)
{
    // Make sure we are saving data only for the 'cart' post type
    if (get_post_type($post_id) != 'cart') {
        return;
    }

    // Example: Cart items data (replace this with your actual cart logic)
    $cart_items = [];

    if (isset($_COOKIE['cart'])) {
        $cart_data = json_decode(stripslashes($_COOKIE['cart']), true);

        if (is_array($cart_data)) {
            foreach ($cart_data as $item) {
                $cart_items[] = $item;
            }
        }
    }

    // Save the cart items as post meta
    update_post_meta($post_id, '_cart_items', $cart_items);
}

add_action('save_post', 'save_cart_items_meta');

// Add a Meta Box to the Cart Post Type in WP Admin
function add_cart_items_meta_box()
{
    add_meta_box(
        'cart_items_meta_box',                 // ID for the meta box
        'Cart Items',                          // Title of the meta box
        'display_cart_items_in_post',          // Callback function to display content
        'cart',                                // Post type (replace with your custom post type)
        'normal',                              // Context (normal or side)
        'high'                                 // Priority
    );
}

add_action('add_meta_boxes', 'add_cart_items_meta_box');

function display_cart_items_in_post($post)
{
    // Get the cart items from post meta
    $cart_items = get_post_meta($post->ID, '_cart_items', true);
    $post_id = $post->ID;

    // FIX: Ensure `$cart_items` is always an array
    if (!is_array($cart_items)) {
        $cart_items = [];
    }

    // If there are no cart items, display a message
    if (empty($cart_items)) {
        echo '<p>No cart items found for this post.</p>';
        echo '<div class="cart-btns">
            <button id="add-product-btn" onClick="func_ADMIN_addProductToCart(`' . site_url() . '`, `' . $post_id . '`)">Добавить товар</button>
            </div>';
        return;
    }

    // Display the cart items in a table format
    echo '<table class="wp-list-table widefat fixed striped">
            <caption>' . $post_id . ', ' . get_field('id', $post_id) . '</caption>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>';

    // Loop through the cart items and display each one
    foreach ($cart_items as $item) {
        $total_price = $item['price'] * $item['quantity'];

        echo '<tr>
                <td>' . esc_html($item['id']) . '</td>
                <td>' . esc_html($item['title']) . '</td>
                <td>' . esc_html($item['price']) . '</td>
                <td>' . esc_html($item['quantity']) . '</td>
                <td>' . esc_html($total_price) . '</td>
                <td><button onClick="func_ADMIN_removeFromCart(`' . esc_html($item['title']) . '`, ' . esc_html($post_id) . ',`' . site_url() . '`, `' . $post_id . '`)">Удалить</button>
                <button onClick="func_ADMIN_changeOrder(`' . esc_html($item['title']) . '`,' . esc_html($item['quantity']) . ', ' . esc_html($item['id']) . ',' . esc_html($item['price']) . ', `' . esc_html($post_id) . '` , `' . site_url() . '`,`' . $post_id . '`)">Изменить</button></td>
              </tr>';
    }

    echo '</tbody></table>
    <div class="cart-btns">
    <button id="add-product-btn" onClick="func_ADMIN_addProductToCart(`' . site_url() . '`, `' . $post_id . '`)">Добавить товар</button>
    <button id="clear-cart-btn" onClick="func_ADMIN_clearCart(' . $post_id . ')">Очистить корзину</button>
    </div>';
}


// Enqueue admin script and pass AJAX URL
function admin_scripts()
{
    wp_enqueue_script('admin-script', get_template_directory_uri() . '/admin.js', array('jquery'), '1.0.0', true);

    // Pass AJAX URL to JavaScript
    wp_localize_script('admin-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}

add_action('admin_enqueue_scripts', 'admin_scripts');

// Get products for select in admin
function get_products_for_select()
{
    global $wpdb;

    // Query to get published WooCommerce products
    $products = $wpdb->get_results("
        SELECT ID as id, post_title as title
        FROM {$wpdb->prefix}posts 
        WHERE post_type = 'product' AND post_status = 'publish'
    ");

    if (empty($products)) {
        wp_send_json_error(['message' => 'No products found']);
        wp_die();
    }

    // Fetch ACF fields for each product (if ACF is installed)
    $products_with_acf = [];
    foreach ($products as $product) {
        $acf_fields = function_exists('get_fields') ? get_fields($product->id) : []; // Get ACF data

        $products_with_acf[] = [
            'id' => $product->id,
            'title' => $product->title,
            'acf' => $acf_fields // Attach ACF fields
        ];
    }

    wp_send_json_success(['message' => 'ACHIEVED', 'products' => $products_with_acf]);
    wp_die();
}

add_action('wp_ajax_get_products_for_select', 'get_products_for_select');
add_action('wp_ajax_nopriv_get_products_for_select', 'get_products_for_select');

function get_exact_product()
{
    if (!isset($_GET['product_id']) || empty($_GET['product_id'])) {
        wp_send_json_error(['message' => 'Missing product ID']);
        wp_die();
    }

    $product_id = intval($_GET['product_id']);
    $product = get_post($product_id); // Fetch the product from WP database

    if (!$product) {
        wp_send_json_error(['message' => 'Product not found']);
        wp_die();
    }

    // Fetch ACF values (if ACF is installed)
    $acf_fields = function_exists('get_fields') ? get_fields($product_id) : [];

    $product_data = [
        'id' => $product->ID,
        'title' => $product->post_title,
        'content' => $product->post_content,
        'acf' => $acf_fields, // Attach ACF fields if available
    ];

    wp_send_json_success(['message' => 'Product found', 'product' => $product_data]);
    wp_die();
}

add_action('wp_ajax_get_exact_product', 'get_exact_product');
add_action('wp_ajax_nopriv_get_exact_product', 'get_exact_product');


//GET ORDERS

add_action('wp_ajax_get_orders', 'get_orders');
add_action('wp_ajax_nopriv_get_orders', 'get_orders'); // Allow access for non-logged-in users if needed

function get_orders()
{
    // Security: Prevent unauthorized access
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Unauthorized'], 403);
        wp_die();
    }

    global $wpdb;
    $orders = [];

    // Secure SQL query using prepare()
    $results = $wpdb->get_results($wpdb->prepare("
        SELECT ID, post_title
        FROM {$wpdb->prefix}posts
        WHERE post_type = %s
        ORDER BY post_date DESC
       
    ", 'cart'));

    foreach ($results as $order) {
        // Fetch ACF custom fields
        $acf_fio_user = get_post_meta($order->ID, 'fio_user', true);
        $acf_email = get_post_meta($order->ID, 'email', true);
        $acf_id = get_post_meta($order->ID, 'id', true);

        // Fetch Cart (meta field)
        $cart = get_post_meta($order->ID, '_cart_items', true);

        if (is_serialized($cart)) {
            $cart = unserialize($cart);
        } elseif (is_string($cart)) {
            $decoded_cart = json_decode($cart, true);
            $cart = json_last_error() === JSON_ERROR_NONE ? $decoded_cart : $cart;
        }

// Add order data to response array
        $orders[] = [
            'id' => $order->ID,
            'title' => $order->post_title,

            // ACF Fields
            'acf' => [
                'fio_user' => $acf_fio_user,
                'email' => $acf_email,
                'id' => $acf_id
            ],

            // Cart Meta Fields
            'cart' => $cart,
        ];
    }

    // Send JSON response
    wp_send_json_success(json_encode($orders));
    wp_die();
}


function resave_orders()
{
    if (isset($_POST['action']) && $_POST['action'] === 'resave_orders') {
        $id = intval($_POST['id']); // Ensure ID is an integer
        $cart_json = stripslashes($_POST['cart']); // Remove unnecessary escape characters
        $cart = json_decode($cart_json, true); // Decode JSON string

        // Ensure JSON decoding is successful
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(['message' => 'Invalid JSON format']);
            wp_die();
        }


        // Store as an actual array in post meta
        update_post_meta($id, '_cart_items', $cart);

        wp_send_json_success(['message' => 'Orders saved successfully']);
    }
    wp_die();
}


// Retrieve function


add_action('wp_ajax_resave_orders', 'resave_orders');
add_action('wp_ajax_nopriv_resave_orders', 'resave_orders'); // For non-logged-in users


//task12 - rating

require_once get_template_directory() . '/assets/widgets/sidebar/meta.php';
require_once get_template_directory() . '/assets/widgets/sidebar/sidebar.php';
require_once get_template_directory() . '/assets/widgets/sidebar/widget.php';


do_action('widgets_init');


function register_product_rating_widget()
{
    register_widget('Product_Rating_Widget');
}

add_action('widgets_init', 'register_product_rating_widget');

function my_enqueue_scripts() {
    // Enqueue your script
    wp_enqueue_script('my-script', get_template_directory_uri() . '/js/auth.js', array('jquery'), null, true);
    
    // Localize ajax_url to the script
    wp_localize_script('my-script', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'my_enqueue_scripts');

function add_site_url_to_js() {
    ?>
    <script type="text/javascript">
        var siteUrl = "<?php echo esc_url( home_url( '/' ) ); ?>";
    </script>
    <?php
}
add_action('wp_head', 'add_site_url_to_js');


?>

