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
 * Class YasrOverallRating
 * Print Yasr Overall Rating
 */
class YasrOverallRating extends YasrShortcode {

    protected $html_stars;
    protected $overall_rating;

    /**
     * This is called when shortcode is used
     *
     * @return string|null
     */
    public function returnShortcode () {

        $overall_attributes    = $this->returnAttributes();

        $this->shortcode_html  = '<!--Yasr Overall Rating Shortcode-->';

        $this->shortcode_html .= $this->customTextBefore();

        $this->shortcode_html .= '<div class="yasr-overall-rating">';
        $this->shortcode_html .= $overall_attributes['html_stars'];
        $this->shortcode_html .= '</div>';

        $this->shortcode_html .= '<!--End Yasr Overall Rating Shortcode-->';

        //Use this filter to customize overall rating
        $this->shortcode_html = apply_filters('yasr_overall_rating_shortcode', $this->shortcode_html, $overall_attributes);

        //If overall rating in loop is enabled don't use is_singular && is main_query
        if (YASR_SHOW_OVERALL_IN_LOOP === 'enabled') {
            return $this->shortcode_html;
        } //default

        if (is_singular() && is_main_query()) {
            return $this->shortcode_html;
        }

        return null;
    }

    /**
     * @param int | bool $stars_size
     * @param int | bool $post_id
     * @param string | bool $class
     * @param string | bool $rating
     *
     * @return array
     *     array(
     *         'overall_rating' => $overall_rating,
     *         'post_id'        => $post_id,
     *         'html_stars'     => $html_stars
     *     );
     */
    public function returnAttributes($stars_size=false, $post_id=false, $class=false, $rating=false) {

        if(!is_int($stars_size)) {
            $stars_size = $this->starSize();
        }

        if(!is_int($post_id)) {
            $post_id = $this->post_id;
        }

        $class .= ' yasr-rater-stars';

        //if here $this->overall_rating is still null, check if rating is not false, and if so, put it in $overall rating
        // if rating is false, get from the db
        if($this->overall_rating === null) {
            if($rating !== false) {
                $overall_rating = $rating;
            } else {
                $overall_rating = YasrDatabaseRatings::getOverallRating($post_id);
            }
        }  else {
            $overall_rating = $this->overall_rating;
        }

        $unique_id               = str_shuffle(uniqid());
        $overall_rating_html_id  = 'yasr-overall-rating-rater-' . $unique_id;

        $html_stars = "<div class='$class'
                           id='$overall_rating_html_id'
                           data-rating='$overall_rating'
                           data-rater-starsize='$stars_size'>
                       </div>";

        $array_to_return = array(
            'overall_rating' => $overall_rating,
            'post_id'        => $post_id,
            'html_stars'     => $html_stars
        );

        return $array_to_return;

    }

    /**
     * If enabled in the settings, this function will show the custom text
     * before yasr_overall_rating
     *
     * @param  void
     * @return string | void
     *
     */
    protected function customTextBefore() {
        if (YASR_STARS_CUSTOM_TEXT === 1 && YASR_TEXT_BEFORE_OVERALL !== '') {
            //Get overall Rating
            $this->overall_rating = YasrDatabaseRatings::getOverallRating();

            $text_before_star = str_replace('%overall_rating%', $this->overall_rating, YASR_TEXT_BEFORE_OVERALL);
            $shortcode_html   = "<div class='yasr-container-custom-text-and-overall'>
                                     <span id='yasr-custom-text-before-overall'>" . $text_before_star . "</span>
                                 </div>";

            return $shortcode_html;
        }

    }

}