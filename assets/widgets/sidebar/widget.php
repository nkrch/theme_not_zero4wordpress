<?php

class Product_Rating_Widget extends WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'product_rating_widget',
            'Рейтинг товаров',
            ['description' => 'Топ-5 товаров по рейтингу']
        );
    }

    public function widget($args, $instance)
    {
        $products = get_posts(['post_type' => 'product', 'numberposts' => -1]);

        foreach ($products as $product) {
            $product_id = $product->ID;
            $ratingManager = new ratingManager();
            $average_rating = $ratingManager->get_product_average_rating_from_comments($product_id);
            update_post_meta($product_id, 'rating', $average_rating);
        }

        echo $args['before_widget'];
        echo $args['before_title'] . 'Рейтинг товаров' . $args['after_title'];

        // Get products
        $query = new WP_Query(array(
            'post_type' => 'product',
            'posts_per_page' => -1
        ));

        //pre demo filter
        $presortable = [];
        if ($query->have_posts()) {
            $presortable = []; // Initialize array before the loop

            while ($query->have_posts()) {
                $query->the_post();
                $name = get_the_title(); // Or get_field('name') if it's a custom field
                $rating = get_post_meta(get_the_ID(), 'rating', true);
                $link = get_permalink();

                // Set default rating to 0 if empty
                if (empty($rating)) {
                    $rating = 0;
                }

                $presortable[] = ['name' => $name, 'rating' => $rating, 'link' => $link];
            }
        }

// Sort the array by rating in descending order
        $sortable = $this->array_sort($presortable, 'rating', SORT_DESC);


        echo '<div class="widget-container">';
        for ($i = 0; $i < 5; $i++) {
            echo '<div class="container-of-rating-item">';
            echo '<a href="' . $sortable[$i]['link'] . '">' . $sortable[$i]['name'] . '</a> ' . $sortable[$i]['rating'] . '<br>';
            echo '</div>';
        }
        echo '</div';


        echo $args['after_widget'];
    }


    function array_sort($array, $key, $sort_order = SORT_DESC)
    {
        if (!is_array($array) || empty($array)) {
            return []; // Возвращаем пустой массив, если входные данные неверные
        }

        usort($array, function ($a, $b) use ($key, $sort_order) {
            if (!isset($a[$key]) || !isset($b[$key])) {
                return 0; // Пропускаем элементы без нужного ключа
            }

            if ($sort_order === SORT_DESC) {
                return $b[$key] <=> $a[$key]; // Сортировка по убыванию
            } else {
                return $a[$key] <=> $b[$key]; // Сортировка по возрастанию
            }
        });

        return $array;
    }

}

