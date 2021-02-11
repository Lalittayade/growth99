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

class YasrNoStarsRankings extends YasrShortcode {

    /**
     * @return string
     */
    public function returnTopReviewers() {
        global $wpdb;

        $query_result = $wpdb->get_results(
            "SELECT COUNT(pm.post_id) AS total_count, 
                        p.post_author AS reviewer
                    FROM $wpdb->posts AS p,
                         $wpdb->postmeta AS pm
                    WHERE pm.post_id = p.ID
                        AND pm.meta_key = 'yasr_overall_rating'
                        AND p.post_status = 'publish'
                    GROUP BY reviewer
                    ORDER BY (total_count) DESC
                    LIMIT 5");


        if ($query_result) {
            $this->shortcode_html = '<!-- Yasr Top 5 Reviewers Shortcode-->';

            $this->shortcode_html .= '<table class="yasr-rankings">
                                        <tr class="yasr-rankings-td-colored">
                                         <th>' . __('Author', 'yet-another-stars-rating') .'</th>
                                         <th>'. __('Reviews', 'yet-another-stars-rating') .'</th>
                                      </tr>';

            $i = 0;
            foreach ($query_result as $result) {
                $user_data = get_userdata($result->reviewer);

                if ($user_data) {
                    $user_profile = get_author_posts_url($result->reviewer);
                } else {
                    $user_profile = '#';
                    $user_data = new stdClass;
                    $user_data->user_login = 'Anonymous';
                }

                if ($i % 2 === 0) {
                    $tr_class = 'yasr-rankings-td-white';
                } else {
                    $tr_class = 'yasr-rankings-td-colored';
                }

                $this->shortcode_html .= "<tr class='$tr_class'>
                                            <td><a href='$user_profile'>$user_data->user_login</a></td>
                                            <td>$result->total_count</td>
                                          </tr>";

                $i++;

            }

            $this->shortcode_html .= '</table>';

            $this->shortcode_html .= '<!-- End Yasr Top 5 Reviewers Shortcode-->';

            return $this->shortcode_html;

        }

        return(__(
            'Problem while retrieving the top 5 most active reviewers. Did you publish any review?',
            'yet-another-stars-rating')
        );

    }

    /**
     * @return string
     */
    public function returnTopUsers() {
        global $wpdb;
        $query_result = $wpdb->get_results(
            "SELECT COUNT(user_id) as total_count, 
                        user_id as user
                    FROM " . YASR_LOG_TABLE . ", 
                        $wpdb->posts AS p
                    WHERE  post_id = p.ID
                        AND p.post_status = 'publish'
                    GROUP BY user_id
                    ORDER BY ( total_count ) DESC
                    LIMIT 10");

        if ($query_result) {
            $shortcode_html = '<!-- Yasr Top 10 Active Users Shortcode-->';

            $shortcode_html .= '<table class="yasr-rankings">
                                    <tr class="yasr-rankings-td-colored">
                                     <th>' . __('UserName', 'yet-another-stars-rating') . '</th>
                                     <th>' . __('Number of votes', 'yet-another-stars-rating') . '</th>
                                    </tr>';

            $i = 0;
            foreach ($query_result as $result) {
                $user_data = get_userdata($result->user);

                if ($user_data) {
                    $user_profile = get_author_posts_url($result->user);
                } else {
                    $user_profile = '#';
                    $user_data = new stdClass;
                    $user_data->user_login = __('Anonymous', 'yet-another-stars-rating');
                }

                if ($i % 2 === 0) {
                    $tr_class = 'yasr-rankings-td-white';
                } else {
                    $tr_class = 'yasr-rankings-td-colored';
                }

                $shortcode_html .= "<tr class='$tr_class'>
                                        <td><a href='$user_profile'>$user_data->user_login</a></td>
                                        <td>$result->total_count</td>
                                    </tr>";

                $i++;

            }

            $shortcode_html .= '</table>';
            $shortcode_html .= '<!--End Yasr Top 10 Active Users Shortcode-->';

            return $shortcode_html;
        }

        return (__('Problem while retrieving the top 10 active users chart. Are you sure you have votes to show?',
            'yet-another-stars-rating'));
    }
}