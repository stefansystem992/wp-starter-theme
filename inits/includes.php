<?php
/**
 * The theme includes file
 *
 * @package starter_wp_theme
 */

// THEME CONSTANTS
define('BASE_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('THEME_ASSETS', get_template_directory_uri() . '/assets/');

// THEME ASSETS
function enqueueStylesAndScripts() {

    // STYLES
    wp_register_style('main', THEME_ASSETS . 'styles/main.css', [], filemtime(get_theme_file_path('assets/styles/main.css')));
    wp_enqueue_style('main');

    wp_register_style('swiper', THEME_ASSETS . 'styles/swiper.min.css', [], filemtime(get_theme_file_path('assets/styles/swiper.min.css')));
    wp_enqueue_style('swiper');

    // HEADER SCRIPTS
    // wp_register_script('modernizr', THEME_ASSETS . 'js/libs/modernizr.js');
    // wp_enqueue_script('modernizr');

    // FOOTER SCRIPTS
    wp_enqueue_script('jquery');
    wp_script_add_data('jquery', 'strategy', 'defer');
    wp_script_add_data('jquery-core', 'strategy', 'defer');

    wp_register_script('swiper', THEME_ASSETS . 'js/libs/swiper.min.js', array(), filemtime(get_theme_file_path('assets/js/libs/swiper.min.js')), array(
        'in_footer' => true,
        'strategy'  => 'defer',
    ));
    wp_enqueue_script('swiper');

    wp_register_script('tselectbox', THEME_ASSETS . 'js/libs/t.selectbox.js', array('jquery'), filemtime(get_theme_file_path('assets/js/libs/t.selectbox.js')), array(
        'in_footer' => true,
        'strategy'  => 'defer',
    ));
    wp_enqueue_script('tselectbox');

    wp_register_script('init', THEME_ASSETS . 'js/init.js', array('jquery', 'swiper'), filemtime(get_theme_file_path('assets/js/init.js')), array(
        'in_footer' => true,
        'strategy'  => 'defer',
    ));
    wp_enqueue_script('init');

}
add_action('wp_enqueue_scripts', 'enqueueStylesAndScripts');