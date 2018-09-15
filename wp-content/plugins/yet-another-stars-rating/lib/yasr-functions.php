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


/***** Adding javascript and css *****/

	add_action( 'wp_enqueue_scripts', 'yasr_add_scripts' );
	add_action( 'admin_enqueue_scripts', 'yasr_add_admin_scripts' );

	function yasr_add_scripts () {

        wp_enqueue_style( 'yasrcss', YASR_CSS_DIR . 'yasr.css', FALSE, NULL, 'all' );

        wp_enqueue_style( 'jquery-ui', YASR_CSS_DIR . 'jquery-ui.css', FALSE, '1.11.2', 'all' );
        
        wp_enqueue_style( 'dashicons' ); //dashicons

        //Run after default css are loaded
        do_action( 'yasr_add_front_script_css' );

        //If choosen is light or not dark (force to be default)
        if (YASR_SCHEME_COLOR === 'light' || YASR_SCHEME_COLOR != 'dark' ) {
            wp_enqueue_style( 'yasrcsslightscheme', YASR_CSS_DIR . 'yasr-table-light.css', array('yasrcss'), NULL, 'all' );
        }

        elseif (YASR_SCHEME_COLOR === 'dark') {
            wp_enqueue_style( 'yasrcssdarkscheme', YASR_CSS_DIR . 'yasr-table-dark.css', array('yasrcss'), NULL, 'all' );
        }

        if (YASR_CUSTOM_CSS_RULES) {
            wp_add_inline_style( 'yasrcss', YASR_CUSTOM_CSS_RULES );
        }

	    wp_enqueue_script( 'rateit', YASR_JS_DIR . 'jquery.rateit.min.js' , array('jquery'), '1.0.22', TRUE );


        //load rater if jquery is disabled
        if (YASR_JQUERY_DISABLED === 'yes') {

	        wp_enqueue_script( 'rater', YASR_JS_DIR . 'rater-js.js' , '', '0.6.0', TRUE );


        }

		wp_enqueue_script( 'jquery-ui-progressbar' ); //script

        wp_enqueue_script( 'jquery-ui-tooltip' ); //script

        wp_enqueue_script( 'yasrfront', YASR_JS_DIR . 'yasr-front.js' , array('jquery', 'rateit'), '1.0.0', TRUE );

        do_action('yasr_add_front_script_js');

        $yasr_visitor_votes_loader = '<div id="loader-visitor-rating" >&nbsp; ' . __("Loading, please wait",'yet-another-stars-rating') . ' <img src=' .  YASR_IMG_DIR . 'loader.gif title="yasr-loader" alt="yasr-loader"></div>';

        $tooltip_values = __("bad, poor, ok, good, super", 'yet-another-stars-rating');
        $tooltip_values_exploded = explode(", ", $tooltip_values);

        wp_localize_script ('yasrfront', 'yasrCommonData', [

            'postid' => get_the_ID(),
            'ajaxurl' => admin_url('admin-ajax.php'),
            'loggedUser' => is_user_logged_in(),
            'visitorStatsEnabled' => YASR_VISITORS_STATS,
            'tooltipValues' => $tooltip_values_exploded,
            'loaderHtml' => $yasr_visitor_votes_loader,
            'jquery_disabled' => YASR_JQUERY_DISABLED

	        ]

        );

	}

    //$hook contain the current page in the admin side
    function yasr_add_admin_scripts ($hook) {

        global $yasr_settings_page;

        if($hook == 'index.php' || $hook == 'edit.php' || $hook == 'post.php' || $hook == 'post-new.php' || $hook == 'edit-comments.php' || $hook == $yasr_settings_page || $hook == 'yet-another-stars-rating_page_yasr_stats_page') {

            do_action('yasr_add_admin_scripts_begin');

            wp_enqueue_style( 'yasrcss', YASR_CSS_DIR . 'yasr-admin.css', FALSE, NULL, 'all' );
            wp_enqueue_style( 'wp-jquery-ui-dialog' ); //style

            wp_enqueue_script( 'rateit', YASR_JS_DIR . 'jquery.rateit.min.js' , array('jquery'), '1.0.22', TRUE );

	        //load rater if jquery is disabled
	        if (YASR_JQUERY_DISABLED === 'yes') {

		        wp_enqueue_script( 'rater', YASR_JS_DIR . 'rater-js.js' , '', '0.6.0', TRUE );


	        }

	        wp_enqueue_script( 'jquery-ui-dialog' ); //script

            wp_enqueue_script( 'yasradmin', YASR_JS_DIR . 'yasr-admin.js' , array('jquery'), '1.0.00', TRUE );

			//this is for tinymce
			wp_enqueue_script('yasr_shortcode_creator', YASR_JS_DIR . 'yasr-shortcode-creator.js', array('jquery'), '1.0', TRUE);

            do_action('yasr_add_admin_scripts_end' );

        }


    }

