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
 * Class YasrVisitorVotes
 * Print Yasr Visitor Votes
 */
class YasrVisitorVotes extends YasrShortcode {

    protected  $is_singular;
    protected  $unique_id;
    protected  $ajax_nonce_visitor;

    /**
     * @var string
     */
    private $average_rating_container;

    /**
     * @var string
     */
    private $number_of_votes_container;

    public function __construct($atts, $shortcode_name) {
        parent::__construct($atts, $shortcode_name);

        if (is_singular()) {
            $this->is_singular = 'true';
        } else {
            $this->is_singular = 'false';
        }

        $this->unique_id = str_shuffle(uniqid());
        $this->ajax_nonce_visitor = wp_create_nonce("yasr_nonce_insert_visitor_rating");

        $this->shortcode_html = '<!--Yasr Visitor Votes Shortcode-->';
        $this->shortcode_html .= "<div id='yasr_visitor_votes_$this->post_id' class='yasr-visitor-votes'>";

        //This will contain the number of votes
        $this->number_of_votes_container  = '<span id="yasr-vv-votes-number-container-'. $this->unique_id .'">';

        //this will contain the average
        $this->average_rating_container   = '<span id="yasr-vv-average-container-'. $this->unique_id .'">';

    }

    /**
     * Print the visitor votes shortcode
     *
     * @return string|null
     */
    public function returnShortcode() {

        $htmlid = 'yasr-visitor-votes-rater-' . $this->unique_id ;

        //returns int
        $stored_votes = YasrDatabaseRatings::getVisitorVotes($this->post_id);

        $number_of_votes = $stored_votes['number_of_votes'];
        if ($number_of_votes > 0) {
            $average_rating = $stored_votes['sum_votes']/$number_of_votes;
        } else {
            $average_rating = 0;
        }

        $average_rating=round($average_rating, 1);

        //if this come from yasr_visitor_votes_readonly...
        if ($this->readonly === true || $this->readonly === "yes") {
            $htmlid = 'yasr-visitor-votes-readonly-rater-'.$this->unique_id;

            $this->shortcode_html = "<div class='yasr-rater-stars-vv'
                                          id='$htmlid'
                                          data-rating='$average_rating'
                                          data-rater-starsize='".$this->starSize()."'
                                          data-rater-postid='$this->post_id' 
                                          data-rater-readonly='true'
                                          data-readonly-attribute='true'
                                          data-cpt='$this->post_type'
                                      ></div>";

            //Use this filter to customize yasr_visitor_votes readonly
            $this->shortcode_html = apply_filters('yasr_vv_ro_shortcode', $this->shortcode_html, $stored_votes);

            return $this->shortcode_html;
        }

        $cookie_value = self::checkCookie($this->post_id);
        $stars_enabled = YasrShortcode::starsEnalbed($cookie_value);

        if($stars_enabled === 'true_logged' || $stars_enabled === 'true_not_logged') {
            $this->readonly = 'false'; //Always false if user is logged in
        } else {
            $this->readonly = 'true';
        }

        //this should run only if settings is enabled
        if (YASR_STARS_CUSTOM_TEXT === 1 && YASR_TEXT_BEFORE_VISITOR_RATING !== '') {
            $this->shortcode_html .= $this->textBeforeStars($number_of_votes, $average_rating);
        }

        $this->shortcode_html .= "<div id='$htmlid'
                                    class='yasr-rater-stars-vv'
                                    data-rater-postid='$this->post_id' 
                                    data-rating='$average_rating'
                                    data-rater-starsize='".$this->starSize()."'
                                    data-rater-readonly='$this->readonly'
                                    data-rater-nonce='$this->ajax_nonce_visitor' 
                                    data-issingular='$this->is_singular'
                                    data-cpt='$this->post_type'>
                                </div>";

        $this->shortcode_html .= $this->containerAfterStars($number_of_votes, $average_rating);

        $this->shortcode_html = apply_filters('yasr_vv_shortcode', $this->shortcode_html, $stored_votes);

        return $this->returnYasrVisitorVotes($cookie_value, $this->post_id);

    } //end function


