<?php

namespace ZwsContactsDatabase;

/**
 * Set options file for ZWS Contacts Database
 *
 * @copyright Copyright (c) 2015, Zaziork Web Solutions
 * @license This plugin uses the Composer library - see composer-license.txt
 * @author    Zaziork Web Solutions
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.zaziork.com/
 */
Class SetOptions {

    const OPTIONS_LABEL = 'zws_contacts_database_options';

    public static function update_options($post) {
        $remove_data = get_site_option('zws_contacts_database_remove_data');
        // grab existing options
        $existing_options = get_site_option(self::OPTIONS_LABEL);
        // iterate POSTed options, filter appropriately, and update array
        foreach ($post as $key => $value) {
            $key = sanitize_text_field($key);
            switch ($key) {
                case 'zws_api_consumer_memcached_period':
                    $existing_options[$key] = apply_filters('zws_filter_validate_integer', $value);
                    break;
                case 'zws_contacts_database_remove_data':
                    // this is an option of it's own, therefore do not add to the new options array
                    $remove_data = apply_filters('zws_filter_basic_sanitize', $value);
                default:
                    $existing_options[$key] = apply_filters('zws_filter_basic_sanitize', $value);
                    break;
            }
        }

        // update options array with new version
        $update_options_array = update_site_option(self::OPTIONS_LABEL, $existing_options);
        $update_remove_data = update_site_option('zws_contacts_database_remove_data', $remove_data);
        // return true if either of the updates change anything, or false if not.
        return true ? $update_options_array || $update_remove_data : false;
    }

}
