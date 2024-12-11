add_action( 'after_setup_theme', 'wp_kama_theme_setup' );
function wp_kama_theme_setup(){
	// Поддержка миниатюр
	add_theme_support( 'post-thumbnails' );
}