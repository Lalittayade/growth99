<?php

if (!defined('ABSPATH')) {
    exit('You\'re not allowed to see this page');
} // Exit if accessed directly

class YasrCustomFields extends WP_REST_Controller {

    /**
     * Add in rest_api_init
     */
    public function restApiInit() {
        add_action('rest_api_init',  array($this, 'customFields'));
    }


    /**
     * Add a new response in
     * YOURSITE.COM/wp-json/wp/v2/posts
     */
    public function customFields () {
        /**
         * Add <yasr-visitor-votes> in
         * YOURSITE.COM/wp-json/wp/v2/POSTTYPE/<POSTID>?_fields=yasr_visitor_votes
         */
        $this->visitorVotes();
        $this->allItemTypes();
        $this->itemTypesAdditionalFields();
    }

    /**
     * Returns a multidimensionalarray like this:
     * "yasr_visitor_votes": {
     *     "number_of_votes": int,
     *     "sum_votes": int,
     *     "stars_attributes": {
     *         "read_only": bool,
     *         "span_bottom": string
     *      }
     *   }
     */
    private function visitorVotes() {
        $post_types = YasrCustomPostTypes::returnAllPostTypes();
        $yasr_vv_schema = array(
            'description'          => 'Yasr Visitor Votes Data',
            'type'                 => 'object',
            'context'              =>  array('view', 'edit'),
            // In JSON Schema you can specify object properties in the properties attribute.
            'properties' => array (
                'sum_votes'        => array(
                    'type'         => 'integer',
                ),
                'number_of_votes'  => array(
                    'type'         => 'integer',
                ),
                'stars_attributes' => array(
                    'read_only'    => array(
                        'type'        => 'boolean',
                    ),
                    'span_bottom' => array(
                        'type'        => 'boolean',
                    )
                )
            ),
        );

        //Register Visitor Votes
        register_rest_field(
            $post_types,
            'yasr_visitor_votes',
            array(
                'get_callback'    => array($this, 'returnArrayVisitorVotes'),
                'update_callback' => null,
                'schema'          => $yasr_vv_schema
            )
        );

    }

    //can't be private
    protected function returnArrayVisitorVotes() {

        //default values
        $array_to_return = array(
            'number_of_votes'   => 0,
            'sum_votes'         => 0,
            'stars_attributes'  => array(
                'read_only'    => true,
                'span_bottom'  => false
            )
        );

        $cookie_value = YasrVisitorVotes::checkCookie();
        $stars_enabled = YasrShortcode::starsEnalbed($cookie_value);

        //if user is enabled to rate, readonly must be false
        if($stars_enabled === 'true_logged' || $stars_enabled === 'true_not_logged') {
            $array_to_return['stars_attributes']['read_only'] = false;
        }

        $array_to_return['stars_attributes']['span_bottom'] = YasrVisitorVotes::showTextBelowStars($cookie_value);

        $array_visitor_votes = YasrDatabaseRatings::getVisitorVotes();

        $array_to_return['number_of_votes'] = $array_visitor_votes['number_of_votes'];
        $array_to_return['sum_votes'] = $array_visitor_votes['sum_votes'];

        return $array_to_return;

}

    /**
     * Function to returns all supported itemtype
     * (YASR_SUPPORTED_SCHEMA_TYPES)
     * only in the editor screen
     */
    private function allItemTypes() {
        $post_types = YasrCustomPostTypes::returnAllPostTypes();
        $yasr_itemtype_schema = array(
            'description'          => 'Yasr Supported Item Types',
            'type'                 => 'object',
            'context'              =>  array('edit'),
        );

        //Register Visitor Votes
        register_rest_field(
            $post_types,
            'yasr_all_itemtypes',
            array(
                'get_callback'    => static function() {return json_decode(YASR_SUPPORTED_SCHEMA_TYPES, true); },
                'update_callback' => null,
                'schema'          => $yasr_itemtype_schema
            )
        );
    }

    /**
     * Function that returns YASR_SUPPORTED_SCHEMA_TYPES_ADDITIONAL_FIELDS
     * only in the edit screen (for guteberg editor)
     */
    private function itemTypesAdditionalFields() {
        $post_types = YasrCustomPostTypes::returnAllPostTypes();
        $yasr_additional_itemtype_schema = array(
            'description'          => 'Yasr Item Types Additional Info',
            'type'                 => 'object',
            'context'              =>  array('edit'),
        );

        //Register Visitor Votes
        register_rest_field(
            $post_types,
            'yasr_all_itemtypes_addition_info',
            array(
                'get_callback'    => static function() {return json_decode(YASR_SUPPORTED_SCHEMA_TYPES_ADDITIONAL_FIELDS, true); },
                'update_callback' => null,
                'schema'          => $yasr_additional_itemtype_schema
            )
        );
    }
}