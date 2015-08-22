<?php

namespace ZwsContactsDatabase;

/**
 * Administration file for ZWS Contacts Database
 *
 * @copyright Copyright (c) 2015, Zaziork Web Solutions
 * @license This plugin uses the Composer library - see composer-license.txt
 * @author    Zaziork Web Solutions
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.zaziork.com/
 */
Class Admin {

    const OPTIONS_LABEL = 'zws_contacts_database_options';
    const MEMCACHED_KEYBASE = 'ZWS_CONTACTS_DATABASE_KEY';

    public static $notifications = array();

    public static function setup_menu() {
        add_menu_page('ZWS Contacts Database', 'ZWS Contacts Database', 'manage_options', 'zws-database-creator', array('\ZwsContactsDatabase\Admin',
            'settings')
        );
        add_action('admin_init', array('\ZwsContactsDatabase\Admin', 'settings_panel_fields'));
    }

    public static function settings() {

        // hande postbacks from form
        if (isset($_GET['postback'])) {
            // check to determine if this is form postback
            switch ($_GET['postback']) {
                case 'set_options':
                    require_once(__DIR__ . '/SetOptions.php');
                    if (isset($_POST)) {
                        // make the update. Returns true if successful, or false if not.
                        $updated = \ZwsContactsDatabase\SetOptions::update_options($_POST);
                    }
                    $updated ? self::$notifications['update_options'] = 'Options updated!' : self::$notifications['update_options'] = 'An error occurred!';
                    break;
                case 'clear_cache':
                    if (self::clear_cache()) {
                        self::$notifications['cache_cleared'] = 'Cache flushed!';
                    } elseif (self::clear_cache() === false) {
                        self::$notifications['cache_cleared'] = 'Cache not flushed!';
                    } else {
                        self::$notifications['cache_cleared'] = 'Memcached does not appear to be installed on your system!';
                    }
                    break;
                default:
                // carry on ...
            }
        }
        ?>

        <h1>ZWS Contacts Database Administration Page</h1>

        <!-- notifications section -->
        <div class="zws_contacts_database_notifications" style="margin:2em 0 0 2em;color:red;"><?php
            foreach (self::$notifications as $key => $value) {
                if (isset($value)) {
                    echo '<span class="zws_contacts_database_notfication_item" style="font-size:2em;">' . $value . '</span><br>';
                }
            }
            ?>
        </div>

        <!-- forms section -->
        <div class="forms"  style="position:relative;margin:1em;float:left;clear:left;">
            <div class="wrap" style="margin:1em;">
                <form method="post" action="<?php
                if ((isset($_GET['postback']) && $_GET['postback'] != 'set_options' ) || (!isset($_GET['postback']))) {
                    echo self::get_url() . '&postback=set_options';
                } else {
                    echo self::get_url();
                }
                ?>">
                          <?php
                          do_settings_sections("basic_options_section");
                          settings_fields("basic_options_section_group");
                          submit_button();
                          ?>          
                </form>
            </div>

            <!-- Clear cache button form  -->
            <div class="wrap" style="margin:1em;">
                <form method="post" action="<?php
                if (isset($_GET['postback']) && $_GET['postback'] != 'clear_cache' || (!isset($_GET['postback']))) {
                    echo self::get_url() . '&postback=clear_cache';
                } else {
                    echo self::get_url();
                }
                ?>">
                          <?php
                          do_settings_sections("clear_section_group");
                          submit_button('Manually Clear Cache Now');
                          ?>          
                </form>
            </div>

            <div class="footer" style="position:relative;float:left;clear:left;">
                <p>Thank you for using ZWS Contacts Database.  <a href="https://www.zaziork.com/donate/">Donations are much appreciated!</a></p>
            </div>
            <?php
            // return true if ran without errors, for unit testing purposes.
            return True;
        }

        // define form elements
        public static function google_api_key_form_field_element() {
            ?>
            <small class="zws-database-creator-form-helper" style="display:block;margin-bottom:1em;">Example help text ...</small>      
            <input type="text" name="example_fieldname" size="55" id="example_fieldname" 
                   value="<?php echo get_site_option(self::OPTIONS_LABEL)['example_fieldname']; ?>" />
            <?php
        }

        public static function settings_panel_fields() {
            // add the sections to the section groups
            add_settings_section('basic_options_section_group', 'Basic Configuration Options', null, 'basic_options_section');
            // add the fields to the sections
            add_settings_field('zws_contacts_database_google_server_api_key', 'Google Server API Key', array('\ZwsContactsDatabase\Admin',
                'google_api_key_form_field_element'), 'basic_options_section', 'basic_options_section_group');
        }

        public static function clear_cache() {
            // method to clear the memcached cache
            $memcached_ip = get_site_option(self::OPTIONS_LABEL)['zws_contacts_database_memcached_ip'];
            $memcached_port = get_site_option(self::OPTIONS_LABEL)['zws_contacts_database_memcached_port'];
            if (class_exists('\Memcached')) {
                $cache = new \Memcached();
                if ($cache->addServer($memcached_ip, $memcached_port)) {
                    if ($cache->delete(self::MEMCACHED_KEYBASE)) {
                        return true;
                    }
                }
            } else {
                return null;
            }
            return false;
        }

        public static function get_url() {
            if (isset($_SERVER['HTTPS'])) {
                $protocol = 'https';
            } else {
                $protocol = 'http';
            }
            return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

    }
   