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

    public static function paginate($set, $page_size = 10, $page_index_batch_size = 5) {
        require_once (__DIR__ . '/Helpers.php');
        $set_size = count($set);
        $page = 1;
        // get the page number to display
        if (isset($_GET['contacts_page'])) {
            $page = apply_filters('zws_filter_validate_integer', $_GET['contacts_page']);
        }
        // display page 
        $dash_url = Zelp::set_url_query(array('show_all' => 'false'));
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
                        . '<span class="zws-db-data" style="' . Zelp::getCss('data_style_tag') . '"><a href="tel:' . apply_filters('zws_filter_validate_integer', $entry_value) . '" style="' . Zelp::getCss('link_style_tag') . '">'
                        . apply_filters('zws_filter_validate_integer', $entry_value) . '</a></span></li>';
                        break;
                    case 'id' :
                        echo '<li class="zws-contacts-database-display-all-inner-list-li" style="' . Zelp::getCss('list_style_tag') . '">'
                        . '<span class="zws-db-label" style="' . Zelp::getCss('label_style_tag') . '">User ID :</span>'
                        . '<span class="zws-db-data" style="' . Zelp::getCss('data_style_tag') . '">' . apply_filters('zws_filter_validate_integer', $entry_value) . '</span></li>';
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
                echo '<li>Earliest :' . $earliest_time . '</li>';
                echo '<li style="border-bottom:1px solid silver;">Latest :' . $latest_time . '</li>';
            }
            echo '</ul></div></li>';
            echo '</ul></div></li>';
            $c++;
        }
        echo '</ul></div>';
        // create index and return true if successful
        return true ? self::create_index($set_size, $page_size, $page_index_batch_size) : false;
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
            $back_button_query_string = \ZwsContactsDatabase\Helpers::set_url_query(array('page_batch' => ($current_batch - 1), 'contacts_page' => $page_counter - $page_index_batch_size));
            echo '<a class="zws-contacts-database-display-index" style="' . Zelp::getCss('page_index') . '" href="' . $back_button_query_string . '"><-Back</a>';
        }
        // for each batch, echo the page numbers with their links
        for ($i = 0; $i < $page_index_batch_size; $i++) {
            $page_url_query_string = \ZwsContactsDatabase\Helpers::set_url_query(array('contacts_page' => $page_counter));
            echo '&nbsp;<a class="zws-contacts-database-display-index" style="' . Zelp::getCss('page_index') . '" href="' . $page_url_query_string . '">' . $page_counter . '</a>';
            $page_counter++;
        }
        // add forward button
        if ($current_batch < ceil($number_of_pages / $page_index_batch_size)) {
            $forward_button_query_string = \ZwsContactsDatabase\Helpers::set_url_query(array('page_batch' => ($current_batch + 1), 'contacts_page' => $page_counter));
            echo '&nbsp;<a class="zws-contacts-database-display-index" style="' . Zelp::getCss('page_index') . '" href="' . $forward_button_query_string . '">Forward-></a>';
        }
        // end div
        echo '</div>';
        return true;
    }

}
