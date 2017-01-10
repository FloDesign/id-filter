<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://flodesign.co.uk
 * @since             1.0.0
 * @package           Id_Filter
 *
 * @wordpress-plugin
 * Plugin Name:       ID Filter
 * Plugin URI:        https://flodesign.co.uk
 * Description:       Filter to check query string for a valid id.
 * Version:           1.0.0
 * Author:            Craig Thompson
 * Author URI:        https://flodesign.co.uk
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       id-filter
 * Domain Path:       /languages
 */

add_filter('query_vars', 'add_access_var');

function add_access_var($vars)
{
    $vars[] = 'access';
    return $vars;
}

add_action('pre_get_posts', 'check_for_id');

function check_for_id($request)
{
    if (!is_admin() && !is_page('access-denied')) {
        if($_COOKIE['access']){
            $query = $_COOKIE['access'];
        } else {
            $query = get_query_var('access');
        }

        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/';

        if (preg_match($pattern, $query) !== 1) {
            //Doesn't match, redirect
            $redirect_page = get_page_by_path('access-denied');
            $redirect_link = get_permalink($redirect_page->ID);
            header('Location: '.$redirect_link, true, 302);
            exit;
        } else {
            //Matches, set cookie
            if(isset($_COOKIE['access'])){
                return true;
            } else {
                setcookie('access', $query, 0, '/', false);
                return true;
            }
        }
    }
}