/*** Css rules for stars set, from version 1.2.7
 Here I use add_action instead of directly use wp_add_inline_style so I can
 use remove_action if needed (e.g. Yasr Stylish)
***/

add_action( 'yasr_add_front_script_css', 'yasr_css_stars_set' );
add_action( 'yasr_add_admin_scripts_end', 'yasr_css_stars_set' );

function yasr_css_stars_set() {

	//if is not flat, default case
	if (YASR_STARS_SET !== 'flat') {

		$stars_16 = YASR_IMG_DIR . 'star.png';
		$stars_24 = YASR_IMG_DIR . 'stars24.png';
		$stars_32 = YASR_IMG_DIR . 'stars32.png';

	}

	else {

		$stars_16 = YASR_IMG_DIR . 'stars_16_flat.png';
		$stars_24 = YASR_IMG_DIR . 'stars_24_flat.png';
		$stars_32 = YASR_IMG_DIR . 'stars_32_flat.png';

	}


	$yasr_st_css = "
		.rateit .rateit-range {
			background: url($stars_16) left 0px !important;
		}

		.rateit .rateit-hover {
			background: url($stars_16) left -21px !important;
		}

		.rateit .rateit-selected {
			background: url($stars_16) left -42px !important;
		}

		div.medium .rateit-range {
			/*White*/
			background: url($stars_24) left 0px !important;
		}

		div.medium .rateit-hover {
			/*Red*/
			background: url($stars_24) left -29px !important;
		}

		div.medium .rateit-selected {
			/*Yellow*/
			background: url($stars_24) left -58px !important;
		}

		/* Creating set 32 */

		div.bigstars .rateit-range {
			/*White*/
			background: url($stars_32) left 0px !important;
		}

		div.bigstars .rateit-hover{
			/*red*/
			background: url($stars_32) left -37px !important;
		}

		div.bigstars .rateit-selected
		{
			/*Gold*/
			background: url($stars_32) left -74px !important;
		}

	";

	wp_add_inline_style( 'yasrcss', $yasr_st_css );

}



/****** Translating YASR ******/

	add_action('init', 'yasr_translate_option', 110);

    	function yasr_translate_option() {
    		load_plugin_textdomain('yet-another-stars-rating', FALSE, YASR_LANG_DIR);
    	}


/****** Create a new Page in Administration Menu ******/

	/* Hook to admin_menu the yasr_add_pages function above */
	add_action( 'admin_menu', 'yasr_add_pages' );

    	function yasr_add_pages() {

            global $yasr_settings_page;

            //Add Settings Page
            $yasr_settings_page = add_menu_page(
                __( 'Yet Another Stars Rating: settings', 'yet-another-stars-rating' ), //Page Title
                __( 'Yet Another Stars Rating', 'yet-another-stars-rating' ), //Menu Title
                'manage_options', //capability
                'yasr_settings_page', //menu slug
                'yasr_settings_page_callback', //The function to be called to output the content for this page.
                'dashicons-star-half'
            );

		    add_submenu_page('yasr_settings_page',
                'Yet Another Stars Rating: settings',
                'Settings',
                'manage_options',
                'yasr_settings_page'
            );

		    add_submenu_page('yasr_settings_page',
			    'Yet Another Stars Rating: All Rating',
			    'Logs',
			    'manage_options',
			    'yasr_stats_page',
                'yasr_stats_page_callback'
		    );
        }


	// Settings Page Content
	function yasr_settings_page_callback () {

    	if ( ! current_user_can( 'manage_options' ) ) {
        	wp_die( __( 'You do not have sufficient permissions to access this page.', 'yet-another-stars-rating' ) );
    	}

	   include(YASR_ABSOLUTE_PATH  . '/yasr-settings-page.php');

	} //End yasr_settings_page_content


    function yasr_stats_page_callback () {

	    if ( ! current_user_can( 'manage_options' ) ) {
		    wp_die( __( 'You do not have sufficient permissions to access this page.', 'yet-another-stars-rating' ) );
	    }

	    include( YASR_ABSOLUTE_PATH . '/yasr-stats-page.php' );

    }



