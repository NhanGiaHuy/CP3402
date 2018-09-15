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

/****** Add shortcode for overall rating ******/
add_shortcode ('yasr_overall_rating', 'shortcode_overall_rating_callback');

    function shortcode_overall_rating_callback ($atts) {

	if (YASR_SHOW_OVERALL_IN_LOOP === 'disabled' && !is_singular() && is_main_query()) {

		return;

	}

	extract( shortcode_atts (
			array(
				'size' => 'large',
				'postid' => FALSE
			), $atts )
	);

	$overall_rating = yasr_get_overall_rating($postid);

	//if still false
	if (!$overall_rating) {
		$overall_rating = "-1";
	}

	$shortcode_html = '
        <!--Yasr Overall Rating Shortcode-->
        ';

	if (YASR_TEXT_BEFORE_STARS == 1 && YASR_TEXT_BEFORE_OVERALL != '') {

		$text_before_star = str_replace('%overall_rating%', $overall_rating, YASR_TEXT_BEFORE_OVERALL);

		$shortcode_html = "<div class=\"yasr-container-custom-text-and-overall\">
                                    <span id=\"yasr-custom-text-before-overall\">" . $text_before_star . "</span>";

	}

	$stars_attribute = yasr_stars_size($size);


	//IF jquery is disabled, use rater
    if (YASR_JQUERY_DISABLED === 'yes') {

	    $shortcode_html .= "<div id=\"yasr-overall-rating-rater\"></div>";

    }

    //by default, use jquery
    else {

	    $shortcode_html .= "<div class=\"$stars_attribute[class]\" id=\"yasr_rateit_overall\" data-rateit-starwidth=\"$stars_attribute[px_size]\" data-rateit-starheight=\"$stars_attribute[px_size]\" data-rateit-value=\"$overall_rating\" data-rateit-step=\"0.1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>";

    }

	if (YASR_TEXT_BEFORE_STARS == 1 && YASR_TEXT_BEFORE_OVERALL != '') {

		$shortcode_html .= "</div>";

	}

	$shortcode_html .= '<!--End Yasr Overall Rating Shortcode-->';

    if (YASR_JQUERY_DISABLED === 'yes') {
	    $shortcode_html .= '
            
            <script type="text/javascript">
            
                document.addEventListener(\'DOMContentLoaded\', function(event) {
                
                    var overallRating=' . json_encode( $overall_rating ) . ';
                    var starSize=' . json_encode( $stars_attribute["px_size"] ) . ';
            
                    yasrOverallRatingFront (overallRating, starSize);
            
                });
            
            </script>
            
            ';

    }

	//If overall rating in loop is enabled don't use is_singular && is main_query
	if ( YASR_SHOW_OVERALL_IN_LOOP === 'enabled' ) {

		return $shortcode_html;

	}

	//default
	else {

		if( is_singular() && is_main_query() ) {

			return $shortcode_html;

		}

	}

} //end function


/****** Add shortcode for user vote ******/

