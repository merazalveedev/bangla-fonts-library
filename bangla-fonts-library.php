<?php
/**
 * Plugin Name:         Bangla Fonts Library
 * Plugin URI:          https://wordpress.org/plugins/bangla-fonts-library
 * Description:         Automatically applies Bangla fonts to Bengali text on your WordPress site.
 * Version:             1.0.0
 * Requires at least:   5.2
 * Requires PHP :       8.3
 * Author:              Meraz Alvee
 * Author URI:          https://merazalvee.com/
 * License:             GPL v2 or later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:         bangla-fonts-library
 * Domain Path:         /languages
 */

defined('ABSPATH') || exit;

// Define constants
define('BFL_VERSION', '1.0.0');
define('BFL_PATH', plugin_dir_path(__FILE__));
define('BFL_URL', plugin_dir_url(__FILE__));

// Include core files
require_once BFL_PATH . 'includes/class-font-loader.php';

// Initialize plugin
add_action('plugins_loaded', function() {
    BFL_Font_Loader::instance();
});

// Activation hook
register_activation_hook(__FILE__, function() {
    if (!get_option('bfl_settings')) {
        update_option('bfl_settings', [
            'selected_font' => 'solaimanlipi',
            'font_display' => 'swap'
        ]);
    }
});