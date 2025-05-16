<?php
class BFL_Font_Loader {
    private static $instance;

    public static function instance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_hooks();
    }

    private function init_hooks() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function add_admin_menu() {
        add_management_page(
            __('Bangla Fonts', 'bangla-fonts-library'),
            __('Bangla Fonts', 'bangla-fonts-library'),
            'manage_options',
            'bfl-settings',
            [$this, 'render_settings_page']
        );
    }

    public function register_settings() {
        register_setting('bfl_settings_group', 'bfl_settings', [$this, 'sanitize_settings']);

        add_settings_section(
            'bfl_main_section',
            __('Bangla Font Settings', 'bangla-fonts-library'),
            [$this, 'render_section'],
            'bfl-settings'
        );

        add_settings_field(
            'selected_font',
            __('Select Font', 'bangla-fonts-library'),
            [$this, 'render_font_select'],
            'bfl-settings',
            'bfl_main_section'
        );

        add_settings_field(
            'font_display',
            __('Loading Behavior', 'bangla-fonts-library'),
            [$this, 'render_font_display'],
            'bfl-settings',
            'bfl_main_section'
        );
    }

    public function sanitize_settings($input) {
        return [
            'selected_font' => sanitize_text_field($input['selected_font']),
            'font_display' => sanitize_text_field($input['font_display'])
        ];
    }

    public function render_section() {
        echo '<p>'.__('Select your preferred Bangla font for Bengali text.', 'bangla-fonts-library').'</p>';
    }

    public function render_font_select() {
        $options = get_option('bfl_settings');
        $fonts = $this->get_available_fonts();
        
        echo '<select name="bfl_settings[selected_font]">';
        foreach ($fonts as $id => $name) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($id),
                selected($options['selected_font'], $id, false),
                esc_html($name)
            );
        }
        echo '</select>';
    }

    public function render_font_display() {
        $options = get_option('bfl_settings');
        $choices = [
            'swap' => __('Swap (immediately shows fallback)', 'bangla-fonts-library'),
            'block' => __('Block (hides text while loading)', 'bangla-fonts-library'),
            'fallback' => __('Fallback (short block then swap)', 'bangla-fonts-library'),
            'optional' => __('Optional (only if available)', 'bangla-fonts-library')
        ];
        
        echo '<select name="bfl_settings[font_display]">';
        foreach ($choices as $val => $label) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($val),
                selected($options['font_display'], $val, false),
                esc_html($label)
            );
        }
        echo '</select>';
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Bangla Fonts Library', 'bangla-fonts-library'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('bfl_settings_group');
                do_settings_sections('bfl-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function enqueue_assets() {
        wp_enqueue_style('bfl-frontend', BFL_URL . 'css/frontend.css', array(), '1.0.0');
        wp_enqueue_script('bfl-autodetect', BFL_URL.'js/autodetect.js', ['jquery'], BFL_VERSION, true);
        
        $options = get_option('bfl_settings');
        $font_css = $this->generate_font_css($options['selected_font'], $options['font_display']);
        
        wp_add_inline_style('bfl-frontend', $font_css);
        wp_localize_script('bfl-autodetect', 'bflSettings', [
            'font' => $options['selected_font']
        ]);
    }

    private function generate_font_css($font_name, $font_display) {
        $fonts = $this->get_available_fonts();
        if (!isset($fonts[$font_name])) {
            $font_name = 'solaimanlipi';
        }
        
        $css = ":root { --bfl-font: '{$font_name}'; }\n";
        $css .= "@font-face {
            font-family: '{$font_name}';
            src: url('".BFL_URL."assets/fonts/{$font_name}-regular.ttf') format('truetype');
            font-display: {$font_display};
        }";
        
        return $css;
    }

    private function get_available_fonts() {
        return [
            'adarsholipi' => 'AdarshoLipi',
            'bangla' => 'Bangla',
            'ekushey-lohit' => 'Ekushey lohit',
            'kalpurush' => 'Kalpurush',
            'likhan' => 'Likhan',
            'lohit-bengali' => 'Lohit Bengali',
            'mukti-narrow' => 'Mukti Narrow',
            'nikosh' => 'Nikosh',
            'siyam-rupali' => 'Siyam Rupali',
            'solaimanlipi' => 'SolaimanLipi'
        ];
    }
}