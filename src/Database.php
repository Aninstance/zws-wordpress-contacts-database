<?php

namespace ZwsContactsDatabase;

/**
 * Installation file for ZWS  Contacts Database
 *
 * @copyright Copyright (c) 2015, Zaziork Web Solutions
 * @author    Zaziork Web Solutions
 * @license This plugin uses the Composer library - see composer-license.txt
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.zaziork.com/zws-contacts-database-plugin/
 */
Class Database {

    const OPTIONS_LABEL = 'zws_contacts_database_options';

    public static function update_database() {
        // increment this when database structure changed or name changed
        $db_version = '1.0';

// updated database 
        global $wpdb;
        $stored_table_name = get_site_option(self::OPTIONS_LABEL)['zws_contacts_database_plugin_table_name'];
        $installed_ver = get_site_option(self::OPTIONS_LABEL)['zws_contacts_database_plugin_db_version'];
        if ($installed_ver !== $db_version) {
            $table_name = $wpdb->prefix . $stored_table_name;
            $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        first_name varchar(255) DEFAULT '' NOT NULL,
        last_name varchar(255) DEFAULT '' NOT NULL,
        postcode varchar(8) DEFAULT '' NOT NULL,
        lat varchar(15) DEFAULT '' NOT NULL,
        lng varchar(15) DEFAULT '' NOT NULL,
        phone varchar(20) NOT NULL,
        email varchar(255) DEFAULT '' NOT NULL,
        max_radius mediumint(9) NOT NULL,
        extra_info varchar(255),
        pp_accepted tinyint(1) DEFAULT '0' NOT NULL,
        CONSTRAINT uc_individuals UNIQUE (phone,email),
        PRIMARY KEY  id (id)
	);";
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta($sql);
// update the stored db version
            $new_options = self::get_updated_options_array($db_version);
            update_site_option(self::OPTIONS_LABEL, $new_options);
// return for unit testing
            return true;
        }
    }

    private static function get_updated_options_array($db_version) {
// grab options
        $opts = get_site_option(self::OPTIONS_LABEL);
// update with new
        $opts['zws_contacts_database_plugin_db_version'] = $db_version;
        return $opts;
    }

    public static function insert($safe_values) {
        $saved_table_name = get_site_option(self::OPTIONS_LABEL)['zws_contacts_database_plugin_table_name'];
        if (is_array($safe_values)) {
            global $wpdb;
            $table_name = $wpdb->prefix . $saved_table_name;
            // insert data
            $safe_values['time'] = current_time('mysql');
            return $wpdb->insert($table_name, $safe_values);
        }
        return false;
    }

    public static function getAllRecords() {
        // setup database connection options
        $saved_table_name = get_site_option(self::OPTIONS_LABEL)['zws_contacts_database_plugin_table_name'];
        global $wpdb;
        $table_name = $wpdb->prefix . $saved_table_name;
        // grab the data
        //$sql = $my_wpdb->prepare("SELECT * FROM $table_name ORDER BY %s;", 'id');
        $sql = "SELECT * FROM " . $table_name . " ORDER BY id";
        return $wpdb->get_results($sql);
    }

}
