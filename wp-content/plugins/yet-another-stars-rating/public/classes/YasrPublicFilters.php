<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit( 'You\'re not allowed to see this page' );
} // Exit if accessed directly

/**
 * Public filters
 *
 * @since 2.4.3
 *
 * Class YasrPublicFilters
 */
class YasrPublicFilters {

    public function addFilters() {

        // Auto insert overall rating and visitor rating
        if (YASR_AUTO_INSERT_ENABLED === 1) {
            add_filter('the_content', array($this, 'autoInsert'));
        }

        add_filter('the_content', array($this, 'addSchema'));

        //stars next to the title
        if (YASR_STARS_TITLE === 'yes') {
            add_filter('the_title', array($this, 'filterTitle'));
        }

    }

    /**
     * @param $content
     *
     * @return bool|string|void
     */
    public static function autoInsert($content) {

        $post_id = get_the_ID();

        //check if for this post or page auto insert is off
        $post_excluded = get_post_meta($post_id, 'yasr_auto_insert_disabled', true);

        if ($post_excluded === 'yes') {
            return $content;
        }

        //create an empty array
        $excluded_cpt = array();

        //this hooks can be used to add cpt to exclude from the auto_insert
        $excluded_cpt = apply_filters('yasr_auto_insert_exclude_cpt', $excluded_cpt);

        //Excluded_cpt must be an array
        if (is_array($excluded_cpt) && !empty($excluded_cpt)) {

            //sanitize
            $excluded_cpt = filter_var_array($excluded_cpt, FILTER_SANITIZE_STRING);

            $post_type = get_post_type();

            //if one element in the array is found, return content
            foreach ($excluded_cpt as $cpt) {
                if ($cpt === $post_type) {
                    return $content;
                }
            }

        }

        $shortcode_align = YASR_AUTO_INSERT_ALIGN;

        //if it is not left, or right, default is center
        if ($shortcode_align !== 'left' && $shortcode_align !== 'right') {
            $shortcode_align = 'center';
        }

        $container_div_overall = '<div style="text-align:' . $shortcode_align . '" class="yasr-auto-insert-overall">';
        $container_div_visitor = '<div style="text-align:' . $shortcode_align . '" class="yasr-auto-insert-visitor">';
        $closing_div           = '</div>';

        $auto_insert_shortcode = null; //To avoid undefined variable notice outside the loop (if (is_singular) )
        $overall_rating_code   = $container_div_overall . '[yasr_overall_rating size="' . YASR_AUTO_INSERT_SIZE . '"]'
            . $closing_div;
        $visitor_votes_code    = $container_div_visitor . '[yasr_visitor_votes size="' . YASR_AUTO_INSERT_SIZE . '"]'
            . $closing_div;

        //avoid undefined
        $content_and_stars = false;

        if (YASR_AUTO_INSERT_WHAT === 'overall_rating') {
            switch (YASR_AUTO_INSERT_WHERE) {
                case 'top':
                    $content_and_stars = $overall_rating_code . $content;
                    break;

                case 'bottom':
                    $content_and_stars = $content . $overall_rating_code;
                    break;
            } //End Switch
        }
        elseif (YASR_AUTO_INSERT_WHAT === 'visitor_rating') {
            switch (YASR_AUTO_INSERT_WHERE) {
                case 'top':
                    $content_and_stars = $visitor_votes_code . $content;
                    break;

                case 'bottom':
                    $content_and_stars = $content . $visitor_votes_code;
                    break;
            } //End Switch
        }
        elseif (YASR_AUTO_INSERT_WHAT === 'both') {
            switch (YASR_AUTO_INSERT_WHERE) {
                case 'top':
                    $content_and_stars = $overall_rating_code . $visitor_votes_code . $content;
                    break;

                case 'bottom':
                    $content_and_stars = $content . $overall_rating_code . $visitor_votes_code;
                    break;
            } //End Switch
        }

        //IF auto insert must work only in custom post type
        if (YASR_AUTO_INSERT_CUSTOM_POST_ONLY === 'yes') {
            $custom_post_types = YasrCustomPostTypes::getCustomPostTypes();
            //If is a post type return content and stars
            if (is_singular($custom_post_types)) {
                return $content_and_stars;
            } //else return just content

            return $content;
        }

        //If page are not excluded
        if (YASR_AUTO_INSERT_EXCLUDE_PAGES === 'no') {
            return $content_and_stars;
        }

        if (YASR_AUTO_INSERT_EXCLUDE_PAGES === 'yes') {
            if (is_page()) {
                return $content;
            } //If is a page return the content without stars

            return $content_and_stars;
        } //else return only if it is not a page

    } //End function yasr_auto_insert_shortcode_callback


