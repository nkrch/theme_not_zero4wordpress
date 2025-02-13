<?php
function add_product_rating_meta_box() {
    add_meta_box(
        'product_rating_meta_box',
        'Рейтинг товара',
        'render_product_rating_meta_box',
        'product',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'add_product_rating_meta_box');

function render_product_rating_meta_box($post) {
    $value = get_post_meta($post->ID, 'product_rating', true);
    echo '<label for="product_rating">Рейтинг (0-5):</label>';
    echo '<input type="number" id="product_rating" name="product_rating" value="' . esc_attr($value) . '" min="0" max="5" step="0.1">';
}

function save_product_rating_meta_box($post_id) {
    if (array_key_exists('product_rating', $_POST)) {
        update_post_meta($post_id, 'product_rating', $_POST['product_rating']);
    }
}
add_action('save_post', 'save_product_rating_meta_box');
?>