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
    const POSTCODE_FAILURE = 'The host base postcode is not recognised, therefore the options update failed!';

    public static function update_options($post) {
        // note: incoming $post is UNFILTERD / UNSANITIZED $_POST data.
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
                case 'zws_contacts_database_plugin_google_map_zoom':
                    $existing_options[$key] = apply_filters('zws_filter_validate_integer', $value);
                    break;
                case 'zws_contacts_database_plugin_base_postcode':
                    // get coordiates of the postcode and add that (if it has been changed) ...
                    if ($existing_options[$key] !== apply_filters('zws_filter_sanitize_postcode', $value)) {
                        $existing_options['zws_contacts_database_plugin_base_coordinates'] = self::getBaseCoordinates(
                                        apply_filters('zws_filter_sanitize_postcode', $value));
                        // return false (set options failed and don't update any) if the base coordinates were not set.
                        if ($existing_options['zws_contacts_database_plugin_base_coordinates'] == false ||
                                empty($existing_options['zws_contacts_database_plugin_base_coordinates']) ||
                                !is_array($existing_options['zws_contacts_database_plugin_base_coordinates'])) {
                            error_log('returning false');
                            return false;
                        }
                    }
                    // record the postcode in the db too
                    $existing_options[$key] = apply_filters('zws_filter_sanitize_postcode', $value);
                    break;
                case 'zws_contacts_database_plugin_admin_email':
                    // input is space separated email addresses. Explode string into array and set if value has changed.
                    if ($existing_options['zws_contacts_database_plugin_admin_email'] !== apply_filters('zws_filter_basic_sanitize', $value)) {
                        // explode to array
                        $emails = explode(' ', apply_filters('zws_filter_basic_sanitize', $value));
                        // strip any extra spaces and validate as emails
                        $validated_emails = array();
                        foreach ($emails as $k => $email) {
                            // trim off any extra whitespace
                            $email = trim($email);
                            // add validated to new array
                            if (is_email($email)) {
                                array_push($validated_emails, $email);
                            }
                        }
                        if (!empty($validated_emails)) {
                            $existing_options['zws_contacts_database_plugin_admin_email'] = $validated_emails;
                        } else {
                            $existing_options['zws_contacts_database_plugin_admin_email'] = '';
                        }
                    }
                    break;
                case 'zws_contacts_database_remove_data':
                    // this is an option of it's own, therefore do not add to the new options array
                    $remove_data = apply_filters('zws_filter_basic_sanitize', $value);
                    break;
                case 'zws_contacts_database_plugin_reg_email_from':
                    // break into name and email, validate it's an email, and store as array.
                    $safe_value = apply_filters('zws_filter_basic_sanitize', $value);
                    $email_array = explode(',', $safe_value);
                    if (is_email(trim($email_array[1]))) {
                        $existing_options['zws_contacts_database_plugin_reg_email_from'] = $email_array;
                    }
                    break;
                case 'zws_contacts_database_plugin_reg_email_subject':
                    // validate it's no more than 25 characters
                    $safe_value = apply_filters('zws_filter_basic_sanitize', $value);
                    $existing_options['zws_contacts_database_plugin_reg_email_subject'] = substr($safe_value, 0, 25);
                    break;
                case 'zws_contacts_database_plugin_reg_email':
                    // ensure text is sanitised but allow linebreaks to be retained.
                    $email = apply_filters('zws_filter_text_with_linebreak', $value);
                    // write to file
                    ////////////// ....... To Do .......
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

    private static function getBaseCoordinates($postcode) {
        // returns an array for the coordinates (lat, lng).
        require_once(__DIR__ . '/QueryAPI.php');
        $base_url = 'https://maps.googleapis.com/maps/api/geocode/json';
        $path = '?address=' . $postcode . '&language=en-EN&sensor=false';
        $result = \ZwsContactsDatabase\QueryAPI::makeQuery($base_url, $path);
        // if successful return and there are some results ...
        if ($result !== false && !empty($result['returned_data']['results'])) {
            // grab and return the lat and lng
            return array(0 =>
                $result['returned_data']
                ['results']
                [0]
                ['geometry']
                ['location']
                ['lat'],
                1 =>
                $result['returned_data']
                ['results']
                [0]
                ['geometry']
                ['location']
                ['lng']);
        }
        echo '<strong style="display:block;margin-top:1em;color:red;font-size:1.5em";>' . self::POSTCODE_FAILURE . '</strong>';
        return false;
    }

}
