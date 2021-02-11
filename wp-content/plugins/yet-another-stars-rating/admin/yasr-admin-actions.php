<?php

if (!defined('ABSPATH')) {
    exit('You\'re not allowed to see this page');
} // Exit if accessed directly

add_action('plugins_loaded', 'yasr_edit_category_form');

function yasr_edit_category_form () {
    if (current_user_can('manage_options')) {
        YasrEditCategory::init();
    }
}

/****** Adding logs widget to the dashboard ******/

add_action('plugins_loaded', 'yasr_add_action_dashboard_widget_log');

function yasr_add_action_dashboard_widget_log() {
    //This is for the admins (show all votes in the site)
    if (current_user_can('manage_options')) {
        add_action('wp_dashboard_setup', 'yasr_add_dashboard_widget_log');
    }

    //This is for all the users to see where they've voted
    add_action('wp_dashboard_setup', 'yasr_add_dashboard_widget_user_log');
}

function yasr_add_dashboard_widget_log() {
    wp_add_dashboard_widget(
        'yasr_widget_log_dashboard', //slug for widget
        'Recent Ratings', //widget name
        'yasr_widget_log_dashboard_callback' //function callback
    );
}

//This add a dashboard log for every users
function yasr_add_dashboard_widget_user_log() {
    wp_add_dashboard_widget(
        'yasr_users_dashboard_widget', //slug for widget
        'Your Ratings', //widget name
        'yasr_users_dashboard_widget_callback' //function callback
    );
}

/****** Delete data value from yasr tabs when a post or page is deleted
 * Added since yasr 0.3.3
 ******/
add_action('admin_init', 'admin_init_delete_data_on_post_callback');

function admin_init_delete_data_on_post_callback() {

    if (current_user_can('delete_posts')) {
        add_action('delete_post', 'yasr_erase_data_on_post_page_remove_callback');
    }

}

function yasr_erase_data_on_post_page_remove_callback($post_id) {
    global $wpdb;

    delete_metadata('post', $post_id, 'yasr_overall_rating');
    delete_metadata('post', $post_id, 'yasr_review_type');
    delete_metadata('post', $post_id, 'yasr_multiset_author_votes');

    //Delete multi value
    $wpdb->delete(
        YASR_LOG_MULTI_SET,
        array(
            'post_id' => $post_id
        ),
        array(
            '%d'
        )
    );

    $wpdb->delete(
        YASR_LOG_TABLE,
        array(
            'post_id' => $post_id
        ),
        array(
            '%d'
        )
    );


}


//Add stars set for yasr style settings page
//from version 1.2.7
add_action('yasr_style_options_add_settings_field', 'yasr_style_options_add_settings_field_callback');

function yasr_style_options_add_settings_field_callback($style_options) {

    add_settings_field(
            'yasr_style_options_choose_stars_lite',
            __('Choose Stars Set', 'yet-another-stars-rating'),
            'yasr_style_options_choose_stars_lite_callback',
            'yasr_style_tab',
            'yasr_style_options_section_id',
            $style_options
    );

}

function yasr_style_options_choose_stars_lite_callback($style_options) {

    ?>

    <div class='yasr_choose_stars' id='yasr_pro_custom_set_choosen_stars'>

        <input type='radio' name='yasr_style_options[stars_set_free]' value='rater'
               class='yasr-general-options-scheme-color' <?php if ($style_options['stars_set_free'] === 'rater') {
            echo " checked=\"checked\" ";
        } ?> />
        <br/>
        <div class='yasr_pro_stars_set' id='yasr_pro_custom_set_choosen_stars'>
            <?php
            echo '<img src="' . YASR_IMG_DIR . 'stars_rater.png">';
            ?>
        </div>

    </div>

    <div class='yasr_choose_stars' id='yasr_pro_custom_set_choosen_stars'>

        <input type='radio' name='yasr_style_options[stars_set_free]' value='rater-yasr'
               class='yasr-general-options-scheme-color' <?php if ($style_options['stars_set_free'] === 'rater-yasr') {
            echo " checked=\"checked\" ";
        } ?> />
        <br/>
        <div class='yasr_pro_stars_set' id='yasr_pro_custom_set_choosen_stars'>
            <?php
            echo '<img src="' . YASR_IMG_DIR . 'stars_rater_yasr.png">';
            ?>
        </div>

    </div>

    <div class='yasr_choose_stars' id='yasr_pro_custom_set_choosen_stars'>

        <input type='radio' name='yasr_style_options[stars_set_free]' value='rater-oxy'
               class='yasr-general-options-scheme-color' <?php if ($style_options['stars_set_free'] === 'rater-oxy') {
            echo " checked=\"checked\" ";
        } ?> />
        <br/>
        <div class='yasr_pro_stars_set' id='yasr_pro_custom_set_choosen_stars'>
            <?php
            echo '<img src="' . YASR_IMG_DIR . 'stars_rater_oxy.png">';
            ?>
        </div>

    </div>

    <div id="yasr-settings-stylish-stars" style="margin-top: 15px">
        <div id="yasr-settings-stylish-image-container">
            <?php
            echo "<img id=\"yasr-settings-stylish-image\" src=" . YASR_IMG_DIR . "yasr-pro-stars.png>";
            ?>
        </div>
    </div>

    <div id='yasr-settings-stylish-text'>

        <?php
        $text = __('Looking for more?', 'yet-another-stars-rating');
        $text .= '<br />';
        $text .= sprintf(__('Upgrade to %s', 'yet-another-stars-rating'), '<a href="?page=yasr_settings_page-pricing">Yasr Pro!</a>');

        echo $text;
        ?>

    </div>

    <script type="text/javascript">

        jQuery('#yasr-settings-stylish-stars').mouseover(function () {
            jQuery('#yasr-settings-stylish-text').css("visibility", "visible");
            jQuery('#yasr-settings-stylish-image').css("opacity", 0.4);
        });

    </script>

    <?php
        submit_button(__('Save Settings', 'yet-another-stars-rating'));
    }

?>
