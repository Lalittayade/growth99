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
 * Class YasrShortcode
 *
 * @since 2.1.5
 *
 */
abstract class YasrShortcode {
    public $shortcode_html;
    public $post_id; //false
    public $size; //large
    public $readonly; //false
    public $set_id; //1
    public $show_average; //null
    public $shorcode_name;
    public $post_type;

    public function __construct($atts, $shortcode_name) {
        $this->shorcode_name = $shortcode_name;

        if ($atts !== false) {
            $atts = shortcode_atts(
                array(
                    'size'         => 'large',
                    'postid'       => false,
                    'readonly'     => false,
                    'setid'        => 1,
                    'show_average' => null
                ),
                $atts,
                $shortcode_name
            );

            if ($atts['postid'] === false) {
                $this->post_id = get_the_ID();
            } else {
                $this->post_id = (int) $atts['postid'];
            }
            $this->size          = sanitize_text_field($atts['size']);
            $this->readonly      = sanitize_text_field($atts['readonly']);
            $this->set_id        = (int) $atts['setid'];
            $this->show_average  = sanitize_text_field($atts['show_average']);
        }

        $this->post_type = YasrCustomPostTypes::returnBaseUrl($this->post_id);

    }

    /**
     * Return the stars size according to size attribute in shortcode.
     * If not used, return 32 (default value)
     *
     * @return int
     */
    protected function starSize() {
        if ($this->shorcode_name === 'yasr_top_ten_highest_rated'
            || $this->shorcode_name === 'yasr_most_or_highest_rated_posts') {
            return 24;
        }

        $size = $this->size;
        $px_size = 32; //default value

        if ($size === 'small') {
            $px_size = 16;
        } elseif ($size === 'medium') {
            $px_size = 24;
        }
        return $px_size;
    }

    /**
     * Enable or disable stars, works for both VisitorVotes and VisitorMultiSet
     *
     * @param $cookie_value
     *
     * @return string|bool;
     */
    public static function starsEnalbed($cookie_value) {
        $is_user_logged_in = is_user_logged_in();

        //Logged in user is always able to vote
        if ( $is_user_logged_in === true ) {
            return 'true_logged';
        }

        //If only logged in users can vote
        if (YASR_ALLOWED_USER === 'logged_only') {
            //IF user is not logged in
            if ( $is_user_logged_in === false ) {
                return 'false_not_logged';
            }
        }

        //if anonymous are allowed to vote
        if (YASR_ALLOWED_USER === 'allow_anonymous') {
            //I've to check if is user is not logged in
            if ($is_user_logged_in === false) {
                //if cookie !== false means that exists, and user can't vote
                if ($cookie_value !== false) {
                    return 'false_already_voted';
                }

                return 'true_not_logged';
            }
        } //end if YASR_ALLOWED_USER === 'allow_anonymous'

        //this should never happen
        return false;
    }
}
