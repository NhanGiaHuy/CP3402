
/*** Constant used by yasr

yasrCommonData (postid, ajaxurl, loggedUser, visitorStatsEnabled, loaderHtml, tooltipValues)

yasrMultiSetData (setType, nonce)


***/

//this is the function that print the overall rating shortcode, get overall rating and starsize
function yasrOverallRatingFront (overallRating, starSize) {

    //convert to be a number
    overallRating = parseFloat(overallRating);
    starSize = parseInt(starSize);

    raterJs({
        starSize: starSize,
        step: 0.1,
        showToolTip: false,
        rating: overallRating,
        readOnly: true,
        element: document.querySelector("#yasr-overall-rating-rater"),
    });

}


function yasrVisitorVotesFront (mediumRating, starSize, readonly, postId) {

    mediumRating = parseFloat(mediumRating);
    starSize = parseInt(starSize);

    //Convert string to boolean
    if (readonly === 'true') {
        readonly = true;
    }
    if (readonly === 'false') {
        readonly = false;
    }

    var yasrVVElementToGet = '#yasr-visitor-votes-rater-'+postId;

    raterJs({
        starSize: starSize,
        step: 1,
        showToolTip: false,
        rating: mediumRating,
        readOnly: readonly,
        element: document.querySelector(yasrVVElementToGet),

        rateCallback: function rateCallback(rating, done) {

            //show the loader
            document.getElementById('yasr_visitor_votes_' + postId).innerHTML=yasrCommonData.loaderHtml;

            //Just leave 1 number after the .
            var rating = rating.toFixed(1);
            //Be sure is a number and not a string
            var rating = parseFloat(rating);

            //Creating an object with data to send
            var data = {
                action: 'yasr_send_visitor_rating',
                rating: rating,
                post_id: postId,
                size: starSize,
                nonce_visitor: yasrVisitorsVotesData.nonceVisitor
            };

            //Convert in a string
            var dataToSend = jsObject_to_URLEncoded(data);

            //Create a new request
            var yasrVVAjaxCall = new Request(yasrCommonData.ajaxurl ,{
                method: 'post',
                headers: new Headers({
                    "Content-Type": "application/x-www-form-urlencoded"
                }),
                body: dataToSend
            });

            //Do the ajax call
            fetch(yasrVVAjaxCall)
                .then(checkResponse)
                .then (function(response) {
                    //return the new average rating
                    return response.json();
                })

                .then(function(data) {

                    document.getElementById('yasr_visitor_votes_' + postId).innerHTML = data;

                    raterJs({
                        starSize: starSize,
                        step: 1,
                        showToolTip: false,
                        rating: rating,
                        readOnly: true,
                        element: document.querySelector(yasrVVElementToGet)
                    });

                })
                .then(done)
                .catch(function(err) {
                    console.log('Error with ajax call', err);
                });

            done();

        }

    });

}


/****** Yasr shortcode page ******/

