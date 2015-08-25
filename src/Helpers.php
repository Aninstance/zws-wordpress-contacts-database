<?php

namespace ZwsContactsDatabase;

/**
 * Helpers class file for ZWS Contacts Database
 *
 * @copyright Copyright (c) 2015, Zaziork Web Solutions
 * @license This plugin uses the Composer library - see composer-license.txt
 * @author    Zaziork Web Solutions
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link https://www.zaziork.com/
 */
Class Helpers {

    public static function set_url_query($new_query = null) {
        // returns the complete requested URI with the incoming parameters changed or added    
        if (empty($new_query)) {
            return false;
        }

        $request_uri = esc_url($_SERVER['REQUEST_URI']);

        foreach ($new_query as $param_name => $param_value) {
            if ($request_uri !== false) {
                $request_uri = self::create_query_string($param_name, $param_value, $request_uri);
            }
        }
        return $request_uri;
    }

    private static function create_query_string($param_name, $param_value, $request_uri) {
        // returns a query string created from the input parameters.
        try {
            // generates and returns a new URI with the incoming parameters changed or added
            $pattern = '/(.*?)(' . $param_name . '=[^&]*)(.*$)/i';
            $replacement = '$1' . $param_name . '=' . $param_value . '$3';
            // if no query string in current url
            if (!strpos($request_uri, '?')) {
                return $request_uri . '?' . $param_name . '=' . $param_value;
            } else {
                // if new query name already exists in string
                if (strpos($request_uri, $param_name)) {
                    return preg_replace($pattern, $replacement, $request_uri);
                } else {
                    return $request_uri .= '&' . $param_name . '=' . $param_value;
                }
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getCss($id) {
        /* method to return pre-defined css styling. 
         * Returns a string of css statements (without the element locators or brackets, obviously!).
         */
        $css = '';
        switch ($id) {
            case 'label_style_tag':
                return
                        'background-color:#1E73BE;'
                        . 'text-align:centre;'
                        . 'color:yellow;padding:0.2em;'
                        . '-webkit-box-shadow: -5px 5px 5px 0px rgba(0,0,0,0.75);'
                        . '-moz-box-shadow: -5px 5px 5px 0px rgba(0,0,0,0.75);'
                        . 'box-shadow: -5px 5px 5px 0px rgba(0,0,0,0.75);';
            case 'header_style_tag':
                return
                        'background-color:#1E73BE;'
                        . 'display:inline-block;'
                        . 'text-align:centre;'
                        . 'color:yellow;padding:0.2em;'
                        . '-webkit-box-shadow: -5px 5px 5px 0px rgba(0,0,0,0.75);'
                        . '-moz-box-shadow: -5px 5px 5px 0px rgba(0,0,0,0.75);'
                        . 'box-shadow: -5px 5px 5px 0px rgba(0,0,0,0.75);'
                        . 'margin-bottom:1em;';
            case 'entry_style_tag':
                return
                        'background-color:#345114;'
                        . 'color:yellow;padding:0.2em;'
                        . '-webkit-box-shadow: -5px 5px 5px 0px rgba(0,0,0,0.75);'
                        . '-moz-box-shadow: -5px 5px 5px 0px rgba(0,0,0,0.75);'
                        . 'box-shadow: -5px 5px 5px 0px rgba(0,0,0,0.75);'
                        . 'margin-bottom:1em;';
            case 'page_index':
                return
                        'padding:0.3em;'
                        . 'color:#345114;'
                        . 'background-color:yellow;'
                        . '-webkit-box-shadow: -5px 5px 5px 0px rgba(0,0,0,0.75);'
                        . '-moz-box-shadow: -5px 5px 5px 0px rgba(0,0,0,0.75);'
                        . 'box-shadow: -5px 5px 5px 0px rgba(0,0,0,0.75);'
                        . 'text-decoration:none;';
            case 'data_style_tag':
                return
                        'margin-left:0.5em;'
                        . 'color:yellow;';
            case 'link_style_tag':
                return
                        'padding:0.3em;'
                        . 'color:yellow;'
                        . 'background-color:#1E73BE;';
            case 'list_style_tag':
                return
                        'margin:0.5em;';
            default:
                return null;
        }
    }

}
