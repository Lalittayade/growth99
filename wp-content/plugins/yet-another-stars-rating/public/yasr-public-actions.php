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

/***** Adding javascript and css *****/
add_action('wp_enqueue_scripts', 'yasr_add_scripts');
function yasr_add_scripts() {

    wp_enqueue_style(
        'yasrcss',
        YASR_CSS_DIR_INCLUDES . 'yasr.css',
        false,
        YASR_VERSION_NUM,
        'all'
    );

    wp_enqueue_style('dashicons'); //dashicons

    //Run after default css are loaded
    do_action('yasr_add_front_script_css');

    $yasr_multiset_theme_handle = 'yasrcsslightscheme';
    $yasr_multiset_theme = 'yasr-table-light.css';

    //default css is the light one
    if (YASR_SCHEME_COLOR === 'dark') {
        $yasr_multiset_theme_handle = 'yasrcssdarkscheme';
        $yasr_multiset_theme = 'yasr-table-dark.css';
    }

    wp_enqueue_style(
        $yasr_multiset_theme_handle,
        YASR_CSS_DIR_INCLUDES . $yasr_multiset_theme,
        array('yasrcss'),
        YASR_VERSION_NUM, 'all'
    );

    if (YASR_CUSTOM_CSS_RULES) {
        wp_add_inline_style(
            'yasrcss',
            YASR_CUSTOM_CSS_RULES
        );
    }

    wp_enqueue_script(
        'yasrfront',
        YASR_JS_DIR_INCLUDES . 'yasr-front.js',
        array('jquery', 'rater', 'wp-i18n'),
        YASR_VERSION_NUM,
        true
    );

    do_action('yasr_add_front_script_js');

    $yasr_visitor_votes_loader = '<div id="loader-visitor-rating" style="display: inline">&nbsp; '.
                                 ' <img src=' . YASR_IMG_DIR . 'loader.gif title="yasr-loader" alt="yasr-loader">
                                 </div>';

    wp_localize_script('yasrfront', 'yasrCommonData',
        array(
            'restEndpoint'        => esc_url_raw(rest_url()),
            'ajaxurl'             => admin_url('admin-ajax.php'),
            'visitorStatsEnabled' => YASR_VISITORS_STATS,
            'ajaxEnabled'         => YASR_ENABLE_AJAX,
            'loaderHtml'          => $yasr_visitor_votes_loader,
            'nonce'               => wp_create_nonce('wp_rest')
        )
    );
}

//Delete caches for supported plugins
//Can't use is_singular() here because always return false
add_action('yasr_action_on_visitor_vote', 'yasr_delete_cache');
add_action('yasr_action_on_visitor_multiset_vote', 'yasr_delete_cache');

function yasr_delete_cache($array_action_visitor_vote) {

    if (isset($array_action_visitor_vote['post_id'])) {
        $post_id = $array_action_visitor_vote['post_id'];
    } else {
        return;
    }

    if (isset($array_action_visitor_vote['is_singular'])) {
        $is_singular = $array_action_visitor_vote['is_singular'];
    } else {
        return;
    }

    yasr_wp_super_cache_support($post_id);
    yasr_wp_rocket_support($post_id);
    yasr_litespeed_cache_support($post_id);
    yasr_cache_enabler_support($post_id, $is_singular);
    yasr_wp_fastest_cache($post_id, $is_singular);

}