/****** Create 2 metaboxes in post and pages ******/

	add_action( 'add_meta_boxes', 'yasr_add_metaboxes' );

	function yasr_add_metaboxes() {

        //Default post type where display metabox
        $post_type_where_display_metabox = array('post', 'page');

        //get the custom post type
        $custom_post_types = yasr_get_custom_post_type();

        if ($custom_post_types) {

            //First merge array then changes keys to int
            $post_type_where_display_metabox = array_values(array_merge($post_type_where_display_metabox, $custom_post_types));

        }

        //Always add this metabox
        foreach ($post_type_where_display_metabox as $post_type) {
            add_meta_box( 'yasr_metabox_overall_rating', 'YASR', 'yasr_metabox_overall_rating_content', $post_type, 'side', 'high' );
        }

        $multi_set=yasr_get_multi_set();
        //If multiset are used then add the second metabox
        if ($multi_set) {
            foreach ($post_type_where_display_metabox as $post_type) {
                add_meta_box( 'yasr_metabox_multiple_rating', __( 'Yet Another Stars Rating: Multiple set', 'yet-another-stars-rating' ), 'yasr_metabox_multiple_rating_content', $post_type, 'normal', 'high' );
            }
        }

    } //End function

	function yasr_metabox_overall_rating_content() {
		if ( current_user_can( 'publish_posts' ) )  {
			include (YASR_ABSOLUTE_PATH . '/yasr-metabox-top-right.php');
		}
		else {
            _e("You don't have enought privileges to insert Overall Rating");
        }

	}

	function yasr_metabox_multiple_rating_content() {
		if ( current_user_can( 'publish_posts' ) )  {
			include (YASR_ABSOLUTE_PATH . '/yasr-metabox-multiple-rating.php');
		}
        else {
            _e("You don't have enough privileges to insert a Multi Set");
        }

	}


/****** Auto insert overall rating and visitor rating  ******/

    if (YASR_AUTO_INSERT_ENABLED == 1) {

        if (!is_admin()) {

            add_filter('the_content', 'yasr_auto_insert_shortcode_callback');

        }

        function yasr_auto_insert_shortcode_callback($content) {

			$post_id = get_the_ID();

			//check if for this post or page auto insert is off
			$post_excluded = get_post_meta($post_id, 'yasr_auto_insert_disabled', TRUE);

			if($post_excluded === 'yes') {

				return $content;

			}

            $auto_insert_shortcode=NULL; //To avoid undefined variable notice outside the loop (if (is_singular) )

            $overall_rating_code = '[yasr_overall_rating size="' . YASR_AUTO_INSERT_SIZE . '"]';

            $visitor_votes_code = '[yasr_visitor_votes size="' . YASR_AUTO_INSERT_SIZE . '"]';

            if (YASR_AUTO_INSERT_WHAT==='overall_rating') {
                switch (YASR_AUTO_INSERT_WHERE) {
                    case 'top':
                        $content_and_stars = $overall_rating_code . $content;
                        break;

                    case 'bottom':
                        $content_and_stars = $content . $overall_rating_code;
                        break;
                } //End Switch
            } //end YASR_AUTO_INSERT_WHAT overall rating

            elseif (YASR_AUTO_INSERT_WHAT==='visitor_rating') {
                switch (YASR_AUTO_INSERT_WHERE) {
                    case 'top':
                        $content_and_stars = $visitor_votes_code . $content;
                        break;

                    case 'bottom':
                        $content_and_stars = $content . $visitor_votes_code;
                        break;
                } //End Switch
            }

            elseif (YASR_AUTO_INSERT_WHAT==='both') {
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

                $custom_post_types = yasr_get_custom_post_type();

                //If is a post type return content and stars
                if (is_singular($custom_post_types)) {
                    return $content_and_stars;
                }

                //else return just content
                else {
                    return $content;
                }

            }

            //If page are not excluded
            if (YASR_AUTO_INSERT_EXCLUDE_PAGES === 'no') {
                return $content_and_stars;
            }

            //else return only if it is not a page
            elseif (YASR_AUTO_INSERT_EXCLUDE_PAGES === 'yes') {
                if ( !is_page() ) {
                    return $content_and_stars;
                }
                //If is a page return the content without stars
                else {
                    return $content;
                }
            }

        } //End function yasr_auto_insert_shortcode_callback

    } //End  if (YASR_AUTO_INSERT_ENABLED


