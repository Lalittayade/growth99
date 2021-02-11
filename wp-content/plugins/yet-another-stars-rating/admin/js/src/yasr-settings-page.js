/****** Yasr Settings Page ******/

document.addEventListener('DOMContentLoaded', function(event) {

    if (typeof document.getElementsByClassName('nav-tab-active')[0] === 'undefined') {
       return;
    }

    //get active Tab
    let activeTab = document.getElementsByClassName('nav-tab-active')[0].id;

    //-------------------General Settings Code---------------------
    if (activeTab === 'general_settings') {

        let autoInsertEnabled = document.getElementById('yasr_auto_insert_switch').checked;
        let starsTitleEnabled = document.getElementById('yasr-general-options-stars-title-switch').checked;
        let textBeforeStars   = document.getElementById('yasr-general-options-text-before-stars-switch').checked;


        if (autoInsertEnabled === false) {
            jQuery('.yasr-auto-insert-options-class').prop('disabled', true);
        }

        if(starsTitleEnabled === false) {
            jQuery('.yasr-stars-title-options-class').prop('disabled', true);
        }

        //First Div, for auto insert
        jQuery('#yasr_auto_insert_switch').change(function () {
            if (jQuery(this).is(':checked')) {
                jQuery('.yasr-auto-insert-options-class').prop('disabled', false);
            } else {
                jQuery('.yasr-auto-insert-options-class').prop('disabled', true);
            }
        });

        //Second Div, for stars title
        jQuery('#yasr-general-options-stars-title-switch').change(function () {
            if (jQuery(this).is(':checked')) {
                jQuery('.yasr-stars-title-options-class').prop('disabled', false);
            } else {
                jQuery('.yasr-stars-title-options-class').prop('disabled', true);
            }
        });

        //for text before stars
        if (textBeforeStars === false) {
            jQuery(".yasr-general-options-text-before").find(':input').prop('disabled', true);
        }

        jQuery('#yasr-general-options-text-before-stars-switch').change(function () {
            if (jQuery(this).is(':checked')) {

                jQuery(".yasr-general-options-text-before").find(':input').prop('disabled', false);
                jQuery('#yasr-general-options-custom-text-before-overall').val('Our Score');
                jQuery('#yasr-general-options-custom-text-before-visitor').val('Click to rate this post!');
                jQuery('#yasr-general-options-custom-text-after-visitor').val('[Total: %total_count%  Average: %average%]');
                jQuery('#yasr-general-options-custom-text-must-sign-in').val('You must sign in to vote');
                jQuery('#yasr-general-options-custom-text-already-rated').val('You have already voted for this article');

            } else {
                jQuery(".yasr-general-options-text-before").find(':input').prop('disabled', true);
            }

        });

        jQuery('#yasr-doc-custom-text-link').on('click', function () {
            jQuery('#yasr-doc-custom-text-div').toggle('slow');
            return false;
        });

        jQuery('#yasr-stats-explained-link').on('click', function () {
            jQuery('#yasr-stats-explained').toggle('slow');
            return false;
        });

    } //End if general settings


    //--------------Multi Sets Page ------------------
    if (activeTab === 'manage_multi') {

        let nMultiSet = document.getElementById('n-multiset').value;

        jQuery('#yasr-multi-set-doc-link').on('click', function () {
            jQuery('#yasr-multi-set-doc-box').toggle("slow");
        });

        jQuery('#yasr-multi-set-doc-link-hide').on('click', function () {
            jQuery('#yasr-multi-set-doc-box').toggle("slow");
        });

        if (nMultiSet === 1) {
            var counter = jQuery("#yasr-edit-form-number-elements").attr('value');

            counter++;

            jQuery("#yasr-add-field-edit-multiset").on('click', function () {
                if (counter > 9) {
                    jQuery('#yasr-element-limit').show();
                    jQuery('#yasr-add-field-edit-multiset').hide();
                    return false;
                }

                var newTextBoxDiv = jQuery(document.createElement('tr'));
                newTextBoxDiv.html('<td colspan="2">Element #' + counter + ' <input type="text" name="edit-multi-set-element-' + counter + '" value="" ></td>');
                newTextBoxDiv.appendTo("#yasr-table-form-edit-multi-set");
                counter++;
            });


        } //End if ($n_multi_set == 1)

        if (nMultiSet > 1) {

            //If more then 1 set is used...
            jQuery('#yasr-button-select-set-edit-form').on("click", function () {

                var data = {
                    action: 'yasr_get_multi_set',
                    set_id: jQuery('#yasr_select_edit_set').val()
                };

                jQuery.post(ajaxurl, data, function (response) {
                    jQuery('#yasr-multi-set-response').show();
                    jQuery('#yasr-multi-set-response').html(response);
                });

                return false; // prevent default click action from happening!

            });

            jQuery(document).ajaxComplete(function () {
                var counter = jQuery("#yasr-edit-form-number-elements").attr('value');
                counter++;

                jQuery("#yasr-add-field-edit-multiset").on('click', function () {
                    if (counter > 9) {
                        jQuery('#yasr-element-limit').show();
                        jQuery('#yasr-add-field-edit-multiset').hide();
                        return false;
                    }
                    var newTextBoxDiv = jQuery(document.createElement('tr'));
                    newTextBoxDiv.html('<td colspan="2">Element #' + counter + ' <input type="text" name="edit-multi-set-element-' + counter + '" value="" ></td>');
                    newTextBoxDiv.appendTo("#yasr-table-form-edit-multi-set");
                    counter++;
                });

            });

        } //End if ($n_multi_set > 1)

    } //end if active_tab=='manage_multi'

    if (activeTab === 'style_options') {
        wp.codeEditor.initialize(
            document.getElementById('yasr_style_options_textarea'),
            yasr_cm_settings
        );

        jQuery('#yasr-color-scheme-preview-link').on('click', function () {
            jQuery('#yasr-color-scheme-preview').toggle('slow');
            return false; // prevent default click action from happening!
        });

    }

    if (activeTab === 'migration_tools') {
        jQuery('#yasr-import-ratemypost-submit').on('click', function () {

            //show loader on click
            document.getElementById('yasr-import-ratemypost-answer').innerHTML = '<img src="'
                + yasrCommonDataAdmin.loaderHtml + '"</img>';

            var nonce = document.getElementById('yasr-import-rmp-nonce').value;

            var data = {
                action: 'yasr_import_ratemypost',
                nonce: nonce
            };

            jQuery.post(ajaxurl, data, function (response) {
                response = JSON.parse(response);
                document.getElementById('yasr-import-ratemypost-answer').innerHTML = response;
            });

        });

        jQuery('#yasr-import-wppr-submit').on('click', function () {

            //show loader on click
            document.getElementById('yasr-import-wppr-answer').innerHTML = '<img src="'
                + yasrCommonDataAdmin.loaderHtml + '"</img>';

            var nonce = document.getElementById('yasr-import-wppr-nonce').value;

            var data = {
                action: 'yasr_import_wppr',
                nonce: nonce
            };

            jQuery.post(ajaxurl, data, function (response) {
                //response = JSON.parse(response);
                document.getElementById('yasr-import-wppr-answer').innerHTML = response;
            });

        });

        jQuery('#yasr-import-kksr-submit').on('click', function () {

            //show loader on click
            document.getElementById('yasr-import-kksr-answer').innerHTML = '<img src="'
                + yasrCommonDataAdmin.loaderHtml + '"</img>';

            var nonce = document.getElementById('yasr-import-kksr-nonce').value;

            var data = {
                action: 'yasr_import_kksr',
                nonce: nonce
            };

            jQuery.post(ajaxurl, data, function (response) {
                //response = JSON.parse(response);
                document.getElementById('yasr-import-kksr-answer').innerHTML = response;
            });

        });

        //import multi rating
        jQuery('#yasr-import-mr-submit').on('click', function () {

            //show loader on click
            document.getElementById('yasr-import-mr-answer').innerHTML = '<img src="'
                + yasrCommonDataAdmin.loaderHtml + '"</img>';

            var nonce = document.getElementById('yasr-import-mr-nonce').value;

            var data = {
                action: 'yasr_import_mr',
                nonce: nonce
            };

            jQuery.post(ajaxurl, data, function (response) {
                //response = JSON.parse(response);
                document.getElementById('yasr-import-mr-answer').innerHTML = response;
            });

        });
    }
});

/****** End Yasr Settings Page ******/