<?php

namespace ZwsContactsDatabase;

use ZwsContactsDatabase\Helpers as Zelp;

/**
 * Administration view file for ZWS Contacts Database
 *
 * @copyright Copyright (c) 2015, Zaziork Web Solutions
 * @license This plugin uses the Composer library - see composer-license.txt
 * @author    Zaziork Web Solutions
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.zaziork.com/
 */
Class AdminView {

    const GOOGLE_MAPS_API = 'https://maps.googleapis.com/maps/api/js';
    const OPTIONS_LABEL = 'zws_contacts_database_options';

    public static function dashboard() {

// check to ensure user has at least 'editor' privileges
        $user = wp_get_current_user();
        $allowed_roles = array('editor', 'administrator');
        if (!array_intersect($allowed_roles, $user->roles)) {
            self::display_error('not_authorised');
            return false;
        }

        // set success variable for return - will be changed on successful completion of action
        $success = null;

        // sanitize all $_GET
        if (!empty($_GET)) {
            $safe_attr = array();
            foreach ($_GET as $key => $value) {
                $safe_attr[apply_filters('zws_filter_basic_sanitize', $key)] = apply_filters('zws_filter_basic_sanitize', $value);
            }
        }

        /* navigation stuff */

        // if postback is set and is true, and show_all is not true
        if (!empty($safe_attr['postback']) && $safe_attr['postback'] == 'true') {
            if (empty($safe_attr['show_all']) || $safe_attr['show_all'] !== 'true') {
                $posted_postcode = self::process_form();
                // if form processing successful, display nearest contacts. Returns true or false.
                $success = true ? self::display_nearest($posted_postcode) : false;
            }
        }
        // if show_all is true and postback is not set or false
        elseif (!empty($safe_attr['show_all']) && $safe_attr['show_all'] == 'true') {
            if (empty($safe_attr['postback']) || $safe_attr['postback'] !== 'true') {
                $success = true ? self::display_all_records() : false;
            }
        }
        // anything else (success still null), display the form
        elseif ($success == null) {
            $success = true ? self::display_form() : false;
        }


// return null if successful, or false if not
        if ($success) {
            return null;
        } else {
            self::display_error('no_data');
            return false;
        }
    }

    public static function display_form() {
        require_once(__DIR__ . '/Helpers.php');
// method to display the target postcode entry form.
        echo '<h3 style="' . Zelp::getCss('header_style_tag') . '">Search for nearest drivers</h3>';
        echo '<form action="' . Zelp::set_url_query(array('postback' => 'true')) . '" method="post">';
        echo '<p style="' . Zelp::getCss('label_style_tag') . '">Please submit target postcode below (no spaces - e.g. AB329BR)</p>';
        echo '<p><input type="text" placeholder="Postcode" name="target_postcode" pattern="[a-zA-Z0-9]+" maxlength="7" value="' . ( isset($_POST["target_postcode"]) ? esc_attr($_POST["target_postcode"]) : '' ) . '" size="8" /></p>';
        wp_nonce_field('submit_details_action', 'my_nonce_field');
        echo '<p><input type="submit" name="submitted" value="Submit"/></p>';
        echo '</form>';
        echo '<h3 style="' . Zelp::getCss('header_style_tag') . '">View entire database</h3>';
        $view_db_url = Zelp::set_url_query(array('show_all' => 'true', 'postback' => 'false'));
        echo '<div><button onclick="viewDatabase()">View the database</button><script>function viewDatabase() { window.location.href="' . html_entity_decode($view_db_url) . '";}</script></div>';
        return true;
    }

    public static function process_form() {
// checks if incoming POST, and that nonce was set, and that nonce details match
        if (isset($_POST['submitted']) && isset($_POST['my_nonce_field']) && wp_verify_nonce($_POST['my_nonce_field'], 'submit_details_action')) {
// set the target postcode from the form post
            $posted_postcode = strtoupper(trim(sanitize_text_field($_POST['target_postcode']), ' '));
            if (!empty($posted_postcode)) {
                return $posted_postcode;
            }
        }
        return false;
    }

    public static function display_error($reason) {
        require_once(__DIR__ . '/Helpers.php');
        switch ($reason) {
            case 'no_data':
                $error_string = '<h2>Nothing to see here ...</h2><p>Oh dear, it looks like there is nothing to display.</p>
            <p>This may be because there are currently no contacts available. Or, the more likely reason is that the postcode you entered is invalid.</p>
            <p>Please <a href="' . \ZwsContactsDatabase\Helpers::set_url_query(array('postback' => 'false')) . '">re-enter the postcode to try again</a>!</p>';
                break;
            case 'not_authorised':
                $error_string = '<h2>Access denied ...</h2><p>It seems you are not logged in as an administrative user. Please log in and try again.</p>';
                break;
            default:
                $error_string = '<h2>Unspecified error ... </h2><p>Oooops, an unspecified error has occurred. Please report this to the website administrator.<p>';
                break;
        }
        echo $error_string;
    }

    public static function display_nearest($target_postcode) {
// check params have been passed
        if (!isset($target_postcode)) {
            return false;
        }
        require_once(__DIR__ . '/DistanceCalculator.php');
        require_once(__DIR__ . '/Helpers.php');
        $options = get_site_option(self::OPTIONS_LABEL);
        $success = false;
        $how_many_contacts = 5;
        $contacts_array = \ZwsContactsDatabase\DistanceCalculator::nearestContacts($how_many_contacts, $target_postcode);
        if ($contacts_array !== false) {
            $contacts_array_safe = array();
// display the  elements
            echo '<div class="contact-list"><h2>' . $how_many_contacts . ' Closest Contacts</h2>';
            echo '<small><a href="' . \ZwsContactsDatabase\Helpers::set_url_query(array('postback' => 'false')) . '">Back to target submission form</a></small>';

            foreach ($contacts_array as $key => $value) {
// ensure variables from database are safe to output and add them to the contacts array. Only include contacts within their specified radius from target.
                if (sanitize_text_field($value['distance']) <= sanitize_text_field($value['max_radius'])) {
                    $id_safe = sanitize_text_field($key);
                    $contacts_array_safe[$id_safe]['distance'] = sanitize_text_field($value['distance']);
                    $contacts_array_safe[$id_safe]['postcode'] = sanitize_text_field($value['postcode']);
                    $contacts_array_safe[$id_safe]['lat'] = sanitize_text_field($value['lat']);
                    $contacts_array_safe[$id_safe]['lng'] = sanitize_text_field($value['lng']);
                    $contacts_array_safe[$id_safe]['first_name'] = sanitize_text_field($value['first_name']);
                    $contacts_array_safe[$id_safe]['last_name'] = sanitize_text_field($value['last_name']);
                    $contacts_array_safe[$id_safe]['phone'] = sanitize_text_field($value['phone']);
                    $contacts_array_safe[$id_safe]['email'] = sanitize_email($value['email']);
                    foreach (unserialize(DAYS) as $key => $day) {
                        $contacts_array_safe[$id_safe]['earliest_time_' . $day] = sanitize_text_field($value['earliest_time_' . $day]);
                        $contacts_array_safe[$id_safe]['latest_time_' . $day] = sanitize_text_field($value['latest_time_' . $day]);
                    }
                    $contacts_array_safe[$id_safe]['max_radius'] = sanitize_text_field($value['max_radius']);
                    $contacts_array_safe[$id_safe]['extra_info'] = nl2br(
                            stripslashes(
                                    apply_filters('zws_filter_text_with_linebreak', $value['extra_info'])));
                }
            }

            // add contacts array to map config
            $map_config['contacts_array_safe'] = $contacts_array_safe;
            // set up additional map config
            $map_config['target_postcode'] = $target_postcode;
            $map_config['contact_icon_url'] = $options['zws_contacts_database_plugin_map_contact_icon_url']; // icon URLs. ToDo: Make these user defined via options.
            $map_config['target_icon_url'] = $options['zws_contacts_database_plugin_map_target_icon_url'];
            $map_config['base_icon_url'] = $options['zws_contacts_database_plugin_map_base_icon_url'];
            $map_config['base_coordinates'] = array('57.4382622', '-2.0930657'); // ToDo: allow modificaiton via options
            $map_config['base_name'] = "The New Arc";
            $map_config['users_id'] = get_current_user_id();

            // display the map
            if (self::display_map($map_config)) {
                $success = true;
            }

            echo '<ol class="contact-info-list">';

            $c = 0; // counter to give each entry's available times fields a unique class name for jQuery
            foreach ($contacts_array_safe as $key => $value) {
// display textual elements
                echo '<li style="margin-bottom:1em;">';
                echo '<ul class="contact-info-list-inner">';
                echo '<li>Distance from target: ' . $value['distance'] . ' miles</li>';
                echo '<li>Name of contact: ' . stripslashes($value['first_name']) . ' ' . stripslashes($value['last_name']) . '</li>';
                echo '<li>Postcode of contact: ' . $value['postcode'] . '</li>';
                echo '<li>Phone of contact: <a href="tel:' . $value['phone'] . '">' . $value['phone'] . '</a></li>';
                echo '<li>Email of contact: <a href="mailto:' . $value['email'] . '">' . $value['email'] . '</a></li>';
                echo '<li>Extra notes: ' . $value['extra_info'] . '</li>';
                echo '<li><button class="modal_opener_' . $c . '">View available times</button><div class="zws-contacts-db-times-available">'
                . '<ul class="contact-info-list-inner_' . $c . '">';
                foreach (unserialize(DAYS) as $key => $day) {
                    if ($value['earliest_time_' . $day] == null || $value['latest_time_' . $day] == null) {
                        $earliest_time = $latest_time = 'Unavailable';
                    } else {
                        $earliest_time = $value['earliest_time_' . $day];
                        $latest_time = $value['latest_time_' . $day];
                    }
                    echo '<h3>' . ucfirst($day) . '</h3>';
                    echo '<li>Earliest :' . $earliest_time . '</li>';
                    echo '<li style="border-bottom:1px solid silver;">Latest :' . $latest_time . '</li>';
                }
                echo '</ul></div></li>';
                echo '</ul>';
                echo '</li>';
                $c++;
            }
            echo '</ol><div>';
        }
// return true if map has successfully displayed, otherwise false.
        return true ? $success : false;
    }

    public static function display_map($map_config) {
// check params have been passed
        if (!isset($map_config)) {
            return false;
        }
// method to display the Google map
// 
// create the javascript filename (add random id to script to ensure a cached version is not returned to client)
        $rand = rand();
        $user_id = $map_config['users_id'];
        $new_filename = plugins_url('/../inc/googlemaps_' . $user_id . '_' . $rand . '_.js', __FILE__);
        $map_config['new_script_uri'] = 'googlemaps_' . $user_id . '_' . $rand . '_.js';
// remove any existing script for this user (globbing any script with this user ID, followed by "anything else" - which includes the random no-cache string)
        $existing_file = glob(__DIR__ . '/../inc/' . 'googlemaps_' . $user_id . '_' . '*');
        if ($existing_file && !empty($existing_file)) {
            foreach ($existing_file as $key => $file) {
                unlink($file);
            }
        }
// generate the javascript file
        require_once(__DIR__ . '/JavascriptBuilder.php');
        if (\ZwsContactsDatabase\JavascriptBuilder::generate_js($map_config)) {
// load up the scripts
            wp_enqueue_script('google_maps_api', self::GOOGLE_MAPS_API);
            wp_enqueue_script('my_implementation', $new_filename, array('jquery'));

// define the display structure
            echo '<div id="map-canvas" style="width:500px;height:400px;background-color:#CCC;margin:1em;"></div>';
            return true;
        }
        return false;
    }

// display all records
    private static function display_all_records() {
// hard code resuts per page and index length for now. Add to user-defined options later?
        $page_size = 10;
        $page_index_batch_size = 5;
        $order_by = 'last_name'; // allow to be configured by users in options later?
// grab all registered users from db
        require_once(__DIR__ . '/Database.php');
        $result_set = \ZwsContactsDatabase\Database::getAllRecords($order_by);
// paginate and display the results
        require_once(__DIR__ . '/ZwsPaginator.php');
        return true ? \ZwsContactsDatabase\ZwsPaginator::paginate($result_set, $page_size, $page_index_batch_size) : false;
    }

}