    /**
     * Function that checks if cookie exists and set the value
     *
     * @param $post_id int|bool
     * @return int |bool
     */
    public static function checkCookie ($post_id = false) {

        $yasr_cookiename = apply_filters('yasr_vv_cookie', 'yasr_visitor_vote_cookie');

        $cookie_value = false;

        if($post_id === false) {
            $post_id = get_the_ID();
        }

        if (isset($_COOKIE[$yasr_cookiename])) {
            $cookie_data = stripslashes($_COOKIE[$yasr_cookiename]);

            //By default, json_decode return an object, true to return an array
            $cookie_data = json_decode($cookie_data, true);

            if (is_array($cookie_data)) {
                foreach ($cookie_data as $value) {
                    $cookie_post_id = (int)$value['post_id'];
                    if ($cookie_post_id === $post_id) {
                        $cookie_value = (int)$value['rating'];
                        //since version 2.4.0 break is removed, because yasr_setcookie PUSH the value (for logged in users)
                        //so to be sure to get the correct value, I need the last
                    }
                }
            }

            //I've to check $cookie_value !== false before because
            //if $cookie_value is false, $cookie_value < 1 return true (...wtf...)
            if($cookie_value !== false) {
                if ($cookie_value > 5) {
                    $cookie_value = 5;
                } elseif ($cookie_value < 1) {
                    $cookie_value = 1;
                }
            }
            //return int
            return $cookie_value;
        }

        //if cookie is not set (return false)
        return $cookie_value;
    }

    /**
     * This function show default (or custom) text depending if rating is allowed or not
     *
     * @param void
     * @param int|bool $post_id
     *
     * @return int|bool|void
     */
    public static function showTextBelowStars ($cookie_value, $post_id=false) {

        $stars_enabled = YasrShortcode::starsEnalbed($cookie_value);
        $span_bottom_line         = false;
        $span_bottom_line_content = false;

        if ($stars_enabled === 'true_logged') {
            //Check if a logged in user has already rated for this post
            $vote_if_user_already_rated = YasrDatabaseRatings::visitorVotesHasUserVoted($post_id);

            //If user has already rated
            if ($vote_if_user_already_rated) {
                $span_bottom_line_content = "<span class='yasr-already-voted-text'>";
                if (YASR_STARS_CUSTOM_TEXT === 1 && YASR_CUSTOM_TEXT_USER_VOTED !== '') {
                    $span_bottom_line_content .= YASR_CUSTOM_TEXT_USER_VOTED;
                } else {
                    $span_bottom_line_content .=
                        __('You\'ve already voted this article with', 'yet-another-stars-rating') . ' ' . $vote_if_user_already_rated;
                }
                $span_bottom_line_content .= '</span>';
            }
        } //true_logged

        elseif ($stars_enabled === 'false_already_voted') {
            $span_bottom_line_content = "<span class='yasr-already-voted-text'>";
            if (YASR_STARS_CUSTOM_TEXT === 1 && YASR_CUSTOM_TEXT_USER_VOTED !== '') {
                $span_bottom_line_content .= YASR_CUSTOM_TEXT_USER_VOTED;
            } else {
                $span_bottom_line_content .=
                    __('You\'ve already voted this article with', 'yet-another-stars-rating') . ' ' . $cookie_value;
            }
            $span_bottom_line_content .= '</span>';
        }

        //If only logged in users can vote
        elseif ($stars_enabled === 'false_not_logged') {
            $span_bottom_line_content = "<span class='yasr-visitor-votes-must-sign-in'>";
            //if custom text is defined
            if (defined('YASR_CUSTOM_TEXT_MUST_SIGN_IN') && YASR_CUSTOM_TEXT_MUST_SIGN_IN !== '') {
                $span_bottom_line_content .= YASR_CUSTOM_TEXT_MUST_SIGN_IN;
            } else {
                $span_bottom_line_content .= __('You must sign in to vote', 'yet-another-stars-rating');
            }
            $span_bottom_line_content .= '</span>';
        }

        if($span_bottom_line_content !== false) {
            $span_bottom_line  = "<span class='yasr-small-block-bold'>";
            $span_bottom_line .= $span_bottom_line_content;
            $span_bottom_line .= '</span>';
        }

        return $span_bottom_line;
    }


    /**
     * @since 2.4.7
     *
     * Returns text before the stars
     *
     * @param $number_of_votes
     * @param $average_rating
     *
     * @return string
     */
    protected function textBeforeStars($number_of_votes, $average_rating) {
        $text_before_star = $this->strReplaceInput(YASR_TEXT_BEFORE_VISITOR_RATING, $number_of_votes, $average_rating);

        $class_text_before = 'yasr-custom-text-vv-before yasr-custom-text-vv-before-'.$this->post_id;

        $shortcode_html = '<div class="'.$class_text_before.'">'
                              . $text_before_star .
                          '</div>';

        //if filters doesn't exists, put $shortcode_html inside $this->shortcode_html
        return apply_filters('yasr_vv_txt_before', $shortcode_html);
    }


