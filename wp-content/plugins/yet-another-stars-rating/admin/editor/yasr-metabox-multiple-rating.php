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

if ( ! defined( 'ABSPATH' ) ) exit('You\'re not allowed to see this page'); // Exit if accessed directly

$multi_set = YasrMultiSetData::returnMultiSetNames();
$post_id = get_the_ID();
$yasr_nonce_multi = wp_nonce_field( "yasr_nonce_save_multi_values_action", "yasr_nonce_save_multi_values");

$set_id = NULL;

global $wpdb;

$n_multi_set = $wpdb->num_rows; //wpdb->num_rows always store the the count number of rows of the last query

if ($n_multi_set > 1) {
    ?>
    <div>
        <?php _e("Choose which set you want to use", 'yet-another-stars-rating'); ?>
        <br />
        <label for="select_set">
            <select id="select_set">
                <?php foreach ($multi_set as $name) { ?>
                        <option value="<?php echo $name->set_id ?>"><?php echo $name->set_name ?></option>
                  <?php } //End foreach ?>
            </select>
        </label>

        <button href="#" class="button-delete" id="yasr-button-select-set"><?php _e("Select"); ?></button>

        <span id="yasr-loader-select-multi-set" style="display:none;" >&nbsp;
            <img src="<?php echo YASR_IMG_DIR . "/loader.gif" ?>" alt="yasr-loader">
        </span>
    </div>

    <?php 

} //End if if ($n_multi_set>1)

elseif ($n_multi_set === 1) {
    //If multiset is only 1, array index will be always 0
    ////get the set_id
    $set_id = $multi_set[0]->set_id;
    $set_id = (int)$set_id;
}


?>

<div id="yasr_rateit_multi_rating">

    <span id="yasr-multi-set-admin-choose-text">
        <?php _e( 'Choose a vote for each element', 'yet-another-stars-rating' ); ?>
    </span>

    <input type="hidden" name="yasr_multiset_author_votes" id="yasr-multiset-author-votes" value="">
    <input type="hidden" name="yasr_multiset_id" id="yasr-multiset-id" value="">

    <table class="yasr_table_multi_set_admin" id="yasr-table-multi-set-admin">

    </table>

    <div id="yasr-multi-set-admin-explain">
        <?php _e( "If you want to insert this multiset, paste this shortcode", 'yet-another-stars-rating' ); ?>
        <span id="yasr-multi-set-admin-explain-with-id-readonly"></span>. <br />

        <?php _e( "If, instead, you want allow your visitor to vote on this multiset, use this shortcode", 'yet-another-stars-rating' ); ?>
        <span id='yasr-multi-set-admin-explain-with-id-visitor'></span>.
        <?php _e('In this case, you don\'t need to vote here', 'yet-another-stars-rating');?>

        <br />
        <br />

        <?php
            $yasr_pro_string = sprintf(
                    __( "With %sYasr Pro%s you can use %s yasr_pro_average_multiset %s and 
                    %s yasr_pro_average_visitor_multiset %s to print a separate average of the Multi Set.",
                    'yet-another-stars-rating' ),
            '<a href="https://yetanotherstarsrating.com/?utm_source=wp-plugin&utm_medium=metabox_multiple_rating&utm_campaign=yasr_editor_screen&utm_content=yasr-pro#yasr-pro">',
            '</a>',
            '<strong>', '</strong>',
            '<strong>', '</strong>');

            echo $yasr_pro_string;
        ?>
        <span id='yasr-multi-set-admin-explain-with-id-visitor'></span>

    </div>

</div>

<script type="text/javascript">

    document.addEventListener('DOMContentLoaded', function(event) {

        var nMultiSet = <?php echo (json_encode("$n_multi_set")); ?>;
        var postid = <?php echo ($post_id); ?>;
        var setId = <?php echo( json_encode( "$set_id" ) ); ?>;

        yasrAdminMultiSet(nMultiSet, postid, setId);

    });

</script>
