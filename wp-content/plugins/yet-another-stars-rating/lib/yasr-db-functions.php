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

/****** Install yasr functions ******/
function yasr_install() {
	global $wpdb; //Database wordpress object

	$prefix=$wpdb->prefix . 'yasr_';  //Table prefix

	$yasr_multi_set_table = $prefix . 'multi_set';
	$yasr_multi_set_fields = $prefix . 'multi_set_fields';
	$yasr_multi_values_table = $prefix . 'multi_values';
	$yasr_log_table = $prefix . 'log';

	$sql_yasr_multi_set_table= "CREATE TABLE IF NOT EXISTS $yasr_multi_set_table (
 		set_id int(2) NOT NULL,
  		set_name varchar(64) COLLATE utf8_unicode_ci NOT NULL,
	  	UNIQUE KEY set_id (set_id),
	  	UNIQUE KEY set_name (set_name)
	)";

	$sql_yasr_multi_set_fields ="CREATE TABLE IF NOT EXISTS $yasr_multi_set_fields (
  		id bigint(20) NOT NULL,
  		parent_set_id int(2) NOT NULL,
  		field_name varchar(23) COLLATE utf8_unicode_ci NOT NULL,
  		field_id int(2) NOT NULL,
  		PRIMARY KEY (id),
  		UNIQUE KEY id (id)
 	)";

	$sql_yasr_multi_value_table = "CREATE TABLE IF NOT EXISTS $yasr_multi_values_table (
  		id bigint(20) NOT NULL,
  		field_id int(2) NOT NULL,
  		set_type int (2) NOT NULL,
  		post_id bigint(20) NOT NULL,
  		votes decimal(2,1) NOT NULL,
  		number_of_votes bigint(20) NOT NULL,
  		sum_votes decimal(11, 1) NOT NULL,
  		PRIMARY KEY (id),
  		UNIQUE KEY id (id)
	);";

	$sql_yasr_log_table = "CREATE TABLE IF NOT EXISTS $yasr_log_table (
  		id bigint(20) NOT NULL AUTO_INCREMENT,
  		post_id bigint(20) NOT NULL,
  		multi_set_id int(2) NOT NULL,
  		user_id int(11) NOT NULL,
  		vote decimal(11,1) NOT NULL,
  		date datetime NOT NULL,
  		ip varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  		PRIMARY KEY (id),
  		UNIQUE KEY id (id)
	);";


	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	dbDelta( $sql_yasr_multi_set_table );
	dbDelta( $sql_yasr_multi_set_fields );
	dbDelta( $sql_yasr_multi_value_table );
	dbDelta( $sql_yasr_log_table );


	//Write default option settings
	$option = get_option( 'yasr_general_options' );

	if (!$option) {

		$option = array();
		$option['auto_insert_enabled'] = 0;
		$option['auto_insert_what'] = 'overall_rating';
		$option['auto_insert_where'] = 'top';
		$option['auto_insert_size']='large';
		$option['auto_insert_exclude_pages'] = 'yes';
		$option['auto_insert_custom_post_only'] = 'no';
		$option['show_overall_in_loop'] = 'disabled';
		$option['show_visitor_votes_in_loop'] = 'disabled';
		$option['text_before_stars'] = 0;
		$option['enable_ip'] = 'no';
		$option['snippet'] = 'overall_rating';
		$option['snippet_itemtype'] = 'Product';
		$option['blogposting_organization_name'] = get_bloginfo('name');
		$option['blogposting_organization_logo'] = get_site_icon_url();
		$option['allowed_user'] = 'allow_anonymous';
		$option['metabox_overall_rating'] = 'stars'; //This is not in settings page but in overall rating metabox
		$option['visitors_stats'] = 'yes';

		add_option("yasr_general_options", $option); //Write here the default value if there is not option

		//Style set options
		$style_options = array();
		$style_options['scheme_color_multiset'] = 'light';
		$style_options['stars_set_free'] = 'flat';

		add_option("yasr_style_options", $style_options);

		//multi set options
		$multi_set_options = array();
		$multi_set_options['show_average'] = 'yes';

		add_option("yasr_multiset_options", $multi_set_options);


	}

}


