import React              from "react";
import {yasrDrawRankings} from "./ranking";

const { __ } = wp.i18n; // Import __() from wp.i18n

/*** Constant used by yasr

yasrCommonData (postid, ajaxurl, loggedUser, visitorStatsEnabled, loaderHtml, tooltipValues')

***/

/****** Do these actions on document load ******/
document.addEventListener('DOMContentLoaded', function(event) {

    //Search and set all div with class yasr-rater-stars
    yasrSearchStarsDom('yasr-rater-stars');

    //Search and set all div with class yasr-rater-stars-vv
    yasrSearchStarsDom('yasr-rater-stars-vv');

    //Search and set all div with class yasr-multiset-visitors-rater
    yasrSearchStarsDom('yasr-multiset-visitors-rater');

    //Drow Rankings
    yasrDrawRankings();

});

/**
 * Search for divs with defined classname
 */
export function yasrSearchStarsDom (starsClass) {
    //At pageload, check if there is some shortcode with class yasr-rater-stars
    const yasrRaterInDom = document.getElementsByClassName(starsClass);
    //If so, call the function to set the rating
    if (yasrRaterInDom.length > 0) {

        //stars class for most shortcodes
        if(starsClass === 'yasr-rater-stars' || starsClass === 'yasr-ranking-stars') {
            yasrSetRating(yasrRaterInDom);
        }

        if(starsClass === 'yasr-rater-stars-vv') {
            yasrVisitorVotesFront(yasrRaterInDom);
            if (yasrCommonData.visitorStatsEnabled === 'yes') {
                let yasrStatsInDom = document.getElementsByClassName('yasr-dashicons-visitor-stats');
                if (yasrStatsInDom) {
                    yasrDrawTipsProgress (yasrStatsInDom);
                }
            }
        }

        if (starsClass === 'yasr-multiset-visitors-rater') {
            yasrRaterVisitorsMultiSet(yasrRaterInDom)
        }
    }
}

/****** Tooltip function ******/

//used in shortcode page and ajax page
function yasrDrawTipsProgress (yasrStatsInDom) {

    //htmlcheckid declared false
    var htmlIdChecked = false;

    for (var i = 0; i < yasrStatsInDom.length; i++) {

        (function (i) {

            var htmlId = '#'+yasrStatsInDom.item(i).id;
            var postId = yasrStatsInDom.item(i).getAttribute('data-postid');

            var data = {
                action: 'yasr_stats_visitors_votes',
                post_id: postId
            };

            //Convert in a string
            //var dataToSend = jsObject_to_URLEncoded(data);

            var initialContent = '<span style="color: #0a0a0a">Loading...</span>';

            tippy(htmlId, {
                content: initialContent,
                theme: 'yasr',
                arrow: 'true',
                arrowType: 'round',

                //When support for IE will be dropped out, this will become onShow(tip)
                onShow: function onShow(tip) {

                    if (htmlId !== htmlIdChecked) {

                        //must be post or wont work
                        jQuery.post(yasrCommonData.ajaxurl, data, function (response) {
                            response = JSON.parse(response);
                            tip.setContent(response);
                        });
                    }
                },
                onHidden: function onHidden() {
                    htmlIdChecked = htmlId;
                }

            });

        })(i);

    }

}

/****** End tooltipfunction ******/

//this is the function that print the overall rating shortcode, get overall rating and starsize
function yasrSetRaterValue (starSize, htmlId) {

    //convert to be a number
    starSize = parseInt(starSize);

    raterJs({
        starSize: starSize,
        step: 0.1,
        showToolTip: false,
        readOnly: true,
        element: document.getElementById(htmlId),
    });

}

function yasrSetRating (yasrRatingsInDom) {

    //Check in the object
    for (let i = 0; i < yasrRatingsInDom.length; i++) {
        const htmlId    = yasrRatingsInDom.item(i).id;
        const starSize  = yasrRatingsInDom.item(i).getAttribute('data-rater-starsize');
        yasrSetRaterValue(starSize, htmlId);
    }

}