document.addEventListener('DOMContentLoaded', function(event) {

    //if is defined yasrVisitorsVotesData means that the shortcode is used
    if (typeof yasrVisitorsVotesData !== 'undefined') {

        yasrVisitorsVotes ();

        if (yasrCommonData.visitorStatsEnabled == 'yes') {

            yasrDrawTipsProgress ();

        }

    }

    if (typeof yasrMultiSetData !== 'undefined') {

        yasrVisitorsMultiSet ();

    }

    if (typeof yasrMostHighestRanking !== 'undefined') {

        yasrMostOrHighestRatedChart();

    }

});


    /*** Yasr visitor Votes ***/

    function yasrVisitorsVotes () {

        jQuery('.rateit').bind('over', function (event, value) { jQuery(this).attr('title', yasrCommonData.tooltipValues[value-1]); });

        //on vote...
        jQuery('.yasr_visitor_votes_stars_div').on('rated', function() {

            var el = jQuery(this);
            var value = el.rateit('value');
            var value = value.toFixed(1); //

            var postid = jQuery( this ).data('postid');
            var classSize = jQuery( this ).attr('class');

            var voteIfUserAlredyRated = jQuery('#yasr-user-vote-'+postid).data('yasr-already-voted');

            if (value < 1) {
                jQuery('#yasr_visitor_votes_' + postid).html('You can\'t vote 0');
            }

            else {

                jQuery('#yasr_visitor_votes_' + postid).html(yasrCommonData.loaderHtml);

                    var data = {
                        action: 'yasr_send_visitor_rating',
                        rating: value,
                        post_id: postid,
                        size: classSize,
                        nonce_visitor: yasrVisitorsVotesData.nonceVisitor
                    };

                //Send value to the Server
                jQuery.post(yasrCommonData.ajaxurl, data, function(response) {
                    //response
                    jQuery('#yasr_visitor_votes_' + postid).html(response);
                    jQuery('.rateit').rateit();

                }) ;

            } //End else value <1

        });//End function insert/update vote


    }
    /*** End Yasr Visitor Votes ***/


    function yasrVisitorsMultiSet () {

        //will have field id and vote
        var ratingObject = "";

        //an array with all the ratingonjects
        var ratingArray = [];

        var postId = yasrCommonData.postid;
        var setType = yasrMultiSetData.setType;
        var nonce = yasrMultiSetData.nonceVisitor;

        jQuery('.yasr-visitor-multi-'+postId+'-'+setType).on('rated', function() {
            var el = jQuery(this);
            var value = el.rateit('value');
            var value = value.toFixed(1);
            var idField = el.attr('id');

            ratingObject = {

                field: idField,
                rating: value

            };

            //creating rating array
            ratingArray.push(ratingObject);

        });

        jQuery('#yasr-send-visitor-multiset-'+postId+'-'+setType).on('click', function() {

            jQuery('#yasr-send-visitor-multiset-'+postId+'-'+setType).hide();

            jQuery('#yasr-loader-multiset-visitor-'+postId+'-'+setType).show();

            var data = {

                action: 'yasr_visitor_multiset_field_vote',
                nonce: nonce,
                post_id: postId,
                rating: ratingArray,
                set_type: setType

            };

            //Send value to the Server
            jQuery.post(yasrCommonData.ajaxurl, data, function(response) {
                jQuery('#yasr-loader-multiset-visitor-'+postId+'-'+setType).text(response);
            });

        });

    } //End function


    function yasrMostOrHighestRatedChart () {

        //By default, hide the highest rated chart
        jQuery('#yasr-highest-rated-posts').hide();

        //On click on highest, hide most and show highest
        jQuery('#link-yasr-highest-rated-posts').on("click", function () {

            jQuery('#yasr-most-rated-posts').hide();

            jQuery('#yasr-highest-rated-posts').show();

            return false; // prevent default click action from happening!

        });

        //Vice versa
        jQuery('#link-yasr-most-rated-posts').on("click", function () {

            jQuery('#yasr-highest-rated-posts').hide();

            jQuery('#yasr-most-rated-posts').show();

            return false; // prevent default click action from happening!

        });

    }


/****** End Yasr shortcode page  ******/


/****** Tooltip function ******/

    //used in ajax page
    function yasrDrawProgressBars (valueProgressbar, postId) {

        var i = null;

        var j = 0; //This is for the array

        for (i=5; i>0; i--) {

            jQuery( "#yasr-progress-bar-postid-"+postId+"-progress-bar-" + i).progressbar({
                value: valueProgressbar[j]
            });

            j=j+1;

        }

    }

    //used in shortcode page and ajax page
    function yasrDrawTipsProgress () {

        var varTipsContent = null;

        jQuery('#yasr-total-average-dashicon-' + yasrCommonData.postid).tooltip({

            position: { 
                my: 'center bottom' , 
                at: 'center top-10',
            },

            tooltipClass: "yasr-visitors-stats-tooltip",

            content: function(tipsContent) {

                if (!varTipsContent) {

                    var data = {
                        action: 'yasr_stats_visitors_votes',
                        post_id: yasrCommonData.postid
                    };

                    jQuery.post(yasrCommonData.ajaxurl, data, function(response) {
                        varTipsContent = response;
                        tipsContent(response);
                    });

                }

                else {
                    return varTipsContent;
                }

            },
            disabled: true,

        });

        jQuery('#yasr-total-average-dashicon-' + yasrCommonData.postid).on("hover", function(){
            jQuery(this).tooltip('enable').tooltip('open');
            jQuery('.ui-helper-hidden-accessible').children(':first').removeAttr('style');
            jQuery('.ui-helper-hidden-accessible').children(':last', this).remove();
        });

    }



/****** End tooltipfunction ******/


// this should give support for all plugin that add content with ajax
jQuery( document ).ajaxComplete(function() {

    jQuery('.rateit').rateit();

});



/***** Function to convert js object (literal  object) to string used for ajax calls in vanilla js ****/

function jsObject_to_URLEncoded(element, key, list){
    var list = list || [];
    if(typeof(element)=='object'){
        for (var idx in element)
            jsObject_to_URLEncoded(element[idx],key?key+'['+idx+']':idx,list);
    } else {
        list.push(key+'='+encodeURIComponent(element));
    }
    return list.join('&');
}


/**** function to check ajax response *****/
function checkResponse(res) {
    if (res.status >= 400 && res.status < 600) {
        throw new Error(`Failed with status ${res.status}`);
    }

    return res;
}
