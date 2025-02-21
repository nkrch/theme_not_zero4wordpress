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

    private function get_star_rating($rating)
    {
        $full_stars = floor($rating);
        $half_star = ($rating - $full_stars) >= 0.5 ? 1 : 0;
        $empty_stars = 5 - ($full_stars + $half_star);

        return str_repeat('★', $full_stars) .
            ($half_star ? '☆' : '') .
            str_repeat('☆', $empty_stars);
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
        $query = new WP_Query([
            'post_type' => 'product',
            'posts_per_page' => -1
        ]);

        $presortable = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $name = get_the_title();
                $rating = get_post_meta(get_the_ID(), 'rating', true);
                $link = get_permalink();

                // Set default rating to 0 if empty
                $rating = empty($rating) ? 0 : (float)$rating;

                $presortable[] = ['name' => $name, 'rating' => $rating, 'link' => $link];
            }
        }
        wp_reset_postdata();

        // Sort the array by rating in descending order
        $sortable = $this->array_sort($presortable, 'rating', SORT_DESC);

        echo '<div class="widget-container">';

        for ($i = 0; $i < min(5, count($sortable)); $i++) {
            echo '<div class="container-of-rating-item">';
            echo '<a href="' . esc_url($sortable[$i]['link']) . '">' . esc_html($sortable[$i]['name']) . '</a> ';
            echo '<p class="stars-widget">' . $this->get_star_rating($sortable[$i]['rating']) . '</p>' . '<br>';
            echo '</div>';
        }

        echo '</div>'; // Corrected closing div tag

        echo $args['after_widget'];
    }

    private function array_sort($array, $key, $sort_order = SORT_DESC)
    {
        if (!is_array($array) || empty($array)) {
            return [];
        }

        usort($array, function ($a, $b) use ($key, $sort_order) {
            if (!isset($a[$key]) || !isset($b[$key])) {
                return 0;
            }

            return ($sort_order === SORT_DESC) ? ($b[$key] <=> $a[$key]) : ($a[$key] <=> $b[$key]);
        });

        return $array;
    }
}
