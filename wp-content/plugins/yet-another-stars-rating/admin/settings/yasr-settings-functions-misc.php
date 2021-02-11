<?php

/**Drow settings tab*/
function yasr_settings_tabs( $active_tab )
{
    ?>

    <h2 class="nav-tab-wrapper yasr-no-underline">

        <a href="?page=yasr_settings_page&tab=general_settings"
           id="general_settings"
           class="nav-tab <?php 
    if ( $active_tab === 'general_settings' ) {
        echo  'nav-tab-active' ;
    }
    ?>">
            <?php 
    _e( 'General Settings', 'yet-another-stars-rating' );
    ?>
        </a>

        <a href="?page=yasr_settings_page&tab=manage_multi"
           id="manage_multi"
           class="nav-tab <?php 
    if ( $active_tab === 'manage_multi' ) {
        echo  'nav-tab-active' ;
    }
    ?>">
            <?php 
    _e( 'Multi Sets', 'yet-another-stars-rating' );
    ?>
        </a>

        <a href="?page=yasr_settings_page&tab=style_options"
           id="style_options"
           class="nav-tab <?php 
    if ( $active_tab === 'style_options' ) {
        echo  'nav-tab-active' ;
    }
    ?>">
            <?php 
    _e( 'Aspect & Styles', 'yet-another-stars-rating' );
    ?>
        </a>

        <?php 
    do_action( 'yasr_add_settings_tab', $active_tab );
    $rating_plugin_exists = new YasrImportRatingPlugins();
    
    if ( $rating_plugin_exists->yasr_search_wppr() || $rating_plugin_exists->yasr_search_rmp() || $rating_plugin_exists->yasr_search_kksr() || $rating_plugin_exists->yasr_search_mr() ) {
        ?>
            <a href="?page=yasr_settings_page&tab=migration_tools"
               id="migration_tools"
               class="nav-tab <?php 
        if ( $active_tab === 'migration_tools' ) {
            echo  'nav-tab-active' ;
        }
        ?>">
                <?php 
        _e( "Migration Tools", 'yet-another-stars-rating' );
        ?>
            </a>
            <?php 
    }
    
    ?>

    </h2>

    <?php 
}

function yasr_upgrade_pro_box( $position = false )
{
    
    if ( yasr_fs()->is_free_plan() ) {
        
        if ( $position && $position === "bottom" ) {
            $yasr_upgrade_class = "yasr-donatedivbottom";
        } else {
            $yasr_upgrade_class = "yasr-donatedivdx";
        }
        
        ?>

        <div class="<?php 
        echo  $yasr_upgrade_class ;
        ?>" style="display: none">

            <h2 class="yasr-donate-title" style="color: #34A7C1">
                <?php 
        _e( 'Upgrade to YASR Pro', 'yet-another-stars-rating' );
        ?>
            </h2>

            <div class="yasr-upgrade-to-pro">
                <ul>
                    <li><strong><?php 
        _e( ' User Reviews', 'yet-another-stars-rating' );
        ?></strong></li>
                    <li><strong><?php 
        _e( ' Custom Rankings', 'yet-another-stars-rating' );
        ?></strong></li>
                    <li><strong><?php 
        _e( ' 20 + ready to use themes', 'yet-another-stars-rating' );
        ?></strong></li>
                    <li><strong><?php 
        _e( ' Upload your own theme', 'yet-another-stars-rating' );
        ?></strong></li>
                    <li><strong><?php 
        _e( ' Dedicate support', 'yet-another-stars-rating' );
        ?></strong></li>
                    <li><strong><?php 
        _e( ' ...And much more!!', 'yet-another-stars-rating' );
        ?></strong></li>
                </ul>
                <a href="<?php 
        echo  yasr_fs()->get_upgrade_url() ;
        ?>">
                    <button class="button button-primary">
                        <span style="font-size: large; font-weight: bold;">
                            <?php 
        _e( 'Upgrade Now', 'yet-another-stars-rating' );
        ?>
                        </span>
                    </button>
                </a>
                <div style="display: block; margin-top: 10px; margin-bottom: 10px; ">
                 --- or ---
                </div>
                <a href="<?php 
        echo  yasr_fs()->get_trial_url() ;
        ?>">
                    <button class="button button-primary">
                        <span style="display: block; font-size: large; font-weight: bold; margin: -3px;">
                            <?php 
        _e( 'Start Free Trial', 'yet-another-stars-rating' );
        ?>
                        </span>
                        <span style="display: block; margin-top: -10px; font-size: smaller;">
                             <?php 
        _e( 'No credit-card, risk free!', 'yet-another-stars-rating' );
        ?>
                        </span>
                    </button>
                </a>
            </div>

        </div>

        <?php 
    }

}

