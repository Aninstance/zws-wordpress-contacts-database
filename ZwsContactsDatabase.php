<?php

namespace ZwsContactsDatabase;

/**
 * Plugin Name: ZWS Contacts Database
 * Plugin URI: https://www.zaziork.com/wp-zws-database-creator
 * Description: Plugin to create and administer a contacts database and calculate nearest contacts to any given UK postcode.
 * Version: 0.1
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

}

//require_once(__DIR__ . '/vendor/autoload.php');
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