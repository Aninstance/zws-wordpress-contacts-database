<?php

namespace ZwsContactsDatabase;

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
        $set_size = count($set);
        $page = 1;
        // get the page number to display
        if (isset($_GET['contacts_page'])) {
            $page = apply_filters('zws_filter_validate_integer', $_GET['contacts_page']);
        }
        // display page 
        echo '<div class="zws-contacts-database-all-entries"><ul class="zws-contacts-database-display-all-list">';
        foreach (array_slice($set, (($page * $page_size) - $page_size), ($page * $page_size)) as $key => $value) {
            echo '<li><ul class="zws-contacts-database-display-all-inner-list">';
            foreach ($value as $entry => $entry_value) {
                // do some specific formatting for certain fields
                switch (apply_filters('zws_filter_basic_sanitize', $entry)) {
                    case 'email':
                        echo '<li>Email : <a href="mailto:' . sanitize_email($entry_value) . '">' . sanitize_email($entry_value) . '</a></li>';
                        break;
                    case 'time':
                        break;
                    default:
                        echo '<li>' . apply_filters('zws_filter_basic_sanitize', $entry) .
                        ' : ' .
                        nl2br(stripslashes(apply_filters('zws_filter_text_with_linebreak', $entry_value))) . '</li>';
                        break;
                }
            }
            echo '</ul></li>';
        }
        echo '</ul></div>';
        // create index and return true if successful
        return true ? self::create_index($set_size, $page_size, $page_index_batch_size) : false;
    }

    private static function create_index($set_size, $page_size, $page_index_batch_size) {

        // function to create and display the index of page numbers.
        $number_of_pages = ceil($set_size / $page_size);

        // sub divide pages into batches and iterate
        if (!empty($_GET['page_batch'])) {
            $current_batch = 1 ?
                    apply_filters('zws_filter_validate_integer', $_GET['page_batch']) == 0 :
                    apply_filters('zws_filter_validate_integer', $_GET['page_batch']);
        } else {
            $current_batch = 1;
        }
        // set up div
        echo '<div class="zws-contacts-database-index">';
        // add back link
        if ($current_batch > 1) {
            $back_button_query_string = self::set_url_query(array('page_batch=', ($current_batch - 1 )));
            echo '<a href="' . $back_button_query_string . '"><- Back</a>';
        }
        // set the initial page start, according to the current batch
        $page_counter = (($current_batch * $page_index_batch_size ) - $page_index_batch_size) + 1;
        // for each batch, echo the page numbers with their links
        for ($i = 0; $i < $page_index_batch_size; $i++) {
            $page_url_query_string = self::set_url_query(array('contacts_page', $page_counter));
            echo '&nbsp;|&nbsp;<a href="' . $page_url_query_string . '">' . $page_counter . '</a>';
            $page_counter++;
        }
        // add forward button
        if ($current_batch < ceil($set_size / $page_index_batch_size)) {
            $forward_button_query_string = self::set_url_query(array('page_batch', ($current_batch + 1)));
            echo '<a href="' . $forward_button_query_string . '">&nbsp;|&nbsp;Forward -></a>';
        }
        // end div
        echo '</div>';
        return true;
    }

    // My helpers
    private static function set_url_query($new_query) {
        $param_name = $new_query[0];
        $param_value = $new_query[1];
        // regex for replacing existing parameter
        $pattern = '/(.*?)(' . $param_name . '=[^&]*)(.*$)/i';
        $replacement = '$1' . $param_name . '=' . $param_value . '$3';
        // if no query string in current url
        if (empty($_SERVER['QUERY_STRING'])) {
            return esc_url($_SERVER['REQUEST_URI'] . '?' . $param_name . '=' . $param_value);
        } else {
            // if new query name already exists in string
            if (strpos(esc_url($_SERVER['QUERY_STRING']), $param_name)) {
                return preg_replace($pattern, $replacement, esc_url($_SERVER['REQUEST_URI']));
            } else {
                return esc_url(str_replace($_SERVER['QUERY_STRING'], $_SERVER['QUERY_STRING'] .
                                '&' .
                                $param_name .
                                '=' .
                                $param_value, $_SERVER['REQUEST_URI']));
            }
        }
    }

}