/****** Get overall rating from yasr_votes table
used in yasr_add_filter_for_schema() and yasr_get_id_value_callback() ******/
function yasr_get_overall_rating($post_id=FALSE, $create_transient=TRUE) {
	global $wpdb;

	//if values it's not passed get the post id, most of cases and default one
	if(!$post_id) {

		$post_id=get_the_ID();

	}

	if (!$post_id) {

		return;

	}

	$post_id = (int)$post_id;

	$overall_rating = FALSE;

	$transient_name = 'yasr_overall_rating_' . $post_id;

	$transient_overall = get_transient($transient_name);

	//if transient exists return it
	if ($transient_overall) {

		return $transient_overall;

	}

	//get value from db
	else {

		$overall_rating = get_post_meta($post_id, 'yasr_overall_rating', TRUE);

	}

	if ($overall_rating) {

		if ($create_transient === TRUE) {

			set_transient( $transient_name, $overall_rating, WEEK_IN_SECONDS );

		}

	}

	return $overall_rating;

}


/****** Return the snippet choosen for a post or page ******/
function yasr_get_snippet_type() {

	global $wpdb;

	$post_id=get_the_ID();

	if (!$post_id) {
		return FALSE;
	}

	else {

		$result = get_post_meta($post_id, 'yasr_review_type', TRUE);

		if($result) {

			$snippet_type = trim($result);

			if ($snippet_type != 'Product' && $snippet_type != 'Place' && $snippet_type != 'Recipe' && $snippet_type != 'Other') {

				$snippet_type = YASR_ITEMTYPE;

			}

		}

		else {

			$snippet_type = YASR_ITEMTYPE;

		}

		return $snippet_type;

	}

}

/****** Get multi set name ******/
function yasr_get_multi_set() {
	global $wpdb;

	$result = $wpdb->get_results("SELECT * FROM " . YASR_MULTI_SET_NAME_TABLE . " ORDER BY set_id ASC");

	return $result;
}