/*
 *   Add a box on with the resouces
 *   Since version 1.9.5
 *
*/
function yasr_resources_box( $position = false )
{
    
    if ( $position && $position === "bottom" ) {
        $yasr_metabox_class = "yasr-donatedivbottom";
    } else {
        $yasr_metabox_class = "yasr-donatedivdx";
    }
    
    $div = "<div class='{$yasr_metabox_class}' id='yasr-resources-box' style='display:none;'>";
    $text = '<div class="yasr-donate-title">Resources</div>';
    $text .= '<div class="yasr-donate-single-resource">
                <span class="dashicons dashicons-star-filled" style="color: #ccc"></span>
                    <a target="blank" href="https://yetanotherstarsrating.com/?utm_source=wp-plugin&utm_medium=settings_resources&utm_campaign=yasr_settings&utm_content=yasr_official">' . __( 'YASR official website', 'yet-another-stars-rating' ) . '</a>
              </div>';
    $text .= '<div class="yasr-donate-single-resource">
                <span class="dashicons dashicons-edit" style="color: #ccc"></span>
                    <a target="blank" href="https://yetanotherstarsrating.com/docs/?utm_source=wp-plugin&utm_medium=settings_resources&utm_campaign=yasr_settings&utm_content=documentation">' . __( 'Documentation', 'yet-another-stars-rating' ) . '</a>
              </div>';
    $text .= '<div class="yasr-donate-single-resource">
                <span class="dashicons dashicons-book-alt" style="color: #ccc"></span>
                    <a target="blank" href="https://yetanotherstarsrating.com/docs/faq/?utm_source=wp-plugin&utm_medium=settings_resources&utm_campaign=yasr_settings&utm_content=faq">' . __( 'F.A.Q.', 'yet-another-stars-rating' ) . '</a>
              </div>';
    $text .= '<div class="yasr-donate-single-resource">
                <span class="dashicons dashicons-video-alt3" style="color: #ccc"></span>
                    <a target="blank" href="https://www.youtube.com/channel/UCU5jbO1PJsUUsCNbME9S-Zw">' . __( 'Youtube channel', 'yet-another-stars-rating' ) . '</a>
              </div>';
    $text .= '<div class="yasr-donate-single-resource">
                <span class="dashicons dashicons-smiley" style="color: #ccc"></span>
                    <a target="blank" href="https://yetanotherstarsrating.com/#yasr-pro?utm_source=wp-plugin&utm_medium=settings_resources&utm_campaign=yasr_settings&utm_content=yasr-pro">
                        Yasr Pro
                    </a>
              </div>';
    $div_and_text = $div . $text . '</div>';
    echo  $div_and_text ;
}

/** Add a box on the right for asking to rate 5 stars on Wordpress.org
 *   Since version 0.9.0
 */
