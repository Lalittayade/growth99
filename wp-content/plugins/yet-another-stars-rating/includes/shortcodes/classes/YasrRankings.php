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
 * Class YasrRankings
 */
class YasrRankings extends YasrShortcode {

    protected $query_highest_rated_overall;
    protected $query_result_most_rated_visitor;
    protected $query_result_highest_rated_visitor;
    protected $vv_highest_rated_table;
    protected $vv_most_rated_table;

    /**
     * Returns the shortcode for yasr_top_ten_highest_rated
     * */
    public function returnHighestRatedOverall () {
        $this->shortcode_html = '<!-- Yasr Highest Rated Shortcode-->';
        $this->query_highest_rated_overall = YasrRankingData::rankingOverallGetResults();
        $this->returnTableOverall();
        $this->shortcode_html .= '<!--End Yasr Top 10 highest Rated Shortcode-->';

        return $this->shortcode_html;
    }

    /**
     * Returns only initial and ending table tag
     * Table content is build with REACT
     *
     * @author Dario Curvino <@dudo>
     * @since 2.5.7
     *
     * @param $sql_params array|string - params that can be used in REST API
     *
     */
    protected function returnTableOverall($sql_params=null) {
        if ($this->query_highest_rated_overall) {

            $table_attributes = $this->sanitizeParams($sql_params);

            $table_id = 'yasr_overall_ranking_'.str_shuffle(uniqid());

            $this->shortcode_html .= "<table class='yasr-rankings yasr-stars-rankings' id=$table_id>";

            $array_with_title = self::rankingData($this->query_highest_rated_overall);

            wp_localize_script('yasrfront',
                $table_id,
                array(
                    'data_overall'  => $array_with_title,
                    'source'        => 'overall_rating',
                    'size'          => $this->starSize(),
                    'params'        => $table_attributes['sql_params'],
                    'text_position' => $table_attributes['text_position'],
                    'custom_text'   => $table_attributes['custom_text']
                ));

            $this->shortcode_html .= "</table>";
        }
        else {
            _e("No data found", 'yet-another-stars-rating');
        }

    }

    /**
     * Returns the shortcode for yasr_most_or_highest_rated_posts
     * */
    public function vvReturnMostHighestRated () {
        $this->shortcode_html = '<!-- Yasr Most Or Highest Rated Shortcode -->';
        $this->query_result_most_rated_visitor    = YasrRankingData::rankingVVGetResults(false, 'most');
        $this->query_result_highest_rated_visitor = YasrRankingData::rankingVVGetResults(false, 'highest');
        $this->returnTableVV();
        $this->shortcode_html .= '<!--End Yasr TMost Or Highest Rated Shortcode -->';

        return $this->shortcode_html;
    }

    /**
     * Create the queries for the rankings
     * Return the full html for the shortcode
     *
     * @param $sql_params array|string - params that can be used in REST API
     */
    public function returnTableVV($sql_params=null) {
        if($this->query_result_most_rated_visitor && $this->query_result_highest_rated_visitor) {

            $table_attributes = $this->sanitizeParams($sql_params);

            $vv_most_data    = self::rankingData($this->query_result_most_rated_visitor);
            $vv_highest_data = self::rankingData($this->query_result_highest_rated_visitor);

            $table_id = 'yasr_vv_ranking_' . str_shuffle(uniqid());

            $this->shortcode_html .= "<table class='yasr-rankings yasr-stars-rankings' id=$table_id>";

            wp_localize_script(
                'yasrfront',
                $table_id,
                array(
                    'most'           => $vv_most_data,
                    'highest'        => $vv_highest_data,
                    'source'         => 'visitor_votes',
                    'size'           => $this->starSize(),
                    'params'         => $table_attributes['sql_params'],
                    'view'           => $table_attributes['view'],
                    'required_votes' => $table_attributes['required_votes']
                )
            );
            $this->shortcode_html .= '</table>';
        } else {
            _e("No data found", 'yet-another-stars-rating');
        }

    }

    /**
     * Returns an array with post titles and links
     *
     * @author Dario Curvino <@dudo>
     * @since 2.5.2
     *
     * @param $query_result array to loop; MUST have:
     * post_id
     * rating
     * (optional) number_of_votes
     *
     * @return array
     */
    public static function rankingData($query_result) {
        $data_array = array();

        $i=0;
        foreach ($query_result as $result) {
            $data_array[$i]['post_id']        = (int)$result->post_id;
            $data_array[$i]['rating']         = round($result->rating,1);
            if(isset($result->number_of_votes)) {
                $data_array[$i]['number_of_votes'] = (int)$result->number_of_votes;
            }
            $data_array[$i]['title']          = wp_strip_all_tags(get_the_title($result->post_id));
            $data_array[$i]['link']           = get_permalink($result->post_id); //Get permalink from post id
            $i++;
        } //End foreach

        return $data_array;
    }


    /**
     * @author Dario Curvino <@dudo>
     * @since 2.5.7
     * @param $sql_params array|string - params that can be used in REST API
     *
     * @return array
     */
    private function sanitizeParams($sql_params=null) {

        $array_to_return['sql_params']     = false;
        $array_to_return['custom_text']    = __('Rating:', 'yet-another-stars-rating');
        $array_to_return['text_position']  = 'after';
        $array_to_return['view']           = 'most';
        $array_to_return['required_votes'] = false;

        if(is_array($sql_params)) {
            $array_to_return['sql_params']     = $sql_params['sql_params'];
            $array_to_return['custom_text']    = $sql_params['custom_text'];
            $array_to_return['text_position']  = $sql_params['text_position'];
            $array_to_return['view']           = $sql_params['view'];
            $array_to_return['required_votes'] = $sql_params['required_votes'];
        }

        return $array_to_return;

    }

}