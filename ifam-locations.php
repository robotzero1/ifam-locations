<?php

/**
 * Plugin Name: iFam Locations
 * Description: Travel locations and weather for the AI family. Shows the current and next location in the site header.
 * Version: 1.0
 * Author: Andrew McCabe
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once(plugin_dir_path(__FILE__) . 'includes/class-ifam-locations-db.php');
require_once(plugin_dir_path(__FILE__) . 'includes/class-ifam-locations-admin.php');
require_once(plugin_dir_path(__FILE__) . 'includes/class-ifam-locations-header.php');
require_once(plugin_dir_path(__FILE__) . 'includes/class-ifam-locations-endpoints.php');

class Ifam_Locations
{

    const VERSION = '1.0';

    public function __construct()
    {
        register_activation_hook(__FILE__, [Ifam_Locations_DB::class, 'create_tables']);
        add_action('plugins_loaded', [self::class, 'load_textdomain']);
        add_action('admin_menu', [Ifam_Locations_Admin::class, 'menu']);
        add_action('rest_api_init', [Ifam_Locations_Endpoints::class, 'register_rest_routes']);
        add_action('init', [self::class, 'register_blocks']);
        add_action('admin_enqueue_scripts', [self::class, 'enqueue_scripts']);
        add_action('wp_ajax_ifam_locations_reorder', [self::class, 'ajax_reorder']);
    }

    public static function load_textdomain()
    {
        load_plugin_textdomain('ifam-locations', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    public static function enqueue_scripts()
    {
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_style('ifam-locations-admin', plugins_url('assets/css/admin.css', __FILE__), [], self::VERSION);
        wp_enqueue_script('ifam-locations-admin', plugins_url('assets/js/admin.js', __FILE__), ['jquery', 'jquery-ui-sortable'], self::VERSION, true);

        wp_localize_script('ifam-locations-admin', 'ifamLocations', [
            'restUrl' => esc_url_raw(rest_url('locations/v1/')),
            'nonce'   => wp_create_nonce('wp_rest'),
        ]);
    }

    /**
     * Register our custom blocks.
     */
    public static function register_blocks()
    {
        $blocks_dir = plugin_dir_path(__FILE__) . 'blocks/';
        $block_dirs = glob($blocks_dir . '*', GLOB_ONLYDIR);

        foreach ($block_dirs as $block_dir) {
            // Look for block.json in the BUILD directory, not root
            $build_json = $block_dir . '/build/block.json';

            if (file_exists($build_json)) {
                // Register using the build directory
                register_block_type($block_dir . '/build');
            }
        }
    }

}

new Ifam_Locations();