function yasr_ask_rating( $position = false )
{
    
    if ( $position && $position === "bottom" ) {
        $yasr_metabox_class = "yasr-donatedivbottom";
    } else {
        $yasr_metabox_class = "yasr-donatedivdx";
    }
    
    $div = "<div class='{$yasr_metabox_class}' id='yasr-ask-five-stars' style='display:none;'>";
    $text = '<div class="yasr-donate-title">' . __( 'Can I ask your help?', 'yet-another-stars-rating' ) . '</div>';
    $text .= '<div style="font-size: 32px; color: #F1CB32; text-align:center; margin-bottom: 20px; margin-top: -5px;">
                <span class="dashicons dashicons-star-filled" style="font-size: 26px;"></span>
                <span class="dashicons dashicons-star-filled" style="font-size: 26px;"></span>
                <span class="dashicons dashicons-star-filled" style="font-size: 26px;"></span>
                <span class="dashicons dashicons-star-filled" style="font-size: 26px;"></span>
                <span class="dashicons dashicons-star-filled" style="font-size: 26px;"></span>
            </div>';
    $text .= __( 'Please rate YASR 5 stars on', 'yet-another-stars-rating' );
    $text .= ' <a href="https://wordpress.org/support/view/plugin-reviews/yet-another-stars-rating?filter=5">
        WordPress.org.</a><br />';
    $text .= __( ' It will require just 1 min but it\'s a HUGE help for me. Thank you.', 'yet-another-stars-rating' );
    $text .= "<br /><br />";
    $text .= "<em>> Dario Curvino</em>";
    $div_and_text = $div . $text . '</div>';
    echo  $div_and_text ;
}

/**
 * Print Sponsors
 *
 * @author Dario Curvino <@dudo>
 * @since  2.5.9
 * @param false $position
 */
function yasr_sponsors( $position = false )
{
    
    if ( $position && $position === "bottom" ) {
        $yasr_metabox_class = "yasr-donatedivbottom";
    } else {
        $yasr_metabox_class = "yasr-donatedivdx";
    }
    
    $div = "<div class='{$yasr_metabox_class}' id='yasr-sponsors' style='display:none;'>";
    $link = '<a href="https://supporthost.net/a69">';
    $title = '<div class="yasr-donate-title">' . __( 'Optimized WordPress-Hosting!', 'yet-another-stars-rating' ) . '</div>';
    $logo = '<div style="font-size: 32px; color: #F1CB32; text-align:center; margin-bottom: 20px; margin-top: -5px;">
                    <img src="' . YASR_IMG_DIR . '/logo-supporthost.svg" alt="supporthost">
                 </div>';
    $end_link = '</a>';
    $text = __( 'Boost your WordPress performance with SupportHost, with highly optimized servers on USA and Europe, 
    LiteSpeed + LSCache, free transfer and expert support!', 'yet-another-stars-rating' );
    $text .= '<br /> <br /><strong>' . __( 'Get 25% off by entering YASR at checkout', 'yet-another-stars-rating' ) . '</strong>';
    $div_and_text = $div . $link . $title . $logo . $end_link . $text . '</div>';
    echo  $div_and_text ;
}

/****
Yasr Right settings panel, since version 1.9.5
 ****/
function yasr_right_settings_panel( $position = false )
{
    do_action( 'yasr_right_settings_panel_box', $position );
    yasr_upgrade_pro_box( $position );
    yasr_resources_box( $position );
    yasr_sponsors( $position );
    yasr_ask_rating( $position );
}

/** Change default admin footer on yasr settings pages
 *       $text is the default wordpress text
 *        Since 0.8.9
 */
add_filter( 'admin_footer_text', 'yasr_custom_admin_footer' );
function yasr_custom_admin_footer( $text )
{
    
    if ( isset( $_GET['page'] ) ) {
        $yasr_page = $_GET['page'];
        
        if ( $yasr_page === 'yasr_settings_page' ) {
            $custom_text = ' | <i>';
            $custom_text .= sprintf(
                __( 'Thank you for using <a href="%s" target="_blank">Yet Another Stars Rating</a>.
                        Please <a href="%s" target="_blank">rate it</a> 5 stars on <a href="%s" target="_blank">WordPress.org</a>', 'yet-another-stars-rating' ),
                'https://yetanotherstarsrating.com/?utm_source=wp-plugin&utm_medium=footer&utm_campaign=yasr_settings',
                'https://wordpress.org/support/view/plugin-reviews/yet-another-stars-rating?filter=5',
                'https://wordpress.org/support/view/plugin-reviews/yet-another-stars-rating?filter=5'
            );
            $custom_text .= '</i>';
            return $text . $custom_text;
        }
        
        return $text;
    }
    
    return $text;
}