add_shortcode ('yasr_visitor_votes', 'shortcode_visitor_votes_callback');

    function shortcode_visitor_votes_callback ($atts) {
        
        if (YASR_SHOW_OVERALL_IN_LOOP === 'disabled' && !is_singular() && is_main_query()) {

            return;

        }

        $shortcode_html = NULL; //Avoid undefined variable outside is_singular && is_main_query

        extract( shortcode_atts (
                array(
                    'size' => 'large',
                    'postid' => FALSE
                ), $atts )
            );

        //If it's not specified use get_the_id
        if (!$postid) {

            $post_id = get_the_ID();

        }

        else {

            $post_id = $postid;

        }

        $ajax_nonce_visitor = wp_create_nonce( "yasr_nonce_insert_visitor_rating" );

	    wp_localize_script( 'yasrfront', "yasrVisitorsVotesData", array(
			    'nonceVisitor' => $ajax_nonce_visitor
		    )
	    );

        $votes=yasr_get_visitor_votes($post_id); //always reference it

        $medium_rating=0;   //Avoid undefined variable

        if (!$votes) {
            $votes=0;         //Avoid undefined variable if there is not overall rating
            $votes_number=0;  //Avoid undefined variable
        }

        else {
            foreach ($votes as $user_votes) {
                $votes_number = $user_votes->number_of_votes;
                if ($votes_number != 0 ) {
                    $medium_rating = ($user_votes->sum_votes/$votes_number);
                }
                else {
                    $medium_rating = 0;
                }
            }
        }

        $medium_rating=round($medium_rating, 1);

        $stars_attribute = yasr_stars_size($size);

        //name of coockie to check
        $yasr_cookiename = 'yasr_visitor_vote_cookie';

        if (isset($_COOKIE[$yasr_cookiename])) {

            $cookie_data = stripslashes($_COOKIE[$yasr_cookiename]);

            $cookie_data = unserialize($cookie_data);

            foreach ($cookie_data as $value) {

                $cookie_post_id = (int)$value['post_id'];

                if ($cookie_post_id === $post_id) {

	                $cookie_value = (int)$value['rating'];

	                //Stop doing foreach, here we've found the rating for current post
	                break;

                }

                else {

                    $cookie_value = FALSE;

                }

            }

            if ($cookie_value !== FALSE && $cookie_value > 5) {

                $cookie_value = 5;

            }

            elseif ($cookie_value !== FALSE && $cookie_value < 1) {

                $cookie_value = 1;

            }

        }

        else {

            $cookie_value = FALSE;

        }

        $shortcode_html = '
        <!--Yasr Visitor Votes Shortcode-->
        ';
        $shortcode_html .= "<div id=\"yasr_visitor_votes_$post_id\" class=\"yasr-visitor-votes\">";
        $span_after_rate_it = "";

        //I've to check a logged in user that has already rated
        if ( is_user_logged_in() ) {

            $readonly = 'false'; //Always false if user is logged in

            //Chek if a logged in user has already rated for this post
            $vote_if_user_already_rated = yasr_check_if_user_already_voted($post_id);

            //If user has already rated
            if ($vote_if_user_already_rated) {

                $span_after_rate_it="<span class=\"yasr-small-block-bold yasr-already-voted-text \" id=\"yasr-user-vote-$post_id\" data-yasr-already-voted=\"$vote_if_user_already_rated\">" . __("You've already voted this article with", 'yet-another-stars-rating') . " $vote_if_user_already_rated </span>";

            }

        } //End if user is logged

        //if anonymous are allowed to vote
        if (YASR_ALLOWED_USER === 'allow_anonymous') {

            //IF user is not logged in
            if(!is_user_logged_in()) {

                //if cookie exists
                if($cookie_value) {

                    $readonly = 'true';

                    if (YASR_TEXT_BEFORE_STARS == 1 && YASR_CUSTOM_TEXT_USER_VOTED!='') {

                        $span_after_rate_it = $span_after_rate_it="<span class=\"yasr-small-block-bold yasr-already-voted-text\">" . YASR_CUSTOM_TEXT_USER_VOTED . " </span>";;

                    }

                    else {

                        $span_after_rate_it="<span class=\"yasr-small-block-bold yasr-already-voted-text \">" . __("You've already voted this article with", 'yet-another-stars-rating') . " $cookie_value </span>";

                    }

                }

                else {

                    $readonly = 'false';

                }

            }

        } //end if  YASR_ALLOWED_USER === 'allow_anonymous' {


        //If only logged in users can vote
        elseif (YASR_ALLOWED_USER === 'logged_only') {

            //IF user is not logged in
            if(!is_user_logged_in()) {

                $readonly = 'true'; //readonly is true if user isn't logged

                $span_after_rate_it = "<span class=\"yasr-visitor-votes-must-sign-in\">" . __("You must sign in to vote", 'yet-another-stars-rating') . "</span>";

            }

        }

        if (YASR_VISITORS_STATS === 'yes') {

            $span_dashicon = "<span class=\"dashicons dashicons-chart-bar yasr-dashicons-visitor-stats \" id=\"yasr-total-average-dashicon-$post_id\" title=\"yasr-stats-dashicon\"></span>";

        }

        else {

            $span_dashicon = "";

        }


	    if(YASR_TEXT_BEFORE_STARS == 1 && YASR_TEXT_BEFORE_VISITOR_RATING != '') {

            $text_before_star = str_replace('%total_count%', $votes_number, YASR_TEXT_BEFORE_VISITOR_RATING);

            $text_before_star = str_replace('%average%', $medium_rating, $text_before_star);

            $shortcode_html .= "<div class=\"yasr-container-custom-text-and-visitor-rating\">
            <span id=\"yasr-custom-text-before-visitor-rating\">" . $text_before_star . "</span></div>";

        }

        if(YASR_TEXT_BEFORE_STARS == 1 && YASR_TEXT_AFTER_VISITOR_RATING != '') {

            $text_after_star = str_replace('%total_count%', $votes_number, YASR_TEXT_AFTER_VISITOR_RATING);

            $text_after_star = str_replace('%average%', $medium_rating, $text_after_star);

            $span_text_after_star = "<span class=\"yasr-total-average-container\" id=\"yasr-total-average-text_$post_id\">" . $text_after_star . "</span>";

        }

        else {

            $span_text_after_star = "<span class=\"yasr-total-average-container\" id=\"yasr-total-average-text_$post_id\">
                    [" . __("Total: ", 'yet-another-stars-rating') . "$votes_number &nbsp; &nbsp;" .  __("Average: ",'yet-another-stars-rating') . "$medium_rating/5]
                </span>";

        }

        if (YASR_JQUERY_DISABLED === 'yes') {

            $shortcode_html .= "<div id=\"yasr-visitor-votes-rater-$post_id\"></div>";

        }

        else {

	        $shortcode_html .= "<div class=\"$stars_attribute[class]\" id=\"yasr_rateit_visitor_votes_$post_id\" data-postid=\"$post_id\" data-rateit-starwidth=\"$stars_attribute[px_size]\" data-rateit-starheight=\"$stars_attribute[px_size]\" data-rateit-value=\"$medium_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"$readonly\"></div>";

        }

        $shortcode_html .= $span_dashicon;

        $shortcode_html .= $span_text_after_star;

        $shortcode_html .= $span_after_rate_it;

        $shortcode_html .= "</div>";

        $shortcode_html .= '
        <!--End Yasr Visitor Votes Shortcode-->
        ';

        if (YASR_JQUERY_DISABLED === 'yes') {

	        $shortcode_html .= '

                <script type="text/javascript">

	                document.addEventListener(\'DOMContentLoaded\', function(event) {
                
                        var mediumRating =' . json_encode( $medium_rating ) . ';
                        var starSize =' . json_encode( $stars_attribute["px_size"] ) . ';
                        var readonly =' . json_encode( $readonly ) . ';
                        var yasrVVPostId = ' . json_encode($post_id) .';
            
                        yasrVisitorVotesFront (mediumRating, starSize, readonly, yasrVVPostId);
            
                    });
            
                </script>';

        }


        //If overall rating in loop is enabled don't use is_singular && is main_query
        if ( YASR_SHOW_VISITOR_VOTES_IN_LOOP === 'enabled' ) {

            return $shortcode_html;

        }

        //default value
        else {

            if( is_singular() && is_main_query() ) {

                return $shortcode_html;

            }

        }

    } //End function shortcode_visitor_votes_callback


