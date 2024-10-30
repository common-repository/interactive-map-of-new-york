<?php
/*
Plugin Name: New York Map ORG
Plugin URI: https://www.wpmapplugins.com/states/interactive-map-of-new-york-wordpress-plugin.html
Description: Customize each county (colors, link, etc) through the dashboard and use the shortcode in your page.
Version: 2.7
Author: WP Map Plugins
Author URI: https://www.wpmapplugins.com/
Text Domain: nyus-map-org
Domain Path: /languages
*/

declare(strict_types=1);

namespace NYUSMap_ORG;

use NYUSMap_ORG\NYUSMap_ORG;

if (!defined('NYUSMAP_ORG_VERSION')) {
    define('NYUSMAP_ORG_VERSION', '2.7');
}

if (!defined('NYUSMAP_ORG_DIR')) {
    define('NYUSMAP_ORG_DIR', plugin_dir_path(__FILE__));
}

if (!defined('NYUSMAP_ORG_URL')) {
    define('NYUSMAP_ORG_URL', plugin_dir_url(__FILE__));
}

(new NYUSMap_ORG())->init();

class NYUSMap_ORG {

    const PLUGIN_NAME = 'New York Map ORG';

    private $options = null;

    public function init() {
        $this->initActions();
        $this->initShortcodes();
        $this->initOptions();
    }

    private function initActions() {
    	if( !function_exists('wp_get_current_user') ) {
            include(ABSPATH . "wp-includes/pluggable.php");
        }
        add_action( 'admin_menu', array($this, 'addOptionsPage') );
        add_action( 'admin_footer', array($this, 'addJsConfigInFooter') );
        add_action( 'admin_enqueue_scripts', array($this, 'initAdminScript') );
        add_action( 'init', array($this, 'loadTextdomain') );
    }

    private function initShortcodes() {
        add_shortcode('nyus_map_org', array($this, 'NYUSMap_ORGShortcode'));
    }

    private function initOptions() {
        $defaultOptions = $this->getDefaultOptions();
        $this->options  = get_option('nyus_map_org');

        if (current_user_can( 'manage_options' )){
            $this->updateOptions($defaultOptions);
        }

        if (!is_array($this->options)) {
            $this->options = $defaultOptions;
        }

        $this->prepareOptionsListForTpl();
    }

    public function addJsConfigInFooter() {
        include __DIR__ . "/includes/js-config.php";
    }

    public function addOptionsPage() {
        add_menu_page(
            NYUSMap_ORG::PLUGIN_NAME,
            NYUSMap_ORG::PLUGIN_NAME,
            'manage_options',
            'nyus-map-org',
            array($this, 'optionsScreen'),
            NYUSMAP_ORG_URL . 'public/images/map-icon.png'
        );
    }

    /**
     * @return array
     */
    private function getDefaultOptions() {
        $default = array(
            'nyusbrdrclr_org'    => '#6B8B9E',
        );

        $areas = array(
            'ALBANY', 'ALLEGANY', 'BRONX', 'BROOME', 'CATTARAUGUS', 'CAYUGA', 'CHAUTAUQUA', 'CHEMUNG', 'CHENANGO', 'CLINTON', 'COLUMBIA', 'CORTLAND', 'DELAWARE', 'DUTCHESS', 'ERIE', 'ESSEX', 'FRANKLIN', 'FULTON', 'GENESEE', 'GREENE', 'HAMILTON', 'HERKIMER', 'JEFFERSON', 'KINGS', 'LEWIS', 'LIVINGSTON', 'MADISON', 'MONROE', 'MONTGOMERY', 'NASSAU', 'NEW YORK', 'NIAGARA', 'ONEIDA', 'ONONDAGA', 'ONTARIO', 'ORANGE', 'ORLEANS', 'OSWEGO', 'OTSEGO', 'PUTNAM', 'QUEENS', 'RENSSELAER', 'RICHMOND', 'ROCKLAND', 'ST. LAWRENCE', 'SARATOGA', 'SCHENECTADY', 'SCHOHARIE', 'SCHUYLER', 'SENECA', 'STEUBEN', 'SUFFOLK', 'SULLIVAN', 'TIOGA', 'TOMPKINS', 'ULSTER', 'WARREN', 'WASHINGTON', 'WAYNE', 'WESTCHESTER', 'WYOMING', 'YATES'
        );

        foreach ($areas as $k => $area) {
            $default['upclr_' . ($k + 1)]  = '#E0F3FF';
            $default['ovrclr_' . ($k + 1)] = '#8FBEE8';
            $default['dwnclr_' . ($k + 1)] = '#477CB2';
            $default['url_' . ($k + 1)]    = '';
            $default['turl_' . ($k + 1)]   = '_self';
            $default['enbl_' . ($k + 1)]   = 1;
        }

        return $default;
    }