    /**
     * Returns container after stars
     *
     * @since 2.4.7
     *
     * @param $number_of_votes
     * @param $average_rating
     *
     * @return mixed|void $span_text_after_stars
     */
    public function containerAfterStars ($number_of_votes, $average_rating) {
        $container_span = '<span class="yasr-total-average-container"
                                            id="yasr-total-average-text-'. $this->unique_id .'">';

        if (YASR_VISITORS_STATS === 'yes') {
            $container_span .= $this->visitorStats();
        }

        $container_span .= $this->textAfterStars($number_of_votes, $average_rating);

        $container_span .= '</span>';

        //use this to costumize text after stars
        return apply_filters('yasr_vv_txt_after', $container_span);
    }

    /**
     * @author Dario Curvino <@dudo>
     * @since 2.4.7
     *
     * @param $number_of_votes
     * @param $average_rating
     *
     * @return string|string[]
     */
    protected function textAfterStars($number_of_votes, $average_rating) {
        $default_text = '['
            . __('Total:', 'yet-another-stars-rating')
            . '&nbsp;'
            . $this->number_of_votes_container
                . $number_of_votes
            . '</span>'
            . '&nbsp; &nbsp;'
            . __('Average:', 'yet-another-stars-rating')
            . '&nbsp;'
            . $this->average_rating_container
                . $average_rating
            . '</span>'
            . '/5]';

        if (YASR_STARS_CUSTOM_TEXT === 1 && YASR_TEXT_AFTER_VISITOR_RATING !== '') {
            $text_after_star = $this->strReplaceInput(YASR_TEXT_AFTER_VISITOR_RATING, $number_of_votes, $average_rating);
            $default_text = $text_after_star;
        }

        return $default_text;
    }

    /**
     * @author Dario Curvino <@dudo>
     * @since 2.5.9
     *
     * @param $subject
     * @param $number_of_votes
     * @param $average_rating
     *
     * @return string|string[]
     */
    protected function strReplaceInput($subject, $number_of_votes, $average_rating) {
        return str_replace(
            array(
                '%total_count%',
                '%average%'
            ),
            array(
                $this->number_of_votes_container . $number_of_votes . '</span>',
                $this->average_rating_container  . $average_rating  . '</span>'
            ),
            $subject
        );
    }


    /**
     * This function will return the html code for the dashicons
     *
     * @param void
     *
     * @return string
     */
    public function visitorStats () {
        global $yasr_plugin_imported;

        //default
        $span_dashicon = "<span class='dashicons dashicons-chart-bar yasr-dashicons-visitor-stats'
        data-postid='$this->post_id' id='yasr-total-average-dashicon-$this->post_id'></span>";

        if (is_array($yasr_plugin_imported)) {
            $plugin_import_date = null; //avoid undefined
            if (array_key_exists('wppr', $yasr_plugin_imported)) {
                $plugin_import_date = $yasr_plugin_imported['wppr']['date'];
            }

            if (array_key_exists('kksr', $yasr_plugin_imported)) {
                $plugin_import_date = $yasr_plugin_imported['kksr']['date'];
            }

            if (array_key_exists('mr', $yasr_plugin_imported)) {
                $plugin_import_date = $yasr_plugin_imported['mr']['date'];
            }

            //remove hour from date
            $plugin_import_date=strtok($plugin_import_date,' ');

            $post_date = get_the_date('Y-m-d', $this->post_id);

            //if one of these plugin has been imported and post is older then import,  hide stats
            if ($post_date < $plugin_import_date) {
                $span_dashicon = "";
            }
        } //End if $yasr_plugin_imported

        return $span_dashicon;
    }

    /**
     * Return Yasr Visitor Votes
     *
     * @param $cookie_value int|bool
     * @param $post_id
     *
     * @return string
     */
    protected function returnYasrVisitorVotes ($cookie_value, $post_id) {
        $span_container_loader = "<div id='yasr-vv-loader-$this->unique_id'
                                             class='yasr-vv-container-loader'>";

        $this->shortcode_html .= $span_container_loader;

        if(YASR_ENABLE_AJAX === 'yes') {
            $id_name = 'yasr-below-stars-hidden-' . $this->unique_id;
            $this->shortcode_html .= '<div style="display:none;" id="'.$id_name.'">';
            $this->shortcode_html .= self::showTextBelowStars($cookie_value, $post_id);
            $this->shortcode_html .= '</div>';
        } else {
            $this->shortcode_html .= self::showTextBelowStars($cookie_value, $post_id);
        }

        $this->shortcode_html .= '</div>'; //Close yasr-visitor-votes-after-stars
        $this->shortcode_html .= '</div>'; //close all
        $this->shortcode_html .= '<!--End Yasr Visitor Votes Shortcode-->';

        return $this->shortcode_html;
    }
}