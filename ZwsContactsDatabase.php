<?php

namespace ZwsContactsDatabase;

define("DAYS", serialize(array(1 => 'mondays', 2 => 'tuesdays', 3 => 'wednesdays', 4 => 'thursdays', 5 => 'fridays', 6 => 'saturdays', 7 => 'sundays')));


/**
 * Plugin Name: ZWS Contacts Database
 * Plugin URI: https://www.zaziork.com/wp-zws-database-creator
 * Description: Plugin to create and administer a contacts database and calculate nearest contacts to any given UK postcode.
 * Version: 0.7
 * Author: Zaziork Web Solutions
 * Author URI: http://www.zaziork.com
 * Copyright (c) 2015 Zaziork Web Solutions. All rights reserved.
 * License: Plugin uses the Composer library - see composer-license.txt
 * License: ZWS Contacts Database Released under the GPL license: http://www.opensource.org/licenses/gpl-license.php
 *
 * @since     0.1
 * @copyright Copyright (c) 2015, Zaziork Web Solutions
 * @author    Zaziork Web Solutions
 * @license This plugin uses the Composer library - see composer-license.txt
 * @license ZWS Contacts Database license: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * **********************************************************************
 */
define("SHORTCODE_TAG_FORM", "zwscontactsdatabase_public_form");
define("SHORTCODE_TAG_RESULTS", "zwscontactsdatabase_results_page");

//define("PATH_TO_INC", plugins_url('ZwsContactsDatabase/inc/', __FILE__));

Class ZwsContactsDatabase {

    public static function run_installer() {
        require_once(__DIR__ . '/src/Installer.php');
        // run installer
        \ZwsContactsDatabase\Installer::install();
    }

    public static function run_admin() {
        require_once(__DIR__ . '/src/Admin.php');
        // run the menu page code
        \ZwsContactsDatabase\Admin::setup_menu();
    }

    public static function add_action_links($links) {
        $mylinks = array(
            '<a href="' . admin_url('admin.php?page=zws-database-creator') . '">Settings</a>',
        );
        return array_merge($mylinks, $links);
    }

    public static function load_scripts() {
        if (!is_admin()) {
            // set up our scripts
            $jquery_ui_js = plugins_url('/vendor/jquery/jquery-ui-1.11.4/jquery-ui.min.js', __FILE__);
            $jquery_time_modal_js = plugins_url('/inc/jquery_time_modal.js', __FILE__);
            $jquery_user_mod_modal_js = plugins_url('/inc/jquery_user_mod_modal.js', __FILE__);
            $jquery_timepicker_js = plugins_url('/vendor/jquery-timepicker/jquery.timepicker.min.js', __FILE__);
            $jquery_timepicker_init_js = plugins_url('/inc/jquery.timepicker.init.js', __FILE__);
            $jquery_delete_record_js = plugins_url('/inc/jquery.deleteRecord.js', __FILE__);
            $jquery_maps_with_places_js = 'https://maps.googleapis.com/maps/api/js?libraries=places&sensor=false';
            $jquery_geocomplete_js = plugins_url('/vendor/jquery.geocomplete/jquery.geocomplete.min.js', __FILE__);
            $jquery_geocomplete_init_js = plugins_url('/inc/jquery.geocomplete.js', __FILE__);
            wp_register_script('jquery_ui_js', $jquery_ui_js, array('jquery'));
            wp_register_script('jquery_time_modal_js', $jquery_time_modal_js, array('jquery_ui_js'));
            wp_register_script('jquery_user_mod_modal_js', $jquery_user_mod_modal_js, array('jquery_ui_js'));
            wp_register_script('jquery_timepicker_js', $jquery_timepicker_js, array('jquery_ui_js'));
            wp_register_script('jquery_timepicker_init_js', $jquery_timepicker_init_js, array('jquery_ui_js'));
            wp_register_script('jquery_delete_record_js', $jquery_delete_record_js, array('jquery_ui_js'));
            wp_register_script('jquery_places_library_js', $jquery_maps_with_places_js, array('jquery_ui_js'));
            wp_register_script('jquery_geocomplete_js', $jquery_geocomplete_js, array('jquery_places_library_js'));
            wp_register_script('jquery_geocomplete_init_js', $jquery_geocomplete_init_js, array('jquery_geocomplete_js'));
            wp_enqueue_script('jquery_ui_js');
            wp_enqueue_script('jquery_time_modal_js');
            wp_enqueue_script('jquery_user_mod_modal_js');
            wp_enqueue_script('jquery_timepicker_js');
            wp_enqueue_script('jquery_timepicker_init_js');
            wp_enqueue_script('jquery_delete_record_js');
            wp_enqueue_script('jquery_places_library');
            wp_enqueue_script('jquery_geocomplete_js');
            wp_enqueue_script('jquery_geocomplete_init_js');
        }
    }

    public static function load_styles() {
        // set up our scripts
        $jquery_ui_css = plugins_url('/vendor/jquery/jquery-ui-1.11.4/jquery-ui.min.css', __FILE__);
        $jquery_timepicker_css = plugins_url('/vendor/jquery-timepicker/jquery.timepicker.css', __FILE__);
        $zws_contacts_db_css = plugins_url('/inc/zws-contacts-database.css', __FILE__);
        wp_register_style('jquery_ui_css', $jquery_ui_css);
        wp_register_style('jquery_timepicker_css', $jquery_timepicker_css);
        wp_register_style('zws_contacts_db_css', $zws_contacts_db_css);
        wp_enqueue_style('jquery_ui_css');
        wp_enqueue_style('jquery_timepicker_css');
        wp_enqueue_style('zws_contacts_db_css');
    }

    // prevent caching in adminview
    public static function add_no_cache() {
        header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
        header("Pragma: no-cache"); //HTTP 1.0
    }

}

// autoload the vendor packages
require_once(__DIR__ . '/vendor/autoload.php');
// include the filters
require_once(__DIR__ . '/src/Filters.php');
// add action for our enqueued scripts and stylesheets
add_action('init', array('\ZwsContactsDatabase\ZwsContactsDatabase', 'load_scripts'));
add_action('init', array('\ZwsContactsDatabase\ZwsContactsDatabase', 'load_styles'));
// add action to send additional headers
add_action('send_headers', array('\ZwsContactsDatabase\ZwsContactsDatabase', 'add_no_cache'));
// add additional links on plugins page
add_filter('plugin_action_links_' . plugin_basename(__FILE__), array('\ZwsContactsDatabase\ZwsContactsDatabase', 'add_action_links'));
// create the administration page
add_action('admin_menu', array('\ZwsContactsDatabase\ZwsContactsDatabase', 'run_admin'));
// add the installer to the activation hook
register_activation_hook(__FILE__, array('\ZwsContactsDatabase\ZwsContactsDatabase', 'run_installer'));
// add the shortcodes
require_once(__DIR__ . '/src/View.php');
add_shortcode(SHORTCODE_TAG_FORM, array('\ZwsContactsDatabase\View', 'submission_form'));
require_once(__DIR__ . '/src/AdminView.php');
add_shortcode(SHORTCODE_TAG_RESULTS, array('ZwsContactsDatabase\AdminView', 'dashboard'));