/****** Get multi set values and field's name, used in ajax function and shortcode function ******/
function yasr_get_multi_set_values_and_field ($post_id, $set_type) {

	$result = get_transient( 'yasr_get_multi_set_values_and_field_' . $post_id . '_' . $set_type );

	if ($result !== FALSE) {

		return $result;

	}

	else {

		global $wpdb;

		$result=$wpdb->get_results($wpdb->prepare("SELECT f.field_name AS name, f.field_id AS id, v.votes AS vote
	                    FROM " . YASR_MULTI_SET_FIELDS_TABLE . " AS f, " . YASR_MULTI_SET_VALUES_TABLE . " AS v
	                    WHERE f.parent_set_id=%d
	                    AND f.field_id = v.field_id
	                    AND v.post_id = %d
	                    AND v.set_type = %d
	                    AND f.parent_set_id=v.set_type
	                    ORDER BY f.field_id ASC", $set_type, $post_id, $set_type));

		if (!empty($result)) {

			set_transient( 'yasr_get_multi_set_values_and_field_' . $post_id . '_' . $set_type, $result, WEEK_IN_SECONDS );

		}

		return $result;

	}


}


/****** Get multi set visitor votes ******/
function yasr_get_multi_set_visitor ($post_id, $set_type) {

	$result = get_transient( 'yasr_visitor_multi_set_' . $post_id . '_' . $set_type );

	if ($result !== FALSE) {

		return $result;

	}

	global $wpdb;

	$result=$wpdb->get_results($wpdb->prepare("SELECT f.field_name AS name, f.field_id AS id, v.number_of_votes AS number_of_votes, v.sum_votes AS sum_votes
                        FROM " . YASR_MULTI_SET_FIELDS_TABLE . " AS f, " . YASR_MULTI_SET_VALUES_TABLE . " AS v
                        WHERE f.parent_set_id=%d
                        AND f.field_id = v.field_id
                        AND v.post_id = %d
                        AND v.set_type = %d
                        AND f.parent_set_id=v.set_type
                        ORDER BY f.field_id ASC", $set_type, $post_id, $set_type));

	//Should be never empty because first time it's called
	//all values are set to 0, just to be safe
	if(!empty($result)) {

		set_transient('yasr_visitor_multi_set_' . $post_id . '_' . $set_type, $result, WEEK_IN_SECONDS );

	}

	return $result;

}



/****** Get visitor votes ******/
function yasr_get_visitor_votes ($post_id=FALSE, $create_transient=TRUE) {
	
	global $wpdb;

	//if values it's not passed get the post id, most of cases and default one
	if(!$post_id) {

		$post_id=get_the_ID();

	}

	if (!$post_id) {

		return;

	}

	$transient_name = 'yasr_visitor_votes_' . $post_id;

	$transient_visitor_votes = get_transient( $transient_name );

	if ($transient_visitor_votes) {

		return $transient_visitor_votes;

	}

	else {

		$result = $wpdb->get_results($wpdb->prepare("SELECT SUM(vote) AS sum_votes, COUNT(vote) as number_of_votes FROM " . YASR_LOG_TABLE . "  WHERE post_id=%d", $post_id));

		if ($create_transient == TRUE) {

			set_transient( $transient_name, $result, WEEK_IN_SECONDS );

		}

		return $result;

	}

}

/****** Check if a logged in user has already rated. Return user vote for a post if exists  ******/

function yasr_check_if_user_already_voted($post_id=FALSE) {

	global $wpdb;

	$current_user = wp_get_current_user();

    $user_id = $current_user->ID;

    //just to be safe
    if(!$post_id) {

    	$post_id = get_the_ID();

    }

    if (!$post_id || !$user_id) {

    	exit();

    }

	$rating = $wpdb->get_var($wpdb->prepare("SELECT vote FROM " . YASR_LOG_TABLE . " WHERE post_id=%d AND user_id=%d ORDER BY id DESC LIMIT 1 ", $post_id, $user_id));

	if ($rating === NULL) {

		$rating = FALSE;

	}

    return $rating;

}



/****** Adding logs widget to the dashboard ******/

add_action( 'plugins_loaded', 'add_action_dashboard_widget_log' );

	function add_action_dashboard_widget_log() {

		//This is for the admins (show all votes in the site)
		if ( current_user_can( 'manage_options' ) )  {
				add_action ('wp_dashboard_setup', 'yasr_add_dashboard_widget_log');
			}

		//This is for all the users to see where they've voted
		add_action ('wp_dashboard_setup', 'yasr_add_dashboard_widget_user_log');

	}

	function yasr_add_dashboard_widget_log () {

		wp_add_dashboard_widget (
			'yasr_widget_log_dashboard', //slug for widget
			'Recent Ratings', //widget name
			'yasr_widget_log_dashboard_callback' //function callback
		);

	}


	function yasr_widget_log_dashboard_callback () {


		$limit = 8; //max number of row to echo

		global $wpdb;

		$log_result = $wpdb->get_results ("SELECT * FROM ". YASR_LOG_TABLE . " ORDER BY date DESC LIMIT 0, $limit");

		$n_rows = $wpdb->get_var("SELECT COUNT(*) FROM ". YASR_LOG_TABLE );

		if (!$log_result) {
            _e("No recenet votes yet", 'yet-another-stars-rating');
        }

        else {

			echo "<div class=\"yasr-log-container\" id=\"yasr-log-container\">";

				foreach ($log_result as $column) {

					$user = get_user_by( 'id', $column->user_id );

					//If ! user means that the vote are anonymous
					if ($user == FALSE) {

						$user = (object) array('user_login');
						$user->user_login = __('anonymous');

					}

					$avatar = get_avatar($column->user_id, '32');

					$title_post = get_the_title( $column->post_id );
					$link = get_permalink( $column->post_id );

					$yasr_log_vote_text = sprintf(__('Vote %d from %s on ', 'yet-another-stars-rating'), $column->vote, '<strong style="color: blue">'.$user->user_login.'</strong>' );

					if (YASR_ENABLE_IP !== 'yes') {

						$column->ip = 'X.X.X.X';

					}

					echo "

						<div class=\"yasr-log-div-child\">

							<div class=\"yasr-log-image\">
								$avatar
							</div>

							<div class=\"yasr-log-child-head\">
								 <span id=\"yasr-log-vote\">$yasr_log_vote_text</span><span class=\"yasr-log-post\"><a href=\"$link\">$title_post</a></span>
							</div>

							<div class=\"yasr-log-ip-date\">

								<span class=\"yasr-log-ip\">" . __("Ip address" , 'yet-another-stars-rating') . ": <span style=\"color:blue\">$column->ip</span></span>

								<span class=\"yasr-log-date\">$column->date</span>

							</div>

						</div>

					";

				} //End foreach

				echo "<div id=\"yasr-log-page-navigation\">";

				$num_of_pages = ceil($n_rows/$limit);

				//use data attribute instead of value of #yasr-log-total-pages, because, on ajaxresponse,
				//the "last" button coul not exists
				//This is required on ajax, not here, but still doing it here to take it simple
				echo "<span id=\"yasr-log-total-pages\" data-yasr-log-total-pages=\"$num_of_pages\">";

					_e("Pages", 'yet-another-stars-rating'); echo ": ($num_of_pages) &nbsp;&nbsp;&nbsp;";

				echo "</span>";

				if ($num_of_pages <= 3) {

					for ($i=1; $i<=$num_of_pages; $i++) {

						if ($i == 1) {
		                    echo "<button class=\"button-primary\" value=\"$i\">$i</button>&nbsp;&nbsp;";
		                }

		                else {
							echo "<button class=\"yasr-log-pagenum\" value=\"$i\">$i</button>&nbsp;&nbsp;";

						}

					}

					echo "<span id=\"yasr-loader-log-metabox\" style=\"display:none;\">&nbsp;<img src=\"" . YASR_IMG_DIR . "/loader.gif\" ></span>";

				}

				else {

					for ($i=1; $i<=3; $i++) {

						if ($i == 1) {
		                    echo "<button class=\"button-primary\" value=\"$i\">$i</button>&nbsp;&nbsp;";
		                }

		                else {
							echo "<button class=\"yasr-log-pagenum\" value=\"$i\">$i</button>&nbsp;&nbsp;";
						}

					}

					echo "...&nbsp;&nbsp;<button class=\"yasr-log-pagenum\" id=\"yasr-log-total-pages\" value=\"$num_of_pages\">Last &raquo;</button>&nbsp;&nbsp;";

					echo "<span id=\"yasr-loader-log-metabox\" style=\"display:none;\">&nbsp;<img src=\"" . YASR_IMG_DIR . "/loader.gif\" ></span>";

				}

				echo "

				</div>

			</div>";

		} //End else

	} //End callback function


	//This add a dashboard log for every users
	//Add action is above
	function yasr_add_dashboard_widget_user_log () {

		wp_add_dashboard_widget (
			'yasr_users_dashboard_widget', //slug for widget
			'Your Ratings', //widget name
			'yasr_users_dashboard_widget_callback' //function callback
		);

	}


	function yasr_users_dashboard_widget_callback () {

		$limit = 8; //max number of row to echo

		//Get current user id: don't need to check if is >0 cause user is logged in here
		$user_id = get_current_user_id();

		global $wpdb;

		$log_result = $wpdb->get_results ("SELECT * FROM ". YASR_LOG_TABLE . " WHERE user_id = $user_id ORDER BY date DESC LIMIT 0, $limit");

		$n_rows = $wpdb->get_var("SELECT COUNT(*) FROM ". YASR_LOG_TABLE . " WHERE user_id = $user_id" );

		if (!$log_result) {
            _e("No recenet votes yet", 'yet-another-stars-rating');
        }

        else {

			echo "<div class=\"yasr-log-container\" id=\"yasr-user-log-container\">";

				foreach ($log_result as $column) {

					$avatar = get_avatar($user_id, '32');

					$title_post = get_the_title( $column->post_id );
					$link = get_permalink( $column->post_id );

					$yasr_log_vote_text = sprintf(__('You rated %s on ', 'yet-another-stars-rating'), '<strong style="color: blue">'.$column->vote.'</strong>');
					$yasr_log_date_text = __('Date:', 'yet-another-stars-rating');

					echo "

						<div class=\"yasr-log-div-child\" style=\"padding-bottom: 2px;\" >

							<div class=\"yasr-log-image\">
								$avatar
							</div>

							<div class=\"yasr-log-child-head\">
								 <span id=\"yasr-log-vote\">$yasr_log_vote_text</span><span class=\"yasr-log-post\"><a href=\"$link\">$title_post</a></span>
							</div>
							
							<div class=\"yasr-log-child-head\">
								<strong>$yasr_log_date_text</strong> <span class=\"yasr-log-date-user\">$column->date</span>
							</div>
							

						</div>

					";

				} //End foreach

				echo "<div id=\"yasr-log-page-navigation\">";

				$num_of_pages = ceil($n_rows/$limit);

				//use data attribute instead of value of #yasr-log-total-pages, because, on ajaxresponse,
				//the "last" button coul not exists
				//This is required on ajax, not here, but still doing it here to take it simple
				echo "<span id=\"yasr-user-log-total-pages\" data-yasr-user-log-total-pages=\"$num_of_pages\">";

					_e("Pages", 'yet-another-stars-rating'); echo ": ($num_of_pages) &nbsp;&nbsp;&nbsp;";

				echo "</span>";

				if ($num_of_pages <= 3) {

					for ($i=1; $i<=$num_of_pages; $i++) {

						if ($i == 1) {
		                    echo "<button class=\"button-primary\" value=\"$i\">$i</button>&nbsp;&nbsp;";
		                }

		                else {
							echo "<button class=\"yasr-user-log-pagenum\" value=\"$i\">$i</button>&nbsp;&nbsp;";

						}

					}

					echo "<span id=\"yasr-loader-user-log-metabox\" style=\"display:none;\">&nbsp;<img src=\"" . YASR_IMG_DIR . "/loader.gif\" ></span>";

				}

				else {

					for ($i=1; $i<=3; $i++) {

						if ($i == 1) {
		                    echo "<button class=\"button-primary\" value=\"$i\">$i</button>&nbsp;&nbsp;";
		                }

		                else {
							echo "<button class=\"yasr-user-log-pagenum\" value=\"$i\">$i</button>&nbsp;&nbsp;";
						}

					}

					echo "...&nbsp;&nbsp;<button class=\"yasr-user-log-pagenum\" id=\"yasr-user-log-total-pages\" value=\"$num_of_pages\">Last &raquo;</button>&nbsp;&nbsp;";

					echo "<span id=\"yasr-loader-user-log-metabox\" style=\"display:none;\">&nbsp;<img src=\"" . YASR_IMG_DIR . "/loader.gif\" ></span>";

				}

				echo "

				</div>

			</div>";

		} //End else

	} //End callback function



/****** Delete data value from yasr tabs when a post or page is deleted
Added since yasr 0.3.3
******/

add_action ('admin_init', 'admin_init_delete_data_on_post_callback');

	function admin_init_delete_data_on_post_callback () {

		if ( current_user_can ('delete_posts') ) {

			add_action( 'delete_post', 'yasr_erase_data_on_post_page_remove_callback' );

		}

	}

	function yasr_erase_data_on_post_page_remove_callback($pid) {

		global $wpdb;

			delete_post_meta($pid, 'yasr_overall_rating');
			delete_post_meta($pid, 'yasr_review_type');

			//Delete multi value
			$wpdb->delete(
				YASR_MULTI_SET_VALUES_TABLE,
				array (
					'post_id' => $pid
					),
				array (
					'%d'
					)
				);

			$wpdb->delete(
				YASR_LOG_TABLE,
				array (
					'post_id' => $pid
					),
				array (
					'%d'
					)
				);


	}






/****** Function to get always the last id in the log table ******/

	function yasr_count_logged_vote () {

		global $wpdb;

		$result = $wpdb->get_var("SELECT COUNT(id) FROM " . YASR_LOG_TABLE );

		if ($result) {

			return $result;

		}

		else {

			return '0';

		}

	}




/******* Add post_meta on save_post if this post is excluded for auto insert *******/

if (YASR_AUTO_INSERT_ENABLED == 1 ) {

	add_action( 'save_post', 'yasr_exclude_auto_insert_callback' );

    	function yasr_exclude_auto_insert_callback() {

			$post_id = get_the_ID();

	        if(isset($_POST['yasr_auto_insert_disabled'])) {

		        update_post_meta($post_id, 'yasr_auto_insert_disabled', 'yes');

	        }

			else {

				delete_post_meta($post_id, 'yasr_auto_insert_disabled');

			}


    	}

}

?>
