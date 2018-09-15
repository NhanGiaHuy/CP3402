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

require (YASR_ABSOLUTE_PATH . '/lib/yasr-stats-functions.php');

$ajax_nonce_hide_ask_rating = wp_create_nonce( "yasr_nonce_hide_ask_rating" );

yasr_include_fb_sdk ();


?>

<div class="wrap">

	<h2>Yet Another Stars Rating: <?php _e("Logs", 'yet-another-stars-rating'); ?></h2>

	<?php

	if (isset($_GET['tab'])) {
		$active_tab = $_GET[ 'tab' ];
	}

	else {
		$active_tab = 'logs';
	}

	//Do the settings tab
	yasr_stats_tabs($active_tab);

	if ($active_tab == 'logs' || $active_tab == '') {

		?>

        <div class="yasr-settingsdiv">
            <form action="#" id="" method="POST">
            <?php
                wp_nonce_field( 'yasr-delete-stats-logs', 'yasr-nonce-delete-stats-logs' );
                $yasr_stats_log_table = new YASR_Stats_Log_List_Table();
                $yasr_stats_log_table->prepare_items();
                $yasr_stats_log_table->display();
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

	do_action( 'yasr_settings_check_active_tab', $active_tab );

	yasr_upgrade_pro_box("bottom");
	yasr_fb_box("bottom");
	yasr_ask_rating ("bottom");

	?>

	<!--End div wrap-->
</div>


<script type="text/javascript">

    jQuery( document ).ready(function() {

        var nonceHideAskRating = <?php echo (json_encode("$ajax_nonce_hide_ask_rating")); ?>

        YasrAsk5Stars(nonceHideAskRating);

    }); //End jquery document ready

</script>
