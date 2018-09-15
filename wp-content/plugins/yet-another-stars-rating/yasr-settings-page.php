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

if ( !current_user_can( 'manage_options' ) ) {
	wp_die( __( 'You do not have sufficient permissions to access this page.', 'yet-another-stars-rating' ));
}

$ajax_nonce_hide_ask_rating = wp_create_nonce( "yasr_nonce_hide_ask_rating" );

yasr_include_fb_sdk ();

$n_multi_set = NULL; //Evoid undefined variable when printed outside multiset tab


?>

	<div class="wrap">

        <h2>Yet Another Stars Rating: <?php _e("Settings", 'yet-another-stars-rating'); ?></h2>

        <?php

        $error_new_multi_set=yasr_process_new_multi_set_form(); //defined in yasr-settings-functions

        $error_edit_multi_set=yasr_process_edit_multi_set_form(); //defined in yasr-settings-functions

        if ($error_new_multi_set) {
        	echo "<div class=\"error\"> <p> <strong>";

      		foreach ($error_new_multi_set as $error) {
      			_e($error, 'yet-another-stars-rating');
      			echo "<br />";
      		}

    		echo "</strong></p></div>";
    	}

        if ($error_edit_multi_set) {
        	echo "<div class=\"error\"> <p> <strong>";

      		foreach ($error_edit_multi_set as $error) {
      			_e($error, 'yet-another-stars-rating');
      			echo "<br />";
      		}

    		echo "</strong></p></div>";
    	}


		if (isset($_GET['tab'])) {
    		$active_tab = $_GET[ 'tab' ];
		}

		else {
			$active_tab = 'general_settings';
		}

		//Do the settings tab
		yasr_settings_tabs($active_tab);

	    if ($active_tab == 'general_settings') {

	    	?>

		    <div class="yasr-settingsdiv">
		        <form action="options.php" method="post" id="yasr_settings_form">
		            <?php
			            settings_fields( 'yasr_general_options_group' );
			            do_settings_sections('yasr_general_settings_tab' );
		            	submit_button( __('Save Settings') );
		           	?>
		       	</form>
		    </div>

	        <?php
                yasr_upgrade_pro_box();
                yasr_fb_box ();
                yasr_ask_rating ();
            ?>

            <div class="yasr-space-settings-div">
            </div>

            <?php


		} //End if tab 'general_settings'


		if ($active_tab == 'manage_multi') {

            global $wpdb;

            //delete all transient that use multiset
            $sql_delete_transient = "
			DELETE FROM {$wpdb->options}
			WHERE option_name LIKE '_transient_yasr_get_multi_set_values_and_field_%'
			OR option_name LIKE '_transient_yasr_visitor_multi_set_%'
			OR option_name LIKE '_transient_timeout_yasr_get_multi_set_values_and_field_%'
			OR option_name LIKE '_transient_timeout_yasr_visitor_multi_set_%'
		";

            $wpdb->query($sql_delete_transient);

			$multi_set=yasr_get_multi_set();

			$n_multi_set = $wpdb->num_rows; //wpdb->num_rows always store the last of the last query

			?>

			<div class="yasr-settingsdiv">

				<h3> <?php _e("Manage Multi Set", 'yet-another-stars-rating'); ?></h3>

				<p>

					<a href="#" id="yasr-multi-set-doc-link"><?php _e("What is a Multi Set?", 'yet-another-stars-rating') ?></a>

				</p>

				<div id="yasr-multi-set-doc-box" style="display:none">
					<?php _e("Multi Set allows you to insert a rate for each aspect about the product / local business / whetever you're reviewing, example in the image below.", 'yet-another-stars-rating');

					echo "<br /><br /><img src=" . YASR_IMG_DIR . "/yasr-multi-set.png> <br /> <br />";

					_e("You can create up to 99 different Multi Set and each one can contain up to 9 different fields. Once you've saved it, you can insert the rates while typing your article in the box below the editor, as you can see in this image (click to see it larger)", 'yet-another-stars-rating');

					echo "<br /><br /><a href=\"" . YASR_IMG_DIR ."yasr-multi-set-insert-rate.jpg\"><img src=" . YASR_IMG_DIR . "/yasr-multi-set-insert-rate-small.jpg></a> <br /> <br />";

					_e("In order to insert your Multi Sets into a post or page, you can either past the short code that will appear at the bottom of the box or just click on the star in the graphic editor and select \"Insert Multi Set\".", 'yet-another-stars-rating');

					?>

					<br /> <br />

					<a href="#" id="yasr-multi-set-doc-link-hide"><?php _e("Close this message", 'yet-another-stars-rating') ?></a>

				</div>

				<div class="yasr-multi-set-left">

					<div class="yasr-new-multi-set" >

						<?php yasr_display_multi_set_form(); ?>

					</div> <!--yasr-new-multi-set-->

				</div> <!--End yasr-multi-set-left-->

				<div class="yasr-multi-set-right">

					<?php yasr_edit_multi_form(); ?>

					<div id="yasr-multi-set-response" style="display:none">

					</div>

				</div> <!--End yasr-multi-set-right-->

				<div class="yasr-space-settings-div">
				</div>


				<div class="yasr-multi-set-choose-theme">

					<!--This allow to choose color for multiset-->
					<form action="options.php" method="post" id="yasr_multiset_form">
				            <?php
					            settings_fields( 'yasr_multiset_options_group' );
					            do_settings_sections('yasr_multiset_tab' );
				            	submit_button( __('Save') );
				           	?>
				    </form>

				</div>


			</div>

			<?php
			    yasr_upgrade_pro_box();
			    yasr_fb_box ();
		        yasr_ask_rating ();
	        ?>

			<div class="yasr-space-settings-div">
			</div>

			<?php

		} //End if ($active_tab=='manage_multi')


		if ($active_tab == 'style_options') {

			?>

			<?php do_action('yasr_add_content_top_style_options_tab', $active_tab); ?>

			<div class="yasr-settingsdiv">
		        <form action="options.php" method="post" enctype='multipart/form-data' id="yasr_settings_form">
		            <?php
			            settings_fields( 'yasr_style_options_group' );
			            do_settings_sections('yasr_style_tab' );
		            	submit_button( __('Save') );
		           	?>
		       	</form>
			</div>

			<?php
                yasr_upgrade_pro_box();
				yasr_fb_box ();
		        yasr_ask_rating ();
	        ?>

			<div class="yasr-space-settings-div">
			</div>

			<?php do_action('yasr_add_content_bottom_style_options_tab', $active_tab); ?>


			<?php

		} //End tab style


		do_action( 'yasr_settings_check_active_tab', $active_tab );


        yasr_upgrade_pro_box("bottom");
        yasr_fb_box("bottom");
	    yasr_ask_rating ("bottom");

	?>

	<!--End div wrap-->
	</div>


    <script type="text/javascript">

	    jQuery( document ).ready(function() {

	    	var activeTab = <?php echo (json_encode("$active_tab")); ?>;

   			var nMultiSet = <?php echo (json_encode("$n_multi_set")); ?> ;//Null in php is different from javascript NULL

   			var autoInsertEnabled = <?php echo (json_encode(YASR_AUTO_INSERT_ENABLED)); ?>;

			var textBeforeStars = <?php echo (json_encode(YASR_TEXT_BEFORE_STARS)); ?>;

   			var nonceHideAskRating = <?php echo (json_encode("$ajax_nonce_hide_ask_rating")); ?>

		   	YasrSettingsPage(activeTab, nMultiSet, autoInsertEnabled, textBeforeStars);

		   	YasrAsk5Stars(nonceHideAskRating);

	    }); //End jquery document ready

	</script>
