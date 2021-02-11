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


/****** Yasr insert visitor votes, called from yasr-shortcode-function ******/
add_action('wp_ajax_yasr_send_visitor_rating', 'yasr_insert_visitor_votes_callback');
add_action('wp_ajax_nopriv_yasr_send_visitor_rating', 'yasr_insert_visitor_votes_callback');

/**
 * Save yasr_visitor_votes
 */
function yasr_insert_visitor_votes_callback() {

    if (isset($_POST['rating'], $_POST['post_id'], $_POST['nonce_visitor'])) {
        $rating        = (int) $_POST['rating'];
        $post_id       = (int) $_POST['post_id'];
        $nonce_visitor = $_POST['nonce_visitor'];
        $is_singular   = $_POST['is_singular'];

    } else {
        die();
    }

    $array_action_visitor_vote = array('post_id' => $post_id, 'is_singular' => $is_singular);

    do_action('yasr_action_on_visitor_vote', $array_action_visitor_vote);

    if (!wp_verify_nonce($nonce_visitor, 'yasr_nonce_insert_visitor_rating')) {
        die('Security check');
    }

    if ($rating < 1) {
        $rating = 1;
    } elseif ($rating > 5) {
        $rating = 5;
    }

    global $wpdb;

    $current_user = wp_get_current_user();
    $ip_adress    = yasr_get_ip();

    $result_update_log = null; //avoid undefined
    $result_insert_log = null; //avoid undefined

    if (is_user_logged_in()) {

        //try to update first, if fails the do the insert
        $result_update_log = $wpdb->update(
            YASR_LOG_TABLE,
            array(
                'post_id' => $post_id,
                'user_id' => $current_user->ID,
                'vote'    => $rating,
                'date'    => date('Y-m-d H:i:s'),
                'ip'      => $ip_adress
            ),
            array(
                'post_id' => $post_id,
                'user_id' => $current_user->ID
            ),
            array('%d', '%d', '%d', '%s', '%s', '%s'),
            array('%d', '%d')

        );

        //insert the new row
        //use ! instead of === FALSE
        if (!$result_update_log) {
            $result_insert_log = $wpdb->insert(
                YASR_LOG_TABLE,
                array(
                    'post_id' => $post_id,
                    'user_id' => $current_user->ID,
                    'vote'    => $rating,
                    'date'    => date('Y-m-d H:i:s'),
                    'ip'      => $ip_adress
                ),
                array('%d', '%d', '%d', '%s', '%s', '%s')
            );
        }

    } //if user is not logged in insert
    else {
        //be sure that allow anonymous is on
        if (YASR_ALLOWED_USER === 'allow_anonymous') {
            $result_insert_log = $wpdb->replace(
                YASR_LOG_TABLE,
                array(
                    'post_id' => $post_id,
                    'user_id' => $current_user->ID,
                    'vote'    => $rating,
                    'date'    => date('Y-m-d H:i:s'),
                    'ip'      => $ip_adress
                ),

                array('%d', '%d', '%d', '%s', '%s', '%s')
            );
        }

    }

    if ($result_update_log || $result_insert_log) {
        $row_exists      = YasrDatabaseRatings::getVisitorVotes($post_id);

        $user_votes_sum  = $row_exists['sum_votes'];
        $number_of_votes = $row_exists['number_of_votes'];

        //customize visitor_votes cookie name
        $cookiename = apply_filters('yasr_vv_cookie', 'yasr_visitor_vote_cookie');

        $data_to_save = array(
            'post_id' => $post_id,
            'rating'  => $rating
        );

        yasr_setcookie($cookiename, $data_to_save);

        $total_rating  = ($user_votes_sum / $number_of_votes);
        $medium_rating = round($total_rating, 1);

        //Default text when rating is saved
        if($result_update_log) {
            $rating_saved_text = __('Rating Updated!', 'yet-another-stars-rating');
        } else {
            $rating_saved_text = __('Rating Saved!', 'yet-another-stars-rating');
        }

        //Customize it
        $rating_saved_text = apply_filters('yasr_vv_saved_text', $rating_saved_text);

        $rating_saved_span = '<span class="yasr-small-block-bold" id="yasr-vote-saved">'
                               . $rating_saved_text .
                             '</span>';

        $array_to_return = array(
            'number_of_votes'   => $number_of_votes,
            'average_rating'    => $medium_rating,
            'rating_saved_text' => $rating_saved_span
        );

        echo json_encode($array_to_return);

    }

    die(); // this is required to return a proper result

}

/****** Get Multiple value from visitor and insert into db, used in yasr-shortcode-functions ******/

