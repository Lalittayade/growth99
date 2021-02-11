<?php
/*

Copyright 2014 Dario Curvino (email : d.curvino@tiscali.it)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit( 'You\'re not allowed to see this page' );
} // Exit if accessed directly


/*** Add support for wp super cache ***/
function yasr_wp_super_cache_support($post_id) {
    if (function_exists('wp_cache_post_change')) {
        wp_cache_post_change($post_id);
    }
}

/*** Add support for wp rocket, thanks to GeekPress
 * https://wordpress.org/support/topic/compatibility-with-wp-rocket-2
 ***/
function yasr_wp_rocket_support($post_id) {
    if (function_exists('rocket_clean_post')) {
        rocket_clean_post($post_id);
    }
}

/*** Add support for LiteSpeed Cache plugin, thanks to Pako69
 * https://wordpress.org/support/topic/yasr-is-litespeed-cache-plugin-compatible/
 ***/
function yasr_litespeed_cache_support($post_id) {
    if (method_exists('LiteSpeed_Cache_API', 'purge_post') == true) {
        LiteSpeed_Cache_API::purge_post($post_id);
    }
}

/*** Add support for cache enabler ***/
function yasr_cache_enabler_support($post_id, $is_singular) {
    if (has_action('ce_clear_cache') || has_action('ce_clear_post_cache')) {
        //IF is in the single post or page delete only that cache
        if ($is_singular === 'true') {
            do_action('ce_clear_post_cache', $post_id);
        } //otherwise, delete everything
        else {
            do_action('ce_clear_cache');
        }
    }
}


/**** Add support for Wp Fastest Cache ****/
function yasr_wp_fastest_cache($post_id, $is_singular) {
    if ($is_singular === 'true') {
        if (isset($GLOBALS['wp_fastest_cache']) && method_exists($GLOBALS['wp_fastest_cache'], 'singleDeleteCache')) {
            $GLOBALS['wp_fastest_cache']->singleDeleteCache(false, $post_id);
        }
    } else {
        if (isset($GLOBALS['wp_fastest_cache']) && method_exists($GLOBALS['wp_fastest_cache'], 'deleteCache')) {
            $GLOBALS['wp_fastest_cache']->deleteCache();
        }
    }
}