/****** Show visitor votes average, READ ONLY ******/
add_shortcode ('yasr_visitor_votes_readonly', 'yasr_visitor_votes_readonly_callback');

    function yasr_visitor_votes_readonly_callback ($atts) {

        $shortcode_html = '
        <!-- Yasr Visitor Votes Readonly Shortcode-->
        ';

        extract( shortcode_atts (
                array(
                    'size' => 'small',
                    'postid' => FALSE
                ), $atts )
            );

        //If it's not specified use get_the_id
        if (!$postid) {

            $post_id = get_the_ID();

        }

        else {

            $post_id = $postid;

        }

        $votes=yasr_get_visitor_votes($post_id);

        $medium_rating=0;   //Avoid undefined variable

        if (!$votes) {
            $votes=0;         //Avoid undefined variable if there is not overall rating
            $votes_number=0;  //Avoid undefined variable
        }

        else {
            foreach ($votes as $user_votes) {
                $votes_number = $user_votes->number_of_votes;
                if ($votes_number != 0 ) {
                    $medium_rating = ($user_votes->sum_votes/$votes_number);
                }
                else {
                    $medium_rating = 0;
                }
            }
        }

        $medium_rating=round($medium_rating, 1);

        $stars_attribute = yasr_stars_size($size);



        $shortcode_html .= "<div id=\"yasr_visitor_votes_readonly_$post_id\" class=\"yasr-visitor-votes_readonly\">";
        $span_after_rate_it = "";

        $shortcode_html .= "<div class=\"$stars_attribute[class]\" id=\"yasr_rateit_visitor_votes_readonly_$post_id\" data-rateit-starwidth=\"$stars_attribute[px_size]\" data-rateit-starheight=\"$stars_attribute[px_size]\" data-rateit-value=\"$medium_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>";

        $shortcode_html .= "</div>";

        $shortcode_html .= '
        <!--End Yasr Visitor Votes Readonly Shortcode-->
        ';


            //IF show visitor votes in loop is disabled use is_singular && is_main query
            if ( YASR_SHOW_VISITOR_VOTES_IN_LOOP === 'disabled' ) {

                if( is_singular() && is_main_query() ) {

                    return $shortcode_html;

                }

            } // End if YASR_SHOW_VISITOR_VOTES_IN_LOOP === 'disabled') {

            //If overall rating in loop is enabled don't use is_singular && is main_query
            elseif ( YASR_SHOW_VISITOR_VOTES_IN_LOOP === 'enabled' ) {

                return $shortcode_html;

            }

       // } //End (!is_feed)

    } //End function shortcode_visitor_votes_only_stars_callback


/****** Add shortcode for multiple set ******/

