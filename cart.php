<?php
/* Template Name: Cart */
get_header(); ?>
<main class="card-container">
    <h1>Корзина</h1>

    <?php
    // Получение данных корзины из куки
    $cart_cookie = isset($_COOKIE['cart']) ? json_decode(stripslashes($_COOKIE['cart']), true) : [];
if (empty($cart_cookie)) {
    echo '<p>Корзина пуста :(</p>';
}else{
    
        echo '<div class="cart-list">';
        echo '<table class="cart-table">';
        echo '<th>';
        echo '<tr> <td>Наименование</td> <td>Цена</td> <td>Количество</td> <td>Итого</td> </tr>';
        foreach ($cart_cookie as $item) {
        // Проверяем наличие необходимых данных в элементе корзины

        $quantity = $item['quantity'];



        // Данные продукта
        $name = $item['title'];
        $price = $item['price'];

        // Вывод карточки товара
        echo '<tr class="cart-card">';
        
        if (!empty($name)) {
            echo '<td>' . esc_html($name) . '</td>';
        } 
        echo '<td> ' . esc_html($price) . ' руб.</td>';
        echo '<td>' . esc_html($quantity) . '</td>';
        echo '<td> ' . esc_html($price * $quantity) . ' руб.</td>';
        echo '</tr>';
    }
    echo '<tr><td colspan="4">Итого: ' . array_sum(array_column($cart_cookie, 'price')) . ' руб.</td></tr>';
    echo '</table>';
        echo '</div>';

        

        wp_reset_postdata();
    }
    ?>
</div>
  

    


</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Удаление товара из корзины
        document.querySelectorAll('.remove-item').forEach(function (button) {
            button.addEventListener('click', function () {
                const cartKey = this.getAttribute('data-cart-key');
                
                // Получение текущих данных корзины из куки
                let cart = JSON.parse(localStorage.getItem('custom_cart')) || {};
                
                // Удаление элемента
                if (cart[cartKey]) {
                    delete cart[cartKey];
                }

                // Сохранение корзины обратно в куки
                document.cookie = 'custom_cart=' + JSON.stringify(cart) + '; path=/';
                localStorage.setItem('custom_cart', JSON.stringify(cart));

                // Обновление страницы
                location.reload();
            });
        });
    });
</script>

<?php
get_footer();