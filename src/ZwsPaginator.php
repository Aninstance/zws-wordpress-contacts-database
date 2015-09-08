<?php

namespace ZwsContactsDatabase;

use ZwsContactsDatabase\Helpers as Zelp;

/**
 * Zws Paginator utility, used in Zws Contacts Database
 *
 * @copyright Copyright (c) 2015, Zaziork Web Solutions
 * @license This plugin uses the Composer library - see composer-license.txt
 * @author    Zaziork Web Solutions
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.zaziork.com/
 */
Class ZwsPaginator {

    const MAPS_API_BASE_URL = 'https://maps.googleapis.com/maps/api/geocode/json';

    public static function paginate($set, $page_size = 10, $page_index_batch_size = 5) {
        require_once (__DIR__ . '/Helpers.php');
        // set up the page variables
        $set_size = count($set);
        $page = 1;
        // get the page number to display
        if (isset($_GET['contacts_page'])) {
            $page = apply_filters('zws_filter_validate_integer', $_GET['contacts_page']);
        }
        // display page 
        $dash_url = Zelp::set_url_query(array('show_all' => 'false', 'postback' => 'false'));
        echo '<div style="' . Zelp::getCss('label_style_tag') . '"><button onclick="viewDatabase()">Back to admin dashboard</button><script>function viewDatabase() { window.location.href="' . html_entity_decode($dash_url) . '";}</script></div>';
        echo '<div class="zws-contacts-database-all-entries"><span class="zws-contacts-database-all-entries-headline" style="' . Zelp::getCss('header_style_tag') . '">All Database Entries</span>'
        . '<ul class="zws-contacts-database-display-all-list" style="list-style:none";>';
        $c = 0; // set counter to append to modal classes to make each entry's class name unique for the jQuery.
        foreach (array_slice($set, (($page * $page_size) - $page_size), ($page * $page_size)) as $key => $value) {
            echo '<li><div class="zws-contacts-database-display-all-inner-div" style="' . Zelp::getCss('entry_style_tag') . '"><ul class="zws-contacts-database-display-all-inner-list" style="list-style:none;">';
            foreach ($value as $entry => $entry_value) {
                // do some specific formatting for certain fields
                switch (apply_filters('zws_filter_basic_sanitize', $entry)) {
                    case 'email':
                        echo '<li class="zws-contacts-database-display-all-inner-list-li" style="' . Zelp::getCss('list_style_tag') . '">'
                        . '<span class="zws-db-label" style="' . Zelp::getCss('label_style_tag') . '">Email :</span>'
                        . '<span class="zws-db-data" style="' . Zelp::getCss('data_style_tag') . '"><a href="mailto:' . sanitize_email($entry_value) . '" style="' . Zelp::getCss('link_style_tag') . '">'
                        . sanitize_email($entry_value) . '</a></span></li>';
                        break;
                    case 'phone' :
                        echo '<li class="zws-contacts-database-display-all-inner-list-li" style="' . Zelp::getCss('list_style_tag') . '">'
                        . '<span class="zws-db-label" style="' . Zelp::getCss('label_style_tag') . '">Phone :</span>'
                        . '<span class="zws-db-data" style="' . Zelp::getCss('data_style_tag') . '"><a href="tel:' . apply_filters('zws_filter_basic_sanitize', $entry_value) . '" style="' . Zelp::getCss('link_style_tag') . '">'
                        . apply_filters('zws_filter_basic_sanitize', $entry_value) . '</a></span></li>';
                        break;
                    case 'id' :
                        echo '<li class="zws-contacts-database-display-all-inner-list-li" style="' . Zelp::getCss('list_style_tag') . '">'
                        . '<span class="zws-db-label" style="' . Zelp::getCss('label_style_tag') . '">User ID :</span>'
                        . '<span class="zws-db-data" style="' . Zelp::getCss('data_style_tag') . '"><button id="user_mod_button_' . $c . '">' . apply_filters('zws_filter_validate_integer', $entry_value) . '</button></span></li>';
                        // user mod div
                        echo self::user_mod($c, $value, apply_filters('zws_filter_validate_integer', $entry_value));
                        break;
                    case 'first_name' :
                        echo '<li class="zws-contacts-database-display-all-inner-list-li" style="' . Zelp::getCss('list_style_tag') . '">'
                        . '<span class="zws-db-label" style="' . Zelp::getCss('label_style_tag') . '">First Name :</span>'
                        . '<span class="zws-db-data" style="' . Zelp::getCss('data_style_tag') . '">' . apply_filters('zws_filter_basic_sanitize', $entry_value) . '</span></li>';
                        break;
                    case 'last_name' :
                        echo '<li class="zws-contacts-database-display-all-inner-list-li" style="' . Zelp::getCss('list_style_tag') . '">'
                        . '<span class="zws-db-label" style="' . Zelp::getCss('label_style_tag') . '">Last Name :</span>'
                        . '<span class="zws-db-data" style="' . Zelp::getCss('data_style_tag') . '">' . apply_filters('zws_filter_basic_sanitize', $entry_value) . '</span></li>';
                        break;
                    case 'postcode' :
                        echo '<li class="zws-contacts-database-display-all-inner-list-li" style="' . Zelp::getCss('list_style_tag') . '">'
                        . '<span class="zws-db-label" style="' . Zelp::getCss('label_style_tag') . '">Post Code :</span>'
                        . '<span class="zws-db-data" style="' . Zelp::getCss('data_style_tag') . '">' . apply_filters('zws_filter_basic_sanitize', $entry_value) . '</span></li>';
                        break;
                    case 'max_radius' :
                        echo '<li class="zws-contacts-database-display-all-inner-list-li" style="' . Zelp::getCss('list_style_tag') . '">'
                        . '<span class="zws-db-label" style="' . Zelp::getCss('label_style_tag') . '">Maximum Travel Distance To Target :</span>'
                        . '<span class="zws-db-data" style="' . Zelp::getCss('data_style_tag') . '">' . apply_filters('zws_filter_validate_integer', $entry_value) . ' miles</span></li>';
                        break;
                    case 'extra_info' :
                        echo '<li class="zws-contacts-database-display-all-inner-list-li" style="' . Zelp::getCss('list_style_tag') . '">'
                        . '<span class="zws-db-label" style="' . Zelp::getCss('label_style_tag') . '">Extra Information :</span>'
                        . '<span class="zws-db-data" style="' . Zelp::getCss('data_style_tag') . '">' .
                        nl2br(stripslashes(apply_filters('zws_filter_text_with_linebreak', $entry_value))) . '</span></li>';
                        break;
                    case 'pp_accepted' :
                        break;
                    case 'time':
                        break;
                    case 'lat' :
                        break;
                    case 'lng' :
                        break;
                    default:
                        break;
                }
            }
            // times available
            echo '<li class="zws-contacts-database-display-all-inner-list-li" style="' . Zelp::getCss('list_style_tag_button_li') . '">'
            . '<button class="modal_opener_' . $c . '">View times that ' . apply_filters('zws_filter_basic_sanitize', $value->first_name) . ' is available</button>'
            . '<div class="zws-contacts-db-times-available">'
            . '<ul class="contact-info-list-inner_' . $c . '">';
            // available times field
            foreach (unserialize(DAYS) as $key => $day) {
                $set_obj_property_earliest = 'earliest_time_' . $day;
                $set_obj_property_latest = 'latest_time_' . $day;
                if (apply_filters('zws_filter_basic_sanitize', $value->$set_obj_property_earliest) == null || apply_filters('zws_filter_basic_sanitize', $value->$set_obj_property_latest) == null) {
                    $earliest_time = $latest_time = 'Unavailable';
                } else {
                    $earliest_time = $value->$set_obj_property_earliest;
                    $latest_time = $value->$set_obj_property_latest;
                }
                echo '<h3>' . ucfirst($day) . '</h3>';
                echo '<li>Earliest: ' . $earliest_time . '</li>';
                echo '<li style="border-bottom:1px solid silver;">Latest: ' . $latest_time . '</li>';
            }
            echo '</ul></div></li>';
            // end of user details list
            echo '</ul></div></li>';
            $c++;
        }
        echo '</ul></div>';
        // create index and return true if successful
        return true ? self::create_index($set_size, $page_size, $page_index_batch_size) : false;
    }

    private static function user_mod($list_position, $full_values_array, $user_id) {
        require_once(__DIR__ . '/Helpers.php');
        $return_string = '<div class="zws_contacts_db_user_mod_outer"><div class="zws_contacts_db_user_mod_' . $list_position . '">
                <div class="zws_contacts_db-update_form">
                <form action="' . Zelp::set_url_query(array('postback' => 'true')) . '" method="post">'
                . self::create_update_form_content($full_values_array) . ''
                // add nonce
                . wp_nonce_field('submit_details_action', 'my_nonce_field') .
                '<p><input type="submit" name="submitted" value="Submit"/></p>' .
                '</form></div></div></div>';
        return $return_string;
    }

    private static function create_update_form_content($values) {
// create the input form
        $form_content = '<h3>Contact details</h3><p>'
                . 'Contact first name (required) <br />'
                . '<input type="text" name="first_name" required="required" placeholder="First name" pattern="[a-zA-Z0-9]+" value="' . esc_attr($values->first_name) . '" size="40" />'
                . '</p>'
                . '<p>'
                . 'Contact last name (required) <br />'
                . '<input type="text" name="last_name" required="required" placeholder="Last name" pattern="[a-zA-Z0-9]+" value="' . esc_attr($values->last_name) . '" size="40" />'
                . '</p>'
                . '<p>'
                . 'Contact postcode (required - no spaces - e.g. AB329BR) <br />'
                . '<input type="text" name="postcode" required="required" placeholder="Postcode" pattern="[a-zA-Z0-9]+" maxlength="7" value="' . esc_attr($values->postcode) . '" size="8" />'
                . '</p>'
                . '<p>'
                . 'Contact phone number (required) <br />'
                . '<input type="text" name="phone" required="required" placeholder="Phone" pattern="[0-9]+" value="' . esc_attr($values->phone) . '" size="40" />'
                . '</p>'
                . '<p>'
                . 'Contact Email (required) <br />'
                . '<input type="email" name="email" required="required" placeholder="Email" value="' . esc_attr($values->email) . '" size="40" />'
                . '</p>'
                . '<h3>How far can contact cover?</h3><p>'
                . 'Distance from contact\'s home they would be prepared to cover (full miles, required)<br />'
                . '<input type="text" name="max_radius" required="required" placeholder="Distance" pattern="[0-9]+" value="' . esc_attr($values->max_radius) . '" size="9" />'
                . '</p>'
                . '<h3>When is the contact available?</h3><p style="display:inline-block;margin-bottom:1em;font-size:0.7em;">'
                . 'Times are in 24 hour clock format (e.g. 00:00 = midnight; 02:30 = 2.30am, 14:30 = 2.30pm).<br>'
                . '"Unavailable" indicates you are unavailable for the <strong>entire day</strong>.<br>'
                . 'By default, the options below are set to <strong>"Unavailable"</strong> every day. Please adjust as required!<br>'
                . 'Feel free to provide more detail in the "Extra information" section if necessary.</p>';
        foreach (unserialize(DAYS) as $value => $day) {
            $earliest_time_key = 'earliest_time_' . $day;
            $latest_time_key = 'latest_time_' . $day;
            if (esc_attr($values->$earliest_time_key) == 'UNAVL') {
                $values->$earliest_time_key = '';
            }
            $form_content .= '<p>'
                    . 'Times available on ' . ucfirst($day) . '<br>'
                    . '<span class="zws-contacts-database-split-input-class" style="display:inline-block;width:35%;margin-right:1em;">'
                    . 'Earliest available<br>'
                    . '<input id="zws-contacts-database-earlist-time-' . $day . '" required="required" type="text" name="earliest_time_' . $day . '" value="' . esc_attr($values->$earliest_time_key) . '" size="8" />'
                    . '</span><span class="zws-contacts-database-split-input-class" style="display:inline-block;width:35%;margin-right:1em;">'
                    . 'Latest available<br>'
                    . '<input id="zws-contacts-database-latest-time-' . $day . '" required="required" type="text" name="latest_time_' . $day . '" value="' . esc_attr($values->$latest_time_key) . '"/>'
                    . '</span></p>';
        }
        $form_content .= '<h3>Additional information</h3><p>'
                . '<textarea rows="10" cols="35" name="extra_info" placeholder="Extra information">' . esc_attr($values->extra_info) . '</textarea>'
                . '</p>'
                . '<input type="hidden" name="id" id="id" value="' . esc_attr($values->id) . '"/>';
        return $form_content;
    }

    public static function process_form($post) {
        $safe_values = array();
// checks if incoming POST, and that nonce was set, and that nonce details match
        if (isset($post['my_nonce_field']) &&
                wp_verify_nonce(apply_filters('zws_filter_basic_sanitize', $post['my_nonce_field']), 'submit_details_action')) {
// sanitise values
            $safe_values['id'] = apply_filters('zws_filter_enforce_numeric', $post['id']);
            $safe_values['first_name'] = apply_filters('zws_filter_basic_sanitize', $post['first_name']);
            $safe_values['last_name'] = apply_filters('zws_filter_basic_sanitize', $post['last_name']);
            $safe_values['postcode'] = apply_filters('zws_filter_sanitize_postcode', $post['postcode']);
            $safe_values['phone'] = apply_filters('zws_filter_enforce_numeric', $post['phone']);
            $safe_values['email'] = apply_filters('zws_filter_basic_sanitize', $post['email']);
            $safe_values['max_radius'] = apply_filters('zws_filter_enforce_numeric', $post['max_radius']);
            $safe_values['extra_info'] = apply_filters('zws_filter_text_with_linebreak', $post['extra_info']);
            foreach (unserialize(DAYS)as $key => $day) {
                if (sanitize_text_field($post['earliest_time_' . $day]) !== 'Unavailable') {
                    $safe_values['earliest_time_' . $day] = apply_filters('zws_filter_basic_sanitize', $post['earliest_time_' . $day]);
                } else {
                    $safe_values['earliest_time_' . $day] = 'UNAVL';
                }
                $safe_values['latest_time_' . $day] = apply_filters('zws_filter_basic_sanitize', $_POST['latest_time_' . $day]);
            }
// query google maps api to get longitute and latitude for the postcode, to pull back from db when displayed on map
            require_once(__DIR__ . '/QueryAPI.php');
            $path = '?address=' . $safe_values['postcode'] . '&language=en-EN&sensor=false&key=AIzaSyBWWGk2H9NwjOcF07YRXDXBimae3x7Dk9Y';
            $data = \ZwsContactsDatabase\QueryAPI::makeQuery(self::MAPS_API_BASE_URL, $path);
            if ($data['returned_data'] && $data['returned_data']['status'] === 'OK') {
                if ($data['cached']) {
// error_log('THE DATA WAS CACHED ...'); // debug
                }
                $safe_values['lat'] = sanitize_text_field($data['returned_data']['results'][0]['geometry']['location']['lat']);
                $safe_values['lng'] = sanitize_text_field($data['returned_data']['results'][0]['geometry']['location']['lng']);
            } else {
                return self::failure_view();
            }

// send to database
            require_once(__DIR__ . '/Database.php');
            if (\ZwsContactsDatabase\Database::update($safe_values, array('id' => $safe_values['id']))) {
                return self::success_view();
            } else {
                return self::failure_view();
            }
        } else {
            return self::failure_view('nonce');
        }
    }

    private static function success_view() {
        require_once(__DIR__ . '/Helpers.php');
        $success_message = '<div class="zws-contacts-db-success-message" style="' . Zelp::getCss('zws-contacts-db-success-message') . '">'
                . '<strong>The database has been successfully updated.</strong></div>';
        return $success_message;
    }

    private static function failure_view($reason = null) {
        require_once(__DIR__ . '/Helpers.php');
        switch ($reason) {
            case 'nonce' :
                $message = '<div class="zws-contacts-db-failure-message" style="' . Zelp::getCss('zws-contacts-db-failure-message') . '"><p>Unfortunately, it does not appear that you are submitting the form from the correct URL!</p>'
                        . '<p>Please try again, but if you receive this message once more please contact support.</p></div>';
                break;
            default:
                $message = '<div class="zws-contacts-db-failure-message" style="' . Zelp::getCss('zws-contacts-db-failure-message') . '"><p>Unfortunately, an error occurred and the contact details have not been submitted. </p>'
                        . '<p>Please try again, but if you receive this message once more please contact support.</p></div>';
                break;
        }
        return $message;
    }

    private static function create_index($set_size, $page_size, $page_index_batch_size) {
        require_once (__DIR__ . '/Helpers.php');

        // function to create and display the index of page numbers.
        $number_of_pages = ceil($set_size / $page_size);

        // sub divide pages into batches and iterate
        if (!empty($_GET['page_batch'])) {
            $current_batch = apply_filters('zws_filter_validate_integer', $_GET['page_batch']);
        } else {
            $current_batch = 1;
        }
        // set the initial page start, according to the current batch
        $page_counter = (($current_batch * $page_index_batch_size ) - $page_index_batch_size) + 1;
        // set up div
        echo '<div class="zws-contacts-database-index">';
        // add back link
        if ($current_batch > 1) {
            // build query for batch
            $back_button_query_string = \ZwsContactsDatabase\Helpers::set_url_query(array('page_batch' => ($current_batch - 1), 'contacts_page' => $page_counter - $page_index_batch_size,  'postback' => 'false'));
            echo '<a class="zws-contacts-database-display-index" style="' . Zelp::getCss('page_index') . '" href="' . $back_button_query_string . '"><-Back</a>';
        }
        // for each batch, echo the page numbers with their links
        for ($i = 0; $i < $page_index_batch_size; $i++) {
            $page_url_query_string = \ZwsContactsDatabase\Helpers::set_url_query(array('contacts_page' => $page_counter, 'postback' => 'false'));
            echo '&nbsp;<a class="zws-contacts-database-display-index" style="' . Zelp::getCss('page_index') . '" href="' . $page_url_query_string . '">' . $page_counter . '</a>';
            $page_counter++;
        }
        // add forward button
        if ($current_batch < ceil($number_of_pages / $page_index_batch_size)) {
            $forward_button_query_string = \ZwsContactsDatabase\Helpers::set_url_query(array('page_batch' => ($current_batch + 1), 'contacts_page' => $page_counter, 'postback' => 'false'));
            echo '&nbsp;<a class="zws-contacts-database-display-index" style="' . Zelp::getCss('page_index') . '" href="' . $forward_button_query_string . '">Forward-></a>';
        }
        // end div
        echo '</div>';
        return true;
    }

}
