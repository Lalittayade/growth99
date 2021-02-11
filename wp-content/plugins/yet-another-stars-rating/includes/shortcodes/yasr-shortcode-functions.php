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

if (!defined('ABSPATH')) {
    exit('You\'re not allowed to see this page');
} // Exit if accessed directly

/**
 * Callback function for the spl_autoload_register above.
 *
 * @param $class
 */
function yasr_autoload_shortcodes($class) {
    /**
     * If the class being requested does not start with 'Yasr' prefix,
     * it's not in Yasr Project
     */
    if (0 !== strpos($class, 'Yasr')) {
        return;
    }
    $file_name =  YASR_ABSOLUTE_PATH_INCLUDES . '/shortcodes/classes/' . $class . '.php';

    // check if file exists, just to be sure
    if (file_exists($file_name)) {
        require($file_name);
    }
}

//AutoLoad YASR Shortcode Classes, only when a object is created
spl_autoload_register('yasr_autoload_shortcodes');

require YASR_ABSOLUTE_PATH_INCLUDES . '/shortcodes/yasr-shortcode-ajax.php';


/****** Add shortcode for overall rating ******/
add_shortcode('yasr_overall_rating', 'shortcode_overall_rating_callback');

/**
 * @param $atts
 *
 * @return string|void|null
 */
function shortcode_overall_rating_callback ($atts) {
    if (YASR_SHOW_OVERALL_IN_LOOP === 'disabled' && !is_singular() && is_main_query()) {
        return;
    }

    $shortcode_name = 'yasr_overall_rating';
    $overall_rating = new YasrOverallRating($atts, $shortcode_name);

    return $overall_rating->returnShortcode();
} //end function


/****** Add shortcode for user vote ******/

add_shortcode('yasr_visitor_votes', 'shortcode_visitor_votes_callback');

/**
 * @param $atts
 *
 * @return string|void|null
 */
function shortcode_visitor_votes_callback($atts) {
    if (YASR_SHOW_VISITOR_VOTES_IN_LOOP === 'disabled' && !is_singular() && is_main_query()) {
        return;
    }

    $shortcode_name = 'yasr_visitor_votes';
    $visitor_votes = new YasrVisitorVotes($atts, $shortcode_name);

    return $visitor_votes->returnShortcode();

} //End function shortcode_visitor_votes_callback

/****** Add shortcode for multiple set ******/

add_shortcode ('yasr_multiset',  'yasr_multiset_callback');

/**
 * @param $atts
 *
 * @return string|void|null
 */
function yasr_multiset_callback($atts) {
    $shortcode_name = 'yasr_multiset';
    $multiset = new YasrMultiSet($atts, $shortcode_name);

    return $multiset->printMultiset();
}

/****** Add shortcode for multiset writable by users  ******/

add_shortcode ('yasr_visitor_multiset', 'yasr_visitor_multiset_callback');

/**
 * @param $atts
 *
 * @return string|void|null
 */
function yasr_visitor_multiset_callback($atts) {

    $multiset = new YasrVisitorMultiSet($atts, 'yasr_visitor_multiset');
    return $multiset->printVisitorMultiSet();
}


/****** Add top 10 highest rated post *****/

add_shortcode ('yasr_top_ten_highest_rated', 'yasr_top_ten_highest_rated_callback');

/**
 * @param $atts
 *
 * @return string|void|null
 */
function yasr_top_ten_highest_rated_callback() {
    $top_ten_highest_obj = new YasrRankings(false, 'yasr_top_ten_highest_rated');

    return $top_ten_highest_obj->returnHighestRatedOverall();
} //End function


/****** Add top 10 most rated / highest rated post *****/
add_shortcode ('yasr_most_or_highest_rated_posts', 'yasr_most_or_highest_rated_posts_callback');
/**
 *
 * @return string|void|null
 */
function yasr_most_or_highest_rated_posts_callback() {
    $most_highest_obj = new YasrRankings(false, 'yasr_most_or_highest_rated_posts');

    return $most_highest_obj->vvReturnMostHighestRated();
} //End function


/****** Add top 5 most active reviewer ******/
add_shortcode ('yasr_top_5_reviewers', 'yasr_top_5_reviewers_callback');

function yasr_top_5_reviewers_callback () {
    $top_5_reviewers_obj = new YasrNoStarsRankings(false, 'yasr_top_5_reviewers');

    return $top_5_reviewers_obj->returnTopReviewers();
} //End top 5 reviewers function

/****** Add top 10 most active user *****/
add_shortcode ('yasr_top_ten_active_users', 'yasr_top_ten_active_users_callback');

function yasr_top_ten_active_users_callback () {
    $most_active_users_obj = new YasrNoStarsRankings(false, 'yasr_top_ten_active_users');

    return $most_active_users_obj->returnTopUsers();
} //End function