    /**
     * @param $content
     *
     * @return string
     */
    public static function addSchema($content) {

        //Add buddypress compatibility
        //If this is a page, return $content without adding schema.
        if (function_exists('bp_is_active') && is_page()) {
            return $content;
        }

        if (is_404() || did_action('get_footer') || (!is_singular() && is_main_query())) {
            return $content;
        }

        $post_id = get_the_ID();
        $overall_rating = YasrDatabaseRatings::getOverallRating();
        $visitor_votes  = YasrDatabaseRatings::getVisitorVotes(false);

        if (!$overall_rating && !$visitor_votes['number_of_votes'] && !$visitor_votes['sum_votes']) {
            return $content;
        }

        //can't be between 0.1 and 1
        if ($overall_rating > 0 && $overall_rating < 1) {
            $overall_rating = 1;
        }

        $is_post_a_review = get_post_meta($post_id, 'yasr_post_is_review', true);

        $script_type     = '<script type="application/ld+json">';
        $end_script_type = '</script>';

        $review_choosen = yasr_get_itemType();

        //Use this hook to write your custom microdata from scratch
        //if doesn't exists a filter for yasr_filter_schema_jsonld
        // $review_chosen value is assigned to $filtered_schema.
        $filtered_schema = apply_filters('yasr_filter_schema_jsonld', $review_choosen);

        //So check here if $schema != $review_choosen
        if ($filtered_schema !== $review_choosen) {
            return $content . $script_type . $filtered_schema . $end_script_type;
        }

        //YASR adds microdata only if is_singular() && is_main_query() && !is_404()
        if (is_singular() && is_main_query() && !is_404()) {

            $rich_snippet['@context'] = 'http://schema.org/';

            $author = get_the_author();

            //use this hook to change the itemType name
            $review_name = wp_strip_all_tags(apply_filters('yasr_filter_schema_title', $post_id));

            $date          = get_the_date('c');
            $date_modified = get_the_modified_date('c');

            $post_image_url = ''; //avoid undefined
            $logo_image_url = ''; //avoid undefined

            if (defined('YASR_PUBLISHER_LOGO')) {
                $logo_image_url = YASR_PUBLISHER_LOGO;
                $post_image_url = $logo_image_url; //this will be overwritten if has_post_thumbnail is true

                $logo_image_url_absolute = $_SERVER['DOCUMENT_ROOT'] . parse_url(YASR_PUBLISHER_LOGO, PHP_URL_PATH);

                $post_image_size = @getimagesize($logo_image_url_absolute);  //the @ should be useless, just to be safe
                $logo_image_size = @getimagesize($logo_image_url_absolute);  //the @ should be useless, just to be safe
            }
            else {
                $post_image_size[0] = 0;
                $post_image_size[1] = 0;
                $logo_image_size[0] = 0;
                $logo_image_size[1] = 0;
            }

            //if exists featuread image get the url and overwrite the variable
            if (has_post_thumbnail()) {
                $post_image_url          = wp_get_attachment_url(get_post_thumbnail_id());
                $post_image_url_absolute = $_SERVER['DOCUMENT_ROOT'] . parse_url($post_image_url, PHP_URL_PATH);
                $post_image_size         = @getimagesize(
                    $post_image_url_absolute
                );  //the @ should be useless, just to be safe
            }

            $rich_snippet['@type'] = $review_choosen;
            $rich_snippet['name']  = $review_name;
            $cleaned_content       = wp_strip_all_tags(strip_shortcodes($content));

            $rich_snippet['description'] = wp_trim_words($cleaned_content, 55, '...');

            $rich_snippet['image'] = array(
                '@type'  => 'ImageObject',
                'url'    => $post_image_url,
                'width'  => $post_image_size[0],
                'height' => $post_image_size[1]
            );

            $publisher_image_index = 'logo';
            if (YASR_PUBLISHER_TYPE === 'Person') {
                $publisher_image_index = 'image';
            }

            elseif ($review_choosen === 'BlogPosting') {
                $rich_snippet['datePublished']    = $date;
                $rich_snippet['headline']         = $review_name;
                $rich_snippet['mainEntityOfPage'] = array(
                    '@type' => 'WebPage',
                    '@id'   => get_permalink()
                );
                $rich_snippet['author']           = array(
                    '@type' => 'Person',
                    'name'  => $author
                );
                $rich_snippet['publisher']        = array(
                    '@type' => 'Organization',
                    'name'  => wp_strip_all_tags(YASR_PUBLISHER_NAME),
                    //already sanitized in the settings, just to be safe
                    'logo'  => array(
                        '@type'  => 'ImageObject',
                        'url'    => $logo_image_url,
                        'width'  => $logo_image_size[0],
                        'height' => $logo_image_size[1]
                    ),
                );

                $rich_snippet['dateModified'] = $date_modified;

                $rich_snippet['image'] = array(
                    '@type'  => 'ImageObject',
                    'url'    => $post_image_url,
                    'width'  => $post_image_size[0],
                    'height' => $post_image_size[1]
                );

            }

            //Add everywhere except for blogPosting
            if ($review_choosen !== 'BlogPosting') {
                if ($overall_rating) {
                    $rich_snippet['Review'] = array(
                        '@type'         => 'Review',
                        'name'          => $review_name,
                        'reviewBody'    => $cleaned_content,
                        'author'        => array(
                            '@type' => 'Person',
                            'name'  => $author
                        ),
                        'datePublished' => $date,
                        'dateModified'  => $date_modified,
                        'reviewRating'  => array(
                            '@type'       => 'Rating',
                            'ratingValue' => $overall_rating,
                            'bestRating'  => 5,
                            'worstRating' => 1
                        ),
                    );
                }

                //if both are included, google will index AggregateRating instead of Review.
                //So, is post is selected as review, exclude AggregateRating
                if ($is_post_a_review !== 'yes') {
                    if ($visitor_votes) {
                        if ($visitor_votes['sum_votes'] !== 0 && $visitor_votes['number_of_votes'] !== 0) {
                            $average_rating = $visitor_votes['sum_votes'] / $visitor_votes['number_of_votes'];
                            $average_rating = round($average_rating, 1);

                            $rich_snippet['aggregateRating'] = array(
                                '@type'       => 'AggregateRating',
                                'ratingValue' => $average_rating,
                                'ratingCount' => $visitor_votes['number_of_votes'],
                                'bestRating'  => 5,
                                'worstRating' => 1,
                            );
                        }
                    }
                }
            }

            if (isset($rich_snippet['Review']) || $review_choosen === 'BlogPosting') {
                $publisher = array(
                    '@type'                => YASR_PUBLISHER_TYPE,
                    'name'                 => wp_strip_all_tags(YASR_PUBLISHER_NAME),
                    //already sanitized in the settings, just to be safe
                    $publisher_image_index => array(
                        '@type'  => 'ImageObject',
                        'url'    => $logo_image_url,
                        'width'  => $logo_image_size[0],
                        'height' => $logo_image_size[1]
                    ),
                );

                /** @noinspection NotOptimalIfConditionsInspection */
                if (isset($rich_snippet['Review'])) {
                    $rich_snippet['Review']['publisher'] = $publisher;
                }
                else {
                    $rich_snippet['publisher'] = $publisher;
                }
            }

            //Use this hook to add additional schema
            //if doesn't exists a filter for yasr_filter_existing_schema, put $rich_snippet into $more_rich_snippet
            $more_rich_snippet = apply_filters('yasr_filter_existing_schema', $rich_snippet);

            if ($more_rich_snippet !== $rich_snippet && is_array($more_rich_snippet)) {
                $rich_snippet = $more_rich_snippet;
            }

            return $content . $script_type . json_encode($rich_snippet) . $end_script_type;
        }

        return $content;

    } //End function