function yasrVisitorVotesFront (yasrRaterVVInDom) {

    //Check in the object
    for (let i = 0; i < yasrRaterVVInDom.length; i++) {

        (function(i) {

            let rating            = yasrRaterVVInDom.item(i).getAttribute('data-rating');
            let readonlyShortcode = yasrRaterVVInDom.item(i).getAttribute('data-readonly-attribute');
            let readonly          = yasrRaterVVInDom.item(i).getAttribute('data-rater-readonly');

            if (readonlyShortcode === null) {
                readonlyShortcode = false;
            }

            readonlyShortcode = yasrTrueFalseStringConvertion(readonlyShortcode);
            readonly          = yasrTrueFalseStringConvertion(readonly);

            //if comes from shortcode attribute, and is true, readonly is always true
            if (readonlyShortcode === true) {
                readonly = true;
            }

            let postId = yasrRaterVVInDom.item(i).getAttribute('data-rater-postid');
            let htmlId = yasrRaterVVInDom.item(i).id;
            let uniqueId = htmlId.replace('yasr-visitor-votes-rater-', '');
            let starSize = parseInt(yasrRaterVVInDom.item(i).getAttribute('data-rater-starsize'));
            let nonce = yasrRaterVVInDom.item(i).getAttribute('data-rater-nonce');
            let isSingular = yasrRaterVVInDom.item(i).getAttribute('data-issingular');

            let containerVotesNumber   = 'yasr-vv-votes-number-container-' + uniqueId;
            let containerAverageNumber = 'yasr-vv-average-container-' + uniqueId;
            let loaderContainer  = 'yasr-vv-loader-' + uniqueId;
            let spanBottom = false;

            if(yasrCommonData.ajaxEnabled === 'yes') {
                let cpt = yasrRaterVVInDom.item(i).getAttribute('data-cpt');

                if(cpt === '') {
                    cpt = 'posts';
                }

                let urlVisitorVotes = 'wp/v2/'+ cpt +'/' + postId + '?_fields=yasr_visitor_votes&_wpnonce='+yasrCommonData.nonce;

                jQuery.get(yasrCommonData.restEndpoint + urlVisitorVotes).done(
                    function (data) {
                        let readonly;
                        //if has readonly attribute, it is always true
                        if(readonlyShortcode === true) {
                            readonly = true;
                        } else {
                            readonly = data.yasr_visitor_votes.stars_attributes.read_only;
                        }

                        if (data.yasr_visitor_votes.number_of_votes > 0) {
                            rating = data.yasr_visitor_votes.sum_votes / data.yasr_visitor_votes.number_of_votes;
                        } else {
                            rating = 0;
                        }
                        rating   = rating.toFixed(1);
                        rating   = parseFloat(rating);

                        yasrSetVisitorVotesRater(starSize, rating, postId, readonly, htmlId, uniqueId, nonce, isSingular, loaderContainer);

                        //do this only if yasr_visitor_votes has not the readonly attribute
                        if(readonlyShortcode !== true) {
                            document.getElementById(containerVotesNumber).innerHTML = data.yasr_visitor_votes.number_of_votes;
                            document.getElementById(containerAverageNumber).innerHTML = rating;

                            //insert span with text after the average
                            if(data.yasr_visitor_votes.stars_attributes.span_bottom !== false) {
                                spanBottom = data.yasr_visitor_votes.stars_attributes.span_bottom;
                                let yasrTotalAverageContainer = document.getElementById(loaderContainer);
                                yasrTotalAverageContainer.innerHTML = spanBottom;
                            }
                        }

                }).fail(
                    function(e, x, settings, exception) {
                        console.info(__('YASR ajax call failed. Showing ratings from html', 'yet-another-stars-rating'));
                        yasrSetVisitorVotesRater(starSize, rating, postId, readonly, htmlId, uniqueId, nonce, isSingular, loaderContainer);

                        //Unhide the div below the stars
                        if(readonlyShortcode !== true) {
                            document.getElementById('yasr-below-stars-hidden-'+uniqueId).style.display = '';
                        }
                    });
            } else {
                yasrSetVisitorVotesRater(starSize, rating, postId, readonly, htmlId, uniqueId, nonce, isSingular, loaderContainer);
            }

        })(i);

    }//End for

}