add_action('wp_ajax_yasr_visitor_multiset_field_vote', 'yasr_visitor_multiset_field_vote_callback');
add_action('wp_ajax_nopriv_yasr_visitor_multiset_field_vote', 'yasr_visitor_multiset_field_vote_callback');

function yasr_visitor_multiset_field_vote_callback() {

    if (isset($_POST['post_id']) && isset($_POST['rating']) && isset($_POST['set_type'])) {
        $post_id  = (int) $_POST['post_id'];
        $rating   = $_POST['rating'];
        $set_type = (int) $_POST['set_type'];
        $nonce    = $_POST['nonce'];

        if (!is_int($post_id) || !is_int($set_type)) {
            exit("Missing post id or set type");
        }

        if ($rating == "") {
            exit("You must insert at least a rating");
        }

    } else {
        exit();
    }

    if (!wp_verify_nonce($nonce, 'yasr_nonce_insert_visitor_rating_multiset')) {
        die('Security Check');
    }

    $current_user = wp_get_current_user();
    $ip_adress    = yasr_get_ip();

    $array_action_visitor_multiset_vote = array('post_id' => $post_id);

    do_action('yasr_action_on_visitor_multiset_vote', $array_action_visitor_multiset_vote);

    global $wpdb;

    $array_error = array();

    //clean array, so if an user rate same field twice, take only the last rating
    $cleaned_array = yasr_unique_multidim_array($rating, 'field');

    //this is a counter: if at the end of the foreach it still 0, means that an user rated in a set
    //and then submit another one
    $counter_matched_fields = 0;

    foreach ($cleaned_array as $rating_values) {

        //check if the set id in the array is the same of the clicked
        if ($rating_values['postid'] == $post_id && $rating_values['setid'] == $set_type) {

            //increase the counter
            $counter_matched_fields = $counter_matched_fields + 1;

            $id_field = (int)$rating_values['field'];
            $rating   = $rating_values['rating'];

            if(is_user_logged_in()) {
                $update_query_success = $wpdb->update(
                    YASR_LOG_MULTI_SET,
                    array(
                        'field_id' => $id_field,
                        'set_type' => $set_type,
                        'post_id'  => $post_id,
                        'vote'     => $rating,
                        'user_id'  => $current_user->ID,
                        'date'     => date( 'Y-m-d H:i:s' ),
                        'ip'       => $ip_adress

                    ),
                    array(
                        'field_id' => $id_field,
                        'set_type' => $set_type,
                        'post_id'  => $post_id,
                        'user_id'  => $current_user->ID
                    ),
                    array( "%d", "%d", "%d", "%d", "%d", "%s", "%s" ),
                    array( "%d", "%d", "%d", "%d" )
                );

                if (!$update_query_success) {
                    $insert_query_success = $wpdb->insert(
                        YASR_LOG_MULTI_SET,
                        array(
                            'field_id' => $id_field,
                            'set_type' => $set_type,
                            'post_id'  => $post_id,
                            'vote'     => $rating,
                            'user_id'  => $current_user->ID,
                            'date'     => date('Y-m-d H:i:s'),
                            'ip'       => $ip_adress
                        ),
                        array("%d", "%d", "%d", "%d", "%d", "%s", "%s")
                    );

                    if (!$insert_query_success) {
                        $array_error[] = 1;
                    }
                }

            } else {
                $replace_query_success = $wpdb->replace(
                    YASR_LOG_MULTI_SET,
                    array(
                        'field_id' => $id_field,
                        'set_type' => $set_type,
                        'post_id'  => $post_id,
                        'vote'     => $rating,
                        'user_id'  => $current_user->ID,
                        'date'     => date('Y-m-d H:i:s'),
                        'ip'       => $ip_adress
                    ),
                    array("%d", "%d", "%d", "%d", "%d", "%s", "%s")
                );

                if (!$replace_query_success) {
                    $array_error[] = 1;
                }
            }

        } //End if $rating_values['postid'] == $post_id

    } //End foreach ($rating as $rating_values)

    if ($counter_matched_fields === 0) {
        $array_error[] = 1;
    }

    $error_found = false;

    foreach ($array_error as $error) {
        if ($error === 1) {
            $error_found = true;
        }
    }

    if (!$error_found) {
        $cookiename = apply_filters('yasr_mv_cookie', 'yasr_multi_visitor_cookie');

        $data_to_save = array(
            'post_id' => $post_id,
            'set_id'  => $set_type
        );

        yasr_setcookie($cookiename, $data_to_save);

        $rating_saved_text = __('Rating Saved', 'yet-another-stars-rating');
        $rating_saved_text = apply_filters('yasr_mv_saved_text', $rating_saved_text);

        echo $rating_saved_text;

    } else {
        _e('Rating not saved. Please Try again', 'yet-another-stars-rating');
    }

    die();

} //End callback function


