<?php
/* Template Name: Order */
get_header(); ?>
<main class="card-container">
    <h1>Корзина</h1>

    <?php
    // Получение данных корзины из куки
    $cart_cookie = isset($_COOKIE['cart']) ? json_decode(stripslashes($_COOKIE['cart']), true) : [];
    if (empty($cart_cookie)) {
        echo '<p>Корзина пуста :(</p>';
    } else {
        echo '<div class="cart-list">';
        echo '<table class="cart-table">';
        echo '<thead>';
        echo '<tr> <th>Наименование</th> <th>Цена</th> <th>Количество</th> <th>Итого</th> </tr>';
        echo '</thead>';
        echo '<tbody>';

        $total = 0; // Переменная для хранения итоговой суммы

        foreach ($cart_cookie as $item) {
            // Проверяем наличие необходимых данных в элементе корзины
            $quantity = $item['quantity'];
            $name = $item['title'];
            $price = $item['price'];

            // Расчёт итоговой стоимости для каждого товара
            $item_total = $price * $quantity;
            $total += $item_total;

            // Вывод карточки товара
            echo '<tr class="cart-card">';
            if (!empty($name)) {
                echo '<td>' . esc_html($name) . '</td>';
            }
            echo '<td>' . esc_html($price) . ' руб.</td>';
            echo '<td>' . esc_html($quantity) . ' 
                <button class="rem-add-btn" onClick="updateFromCartPage(`' . $name . '`, ' . ($quantity + 1) . ')">+</button>
                <button class="rem-add-btn" onClick="updateFromCartPage(`' . $name . '`, ' . ($quantity - 1) . ')">-</button>
                <button class="rem-add-btn" id="delete-btn" onClick="removeFromCart(`' . $name . '`)"><svg fill="#000000" height="14px" width="14px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
	 viewBox="0 0 290 290" xml:space="preserve">
<g id="XMLID_24_">
	<g id="XMLID_29_">
		<path d="M265,60h-30h-15V15c0-8.284-6.716-15-15-15H85c-8.284,0-15,6.716-15,15v45H55H25c-8.284,0-15,6.716-15,15s6.716,15,15,15
			h5.215H40h210h9.166H265c8.284,0,15-6.716,15-15S273.284,60,265,60z M190,60h-15h-60h-15V30h90V60z"/>
	</g>
	<g id="XMLID_86_">
		<path d="M40,275c0,8.284,6.716,15,15,15h180c8.284,0,15-6.716,15-15V120H40V275z"/>
	</g>
</g></button>
                </td>';
            echo '<td>' . esc_html($item_total) . ' руб.</td>';
            echo '</tr>';
        }

        // Итоговая строка
        echo '<tr><td colspan="3" style="text-align: right; font-weight: bold;">Итого:</td><td>' . esc_html($total) . ' руб.</td></tr>';

        echo '</tbody>';
        echo '</table>';
        echo '</div>';

        wp_reset_postdata();
    }
    ?>

<?php 
    $siteUrl=site_url();

?>

    <h1>Данные пользователя</h1>
    <form>
        <input type="text" placeholder="Имя"/>
        <input type="email" placeholder="Email"/>
        <button onClick="orderFunc(event, `<?php echo $siteUrl ?>`)">Заказать</button>
    </form>
    <?php $value = get_post_meta(get_the_ID(), '_meta_key', true);
if (!empty($value)) {
    echo '<p>Дополнительная информация: ' . esc_html($value) . '</p>';
    // Получение массива товаров
$cart = get_post_meta($order_id, 'cart', true);
if (!empty($cart)) {
    foreach ($cart as $product) {
        echo 'ID товара: ' . $product['id'] . '<br>';
        echo 'Название: ' . $product['name'] . '<br>';
        echo 'Цена: ' . $product['price'] . '<br>';
        echo 'Количество: ' . $product['quantity'] . '<br><br>';
    }
}
} ?>
</main>

<script>
    
</script>

<?php
get_footer();