    private function updateOptions(array $defaultOptions) {
        if (isset($_POST['nyus_map_org']) && isset($_POST['submit-clrs'])) {
            $i = 1;
            while (isset($_POST['url_' . $i])) {
                $_POST['upclr_' . $i]  = $_POST['upclr_all'];
                $_POST['ovrclr_' . $i] = $_POST['ovrclr_all'];
                $_POST['dwnclr_' . $i] = $_POST['dwnclr_all'];

                $i++;
            }

            update_option('nyus_map_org',$_POST);

            $this->options = $_POST;
        }

        if (isset($_POST['nyus_map_org']) && isset($_POST['submit-url'])) {
            $i = 1;
            while (isset($_POST['url_' . $i])) {
                $_POST['url_' . $i]  = $_POST['url_all'];
                $_POST['turl_' . $i] = $_POST['turl_all'];

                $i++;
            }

            update_option('nyus_map_org',$_POST);

            $this->options = $_POST;
        }

        if (isset($_POST['nyus_map_org']) && !isset($_POST['preview_map'])) {
            update_option('nyus_map_org',$_POST);

            $this->options = $_POST;
        }

        if (isset($_POST['nyus_map_org']) && isset($_POST['restore_default'])) {
            update_option('nyus_map_org', $defaultOptions);

            $this->options = $defaultOptions;
        }
    }

    private function prepareOptionsListForTpl() {
        $this->options['prepared_list'] = array();
        $i                              = 1;
        while (isset($this->options['url_' . $i])) {
            $this->options['prepared_list'][] = array(
                'index'  => $i,
                'url'    => $this->options['url_' . $i],
                'turl'   => $this->options['turl_' . $i],
                'upclr'  => $this->options['upclr_' . $i],
                'ovrclr' => $this->options['ovrclr_' . $i],
                'dwnclr' => $this->options['dwnclr_' . $i],
                'enbl'   => isset($this->options['enbl_' . $i]),
            );

            $i++;
        }
    }

    public function NYUSMap_ORGShortcode() {
        wp_enqueue_style('nyus-map-org-style-frontend', NYUSMAP_ORG_URL . 'public/css/map-style.css', false, '1.0', 'all');
        wp_enqueue_script('nyus-map-org-interact', NYUSMAP_ORG_URL . 'public/js/map-interact.js?t=' . time(), array('jquery'), 10, '1.0', true);

        ob_start();

        include __DIR__ . "/includes/map.php";
        include __DIR__ . "/includes/js-config.php";

        return ob_get_clean();
    }

    public function optionsScreen() {
        include __DIR__ . "/includes/admin.php";
    }

    public function initAdminScript() {
        if ( current_user_can( 'manage_options') && ( esc_attr(isset($_GET['page'])) && esc_attr($_GET['page']) == 'nyus-map-org') ):
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_style('thickbox');
            wp_enqueue_script('thickbox');
            wp_enqueue_script('media-upload');

            wp_enqueue_style('nyus-map-org-dashboard-style', NYUSMAP_ORG_URL . 'public/css/dashboard-style.css', false, '1.0', 'all');
            wp_enqueue_style('nyus-map-org-style', NYUSMAP_ORG_URL . 'public/css/map-style.css', false, '1.0', 'all');
            wp_enqueue_style('wp-tinyeditor', NYUSMAP_ORG_URL . 'public/css/tinyeditor.css', false, '1.0', 'all');

            wp_enqueue_script('nyus-map-org-interact', NYUSMAP_ORG_URL . 'public/js/map-interact.js?t=' . time(), array('jquery'), 10, '1.0', true);
            wp_enqueue_script('nyus-map-org-tiny.editor', NYUSMAP_ORG_URL . 'public/js/editor/tinymce.min.js', 10, '1.0', true);
            wp_enqueue_script('nyus-map-org-script', NYUSMAP_ORG_URL . 'public/js/editor/scripts.js', array('wp-color-picker'), false, true);
        endif;
    }
    
    public function loadTextdomain() {
        load_plugin_textdomain( 'nyus-map-org', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }
}
