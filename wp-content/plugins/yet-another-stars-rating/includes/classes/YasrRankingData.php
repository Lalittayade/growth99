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

class YasrRankingData {

    /**
     * Run $wpdb->get_results for overall Rating
     *
     * @author Dario Curvino <@dudo>
     * @since  2.5.2
     *
     * @param bool|array $attributes
     *
     * @return array|false|object|void
     */
    public static function rankingOverallGetResults($attributes = false) {
        global $wpdb;

        //use this hook to adds query params
        $sql_params = apply_filters('yasr_set_query_attributes', $attributes);

        //do a custom query here
        //must returns rating and post_id
        $query      = apply_filters('yasr_rankings_query_ov', $sql_params);

        //if query === $sql_params (both are falses) means that filters doesn't exists
        if($query === $sql_params) {
            //default query
            $query = "SELECT pm.meta_value AS rating, 
                         pm.post_id AS post_id
                  FROM $wpdb->postmeta AS pm, 
                       $wpdb->posts AS p
                  WHERE  pm.post_id = p.ID
                      AND p.post_status = 'publish'
                      AND pm.meta_key = 'yasr_overall_rating'
                      AND pm.meta_value > 0
                  ORDER BY pm.meta_value DESC,
                           pm.post_id 
                  LIMIT 10";
        }

        $query_result = $wpdb->get_results($query);

        if ($query_result) {
            return $query_result;
        }
        return false;
    }

    /**
     * Run $wpdb->get_results for VV
     *
     * @author Dario Curvino <@dudo>
     * @since  2.5.2
     *
     * @param bool|array $attributes
     * @param            $ranking
     * @param bool       $required_votes
     *
     * @return array|false|object|void
     */

    public static function rankingVVGetResults($attributes, $ranking, $required_votes=false) {
        global $wpdb;

        if($required_votes !== false) {
            $attributes['required_votes'] = $required_votes;
        }

        $sql_params = apply_filters('yasr_set_query_attributes', $attributes);

        //This filter is used to filter the query
        $query      = apply_filters('yasr_rankings_query_vv', $sql_params, $ranking);

        //if no custom query is hooked
        if($query === $sql_params) {

            $common_query = "SELECT post_id, 
                COUNT(post_id) AS number_of_votes,
                ROUND(SUM(vote) / COUNT(post_id),1) AS rating
            FROM " . YASR_LOG_TABLE . ",
                $wpdb->posts AS p
            WHERE post_id = p.ID
                AND p.post_status = 'publish'
            GROUP BY post_id
                HAVING number_of_votes > 1
            ";

            if ($ranking === 'highest') {
                $order_by = ' ORDER BY rating DESC, number_of_votes DESC';
            }
            else {
                $order_by = ' ORDER BY number_of_votes DESC, rating DESC, post_id DESC';
            }

            $limit = ' LIMIT 10';
            $query = $common_query . $order_by . $limit;

        }

        return $wpdb->get_results($query);
    }
}