    /**
     * @since 2.4.3
     * Filter the_title to show stars next it
     *
     * @param $title
     *
     * @return string
     */
    public function filterTitle($title) {

        if (in_the_loop()) {
            $post_id = get_the_ID();

            if (YASR_STARS_TITLE_EXCLUDE_PAGES === 'yes' && get_post_type($post_id) === 'page') {
                return $title;
            }

            $content_after_title = false;

            if (YASR_STARS_TITLE_WHAT === 'visitor_rating') {
                //returns int
                $stored_votes = YasrDatabaseRatings::getVisitorVotes();

                $number_of_votes = $stored_votes['number_of_votes'];
                if ($number_of_votes > 0) {
                    $average_rating = $stored_votes['sum_votes'] / $number_of_votes;
                }
                else {
                    $average_rating = 0;
                }

                $average_rating = round($average_rating, 1);

                $htmlid = 'yasr-visitor-votes-readonly-rater-' . str_shuffle(uniqid());

                $post_type = YasrCustomPostTypes::returnBaseUrl($post_id);

                $vv_widget = "<div class='yasr-stars-title yasr-rater-stars-vv'
                                          id='$htmlid'
                                          data-rating='$average_rating'
                                          data-rater-starsize='16'
                                          data-rater-postid='$post_id' 
                                          data-rater-readonly='true'
                                          data-readonly-attribute='true'
                                          data-cpt='$post_type'
                                      ></div>";

                $vv_widget .= "<span class='yasr-stars-title-average'>$average_rating ($number_of_votes)</span>";

                //Use this hook to customize widget
                //if doesn't exists a filter for yasr_title_vv_widget, put $vv_widget into $content_after_title
                $content_after_title = apply_filters('yasr_title_vv_widget', $vv_widget, $stored_votes);
            }

            if (YASR_STARS_TITLE_WHAT === 'overall_rating') {
                $overall_rating = YasrDatabaseRatings::getOverallRating($post_id);

                //first, overall widget contains overall rating
                $overall_widget = '';

                //only if overall rating > 0
                if ($overall_rating > 0) {
                    $overall_rating_obj = new YasrOverallRating(false, false);
                    $overall_attributes = $overall_rating_obj->returnAttributes(
                        16, $post_id, 'yasr-stars-title', $overall_rating
                    );

                    $overall_widget = "<span class='yasr-stars-title-average'>";
                    $overall_widget .= $overall_attributes['html_stars'];
                    $overall_widget .= "</span>";
                }

                //Use this hook to customize widget overall
                //if doesn't exists a filter for yasr_title_overall_widget, put $overall_widget into $content_after_title
                $content_after_title = apply_filters('yasr_title_overall_widget', $overall_widget, $overall_rating);
            }

            //if only in archive pages
            if (YASR_STARS_TITLE_WHERE === 'archive') {
                if (is_archive() || is_home()) {
                    return $title . $content_after_title;
                }
            } //if only in single posts/pages
            elseif (YASR_STARS_TITLE_WHERE === 'single') {
                if (is_singular()) {
                    return $title . $content_after_title;
                }
            } //always return in both
            elseif (YASR_STARS_TITLE_WHERE === 'both') {
                return $title . $content_after_title;
            } //else return just the title (should never happens)
            else {
                return $title;
            }
        }

        //if not in the loop
        return $title;
    }

}