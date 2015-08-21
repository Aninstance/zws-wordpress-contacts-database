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
        // grab existing options
        $existing_options = get_site_option(self::OPTIONS_LABEL);
        // iterate POSTed options, filter appropriately, and update array
        foreach ($post as $key => $value) {
            $key = sanitize_text_field($key);
            switch ($key) {
                case 'zws_api_consumer_memcached_period':
                    $existing_options[$key] = apply_filters('\validate_integer', $value);
                    break;
                default:
                    $existing_options[$key] = apply_filters('\validate_sanitize_text_field', $value);
                    break;
            }
        }

        // update options array with new version
        return update_site_option(self::OPTIONS_LABEL, $existing_options);
    }

}