add_shortcode ('yasr_multiset', 'shortcode_multi_set_callback');

    function shortcode_multi_set_callback( $atts ) {

    	global $wpdb;

    	// Attributes
    	extract( shortcode_atts(
    		array(
    			'setid' => '0',
                'postid' => FALSE,
                'show_average' => FALSE
    		), $atts )
    	);

        //If it's not specified use get_the_id
        if (!$postid) {

            $post_id = get_the_ID();

        }

        else {

            $post_id = $postid;

        }

        $shortcode_html = '
        <!-- Yasr Multi Set Shortcode-->
        ';

        $multiset_vote_sum = 0;
        $multiset_rows_number = 0; //

        $set_name_content=yasr_get_multi_set_values_and_field ($post_id, $setid);

    	if (!$set_name_content) {

            $set_name_content=$wpdb->get_results($wpdb->prepare("SELECT field_name AS name, field_id AS id
                        FROM " . YASR_MULTI_SET_FIELDS_TABLE . "
                        WHERE parent_set_id=%d
                        ORDER BY field_id ASC", $setid));

            //if it still to be empty (e.g. wrong id passed)
            if (!$set_name_content) {

                return $shortcode_html;

            }

        }

        $shortcode_html.="<table class=\"yasr_table_multi_set_shortcode\">";

        //this is to avoid undefined, may happens when an user insert the shortcode without insert vote (only happens if more than 1 multiset are used)
        $set_content = new stdClass();
        $set_content->vote = NULL; //avoid undefined

     	foreach ($set_name_content as $set_content) {

        	$shortcode_html .=  "<tr> <td><span class=\"yasr-multi-set-name-field\">$set_content->name </span></td>
      		   					 <td><div class=\"rateit\" id=\"$set_content->id\" data-rateit-value=\"$set_content->vote\" data-rateit-step=\"0.5\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div></td>
        						 </tr>";

            $multiset_vote_sum = $multiset_vote_sum + $set_content->vote;
            $multiset_rows_number++;

        }

        if ($show_average !== FALSE && $show_average !=='no'|| $show_average===FALSE && YASR_MULTI_SHOW_AVERAGE !== 'no') {

            $multiset_average = $multiset_vote_sum / $multiset_rows_number;
            $multiset_average = round($multiset_average, 1);

            $average_txt = __("Average", "yet-another-stars-rating");

            $shortcode_html .= "<tr><td colspan=\"2\" class=\"yasr-multiset-average\">$average_txt<div class=\"rateit medium \" data-rateit-value=\"$multiset_average\" data-rateit-starwidth=\"24\" data-rateit-starheight=\"24\" data-rateit-step=\"0.1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div></td></tr>";

        }

        $shortcode_html.="</table>";

        $shortcode_html .= '
        <!--End Yasr Multi Set Shortcode-->
        ';

    	return $shortcode_html;

    } //End function


/****** Add shortcode for multiset writable by users  ******/

add_shortcode ('yasr_visitor_multiset', 'yasr_visitor_multiset_callback');

    function yasr_visitor_multiset_callback ( $atts ) {

        $ajax_nonce_visitor_multiset = wp_create_nonce( "yasr_nonce_insert_visitor_rating_multiset" );

        global $wpdb;

        // Attributes
        extract( shortcode_atts(
            array(
                'setid' => '0',
                'postid' => FALSE,
                'show_average' => FALSE
            ), $atts )
        );

        //If it's not specified use get_the_id
        if (!$postid) {

            $post_id = get_the_ID();

        }

        else {

            $post_id = $postid;

        }

        $image = YASR_IMG_DIR . "/loader.gif";

        $average_txt = __("Average", "yet-another-stars-rating");

        $loader_html = "<span class=\"yasr-loader-multiset-visitor\" id=\"yasr-loader-multiset-visitor-$post_id-$setid\" >&nbsp; " . __("Loading, please wait",'yet-another-stars-rating') . ' <img src=' .  "$image" .' title="yasr-loader" alt="yasr-loader"></span>';

        $button_html = "<input type=\"submit\" name=\"submit\" id=\"yasr-send-visitor-multiset-$post_id-$setid\" class=\"button button-primary\" value=\"" . __('Submit!', 'yet-another-stars-rating') . " \"  />";

	    $yasr_cookiename = 'yasr_multi_visitor_cookie';

	    if (isset($_COOKIE[$yasr_cookiename])) {

		    $cookie_data = stripslashes($_COOKIE[$yasr_cookiename]);

		    $cookie_data = unserialize($cookie_data);

		    foreach ($cookie_data as $value) {

			    $cookie_post_id = (int)$value['post_id'];
			    $cookie_set_id = (int)$value['set_id'];

			    if ($cookie_post_id === $post_id && $cookie_set_id == $setid) {

				    $button = "";
				    $star_readonly = 'true';
				    $span_message_content = __('Thank you for voting! ', 'yet-another-stars-rating');

				    //Stop doing foreach, here we've found the rating for current post
				    break;

			    }

			    else {

				    $button = $button_html;
				    $star_readonly = 'false';
				    $span_message_content = "";

			    }

		    }


	    }

        else {

            //If user is not logged in
            if (!is_user_logged_in()) {

                if (YASR_ALLOWED_USER === 'allow_anonymous') {

                    $button = $button_html;
                    $star_readonly = 'false';
                    $span_message_content = "";

                }

                elseif (YASR_ALLOWED_USER === 'logged_only') {

                    $button = $button_html;
                    $star_readonly = 'true';
                    $span_message_content = "<span class=\"yasr-visitor-votes-must-sign-in\">" . __("You must sign in to vote", 'yet-another-stars-rating') . "</span>";

                }


            } //End if user logged in

            //Is user is logged in
            else {

                    $button = $button_html;
                    $star_readonly = 'false';
                    $span_message_content = "";

            }

        }

        $set_name_content = yasr_get_multi_set_visitor ($post_id, $setid);

        $shortcode_html = '
        <!-- Yasr Visitor Multi Set Shortcode-->
        ';

        if ($set_name_content) {

            $multiset_vote_sum = 0;//avoid undefined variable
            $multiset_rows_number = 0;//avoid undefined variable

            $shortcode_html.="<table class=\"yasr_table_multi_set_shortcode\">";

            foreach ($set_name_content as $set_content) {

                if($set_content->number_of_votes > 0) {

                    $average_rating = $set_content->sum_votes / $set_content->number_of_votes;

                    $average_rating = round($average_rating, 1);

                }

                else {

                    $average_rating = 0;

                }

                $shortcode_html .=  "<tr>
                                        <td>
                                            <span class=\"yasr-multi-set-name-field\">$set_content->name </span>
                                        </td>
                                        <td>
                                            <div class=\"rateit yasr-visitor-multi-$post_id-$setid\" id=\"$set_content->id \" data-rateit-value=\"$average_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"$star_readonly\"></div>
                                            <span class=\"yasr-visitor-multiset-vote-count\">$set_content->number_of_votes</span>
                                        </td>
                                     </tr>";

                 $multiset_vote_sum = $multiset_vote_sum + $average_rating;
                 $multiset_rows_number++;

            } //End foreach

            if ($show_average !== FALSE && $show_average !=='no'|| $show_average===FALSE && YASR_MULTI_SHOW_AVERAGE !== 'no') {

                $multiset_average = $multiset_vote_sum / $multiset_rows_number;
                $multiset_average = round($multiset_average, 1);

                $shortcode_html .= "<tr>
                                        <td colspan=\"2\" class=\"yasr-multiset-average\">
                                            $average_txt<div class=\"rateit medium \" data-rateit-value=\"$multiset_average\" data-rateit-starwidth=\"24\" data-rateit-starheight=\"24\" data-rateit-step=\"0.1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
                                        </td>
                                    </tr>";

            }

            $shortcode_html.="<tr>
                                <td colspan=\"2\">
                                    $button
                                    $loader_html
                                    <span class=\"yasr-visitor-multiset-message\">$span_message_content</span>
                                </td>
                            </tr>
                            </table>";
        }

        else {

            $set_name=$wpdb->get_results($wpdb->prepare("SELECT field_name AS name, field_id AS id
                        FROM " . YASR_MULTI_SET_FIELDS_TABLE . "
                        WHERE parent_set_id=%d
                        ORDER BY field_id ASC", $setid));


            $shortcode_html.="<table class=\"yasr_table_multi_set_shortcode\">";

            foreach ($set_name as $set_content) {

                $shortcode_html .=  "<tr>
                                        <td>
                                            <span class=\"yasr-multi-set-name-field\">$set_content->name </span>
                                        </td>
                                        <td>
                                            <div class=\"rateit yasr-visitor-multi-$post_id-$setid\" id=\"$set_content->id\" data-rateit-value=\"0\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"false\"></div>
                                            <span class=\"yasr-visitor-multiset-vote-count\"> 0 </span>
                                        </td>
                                     </tr>";


                //First time, initialize all fields to 0

                //Find the highest_id (it's not auto increment on  db due to gd star compatibility)
                $highest_id=$wpdb->get_var("SELECT id FROM " . YASR_MULTI_SET_VALUES_TABLE . " ORDER BY id DESC LIMIT 1 ");

                //highest id is 0 if data is empty
                if (!$highest_id) {
                    $new_id=0;
                }

                $new_id=$highest_id + 1;

                $wpdb->replace(
                        YASR_MULTI_SET_VALUES_TABLE,
                        array (
                                'id'=>$new_id,
                                'post_id'=>$post_id,
                                'field_id'=>$set_content->id,
                                'set_type'=>$setid,
                                'number_of_votes' => 0,
                                'sum_votes' => 0
                                ),
                        array ("%d", "%d", "%d",  "%d", "%d", "%d")
                        );


            } //end foreach ($set_name as $set_content)


            if ($show_average !== FALSE && $show_average !=='no' || $show_average===FALSE && YASR_MULTI_SHOW_AVERAGE !== 'no') {

                $shortcode_html .= "<tr>
                    <td colspan=\"2\" class=\"yasr-multiset-average\">
                       $average_txt<div class=\"rateit medium \" data-rateit-value=\"0\" data-rateit-starwidth=\"24\" data-rateit-starheight=\"24\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
                    </td>
                </tr>";

            }

            $shortcode_html.="<tr>
                                <td colspan=\"2\">
                                    $button
                                    $loader_html
                                    <span class=\"yasr-visitor-multiset-message\">$span_message_content</span>
                                </td>
                            </tr>
                            </table>";

            $shortcode_html.="</table>";

        }


        $var_ajax_nonce_visitor_multiset = json_encode($ajax_nonce_visitor_multiset);

        $shortcode_html .= '
        <!-- End Yasr Multi Set Shortcode-->
        ';

        wp_localize_script( 'yasrfront', "yasrMultiSetData", array(
                'nonceVisitor' => $var_ajax_nonce_visitor_multiset,
                'setType' => $setid
                )
            );

    return $shortcode_html;

}




/****** Add top 10 highest rated post *****/

add_shortcode ('yasr_top_ten_highest_rated', 'yasr_top_ten_highest_rated_callback');

    function yasr_top_ten_highest_rated_callback () {

        global $wpdb;

        $query_result = $wpdb->get_results("SELECT pm.meta_value AS overall_rating, pm.post_id AS post_id
                                            FROM $wpdb->postmeta AS pm, $wpdb->posts AS p
                                            WHERE  pm.post_id = p.ID
                                            AND p.post_status = 'publish'
                                            AND pm.meta_key = 'yasr_overall_rating'
                                            ORDER BY pm.meta_value DESC, pm.post_id ASC LIMIT 10");


        $shortcode_html = '
        <!-- Yasr Top 10 highest Rated Shortcode-->
        ';

        if ($query_result) {

            $shortcode_html .= "<table class=\"yasr-table-chart\">";

            foreach ($query_result as $result) {

                $post_title = get_the_title($result->post_id);

                $link = get_permalink($result->post_id); //Get permalink from post it

                $shortcode_html .= "<tr>
                                        <td width=\"60%\" class=\"yasr-top-10-overall-left\"><a href=\"$link\">$post_title</a></td>
                                        <td width=\"40%\" class=\"yasr-top-10-overall-right\">
                                            <div class=\"rateit medium\" data-rateit-starwidth=\"24\" data-rateit-starheight=\"24\" data-rateit-value=\"$result->overall_rating\" data-rateit-step=\"0.1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
                                            <span class=\"yasr-highest-rated-text\">" . __("Rating", 'yet-another-stars-rating') . " $result->overall_rating </span>
                                            </td>
                                    </tr>";


            } //End foreach

            $shortcode_html .= "</table>";

            $shortcode_html .= '
            <!--End Yasr Top 10 highest Rated Shortcode-->
            ';

            return $shortcode_html;

        } //end if $query_result

        else {
            _e("You don't have any votes stored", 'yet-another-stars-rating');
        }

    } //End function


/****** Add top 10 most rated / highest rated post *****/

add_shortcode ('yasr_most_or_highest_rated_posts', 'yasr_most_or_highest_rated_posts_callback');

    function yasr_most_or_highest_rated_posts_callback () {


        $shortcode_html = '
        <!-- Yasr Most Or Highest Rated Shortcode-->
        ';

        global $wpdb;

        $query_result_most_rated = $wpdb->get_results ("SELECT post_id, COUNT(post_id) AS number_of_votes, SUM(vote) AS sum_votes
                                                FROM " . YASR_LOG_TABLE . " , $wpdb->posts AS p
                                                WHERE post_id = p.ID
                                                AND p.post_status = 'publish'
                                                GROUP BY post_id
                                                HAVING number_of_votes > 1
                                                ORDER BY number_of_votes DESC, post_id ASC
                                                LIMIT 10
                                                ");


        //count run twice but access data only once: tested with query monitor and asked
        //here http://stackoverflow.com/questions/39201235/does-count-run-twice/39201492
        $query_result_highest = $wpdb->get_results ("SELECT post_id, COUNT(post_id) AS number_of_votes, (SUM(vote) / COUNT(post_id)) AS result
                                                FROM " . YASR_LOG_TABLE . " , $wpdb->posts AS p
                                                WHERE post_id = p.ID
                                                AND p.post_status = 'publish'
                                                GROUP BY post_id
                                                HAVING COUNT(post_id) >= 2
                                                ORDER BY result DESC, number_of_votes DESC
                                                LIMIT 10
                                                ");

        if ($query_result_most_rated) {

            $shortcode_html .= "<table class=\"yasr-table-chart\" id=\"yasr-most-rated-posts\">
                            <tr class=\"yasr-visitor-votes-title\">
                                <th>" . __("Post / Page" , 'yet-another-stars-rating') ." </th>
                                <th>". __("Order By" , 'yet-another-stars-rating') .":&nbsp;&nbsp;
                                    <span id=\"yasr_multi_chart_link_to_nothing\">" . __("Most Rated" , 'yet-another-stars-rating') ."</span> | 
                                    <span id=\"link-yasr-highest-rated-posts\"><a href=\"\">" . __("Highest Rated" , 'yet-another-stars-rating') ."</a></span>
                                </th>
                            </tr>"
                            ;

            foreach ($query_result_most_rated as $result) {

                $rating = $result->sum_votes / $result->number_of_votes;

                $rating = round($rating, 1);

                $post_title = get_the_title($result->post_id);

                $link = get_permalink($result->post_id); //Get permalink from post it

                $shortcode_html .= "<tr>
                            <td width=\"60%\" class=\"yasr-top-10-most-highest-left\"><a href=\"$link\">$post_title</a></td>
                                <td width=\"40%\" class=\"yasr-top-10-most-highest-right\">
                                    <div class=\"rateit medium\" data-rateit-starwidth=\"24\" data-rateit-starheight=\"24\" data-rateit-value=\"$rating\" data-rateit-step=\"0.1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
                                    <br /> [" .  __("Total:" , 'yet-another-stars-rating') . "$result->number_of_votes &nbsp;&nbsp;&nbsp;" . __("Average" , 'yet-another-stars-rating') . " $rating]
                                </td>
                        </tr>";


            } //End foreach

            $shortcode_html .= "</table>" ;

        } //End if $query_result_most_rated)

        else {
            $shortcode_html = __("You've not enough data",'yet-another-stars-rating') . "<br />";
        }


        if ($query_result_highest) {

            $shortcode_html .= "<table class=\"yasr-table-chart\" id=\"yasr-highest-rated-posts\">
                            <tr class=\"yasr-visitor-votes-title\">
                                <th>" . __("Post / Page" , 'yet-another-stars-rating') ." </th>
                                <th>". __("Order By" , 'yet-another-stars-rating') .":&nbsp;&nbsp; 
                                    <span id=\"link-yasr-most-rated-posts\"><a href=\"\">". __("Most Rated" , 'yet-another-stars-rating') . "</a> | 
                                    <span id=\"yasr_multi_chart_link_to_nothing\">". __("Highest Rated" , 'yet-another-stars-rating') ."</span>
                               </th>
                            </tr>";

            foreach ($query_result_highest as $result) {

                $rating = round($result->result, 1);

                $post_title = get_the_title($result->post_id);

                $link = get_permalink($result->post_id); //Get permalink from post it

                $shortcode_html .= "<tr>
                            <td width=\"60%\" class=\"yasr-top-10-most-highest-left\"><a href=\"$link\">$post_title</a></td>
                            <td width=\"40%\" class=\"yasr-top-10-most-highest-right\"><div class=\"rateit medium\" data-rateit-starwidth=\"24\" data-rateit-starheight=\"24\" data-rateit-value=\"$rating\" data-rateit-step=\"0.1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
                                <br /> [" .  __("Total:" , 'yet-another-stars-rating') . "$result->number_of_votes &nbsp;&nbsp;&nbsp;" . __("Average" , 'yet-another-stars-rating') . " $rating]
                            </td>
                       </tr>";


            } //End foreach

            $shortcode_html .= "</table>";

        } //end if $query_result

        else {
            $shortcode_html = __("You've not enought data",'yet-another-stars-rating') . "<br />";
        }

        $shortcode_html .= '
        <!-- End Yasr Most Or Highest Rated Shortcode-->
        ';

	    wp_localize_script( 'yasrfront', "yasrMostHighestRanking", array(
			    'enable' => 'yes'
		    )
	    );

        return $shortcode_html;


    } //End function


/****** Add top 5 most active reviewer ******/

add_shortcode ('yasr_top_5_reviewers', 'yasr_top_5_reviewers_callback');

    function yasr_top_5_reviewers_callback () {

        global $wpdb;

        $query_result = $wpdb->get_results("SELECT COUNT( pm.post_id ) AS total_count, p.post_author AS reviewer
                                            FROM $wpdb->posts AS p, $wpdb->postmeta AS pm
                                            WHERE pm.post_id = p.ID
                                            AND pm.meta_key = 'yasr_overall_rating'
                                            AND p.post_status = 'publish'
                                            GROUP BY reviewer
                                            ORDER BY (total_count) DESC
                                            LIMIT 5");


        if ($query_result) {

            $shortcode_html = '
            <!-- Yasr Top 5 Reviewers Shortcode-->
            ';

            $shortcode_html .= "
            <table class=\"yasr-table-chart\">
            <tr>
             <th>Author</th>
             <th>Reviews</th>
            </tr>
            ";

            foreach ($query_result as $result) {

                $user_data = get_userdata($result->reviewer);

                if ($user_data) {

                    $user_profile = get_author_posts_url($result->reviewer);

                }

                else {

                    $user_profile = '#';
                    $user_data = new stdClass;
                    $user_data->user_login = 'Anonymous';

                }


                $shortcode_html .= "<tr>
                                        <td><a href=\"$user_profile\">$user_data->user_login</a></td>
                                        <td>$result->total_count</td>
                                    </tr>";

            }

            $shortcode_html .= "</table>";

            $shortcode_html .= '
            <!-- End Yasr Top 5 Reviewers Shortcode-->
            ';

            return $shortcode_html;

        }

        else {

            _e("Problem while retrieving the top 5 most active reviewers. Did you publish any review?");

        }


    } //End top 5 reviewers function





/****** Add top 10 most active user *****/

add_shortcode ('yasr_top_ten_active_users', 'yasr_top_ten_active_users_callback');

    function yasr_top_ten_active_users_callback () {

        global $wpdb;

        $query_result = $wpdb->get_results("SELECT COUNT( user_id ) as total_count, user_id as user
                                            FROM " . YASR_LOG_TABLE . ", $wpdb->posts AS p
                                            WHERE  post_id = p.ID
                                            AND p.post_status = 'publish'
                                            GROUP BY user_id
                                            ORDER BY ( total_count ) DESC
                                            LIMIT 10");

        if ($query_result) {

            $shortcode_html = '
            <!-- Yasr Top 10 Active Users Shortcode-->
            ';

            $shortcode_html .= "
            <table class=\"yasr-table-chart\">
            <tr>
             <th>UserName</th>
             <th>Number of votes</th>
            </tr>
            ";

            foreach ($query_result as $result) {

                $user_data = get_userdata($result->user);

                if ($user_data) {

                    $user_profile = get_author_posts_url($result->user);

                }

                else {
                    $user_profile = '#';
                    $user_data = new stdClass;
                    $user_data->user_login = 'Anonymous';
                }

                $shortcode_html .= "<tr>
                                        <td><a href=\"$user_profile\">$user_data->user_login</a></td>
                                        <td>$result->total_count</td>
                                    </tr>";

            }


            $shortcode_html .= "</table>";

            $shortcode_html .= '
            <!--End Yasr Top 10 Active Users Shortcode-->
            ';

            return $shortcode_html;

        }

        else {
            _e("Problem while retrieving the top 10 active users chart. Are you sure you have votes to show?");
        }


    } //End function


//this shortcode is in the plugin but not ready to use yet.
//that's why there isn't doc about
add_shortcode ('yasr_highest_rated_visitor_multi_set', 'yasr_highest_rated_visitor_multi_set_callback');

function yasr_highest_rated_visitor_multi_set_callback ($atts) {

    global $wpdb;

    // Attributes
    extract( shortcode_atts(
        array(
            'setid' => '0',
        ), $atts )
    );

    $set_fields = FALSE;
    $shortcode_html = '';

    $set_fields=$wpdb->get_results($wpdb->prepare("SELECT v.post_id, v.sum_votes / v.number_of_votes AS average, v.number_of_votes, f.field_name
                                       FROM " . YASR_MULTI_SET_VALUES_TABLE . " AS v, " . YASR_MULTI_SET_FIELDS_TABLE . " AS f
                                       WHERE v.set_type = %d
                                       AND v.set_type = f.parent_set_id
                                       AND v.field_id = f.field_id
                                       AND v.number_of_votes > 0
                                       AND v.sum_votes > 0
                                       ORDER BY f.parent_set_id ASC, f.field_id ASC, v.post_id ASC", $setid));

    if ($set_fields) {

        $shortcode_html = "
        <table class=\"yasr-table-chart\">";

        foreach ($set_fields as $results) {

        if (!isset($field_name) || $field_name != $results->field_name ) {

            $shortcode_html .= "<tr><td colspan=\"2\"><h3> $results->field_name</h3></td></tr>";

        }

        $link = get_permalink($results->post_id);
        $title = get_the_title($results->post_id);

        $average = round($results->average, 1);

        $shortcode_html .= "<tr>
                                <td>
                                    <a href=\"$link\">$title</a>
                                </td>
                                <td>
                                    <div class=\"rateit medium\" data-rateit-starwidth=\"24\" data-rateit-starheight=\"24\" data-rateit-value=\"$average\" data-rateit-step=\"0.1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
                                 $average" . sprintf(__(' based on %d votes', 'yet-another-stars-rating'), $results->number_of_votes);  "
                                </td>
                            </tr>";


        $field_name = $results->field_name;

    }

        $shortcode_html .= "</table>";

    }

    else {

        _e("No results, try a different setid", "yet-another-stars-rating");

    }

    return $shortcode_html;

}

?>