function yasrSetVisitorVotesRater (starSize, rating, postId, readonly, htmlId, uniqueId, nonce, isSingular, loaderContainer) {

    //Be sure is a number and not a string
    rating = parseFloat(rating);

    //raterjs accepts only boolean for readOnly element
    readonly = yasrTrueFalseStringConvertion(readonly);

    let containerVotesNumber   = 'yasr-vv-votes-number-container-' + uniqueId;
    let containerAverageNumber = 'yasr-vv-average-container-' + uniqueId;

    raterJs({
        starSize: starSize,
        rating: rating,
        step: 1,
        showToolTip: false,
        readOnly: readonly,
        element: document.getElementById(htmlId),

        rateCallback: function rateCallback(rating, done) {
            //show the loader
            document.getElementById(loaderContainer).innerHTML = yasrCommonData.loaderHtml;

            //Creating an object with data to send
            var data = {
                action: 'yasr_send_visitor_rating',
                rating: rating,
                post_id: postId,
                nonce_visitor: nonce,
                is_singular : isSingular
            };

            this.setRating(rating);
            this.disable();

            //Send value to the Server
            jQuery.post(yasrCommonData.ajaxurl, data, function (response) {
                //decode json
                response = JSON.parse(response);
                //hide the loader
                document.getElementById(containerVotesNumber).innerHTML   = response.number_of_votes;
                document.getElementById(containerAverageNumber).innerHTML = response.average_rating;
                document.getElementById(loaderContainer).innerHTML        = response.rating_saved_text ;

            });
            done();
        }
    });

}

function yasrRaterVisitorsMultiSet (yasrMultiSetVisitorInDom) {

    //will have field id and vote
    var ratingObject = "";

    //an array with all the ratings objects
    var ratingArray = [];

    //Check in the object
    for (var i = 0; i < yasrMultiSetVisitorInDom.length; i++) {

        (function (i) {

            var htmlId = yasrMultiSetVisitorInDom.item(i).id;
            var readonly = yasrMultiSetVisitorInDom.item(i).getAttribute('data-rater-readonly');

            readonly = yasrTrueFalseStringConvertion(readonly);

            var elem = document.querySelector("#" + htmlId);
            raterJs({
                starSize: 16,
                step: 1,
                showToolTip: false,
                readOnly: readonly,
                element: elem,

                rateCallback: function rateCallback(rating, done) {

                    var postId = elem.getAttribute('data-rater-postid');
                    var setId = elem.getAttribute('data-rater-setid');
                    var setIdField = elem.getAttribute('data-rater-set-field-id');

                    //Just leave 1 number after the .
                    rating = rating.toFixed(1);
                    //Be sure is a number and not a string
                    var vote = parseInt(rating);

                    this.setRating(vote); //set the new rating

                    ratingObject = {
                        postid: postId,
                        setid: setId,
                        field: setIdField,
                        rating: vote
                    };

                    //creating rating array
                    ratingArray.push(ratingObject);

                    done();

                }

            });

        })(i);

    }

    jQuery('.yasr-send-visitor-multiset').on('click', function() {

        const multiSetPostId = this.getAttribute('data-postid');
        const multiSetId     = this.getAttribute('data-setid');
        const nonce          = this.getAttribute('data-nonce');

        jQuery('#yasr-send-visitor-multiset-'+multiSetPostId+'-'+multiSetId).hide();
        jQuery('#yasr-loader-multiset-visitor-'+multiSetPostId+'-'+multiSetId).show();

        var data = {
            action: 'yasr_visitor_multiset_field_vote',
            nonce: nonce,
            post_id: multiSetPostId,
            rating: ratingArray,
            set_type: multiSetId
        };

        //Send value to the Server
        jQuery.post(yasrCommonData.ajaxurl, data, function(response) {
            jQuery('#yasr-loader-multiset-visitor-'+multiSetPostId+'-'+multiSetId).text(response);
        });

    });
    
} //End function

function yasrTrueFalseStringConvertion(string) {

    if (typeof string === 'undefined' || string === null || string === '') {
        string = true;
    }

    //Convert string to boolean
    if (string === 'true' || string === '1') {
        string = true;
    }
    if (string === 'false' || string === '0') {
        string = false;
    }

    return string;

}