add_action('wp_ajax_yasr_stats_visitors_votes', 'yasr_stats_visitors_votes_callback');
add_action('wp_ajax_nopriv_yasr_stats_visitors_votes', 'yasr_stats_visitors_votes_callback');

function yasr_stats_visitors_votes_callback() {

    if (isset($_POST['post_id']) && $_POST['post_id'] !== '') {
        $post_id = (int)$_POST['post_id'];
    } else {
        return;
    }

    $votes_array       = YasrDatabaseRatings::getVisitorVotes($post_id);
    $votes_number      = $votes_array['number_of_votes'];

    if ($votes_number !== 0) {
        $medium_rating = ($votes_array['sum_votes'] / $votes_number);
    } else {
        $medium_rating = 0;
    }

    $medium_rating = round($medium_rating, 1);
    $missing_vote  = null; //avoid undefined variable

    global $wpdb;

    //create an empty array
    $existing_votes = array();

    $stats = $wpdb->get_results(
        $wpdb->prepare(
        "SELECT ROUND(vote, 0) as vote, 
                       COUNT(vote)    as n_of_votes
                FROM " . YASR_LOG_TABLE . "
                    WHERE post_id=%d
                    AND   vote > 0 
                    AND   vote <= 5
                GROUP BY vote
                ORDER BY vote DESC
                ",
                $post_id
        ),
        ARRAY_A);

    $total_votes = 0; //Avoid undefined variable if stats exists. Necessary if $stats not exists

    //if query return 0 write an empty array $existing_votes
    if ($stats) {
        //Write a new array with only existing votes, and count all the number of votes
        foreach ($stats as $votes_array) {
            $existing_votes[] = $votes_array['vote'];//Create an array with only existing votes
            $total_votes      = $total_votes + $votes_array['n_of_votes'];
        }
    }

    for ($i = 1; $i <= 5; $i ++) {
        //If query return 0 write a new $stats array with index
        if (!$stats) {
            $stats[$i]               = array();
            $stats[$i]['vote']       = $i;
            $stats[$i]['n_of_votes'] = 0;
        } else {
            //If in the new array there are some vote missing create a new array
            /** @noinspection TypeUnsafeArraySearchInspection */
            if (!in_array($i, $existing_votes)) {
                $missing_vote[$i]               = array();
                $missing_vote[$i]['vote']       = $i;
                $missing_vote[$i]['n_of_votes'] = 0;
            }
        }
    }

    //If missing_vote exists merge it
    if ($missing_vote) {
        $stats = array_merge($stats, $missing_vote);
    }

    arsort($stats); //sort it by $votes[n_of_votes]

    $html_to_return  = '<div class="yasr-visitors-stats-tooltip">';
    $html_to_return .=     '<span id="yasr-medium-rating-tooltip">' . $medium_rating . ' '
                               . __('out of 5 stars', 'yet-another-stars-rating') .
                           '</span>';
    $html_to_return .= '<div class="yasr-progress-bars-container">';

    if ($total_votes === 0) {
        $increase_bar_value = 0;
    } else {
        $increase_bar_value = 100 / $total_votes; //Find how much all the bars should increase per vote
    }

    $i = 5;

    $stars_text = __('stars', 'yet-another-stars-rating');

    foreach ($stats as $logged_votes) {

        //cast int
        $logged_votes['n_of_votes'] = (int)$logged_votes['n_of_votes'];

        if ($i === 1) {
            $stars_text = __('star', 'yet-another-stars-rating');
        }

        $value_progressbar = $increase_bar_value * $logged_votes['n_of_votes']; //value of the single bar
        $value_progressbar = round($value_progressbar, 2) . '%'; //use only 2 decimal

        $html_to_return .= "<div class='yasr-progress-bar-row-container yasr-w3-container'>
                                <div class='yasr-progress-bar-name'>$i $stars_text</div>
                                <div class='yasr-single-progress-bar-container'>
                                    <div class='yasr-w3-border '>
                                        <div class='yasr-w3-amber' style='height:17px;width:$value_progressbar'></div>
                                    </div></div>
                                <div class='yasr-progress-bar-votes-count'>" . $logged_votes['n_of_votes'] . "</div><br />
                                </div>";

        $i --;

        //if there is a 0 rating in the database (only possible if manually added) break foreach
        if ($i < 1) {
            break;
        }

    } //End foreach

    $html_to_return .= '</div></div>';
    echo json_encode($html_to_return);

    die();

}
