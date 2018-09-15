<?php

if ( ! defined( 'ABSPATH' ) ) exit('You\'re not allowed to see this page'); // Exit if accessed directly


//Delete caches for wp_super_Cache and wp_rocket
add_action('yasr_action_on_visitor_vote', 'yasr_delete_cache' );
add_action('yasr_action_on_update_visitor_vote', 'yasr_delete_cache');


	function yasr_delete_cache($post_id) {

		yasr_wp_super_cache_support($post_id);

        yasr_wp_rocket_support($post_id);

        yasr_litespeed_cache_support($post_id);

	}


//
add_filter('yasr_filter_style_options', 'yasr_filter_style_options_callback');

	function yasr_filter_style_options_callback ($style_options) {

		if(!array_key_exists('stars_set_free', $style_options)) {

			$style_options['stars_set_free'] = 'flat'; //..default value if not exists

		}

		return $style_options;

	}



//Add stars set for yasr style settings page
//from version 1.2.7
add_action('yasr_style_options_add_settings_field', 'yasr_style_options_add_settings_field_callback');

	function yasr_style_options_add_settings_field_callback($style_options) {

		add_settings_field( 'yasr_style_options_choose_stars_lite', __('Choose Stars Set', 'yet-another-stars-rating'), 'yasr_style_options_choose_stars_lite_callback', 'yasr_style_tab', 'yasr_style_options_section_id', $style_options );

	}

	function yasr_style_options_choose_stars_lite_callback($style_options) {

		?>

			<div class='yasr_choose_stars' id='yasr_pro_custom_set_choosen_stars'>

				<input type='radio' name='yasr_style_options[stars_set_free]' value='oxy' class='yasr-general-options-scheme-color' <?php if ($style_options['stars_set_free']==='oxy') echo " checked=\"checked\" "; ?>  />
				<br />
				<div class='yasr_pro_stars_set' id='yasr_pro_custom_set_choosen_stars'>
					<?php
						echo "<img src=" . YASR_IMG_DIR . "stars32.png>";
					?>
				</div>

			</div>

			<div class='yasr_choose_stars' id='yasr_pro_custom_set_choosen_stars'>

				<input type='radio' name='yasr_style_options[stars_set_free]' value='flat' class='yasr-general-options-scheme-color' <?php if ($style_options['stars_set_free']==='flat') echo " checked=\"checked\" "; ?>  />
				<br />
				<div class='yasr_pro_stars_set' id='yasr_pro_custom_set_choosen_stars'>
					<?php
						echo "<img src=" . YASR_IMG_DIR . "stars_32_flat.png>";
					?>
				</div>

			</div>



			<div id="yasr-settings-stylish-stars">


				<div id="yasr-settings-stylish-image-container">
				
					<?php
			
						echo "<img id=\"yasr-settings-stylish-image\" src=" . YASR_IMG_DIR . "yasr-stylish-stars.png>";
				
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

		        jQuery('#yasr-settings-stylish-stars').mouseover(function() {
				    jQuery('#yasr-settings-stylish-text').css("visibility","visible");
				    jQuery('#yasr-settings-stylish-image').css("opacity", 0.4);
				});

	        </script>


		<?php

	}

?>