/****** Add review schema data at the end of the post *******/


add_filter('the_content', 'yasr_add_schema');


	function yasr_add_schema($content) {

        //Add buddypress compatibility
        if (function_exists('bp_is_active')) {

            //Return content only if is page. This will disable schema for all page.
            //If I try to return $content after if (YASR_SNIPPET == 'overall_rating')
            //or (YASR_SNIPPET == 'visitor_rating') $content will have only wp content, losing the buddypress one
            if (is_page()) {

                return $content;

            }

        }

        if(!is_singular() && is_main_query() || is_404()){

            return $content;

        }

        $script_type = '<script type="application/ld+json">';

        $end_script_type = '</script>';

        $review_choosen = yasr_get_snippet_type();

        //if doesn't exists a filter for yasr_filter_schema_jsonld $review_chosen value is assigned to $filtered_schema...
        $filtered_schema = apply_filters( 'yasr_filter_schema_jsonld', $review_choosen );

        //So check here if $schema != $review_choosen
        if ($filtered_schema !== $review_choosen) {

            return $content . $script_type . $filtered_schema . $end_script_type;

        }

        $rich_snippet["@context"] = "http://schema.org/";


		$author = get_the_author();

		$review_name = get_the_title();

		$date = get_the_date('c');

		$date_modified = get_the_modified_date('c');

		$post_image_url = ''; //avoid undefined
		$logo_image_url = ''; //avoid undefined

		if (defined('YASR_BLOGPOSTING_ORGANIZATION_LOGO')) {

			$logo_image_url = YASR_BLOGPOSTING_ORGANIZATION_LOGO;

            $post_image_url =  $logo_image_url; //this will be overwritten if has_post_thumbnail is true 

			$logo_image_url_absolute = $_SERVER['DOCUMENT_ROOT'] . parse_url(YASR_BLOGPOSTING_ORGANIZATION_LOGO, PHP_URL_PATH);

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
		if (has_post_thumbnail() ) {

            $post_image_url =  wp_get_attachment_url(get_post_thumbnail_id());

			$post_image_url_absolute = $_SERVER['DOCUMENT_ROOT'] . parse_url($post_image_url, PHP_URL_PATH);

			$post_image_size = @getimagesize($post_image_url_absolute);  //the @ should be useless, just to be safe

		}


		if ($review_choosen == "Product") {

			$rich_snippet["@type"]="Product";

		}

		elseif ($review_choosen == "Recipe") {

			$rich_snippet["@type"]="Recipe";

			$rich_snippet["image"] = array(
				"@type" => "ImageObject",
				"url" => $post_image_url,
				"width" => $post_image_size[0],
				"height" => $post_image_size[1]
			);

		}

		elseif ($review_choosen == "Place") {

			$rich_snippet["@type"]="LocalBusiness";

		}

		elseif ($review_choosen == "Other") {

			$rich_snippet["@type"] = "BlogPosting";

			$rich_snippet["datePublished"] = $date;

			$rich_snippet["headline"] = $review_name;

			$rich_snippet["mainEntityOfPage"]  = array(
				"@type" => "WebPage",
				"@id" => get_permalink()
			);


			$rich_snippet["author"] = array(
				"@type" => "Person",
				"name" => "$author"
			);

			$rich_snippet["publisher"] = array(
				"@type" => "Organization",
				"name" => YASR_BLOGPOSTING_ORGANIZATION_NAME,
				"logo" => array(
					"@type" => "ImageObject",
					"url" =>  $logo_image_url,
					"width" => $logo_image_size[0],
					"height" => $logo_image_size[1]
				)

			);

			$rich_snippet["dateModified"] =  $date_modified;

			$rich_snippet["image"] = array(
				"@type" => "ImageObject",
				"url" => $post_image_url,
				"width" => $post_image_size[0],
				"height" => $post_image_size[1]
			);

		}

		if (YASR_SNIPPET == 'overall_rating') {

			$overall_rating=yasr_get_overall_rating(FALSE, FALSE);

			if($overall_rating && $overall_rating != '-1' && $overall_rating != '0.0') {

					global $post;

                    //name
                    $rich_snippet["name"] = $review_name;

                    $rich_snippet["Review"] = array (
                        "@type" => "Review",
                        "name" => "$review_name",
                        "author" => array(
                            "@type" => "Person",
                            "name" => "$author"
                        ),
                        "datePublished" => "$date",
                        "reviewRating" => array(
                            "@type" => "Rating",
                            "ratingValue" => "$overall_rating",
                        ),
                    );

			} //END id if $overall_rating != '-1'

			else {

				return $content;

			}

		}  //end if ($choosen_snippet['snippet'] == 'overall_rating')

		if (YASR_SNIPPET == 'visitor_rating') {

			$visitor_votes = yasr_get_visitor_votes (FALSE, FALSE);

            if ($visitor_votes) {

                foreach ($visitor_votes as $rating) {
                    $visitor_rating['votes_number']=$rating->number_of_votes;
                    $visitor_rating['sum']=$rating->sum_votes;
                }

            }

            else {

				return $content;

            }

            if ($visitor_rating['sum'] != 0 && $visitor_rating['votes_number'] != 0) {

                $average_rating = $visitor_rating['sum'] / $visitor_rating['votes_number'];

                $average_rating = round($average_rating, 1);

                //name
                $rich_snippet["name"] = $review_name;

                $rich_snippet["aggregateRating"] = array (
                    "@type" => "AggregateRating",
                    "ratingValue" => "$average_rating",
                    "ratingCount" => $visitor_rating['votes_number'],
                );

            }

			else {

				return $content;

			}

        }

        if ( is_singular() && is_main_query() && !is_404() ) {
            return $content . $script_type . json_encode($rich_snippet) . $end_script_type;
        }

        else {
            return $content;
        }



    } //End function


/****** Create a select menu to choose the rich snippet itemtype ******/

    function yasr_select_itemtype() {

        $i18n_array_review = __('Product, Place, Recipe, BlogPosting', 'yet-another-stars-rating');

        $array_review_type = explode(',', $i18n_array_review);

        $review_type = array_map('trim', $array_review_type);

        $review_type_choosen = yasr_get_snippet_type();

        switch ($review_type_choosen) {
            case 'Product':
                $review_type_choosen = 1;
                break;
            case 'Place':
                $review_type_choosen = 2;
                break;
            case 'Recipe':
                $review_type_choosen = 3;
                break;
            case 'Other':
                $review_type_choosen = 4;
                break;
        }

        ?>

        <select id="yasr-choose-reviews-types-list">

            <?php

                $i = 1;

                foreach ($review_type as $type) {

                    if ($i == $review_type_choosen) {

                        echo "<option value=\"$i\" selected>$type</option>";

                    }

                    else {
                        echo "<option value=\"$i\">$type</option>";
                    }

                    $i = $i+1;

                }


            ?>

        </select>

        <?php

    } //End function yasr_select_itemtype()


/******* Add a media content button ******/

add_action('media_buttons', 'yasr_shortcode_button_media', 99);

	function yasr_shortcode_button_media() {

		if (is_admin()) {

			add_thickbox();

			echo '<a href="#TB_inline?width=530&height=600&inlineId=yasr-tinypopup-form" id="yasr-shortcode-creator" class="button thickbox"><span class="dashicons dashicons-star-half" style="vertical-align: midlle;"></span> Yasr Shortcode</a>';

		}

	}



/****** Return the custom post type if exists
Argument is to set what to return, if array or boolean value.
Default: array******/

add_action( 'admin_init', 'yasr_get_custom_post_type');
    function yasr_get_custom_post_type($exit='array') {

        $args = array(
            'public'   => true,
            '_builtin' => false
        );

        $output = 'names'; // names or objects, note names is the default
        $operator = 'and'; // 'and' or 'or'

        $post_types = get_post_types( $args, $output, $operator );

        if ($post_types) {
            if ($exit == 'array') {
                return ($post_types);
            }
            else {
                return TRUE;
            }
        }

        else {
            return FALSE;
        }

    }


/*** function that get the star size and return it***/
function yasr_stars_size ($size) {

    $size = sanitize_text_field($size);


    $stars_attribute = array();

    if ($size === 'small') {

        $stars_attribute['class'] = 'rateit yasr_visitor_votes_stars_div';
        $stars_attribute['px_size'] = '16';
    }

    elseif ($size === 'medium') {

        $stars_attribute['class'] = 'rateit medium yasr_visitor_votes_stars_div';
        $stars_attribute['px_size'] = '24';
    }

    //default values
    else {

        $stars_attribute['class'] = 'rateit bigstars yasr_visitor_votes_stars_div';
        $stars_attribute['px_size'] = '32';
    }

    return $stars_attribute;

}



/*** Add support for wp super cache ***/

function yasr_wp_super_cache_support($post_id) {

    if(function_exists('wp_cache_post_change')) {
        wp_cache_post_change($post_id);
    }

}

/*** Add support for wp rocket, thanks to GeekPress
https://wordpress.org/support/topic/compatibility-with-wp-rocket-2
***/

function yasr_wp_rocket_support($post_id) {

    if(function_exists('rocket_clean_post')) {
        rocket_clean_post($post_id);
    }

}

/*** Add support for LiteSpeed Cache plugin, thanks to Pako69 
https://wordpress.org/support/topic/yasr-is-litespeed-cache-plugin-compatible/
***/

function yasr_litespeed_cache_support($post_id) {

    if (method_exists( 'LiteSpeed_Cache_API', 'purge_post' ) == TRUE) {
        LiteSpeed_Cache_API::purge_post( $post_id ) ;
    }

}


/*** Function to set cookie, since version 0.8.3 ***/
    function yasr_setcookie($cookiename, $data_to_save) {

	    if ( ! $data_to_save || ! $cookiename ) {
		    exit( 'Error setting yasr cookie' );
	    }

	    //setcookie add \ , so I need to stripslahes
	    $existing_data = stripslashes( $_COOKIE[ $cookiename ] );

	    //unserialize
	    $existing_data = unserialize( $existing_data);

	    //whetever exists or not, push into at the end of array
	    $existing_data[] = $data_to_save;

	    $encoded_data = serialize( $existing_data );

	    setcookie( $cookiename, $encoded_data, time() + 31536000, COOKIEPATH, COOKIE_DOMAIN );

    }

/*** Function to get ip, since version 0.8.8
This code can be found on http://codex.wordpress.org/Plugin_API/Filter_Reference/pre_comment_user_ip
***/

function yasr_get_ip() {

    if (YASR_ENABLE_IP === 'yes') {

	    $ip = null;

	    $ip = apply_filters( 'yasr_filter_ip', $ip );

	    if ( isset( $ip ) ) {

		    return $ip;

	    }


	    $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];

	    if ( ! empty( $_SERVER['X_FORWARDED_FOR'] ) ) {

		    $X_FORWARDED_FOR = explode( ',', $_SERVER['X_FORWARDED_FOR'] );

		    if ( ! empty( $X_FORWARDED_FOR ) ) {
			    $REMOTE_ADDR = trim( $X_FORWARDED_FOR[0] );
		    }

	    } /*
        * Some php environments will use the $_SERVER['HTTP_X_FORWARDED_FOR']
        * variable to capture visitor address information.
        */

        elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {

		    $HTTP_X_FORWARDED_FOR = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );

		    if ( ! empty( $HTTP_X_FORWARDED_FOR ) ) {
			    $REMOTE_ADDR = trim( $HTTP_X_FORWARDED_FOR[0] );
		    }

	    }

	    return preg_replace( '/[^0-9a-f:\., ]/si', '', $REMOTE_ADDR );

    }

    else {

        return ('X.X.X.X');

    }



}




/*function to remove duplicate in an array for a specific key
Taken value: array to search, key
*/

function yasr_unique_multidim_array($array, $key) {

	$temp_array = array();
	$i = 0;

	//creo un array vuoto che conterrà solo gli indici
	$key_array = array();

	foreach($array as $val) {

		$result_search_array = array_search($val[$key], $key_array);

		$key_array[$i] = $val[$key];
		$temp_array[$i] = $val;

		//if result is found
		if ($result_search_array !== FALSE) {

			unset($key_array[$result_search_array], $temp_array[$result_search_array]);

		}

		$i++;

	}

	sort($temp_array);

	return $temp_array;

}

?>
