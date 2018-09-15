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

function yasr_settings_tabs($active_tab) {

    ?>

    <h2 class="nav-tab-wrapper yasr-no-underline">

        <a href="?page=yasr_settings_page&tab=general_settings" class="nav-tab <?php if ($active_tab == 'general_settings') echo 'nav-tab-active'; ?>" > <?php _e("General Settings", 'yet-another-stars-rating'); ?> </a>
        <a href="?page=yasr_settings_page&tab=manage_multi" class="nav-tab <?php if ($active_tab == 'manage_multi') echo 'nav-tab-active'; ?>" > <?php _e("Multi Sets", 'yet-another-stars-rating'); ?> </a>
        <a href="?page=yasr_settings_page&tab=style_options" class="nav-tab <?php if ($active_tab == 'style_options') echo 'nav-tab-active'; ?>" > <?php _e("Aspect & Styles", 'yet-another-stars-rating'); ?> </a>
        <?php do_action( 'yasr_add_settings_tab', $active_tab ); ?>



    </h2>

    <?php
}

/************ Add yasr general options ***********/

	add_action( 'admin_init', 'yasr_general_options_init' ); //This is for general options

		function yasr_general_options_init() {

	    	register_setting(
	        	'yasr_general_options_group', // A settings group name. Must exist prior to the register_setting call. This must match the group name in settings_fields()
	        	'yasr_general_options', //The name of an option to sanitize and save.
	        	'yasr_general_options_sanitize'
	    	);

	    	$option = get_option( 'yasr_general_options' );

	    	//This is to avoid undefined offset
	    	if ($option && $option['auto_insert_enabled']==0) {
	    		$option['auto_insert_what'] = 'overall_rating';
	    		$option['auto_insert_where'] = 'top';
	    		$option['auto_insert_exclude_pages'] = 'yes';
	    		$option['auto_insert_size'] = 'large';
	    		$option['auto_insert_custom_post_only'] = 'no';
	    	}

	    	//This is to avoid undefined offset
	    	if ($option && $option['text_before_stars']==0) {
	    		$option['text_before_overall'] = '';
	    		$option['text_before_visitor_rating'] = '';
	    		$option['text_after_visitor_rating'] = '';
	    		$option['custom_text_user_voted'] = '';
	    	}

			//if it's not blogposting avoid undefined variable
			if (!isset($option['blogposting_organization_name'])) {

				$option['blogposting_organization_name'] = get_bloginfo('name');

			}

			if (!isset($option['blogposting_organization_logo'])) {

				$option['blogposting_organization_logo'] = get_site_icon_url();

			}

			if(!isset($option['disable_jquery'])) {

			    $option['disable_jquery'] = 'no';

            }


	    	add_settings_section( 'yasr_general_options_section_id', __('General settings', 'yet-another-stars-rating'), 'yasr_section_callback', 'yasr_general_settings_tab' );
	    		add_settings_field( 'yasr_use_auto_insert_id', __('Auto insert options', 'yet-another-stars-rating'), 'yasr_auto_insert_callback', 'yasr_general_settings_tab', 'yasr_general_options_section_id', $option );
	       		add_settings_field( 'yasr_show_overall_in_loop', __('Show "Overall Rating" in Archive Page?', 'yet-another-stars-rating'), 'yasr_show_overall_in_loop_callback', 'yasr_general_settings_tab',  'yasr_general_options_section_id', $option);
	       		add_settings_field( 'yasr_show_visitor_votes_in_loop', __('Show "Visitor Votes" in Archive Page?', 'yet-another-stars-rating'), 'yasr_show_visitor_votes_in_loop_callback', 'yasr_general_settings_tab',  'yasr_general_options_section_id', $option);
	       		add_settings_field( 'yasr_custom_text', __('Insert custom text to show before / after stars', 'yet-another-stars-rating'), 'yasr_custom_text_callback', 'yasr_general_settings_tab',  'yasr_general_options_section_id', $option);
	       		add_settings_field( 'yasr_visitors_stats', __('Do you want show stats for visitors votes?', 'yet-another-stars-rating'), 'yasr_visitors_stats_callback', 'yasr_general_settings_tab',  'yasr_general_options_section_id', $option);
			    add_settings_field( 'yasr_allow_only_logged_in_id', __('Allow only logged in user to vote?', 'yet-another-stars-rating'), 'yasr_allow_only_logged_in_callback', 'yasr_general_settings_tab', 'yasr_general_options_section_id', $option );
			    add_settings_field( 'yasr_enable_ip', __('Do you want to save ip address?', 'yet-another-stars-rating'), 'yasr_enable_ip_callback', 'yasr_general_settings_tab',  'yasr_general_options_section_id', $option);
			    add_settings_field( 'yasr_choose_snippet_id', __('Rich snippet options', 'yet-another-stars-rating'), 'yasr_choose_snippet_callback', 'yasr_general_settings_tab', 'yasr_general_options_section_id', $option );
	       		add_settings_field( 'yasr_choose_overall_rating_method', __('How do you want to rate "Overall Rating"?', 'yet-another-stars-rating'), 'yasr_choose_overall_rating_method_callback', 'yasr_general_settings_tab', 'yasr_general_options_section_id', $option );
	       		add_settings_field( 'yasr_rater', __('Do you want disable jQuery?', 'yet-another-stars-rating'), 'yasr_rater_callback', 'yasr_general_settings_tab', 'yasr_general_options_section_id', $option );

		}


		function yasr_section_callback() {
	    	//_e('Manage auto insert', 'yet-another-stars-rating');
		}

		function yasr_auto_insert_callback($option) {

	    	?>

	    		<strong><?php _e('Use Auto Insert?', 'yet-another-stars-rating'); ?></strong>


				<div class="yasr-onoffswitch-big">
					<input type="checkbox" name="yasr_general_options[auto_insert_enabled]" class="yasr-onoffswitch-checkbox" value="1" id="yasr_auto_insert_switch" <?php if ($option['auto_insert_enabled']==1) echo " checked='checked' "; ?> >
					<label class="yasr-onoffswitch-label" for="yasr_auto_insert_switch">
						<span class="yasr-onoffswitch-inner"></span>
						<span class="yasr-onoffswitch-switch"></span>
					</label>
				</div>

				<p>&nbsp;</p>

	    		<strong><?php _e('What?', 'yet-another-stars-rating'); ?></strong>

	    		<div class="yasr-indented-answer">

		    		<input type="radio" name="yasr_general_options[auto_insert_what]" value="overall_rating" class="yasr-auto-insert-options-class" <?php if ($option['auto_insert_what']==='overall_rating') echo " checked=\"checked\" "; ?> >
			    		<?php _e('Overall Rating / Author Rating', 'yet-another-stars-rating') ?>
			   			<br />

			    	<input type="radio" name="yasr_general_options[auto_insert_what]" value="visitor_rating" class="yasr-auto-insert-options-class" <?php if ($option['auto_insert_what']==='visitor_rating') echo " checked=\"checked\" "; ?> >
			    		<?php _e('Visitor Votes', 'yet-another-stars-rating')?>
			   			<br />

			    	<input type="radio" name="yasr_general_options[auto_insert_what]" value="both" class="yasr-auto-insert-options-class" <?php if ($option['auto_insert_what']==='both') echo " checked=\"checked\" "; ?> >
			    		<?php _e('Both', 'yet-another-stars-rating')?>

			    	<p>&nbsp;</p>

			    </div>

		    	<strong><?php _e('Where?', 'yet-another-stars-rating'); ?></strong>

		    	<div class="yasr-indented-answer">

			    	<input type="radio" name="yasr_general_options[auto_insert_where]" value="top" class="yasr-auto-insert-options-class" <?php if ($option['auto_insert_where']==='top' ) echo " checked=\"checked\" ";  ?> >
						<?php _e('Before the post', 'yet-another-stars-rating')?>
						<br />

			    	<input type="radio" name="yasr_general_options[auto_insert_where]" value="bottom" class="yasr-auto-insert-options-class" <?php if ($option['auto_insert_where']==='bottom') echo " checked=\"checked\" "; ?> >
			    		<?php _e('After the post', 'yet-another-stars-rating')?>
			    		<br />

		    		<p>&nbsp;</p>

		    	</div>

		    	<strong><?php _e('Size', 'yet-another-stars-rating'); ?></strong>

		    	<div class="yasr-indented-answer">

			    	<div class="yasr-option-size">
				    	<input type="radio" name="yasr_general_options[auto_insert_size]" value="small" class="yasr-auto-insert-options-class" <?php if ($option['auto_insert_size']==='small' ) echo " checked=\"checked\" ";  ?> >
							<img src="<?php echo  YASR_IMG_DIR . "yasr-stars-small.png" ?>" class="yasr-img-option-size"><span class="yasr-text-options-size"><?php _e('Small', 'yet-another-stars-rating')?></span>
					</div>

					<div class="yasr-option-size">
			    	<input type="radio" name="yasr_general_options[auto_insert_size]" value="medium" class="yasr-auto-insert-options-class" <?php if ($option['auto_insert_size']==='medium' ) echo " checked=\"checked\" ";  ?> >
						<img src="<?php echo  YASR_IMG_DIR . "yasr-stars-medium.png" ?>" class="yasr-img-option-size"><span class="yasr-text-options-size"><?php _e('Medium', 'yet-another-stars-rating')?></span>
					</div>

			    	<div class="yasr-option-size">
					<input type="radio" name="yasr_general_options[auto_insert_size]" value="large" class="yasr-auto-insert-options-class" <?php if ($option['auto_insert_size']==='large' ) echo " checked=\"checked\" ";  ?> >
						<img src="<?php echo  YASR_IMG_DIR . "yasr-stars-large.png" ?>" class="yasr-img-option-size"><span class="yasr-text-options-size"><?php _e('Large', 'yet-another-stars-rating')?></span>
					</div>

			    	<p>&nbsp;</p>

			    </div>

		    	<strong><?php _e('Exclude Pages?', 'yet-another-stars-rating'); ?></strong>

		    	<div class="yasr-indented-answer">
			    	<input type="radio" name="yasr_general_options[auto_insert_exclude_pages]" value="yes" class="yasr-auto-insert-options-class" <?php if ($option['auto_insert_exclude_pages']==='yes' ) echo " checked=\"checked\" ";  ?> >
						<?php _e('Yes', 'yet-another-stars-rating'); ?>

					&nbsp;&nbsp;&nbsp;

			    	<input type="radio" name="yasr_general_options[auto_insert_exclude_pages]" value="no" class="yasr-auto-insert-options-class" <?php if ($option['auto_insert_exclude_pages']==='no') echo " checked=\"checked\" "; ?> >
			    		<?php _e('No', 'yet-another-stars-rating'); ?>
			    		<br />

			    	<p>&nbsp;</p>

			    </div>

		    	<?php

		    	$custom_post_types = yasr_get_custom_post_type('bool');

		    	if ($custom_post_types) { ?>
		    		<strong><?php _e('Use only in custom post types?', 'yet-another-stars-rating'); ?></strong>
		    		<div class="yasr-indented-answer">
			    		<input type="radio" name="yasr_general_options[auto_insert_custom_post_only]" value="yes" class="yasr-auto-insert-options-class" <?php if ($option['auto_insert_custom_post_only']==='yes' ) echo " checked=\"checked\" ";  ?> >
						<?php _e('Yes', 'yet-another-stars-rating'); ?>

						&nbsp;&nbsp;&nbsp;

			    		<input type="radio" name="yasr_general_options[auto_insert_custom_post_only]" value="no" class="yasr-auto-insert-options-class" <?php if ($option['auto_insert_custom_post_only']==='no') echo " checked=\"checked\" "; ?> >
			    		<?php _e('No', 'yet-another-stars-rating'); ?>

			    		<p>

			    		<?php _e("You see this because you use custom post types.", 'yet-another-stars-rating'); ?>
			    		<br/>
			    		<?php _e("If you want to use auto insert only in custom post types, choose Yes", 'yet-another-stars-rating'); ?>

			    		<p>&nbsp;</p>

			    	</div>

		    		<?php
		    	}

		    	else {

		    		?>

		    		<input type="hidden" name="yasr_general_options[auto_insert_custom_post_only]" value="no" ?>

		    		<?php

		    	}

		    	?>


	    	<?php

			submit_button( __('Save Settings') );


		} //End yasr_auto_insert_callback


	    function yasr_show_overall_in_loop_callback($option) {

	    	?>

			<div class="yasr-onoffswitch-big">
				<input type="checkbox" name="yasr_general_options[show_overall_in_loop]" class="yasr-onoffswitch-checkbox" id="yasr-show-overall-in-loop-switch" <?php if ($option['show_overall_in_loop']==='enabled') echo " checked='checked' "; ?> >
				<label class="yasr-onoffswitch-label" for="yasr-show-overall-in-loop-switch">
					<span class="yasr-onoffswitch-inner"></span>
					<span class="yasr-onoffswitch-switch"></span>
				</label>
			</div>

			<br />
			<br />

			<?php _e('If you enable this, "Overall Rating" will be showed not only in the single article or page, but also in pages like Home Page, category pages or archives.', 'yet-another-stars-rating')?>

			<p>&nbsp;</p>

			<br />

			<hr>

	    	<?php

	    }

	    function yasr_show_visitor_votes_in_loop_callback ($option) {

			?>

			<div class="yasr-onoffswitch-big">
				<input type="checkbox" name="yasr_general_options[show_visitor_votes_in_loop]" class="yasr-onoffswitch-checkbox" id="yasr-show-visitor-votes-in-loop-switch" <?php if ($option['show_visitor_votes_in_loop']==='enabled') echo " checked='checked' "; ?> >
				<label class="yasr-onoffswitch-label" for="yasr-show-visitor-votes-in-loop-switch">
					<span class="yasr-onoffswitch-inner"></span>
					<span class="yasr-onoffswitch-switch"></span>
				</label>
			</div>

			<br />
			<br />

			<?php _e('If you enable this, "Visitor Votes" will be showed not only in the single article or page, but also in pages like Home Page, category pages or archives.', 'yet-another-stars-rating')?>

			<p>&nbsp;</p>

			<br />

			<hr>

	    	<?php

	    }

	    function yasr_custom_text_callback($option) {

	    	$text_before_overall = htmlspecialchars("$option[text_before_overall]");

	    	$text_before_visitor_rating = htmlspecialchars("$option[text_before_visitor_rating]");

	    	$text_after_visitor_rating = htmlspecialchars("$option[text_after_visitor_rating]");

	    	$custom_text_user_votes = htmlentities("$option[custom_text_user_voted]");

	    	?>

			<div class="yasr-onoffswitch-big">
				<input type="checkbox" name="yasr_general_options[text_before_stars]" class="yasr-onoffswitch-checkbox" id="yasr-general-options-text-before-stars-switch" <?php if ($option['text_before_stars']==1) echo " checked='checked' "; ?> >
				<label class="yasr-onoffswitch-label" for="yasr-general-options-text-before-stars-switch">
					<span class="yasr-onoffswitch-inner"></span>
					<span class="yasr-onoffswitch-switch"></span>
				</label>
			</div>

	    	<br /> <br />

	    	<input type='text' name='yasr_general_options[text_before_overall]' id="yasr-general-options-custom-text-before-overall" class='yasr-general-options-text-before' <?php printf('value="%s"', $text_before_overall); ?> maxlength="40"/>
			<?php _e('Custom text to display before Overall Rating', 'yet-another-stars-rating')?>

			<br /> <br /> <br />

			<input type='text' name='yasr_general_options[text_before_visitor_rating]' id="yasr-general-options-custom-text-before-visitor" class='yasr-general-options-text-before' <?php printf('value="%s"', $text_before_visitor_rating); ?> maxlength="80"/>
			<?php _e('Custom text to display BEFORE Visitor Rating', 'yet-another-stars-rating')?>

			<br /> <br />


			<input type='text' name='yasr_general_options[text_after_visitor_rating]' id="yasr-general-options-custom-text-after-visitor" class='yasr-general-options-text-before' <?php printf('value="%s"', $text_after_visitor_rating); ?> maxlength="80"/>
			<?php _e('Custom text to display AFTER Visitor Rating', 'yet-another-stars-rating')?>

			<br /> <br /> <br />

			<input type='text' name='yasr_general_options[custom_text_user_voted]' id="yasr-general-options-custom-text-already-rated" class='yasr-general-options-text-before' <?php printf('value="%s"', $custom_text_user_votes); ?> maxlength="60"/>
			<?php _e('Custom text to display when a non logged user has already rated', 'yet-another-stars-rating')?>


			<br /> <br />

			<a href="#" id="yasr-doc-custom-text-link"><?php _e('Help', 'yet-another-stars-rating'); ?></a>

			<div id="yasr-doc-custom-text-div" class="yasr-help-box-settings">

				<?php _e('In the first field you can use %overall_rating% pattern to show the overall rating.', 'yet-another-stars-rating');?>

				<br /> <br />

				<?php _e('In the Second and Third fields you can use %total_count% pattern to show the total count, and %average% pattern to show the average', 'yet-another-stars-rating');?>

			</div>

			<p>&nbsp;</p>

			<?php

			submit_button( __('Save Settings') );

	    }

	    function yasr_visitors_stats_callback($option) {

	    	?>

			<div class="yasr-onoffswitch-big">
				<input type="checkbox" name="yasr_general_options[visitors_stats]" class="yasr-onoffswitch-checkbox" id="yasr-general-options-visitors-stats-switch" <?php if ($option['visitors_stats']==='yes') echo " checked='checked' "; ?> >
				<label class="yasr-onoffswitch-label" for="yasr-general-options-visitors-stats-switch">
					<span class="yasr-onoffswitch-inner"></span>
					<span class="yasr-onoffswitch-switch"></span>
				</label>
			</div>

			<br />

			<br />

			<p>&nbsp;</p>

			<hr>

	    	<?php

	    }

	    function yasr_allow_only_logged_in_callback($option) {

	    	?>

			<input type='radio' name='yasr_general_options[allowed_user]' value='logged_only' class='yasr_auto_insert_loggedonly' <?php if ($option['allowed_user']==='logged_only') echo " checked=\"checked\" "; ?>  />
				<?php _e('Allow only logged-in users', 'yet-another-stars-rating')?>
				<br />

			<input type='radio' name='yasr_general_options[allowed_user]' value='allow_anonymous' class='yasr_auto_insert_loggedonly' <?php if ($option['allowed_user']==='allow_anonymous') echo " checked=\"checked\" "; ?>  />
				<?php _e('Allow everybody (logged in and anonymous)', 'yet-another-stars-rating')?>
				<br />

			<p>&nbsp;</p>

			<hr>

			<?php

		} //End function

        function yasr_enable_ip_callback($option) {

	        ?>


            <div class="yasr-onoffswitch-big">
                <input type="checkbox" name="yasr_general_options[enable_ip]" class="yasr-onoffswitch-checkbox" id="yasr-general-options-enable-ip-switch" <?php if ($option['enable_ip']==='yes') echo " checked='checked' "; ?> >
                <label class="yasr-onoffswitch-label" for="yasr-general-options-enable-ip-switch">
                    <span class="yasr-onoffswitch-inner"></span>
                    <span class="yasr-onoffswitch-switch"></span>
                </label>
            </div>

            <br />

	        <?php

	            $string = sprintf(__('Please note that to comply with the %s EU law, you must warn your users that you\'re storing their ip', 'yet-another-stars-rating'), '<a href="https://en.wikipedia.org/wiki/General_Data_Protection_Regulation">GDPR</a>');

	            echo $string;

	        ?>

            <br />

            <hr>

	        <?php

        } //End function

	    function yasr_choose_snippet_callback($option) {

			$blogposting_organization_name = htmlspecialchars("$option[blogposting_organization_name]");

			$blogposting_organization_logo = htmlspecialchars("$option[blogposting_organization_logo]");

			?>

				<strong><?php _e('Which rich snippet do you want to use?', 'yet-another-stars-rating'); ?></strong>

				<div class="yasr-indented-answer">
			    	<input type="radio" name="yasr_general_options[snippet]" value="overall_rating" class="yasr_choose_snippet" <?php if ($option['snippet']==='overall_rating') echo " checked=\"checked\" "; ?> >
			    		<?php _e('Review Rating', 'yet-another-stars-rating') ?>
			   			<br />

			    	<input type="radio" name="yasr_general_options[snippet]" value="visitor_rating" class="yasr_choose_snippet" <?php if ($option['snippet']==='visitor_rating') echo " checked=\"checked\" "; ?> >
			    		<?php _e('Aggregate Rating', 'yet-another-stars-rating')?>
			   			<br />

			   		<p>&nbsp;</p>

			   		<a href="#" id="yasr-snippet-explained-link"><?php _e("What is this?", 'yet-another-stars-rating') ?></a>

			   		<div id="yasr-snippet-explained" class="yasr-help-box-settings">
			   			<?php

			   				_e("If you select \"Review Rating\", your site will be indexed from search engines like this: ", 'yet-another-stars-rating');
			   				echo "<br /><br /><img src=" . YASR_IMG_DIR . "yasr_review.png>";

			   				echo "<br /> <br />";

			   				_e("If, instead, you choose \"Aggregate Rating\", your site will be indexed like this", 'yet-another-stars-rating');
			   				echo "<br /><br /><img src=" . YASR_IMG_DIR . "yasr_aggregate.jpg>";
			   			 ?>
			   		</div>

			   	</div>

		   		<br />

		   		<br />

				<strong><?php _e('Select default item type for all post or pages', 'yet-another-stars-rating'); ?></strong>

		        <div class="yasr-indented-answer" id="yasr-choose-snippet-type">

			        <input type="radio" name="yasr_general_options[snippet_itemtype]" value="Product" class="yasr_choose_snippet" <?php if ($option['snippet_itemtype']==='Product') echo " checked=\"checked\" "; ?> >
			    		<?php _e('Product', 'yet-another-stars-rating') ?>
			   			<br />

			    	<input type="radio" name="yasr_general_options[snippet_itemtype]" value="Place" class="yasr_choose_snippet" <?php if ($option['snippet_itemtype']==='Place') echo " checked=\"checked\" "; ?> >
			    		<?php _e('Place', 'yet-another-stars-rating')?>
			   			<br />

			   		<input type="radio" name="yasr_general_options[snippet_itemtype]" value="Recipe" class="yasr_choose_snippet" <?php if ($option['snippet_itemtype']==='Recipe') echo " checked=\"checked\" "; ?> >
			    		<?php _e('Recipe', 'yet-another-stars-rating')?>
			   			<br />

			   		<input type="radio" name="yasr_general_options[snippet_itemtype]" value="Other" class="yasr_choose_snippet" id="yasr_choose_snippet_blogposting" <?php if ($option['snippet_itemtype']==='Other') echo " checked=\"checked\" "; ?> >
			    		<?php _e('BlogPosting')?>
			   			<br />


						<div class="yasr-help-box-settings" id="yasr-blogPosting-additional-info" style="display:block">

							<?php _e("If you need to use BlogPosting as item type, even for just one post or page, please fill these forms", "yet-another-stars-rating"); ?>

							<br /> <br />

							<input type='text' name='yasr_general_options[blogposting_organization_name]' id="yasr-general-options-blogposting-organization-name" class="yasr-blogPosting-additional-info-inputs" <?php printf('value="%s"', $blogposting_organization_name); ?> maxlength="180"/>
							<?php _e('Publisher name (e.g. Google)', 'yet-another-stars-rating')?>

							<br /> <br />

							<input type='text' name='yasr_general_options[blogposting_organization_logo]' id="yasr-general-options-blogposting-organization-logo" class="yasr-blogPosting-additional-info-inputs" <?php printf('value="%s"', $blogposting_organization_logo); ?> maxlength="300"/>
							<?php _e('Logo Url (if empty siteicon will be used instead)', 'yet-another-stars-rating')?>

						</div>


			        <br />

			        <small><?php _e('You can always change it in the single post or page.', 'yet-another-stars-rating'); ?></small>
			        <br />
			        <small><?php _e('This will affect only the post/page where you didn\'t change manually the itemtype yet.', 'yet-another-stars-rating'); ?> </small>

			        <p>&nbsp;</p>

			    </div>


		   		<p>&nbsp;</p>

		   		<hr>

			<?php

		} //End function yasr_choose_snippet_callback

		function yasr_choose_overall_rating_method_callback($option) {

			?>

			<input type="radio" name="yasr_general_options[metabox_overall_rating]" value="stars" class="yasr_choose_overall_rating_method" <?php if ($option['metabox_overall_rating']==='stars') echo " checked=\"checked\" "; ?> >
		    		<?php _e('Stars', 'yet-another-stars-rating') ?>
		   			<br />

	    	<input type="radio" name="yasr_general_options[metabox_overall_rating]" value="numbers" class="yasr_choose_overall_rating_method" <?php if ($option['metabox_overall_rating']==='numbers') echo " checked=\"checked\" "; ?> >
	    		<?php _e('Numbers', 'yet-another-stars-rating')?>
	   			<br />

            <p>&nbsp;</p>

            <hr>

		    <?php

		}

        function yasr_rater_callback($option) {

		    ?>

		    <div class="yasr-onoffswitch-big">
                <input type="checkbox" name="yasr_general_options[disable_jquery]" class="yasr-onoffswitch-checkbox" id="yasr-general-options-disable-query" <?php if ($option['disable_jquery']==='yes') echo " checked='checked' "; ?> >
                <label class="yasr-onoffswitch-label" for="yasr-general-options-disable-query">
                    <span class="yasr-onoffswitch-inner"></span>
                    <span class="yasr-onoffswitch-switch"></span>
                </label>
            </div>

            <br />

	        <?php

	            $string = sprintf(__('If you disable jQuery, YASR will work on vanilla javascript. %s
	            Please note this feature is still in beta. %s
	            For now, this work only with yasr_overall_rating and yasr_visitor_votes shortcodes. %s
	            You will not able to change star set colors %s
	            Please report any bug %s', 'yet-another-stars-rating'),
                    '<br />', '<br />', '<br />', '<br />', '<a href="https://wordpress.org/plugins/yet-another-stars-rating/">here</a>');

	            echo $string;

        }


		function yasr_general_options_sanitize($option) {

			//Array to return
			$output = array();


			// Loop through each of the incoming options
		    foreach( $option as $key => $value ) {

		        // Check to see if the current option has a value. If so, process it.
		        if( isset( $option[$key] ) ) {

		            // Strip all HTML and PHP tags and properly handle quoted strings
		            $output[$key] = strip_tags( stripslashes( $option[ $key ] ) );

					if ($key=='blogposting_organization_logo') {

						//if is not a valid url get_site_icon_url instead
						if (filter_var($value, FILTER_VALIDATE_URL) === FALSE) {
							$output[$key] = get_site_icon_url();
						}

					}

		        } // end if

    		} // end foreach

			//if in array doesn't exists [auto_insert_enabled] key, create it and set to 0
			if(!array_key_exists('auto_insert_enabled', $output)) {

				$output['auto_insert_enabled'] = 0;

			}
			//if exists value must be 1
			else {

				$output['auto_insert_enabled'] = 1;

			}


			//Same as above but for [show_overall_in_loop] key
			if(!array_key_exists('show_overall_in_loop', $output)) {

				$output['show_overall_in_loop'] = 'disabled';

			}
			//if exists must be string 'enabled'
			else {

				$output['show_overall_in_loop'] = 'enabled';

			}


			//Same as above but for [show_visitor_votes_in_loop] key
			if(!array_key_exists('show_visitor_votes_in_loop', $output)) {

				$output['show_visitor_votes_in_loop'] = 'disabled';

			}
			//if exists must be string 'enabled'
			else {

				$output['show_visitor_votes_in_loop'] = 'enabled';

			}

			//Same as above but for text_before_stars key
			if(!array_key_exists('text_before_stars', $output)) {

				$output['text_before_stars'] = 0;

			}
			//if exists must be 1
			else {

				$output['text_before_stars'] = 1;

			}


			//Same as above but for visitors_stats key
			if(!array_key_exists('visitors_stats', $output)) {

				$output['visitors_stats'] = 'no';

			}
			//if exists must be string 'yes'
			else {

				$output['visitors_stats'] = 'yes';

			}

			//Same as above but for enable_ip key
			if(!array_key_exists('enable_ip', $output)) {

				$output['enable_ip'] = 'no';

			}
			//if exists must be string 'yes'
			else {

				$output['enable_ip'] = 'yes';

			}

			//Same as above but for enable_ip key
			if(!array_key_exists('disable_jquery', $output)) {

				$output['disable_jquery'] = 'no';

			}
			//if exists must be string 'yes'
			else {

				$output['disable_jquery'] = 'yes';

			}

		    return $output;

		}


/************ End Yasr General Settings ************/



/**************** Add yasr multiset options and settings ************/

add_action( 'admin_init', 'yasr_multiset_options_init' ); //This is for general options

	function yasr_multiset_options_init() {
    	register_setting(
        	'yasr_multiset_options_group', // A settings group name. Must exist prior to the register_setting call. This must match the group name in settings_fields()
        	'yasr_multiset_options', //The name of an option to sanitize and save.
			'yasr_sanitize_multiset_options'
    	);

    	$option_multiset = get_option( 'yasr_multiset_options' );

		if ($option_multiset === FALSE) {

			$option_multiset = array();

		}

		if (!isset($option_multiset['show_average'])) {

			$option_multiset['show_average'] = 'yes';

		}

    	add_settings_section( 'yasr_multiset_options_section_id', '', 'yasr_multiset_section_callback', 'yasr_multiset_tab' );
			add_settings_field( 'yasr_multiset_hide_average_id', __('Show average?', 'yet-another-stars-rating'), 'yasr_multiset_hide_average_callback', 'yasr_multiset_tab', 'yasr_multiset_options_section_id', $option_multiset );

	}

	function yasr_multiset_section_callback () {

		//Silence

	}

	function yasr_multiset_hide_average_callback ($option_multiset) {

		?>

		<div class="yasr-onoffswitch-big">
			<input type="checkbox" name="yasr_multiset_options[show_average]" class="yasr-onoffswitch-checkbox" id="yasr-multiset-options-show-average-switch" <?php if ($option_multiset['show_average']==='yes') echo " checked='checked' "; ?> >
			<label class="yasr-onoffswitch-label" for="yasr-multiset-options-show-average-switch">
				<span class="yasr-onoffswitch-inner"></span>
				<span class="yasr-onoffswitch-switch"></span>
			</label>
		</div>

			<br />

			<br />

		<?php

		_e("If you select no, the \"Average\" row will not be displayed. You can override this in the single multi set by using the parameter \"show_average\"", "yasr");

	}


	function yasr_sanitize_multiset_options ($option_multiset) {

		if (!array_key_exists('show_average', $option_multiset)) {

			$option_multiset['show_average'] = 'no';

		}

		else {

			$option_multiset['show_average'] = 'yes';

		}

		return $option_multiset;

	}


	/****** Create a form for settings page to create new multi set ******/
	function yasr_display_multi_set_form() {

		?>

		<h4 class="yasr-multi-set-form-headers"><?php _e("Add New Multiple Set", 'yet-another-stars-rating'); ?></h4>
		<em><?php _e('Name, Element#1 and Element#2 MUST be filled and must be long at least 3 characters', 'yet-another-stars-rating') ?></em>
		<p>
		<form action="<?php echo admin_url('options-general.php?page=yasr_settings_page&tab=manage_multi') ?>" id="form_add_multi_set" method="post">
			<strong><?php _e("Name", 'yet-another-stars-rating')?></strong>
			<input type="text" name="multi-set-name" id="new-multi-set-name" class="input-text-multi-set">
			<!--input type="hidden" name="action" value="yasr_new_multi_set_form" />-->

			<p></p>
			<?php _e("You can insert up to nine elements", 'yet-another-stars-rating') ?>
			<br />

			<?php for($i=1; $i<=9; $i++) {

				echo "<strong>" . __("Element ", 'yet-another-stars-rating') . "#$i" . "</strong>";
				?>
				<input type="text" name="multi-set-name-element-<?php echo $i ?>" id="multi-set-name-element-<?php echo $i ?>" class="input-text-multi-set">
				<br />

			<?php } //End foreach

			wp_nonce_field( 'add-multi-set', 'add-nonce-new-multi-set' ) ?><!-- a little security to process on submission -->

	       	<br />
			<input type="submit" value="<?php _e("Create New Set", 'yet-another-stars-rating') ?>" class="button-primary"/>
		</form>

		<?php
	} //End function



	function yasr_edit_multi_form() {

		global $wpdb;

		$multi_set=yasr_get_multi_set();

		$n_multi_set = $wpdb->num_rows; //wpdb->num_rows always store the last of the last query

		if ($n_multi_set > 1) {
			?>

			<div class="yasr-manage-multiset">

				<h4 class="yasr-multi-set-form-headers"><?php _e("Manage Multiple Set" , 'yet-another-stars-rating'); ?></h4>

				<?php _e('Wich set do you want to edit or remove?', 'yet-another-stars-rating')?>

				<select id ="yasr_select_edit_set">
    				<?php foreach ($multi_set as $name) { ?>
		    		<option value="<?php echo $name->set_id ?>"><?php echo $name->set_name ?></option>
	  				<?php } //End foreach ?>
  				</select>

  				<button href="#" class="button-delete" id="yasr-button-select-set-edit-form"><?php _e("Select"); ?></button>


			</div>

				<?php

		} //End if n_multi_set >1

		elseif ($n_multi_set == 1) {

			$set_name=$wpdb->get_results("SELECT field_name AS name, field_id AS id, parent_set_id AS set_id
			                            FROM " . YASR_MULTI_SET_FIELDS_TABLE . "
			                            ORDER BY field_id ASC");

			foreach ($multi_set as $find_set_id) {
				$set_type = $find_set_id->set_id;
			}

			?>

				<div class="yasr-manage-multiset-single">

					<h4 class="yasr-multi-set-form-headers"><?php _e("Manage Multiple Set", 'yet-another-stars-rating'); ?></h4>

					<form action=" <?php echo admin_url('options-general.php?page=yasr_settings_page&tab=manage_multi') ?>" id="form_edit_multi_set" method="post">

		        		<input type="hidden" name="yasr_edit_multi_set_form" value="<?php echo $set_type ?>" />

						<table id="yasr-table-form-edit-multi-set">
		                <tr>

		                    <td id="yasr-table-form-edit-multi-set-header">
		                         <?php _e('Field name', 'yet-another-stars-rating') ?>
		                    </td>

		                     <td id="yasr-table-form-edit-multi-set-remove">
		                        <?php _e('Remove', 'yet-another-stars-rating') ?>
		                     </td>

		                </tr>

						<?php

		       			$i=1;

	        			foreach ($set_name as $name) {

			                echo "
			                <tr>

			                    <td width=\"80%\">
			                        Element #$i <input type=\"text\" value=\"$name->name\" name=\"edit-multi-set-element-$i\">
			                        <input type=\"hidden\" value=\"$name->id\" name=\"db-id-for-element-$i\">
			                    </td>

			                    <td width=\"20%\" style=\"text-align:center\">
			                        <input type=\"checkbox\" value=\"$name->id\" name=\"remove-element-$i\">
			                    </td>

		                	</tr>
		                	";

		                    $i++;

		            	}


			            $i = $i-1; //This is the number of the fields

			            echo "

			            <input type=\"hidden\" name=\"yasr-edit-form-number-elements\" id=\"yasr-edit-form-number-elements\" value=\"$i\">

			            </table>

			            <table width=\"100%\" class=\"yasr-edit-form-remove-entire-set\">
			            <tr>

			                <td width=\"80%\">" . __("Remove whole set?", 'yet-another-stars-rating') . "</td>

			                <td width=\"20%\" style=\"text-align:center\">
			                    <input type=\"checkbox\" name=\"yasr-remove-multi-set\" value=\"$set_type\">
			                </td>

			            </tr>

			            </table>

			            ";

			            echo "<p>";
			                _e("If you remove something you will remove all the votes for that set or field. This operation CAN'T BE undone." , 'yet-another-stars-rating');
			            echo "</p>";

			            wp_nonce_field( 'edit-multi-set', 'add-nonce-edit-multi-set' )

			            ?>

			            <div id="yasr-element-limit" style="display:none; color:red"><?php _e("You can use up to 9 elements" , 'yet-another-stars-rating') ?></div>

			            <input type="button" class="button-delete" id="yasr-add-field-edit-multiset" value="<?php _e('Add element', 'yet-another-stars-rating'); ?>">

			            <input type="submit" value="<?php _e('Save changes', 'yet-another-stars-rating') ?>" class="button-primary" >

					</form>

				</div>

			<?php
		}

		else {
			_e("No Multiple Set were found", 'yet-another-stars-rating');
		}

	}//End function



	/****** Get and output multiple set in a form and table, used in settings page ******/

    add_action( 'wp_ajax_yasr_get_multi_set', 'yasr_get_multi_set_callback' );

    function yasr_get_multi_set_callback() {
        if (isset($_POST['set_id']) && $_POST['set_id'] != '' ) {
            $set_type = $_POST['set_id'];
        }
        else {
            exit ();
        }

        global $wpdb;

        $set_name=$wpdb->get_results($wpdb->prepare("SELECT field_name AS name, field_id AS id
                            FROM " . YASR_MULTI_SET_FIELDS_TABLE . "
                            WHERE parent_set_id=%d
                            ORDER BY field_id ASC", $set_type));



        ?>

        <form action=" <?php echo admin_url('options-general.php?page=yasr_settings_page&tab=manage_multi') ?>" id="form_edit_multi_set" method="post">
        <input type="hidden" name="yasr_edit_multi_set_form" value="<?php echo $set_type ?>" />


            <table id="yasr-table-form-edit-multi-set">
                <tr>

                    <td id="yasr-table-form-edit-multi-set-header">
                         <?php _e('Field name', 'yet-another-stars-rating') ?>
                    </td>

                     <td id="yasr-table-form-edit-multi-set-remove">
                        <?php _e('Remove', 'yet-another-stars-rating') ?>
                     </td>

                </tr>

        <?php

        	$i=1;

            foreach ($set_name as $name) {

                echo "
                <tr>

                    <td width=\"80%\">
                        Element #$i <input type=\"text\" value=\"$name->name\" name=\"edit-multi-set-element-$i\">
                        <input type=\"hidden\" value=\"$name->id\" name=\"db-id-for-element-$i\">
                    </td>

                    <td width=\"20%\" style=\"text-align:center\">
                        <input type=\"checkbox\" value=\"$name->id\" name=\"remove-element-$i\">
                    </td>

                </tr>
                ";

                $i++;

            }


            $i = $i-1; //This is the number of the fields

            echo "

            <input type=\"hidden\" name=\"yasr-edit-form-number-elements\" id=\"yasr-edit-form-number-elements\" value=\"$i\">

            </table>

            <table width=\"100%\" class=\"yasr-edit-form-remove-entire-set\">
            <tr>

                <td width=\"80%\">" . __("Remove whole set?", 'yet-another-stars-rating') . "</td>

                <td width=\"20%\" style=\"text-align:center\">
                    <input type=\"checkbox\" name=\"yasr-remove-multi-set\" value=\"$set_type\">
                </td>

            </tr>

            </table>

            ";

            echo "<p>";
                _e("If you remove something you will remove all the votes for that set or field. This operation CAN'T BE undone." , 'yet-another-stars-rating');
            echo "</p>";

            wp_nonce_field( 'edit-multi-set', 'add-nonce-edit-multi-set' )

            ?>

            <div id="yasr-element-limit" style="display:none; color:red"><?php _e("You can use up to 9 elements" , 'yet-another-stars-rating') ?></div>

            <input type="button" class="button-delete" id="yasr-add-field-edit-multiset" value="<?php _e('Add element', 'yet-another-stars-rating'); ?>">

            <input type="submit" value="<?php _e('Save changes', 'yet-another-stars-rating') ?>" class="button-primary" >

        </form>

        <?php

        die();

    } //End function




	/****** Validate new multi set form ******/
	function yasr_process_new_multi_set_form() {

		if ( isset( $_POST['multi-set-name'])) {

			global $wpdb;

			if ( !current_user_can( 'manage_options' ) ) {
	      		wp_die( 'You are not allowed to be on this page.' );
	   		}

	   		// Check nonce field
	  		check_admin_referer( 'add-multi-set', 'add-nonce-new-multi-set' );

	   		$array_errors = array();
	   		$error = FALSE;

	   		//IF thes fields are not empty go ahed
	  		if ($_POST['multi-set-name']!='' && $_POST['multi-set-name-element-1']!='' && $_POST['multi-set-name-element-2']!=''  ) {

	  			$multi_set_name = ucfirst(strtolower($_POST['multi-set-name']));

	  			$multi_set_name_element_=array();

	  			$multi_set_name_element_[1]=$_POST['multi-set-name-element-1'];
	  			$multi_set_name_element_[2]=$_POST['multi-set-name-element-2'];

	  			//If multi set name is shorter than 3 characher come back
	   			if (mb_strlen($multi_set_name) < 3 || mb_strlen($multi_set_name_element_[1]) <3 || mb_strlen($multi_set_name_element_[2]) < 3 ) {
	   				$array_errors[] = "Content field must be longer than 3 chars";
	   				$error=TRUE;
	   			}


	   			if (mb_strlen($multi_set_name) > 40 || mb_strlen($multi_set_name_element_[1]) > 40 || mb_strlen($multi_set_name_element_[2]) > 40 ) {
	   				$array_errors[] = "Content field must be shorter than 40 chars";
	   				$error=TRUE;
	   			}

	   			//Check if a set with that name already exists
	   			$check_name_exists=$wpdb->get_results("SELECT set_name FROM " . YASR_MULTI_SET_NAME_TABLE . " ORDER BY set_id ASC");

				foreach ($check_name_exists as $set_name) {

	   				if ($multi_set_name==$set_name->set_name) {
	   					$array_errors[] = "You already have a set with this name";
	   					$error=TRUE;
	   				}

	   			}

	   			$element_filled=2;

	   			//If filled get the element from 3 to 9
	  				for($i=3; $i<=9; $i++) {

	  					if (isset($_POST["multi-set-name-element-$i"]) && $_POST["multi-set-name-element-$i"]!='') {

	  						$multi_set_name_element_[$i]=$_POST["multi-set-name-element-$i"];

	  						if (mb_strlen($multi_set_name_element_[$i]) < 3) {
	  							$array_errors[] = "Field # $i must be at least 3 characters";
	   							$error=TRUE;
	  						}

	  						if (mb_strlen($multi_set_name_element_[$i]) > 40) {
	  							$array_errors[] = "Field # $i must be shorter than 40 characters";
	   							$error=TRUE;
	  						}

	  						$element_filled++;
	  					}

	  				}

	  			//If there isnt any error write in the table
	   			if (!$error) {

	   					//get the highest id in table
	                    $highest_id=$wpdb->get_results("SELECT set_id FROM " . YASR_MULTI_SET_NAME_TABLE . " ORDER BY set_id DESC LIMIT 1 ");

	                    if (!$highest_id) {
	                        $name_table_new_id=0;
	                    }

	                    foreach ($highest_id as $id) {
	                        $name_table_new_id=$id->set_id + 1;
	                    }

	   					$insert_multi_name_success = $wpdb->replace(
							YASR_MULTI_SET_NAME_TABLE,
							array(
								'set_id' =>$name_table_new_id,
								'set_name' =>$multi_set_name
							),
							array ('%d', '%s')
						);

	   					//If multi set name hase been inserted, now we're going to insert elements
	   					if ($insert_multi_name_success) {

	   						//get the highest id in table
	                    	$highest_id=$wpdb->get_results("SELECT id FROM " . YASR_MULTI_SET_FIELDS_TABLE . " ORDER BY id DESC LIMIT 1 ");

	                    	if (!$highest_id) {
	                        	$field_table_new_id=0;
	                    	}

	                    	foreach ($highest_id as $id) {
	                        	$field_table_new_id=$id->id + 1;
	                    	}

	                    	for ($i=1; $i<=$element_filled; $i++) {
	   							$insert_set_value=$wpdb->replace(
								YASR_MULTI_SET_FIELDS_TABLE,
									array(
										'id' => $field_table_new_id,
										'parent_set_id' =>$name_table_new_id,
										'field_name' =>$multi_set_name_element_[$i],
										'field_id' =>$i
									),
									array ('%d', '%d', '%s', '%d')
								);
								$field_table_new_id++; //Avoid overwrite
	   						} //End for

	   						if ($insert_set_value) {
	   							echo "<div class=\"updated\"><p><strong>";
	   								_e("Settings Saved", 'yet-another-stars-rating');
	   							echo "</strong></p></div> ";
	   						}

	   						else {
	   							_e("Something goes wrong trying insert set field name. Please report it", 'yet-another-stars-rating');
	   						}

	   					} //End if $insert_multi_name_success

	   					else {
	   						_e("Something goes wrong trying insert Multi Set name. Please report it", 'yet-another-stars-rating');
	   					}

	   			} //End if !$error

	   		}  //End if $_POST['multi-set-name']!=''

	  		//Else multi set's name and first 2 elements are empty
	   		else {
	   			$array_errors[] = "Multi Set's name and first 2 elements can't be empty";
	   			$error=TRUE;
	   		}

	   		if ($error) {
	   			return $array_errors;
			}

	    } //End if ( isset( $_POST['multi-set-name']) ) {

	} //End yasr_process_new_multi_set_form() function



	function yasr_process_edit_multi_set_form() {

		$error = FALSE;

		if ( isset( $_POST['yasr_edit_multi_set_form']) ) {

			$set_id = $_POST['yasr_edit_multi_set_form'];

			$number_of_stored_elements = $_POST['yasr-edit-form-number-elements'];

			global $wpdb;

			$array_errors = array();

			if ( !current_user_can( 'manage_options' ) ) {
	      		wp_die( 'You are not allowed to be on this page.' );
	   		}

	   		// Check nonce field
	  		check_admin_referer( 'edit-multi-set', 'add-nonce-edit-multi-set' );


	  		//Check if user want to delete entire set

	  		if (isset($_POST["yasr-remove-multi-set"])) {

	  			$remove_set = $wpdb->delete (
	  								YASR_MULTI_SET_NAME_TABLE,
									array(
										'set_id' => $set_id,
									),
									array ('%d')
								);

	  			$remove_set_values = $wpdb->delete (
	  								YASR_MULTI_SET_FIELDS_TABLE,
									array(
										'parent_set_id' => $set_id,
									),
									array ('%d')
								);

	  			$remove_set_votes = $wpdb->delete (
	  								YASR_MULTI_SET_VALUES_TABLE,
									array(
										'set_type' => $set_id,
									),
									array ('%d')
								);

	  			if ($remove_set==FALSE) {
	  				$error = TRUE;
					$array_errors[] .= __("Something goes wrong trying to delete a Multi Set . Please report it", 'yet-another-stars-rating');
	  			}


	  			//Comment this out, if try to delete an empty set print error
	  			/*if ($remove_set_values==FALSE) {
	  				$error = TRUE;
					$array_errors[] .= __("Something goes wrong trying to delete data fields for a set. Please report it", 'yet-another-stars-rating');
				}
				*/

				//Comment this out, will echo error even if the value for that field it's just empty
				/*if ($remove_set_votes==FALSE) {
	  				$error = TRUE;
					$array_errors[] .= __("Something goes wrong trying to delete data values for a set. Please report it", 'yet-another-stars-rating');
				}*/

	  		}

	  		for ($i = 0; $i <= 9; $i++) {

	  			//Than, check if the user want to remove some field
	  			if (isset($_POST["remove-element-$i"]) && !isset($_POST["yasr-remove-multi-set"]) ) {

	  				$field_to_remove = $_POST["remove-element-$i"];

	  				$remove_field = $wpdb->delete (
	  								YASR_MULTI_SET_FIELDS_TABLE,
									array(
										'parent_set_id' => $set_id,
										'field_id' =>$field_to_remove
									),
									array ('%d', '%d')
								);

	  				$remove_values = $wpdb->delete (
	  								YASR_MULTI_SET_VALUES_TABLE,
									array(
										'set_type' => $set_id,
										'field_id' =>$field_to_remove
									),
									array ('%d', '%d')
								);

	  				if ($remove_field == FALSE) {
						$error = TRUE;
						$array_errors[] = __("Something goes wrong trying to delete a Multi Set's element. Please report it", 'yet-another-stars-rating');
	  				}


	  				//Comment this out, will echo error even if the value for that field it's just empty
	  				/*if ($remove_values == FALSE) {
						$error = TRUE;
						$array_errors[] = __("Something goes wrong trying to delete data value for an element. Please report it", 'yet-another-stars-rating');
	  				}*/


	  			}  //End if isset $_POST['remove-element-$i']


	  			//update the stored elements with the new ones
	  			if (isset($_POST["edit-multi-set-element-$i"]) && !isset($_POST["yasr-remove-multi-set"]) && !isset($_POST["remove-element-$i"]) && $i <= $number_of_stored_elements ) {

	  				$field_name = $_POST["edit-multi-set-element-$i"];

	  				$field_id = $_POST["db-id-for-element-$i"];

		  			//if elements name is shorter than 3 chars
		  			if (mb_strlen($field_name) <3) {
						$array_errors[] = __("Field # $i must be at least 3 characters", 'yet-another-stars-rating');
						$error=TRUE;
		  			}

		  			if(mb_strlen($field_name) > 40) {
		  				$array_errors[] = __("Field # $i must be shorter than 40 characters", 'yet-another-stars-rating');
						$error=TRUE;
		  			}

	  				else {

	  					//Check if field name is changed
	  					$field_name_in_db = $wpdb->get_results($wpdb->prepare("SELECT field_name FROM " . YASR_MULTI_SET_FIELDS_TABLE . " WHERE field_id=%d AND parent_set_id=%d", $field_id, $set_id ) );

	  					foreach ($field_name_in_db as $field_in_db) {
	  						$field_name_in_database = $field_in_db->field_name;
	  					}

	  					//if field name in db is different from field name in form update it
	  					if ($field_name_in_database != $field_name) {

		  					$insert_field_name=$wpdb->update(
									YASR_MULTI_SET_FIELDS_TABLE,

										array(
											'field_name' =>$field_name,
										),

										array(
											'parent_set_id' =>$set_id,
											'field_id' =>$field_id
										),

										array ('%s'),

										array ('%d', '%d')

									);

		  					if ($insert_field_name == FALSE) {
		  						$error = TRUE;
								$array_errors[] = __("Something goes wrong trying to update a Multi Set's element. Please report it", 'yet-another-stars-rating');
		  					}

	  				    } //End if ($field_name_in_database != $field_name) {

	  				}

	  			} //End if (isset($_POST["edit-multi-set-element-$i"]) && !isset($_POST["remove-element-$i"]) && $i<=$number_of_stored_elements )


	  			//If $i > number of stored elements, user is adding new elements, so we're going to insert the new ones
	  			if (isset($_POST["edit-multi-set-element-$i"]) && !isset($_POST["yasr-remove-multi-set"]) && !isset($_POST["remove-element-$i"]) && $i > $number_of_stored_elements ) {

	  				$field_name = $_POST["edit-multi-set-element-$i"];

	  				//if elements name is shorter than 3 chars return error. I use mb_strlen($field_name) > 1
	  				//because I don't wont return error if an user add an empty field. An empty field will be
	  				//just ignored
	  				if (mb_strlen($field_name) > 1 && mb_strlen($field_name) < 3) {
	  							$array_errors[] = __("Field # $i must be at least 3 characters", 'yet-another-stars-rating');
	   							$error=TRUE;
	  				}

	  				if(mb_strlen($field_name) > 40) {
		  				$array_errors[] = __("Field # $i must be shorter than 40 characters", 'yet-another-stars-rating');
						$error=TRUE;
		  			}

	  				//if field is not empty
	  				elseif ($field_name != '') {

	  					$highest_id=$wpdb->get_results("SELECT id FROM " . YASR_MULTI_SET_FIELDS_TABLE . " ORDER BY id DESC LIMIT 1 ");

	  					$highest_field_id = $wpdb->get_results("SELECT field_id FROM " . YASR_MULTI_SET_FIELDS_TABLE . " ORDER BY field_id DESC LIMIT 1 ");

	  					foreach ($highest_id as $id) {
	                        $field_table_new_id=$id->id + 1;
	                    }

	                    foreach ($highest_field_id as $id) {
	                    	$new_field_id = $id->field_id+1;
	                    }

	  					$insert_set_value=$wpdb->replace(
								YASR_MULTI_SET_FIELDS_TABLE,
									array(
										'id' => $field_table_new_id,
										'parent_set_id' =>$set_id,
										'field_name' =>$field_name,
										'field_id' =>$new_field_id
									),
									array ('%d', '%d', '%s', '%d')
								);

						//now, I've to find which post is using the edited multi set and add the new field with value 0

	  					//get the highest id
						$highest_id_value_table = $wpdb->get_results("SELECT id FROM " . YASR_MULTI_SET_VALUES_TABLE . " ORDER BY id DESC LIMIT 1 ");

						foreach ($highest_id_value_table as $id) {
	                        $field_value_table_new_id=$id->id + 1;
	                    }

						//get all the post id that are using this multiset
						$array_post_id = $wpdb->get_results($wpdb->prepare("SELECT post_id
							FROM " . YASR_MULTI_SET_VALUES_TABLE .
							" WHERE set_type=%d
							GROUP BY post_id", $set_id));

						//Insert the new fields
						foreach ($array_post_id as $postid) {

							$insert_new_field_values_table = $wpdb->insert(
							YASR_MULTI_SET_VALUES_TABLE,
								array(
										'id' => $field_value_table_new_id,
										'set_type' => $set_id,
										'field_id' => $new_field_id,
										'post_id' => $postid->post_id
									),
								array ('%d', '%d', '%d', '%d')
							);

							$field_value_table_new_id++;

						}

	  					if ($insert_set_value == FALSE) {
	  						$error = TRUE;
							$array_errors[] = __("Something goes wrong trying to insert set field name in edit form. Please report it", 'yet-another-stars-rating');
	  					}

	  				} //end else
	  			}


	  		} //End for


	  		if ($error) {
	   			return $array_errors;
			}
			else {
				echo "<div class=\"updated\"><p><strong>";
	   				_e("Settings Saved", 'yet-another-stars-rating');
	   			echo "</strong></p></div> ";
			}


		} //End if isset( $_POST['yasr_edit_multi_set_form']


	} //End yasr_process_edit_multi_set_form() function




add_action( 'admin_init', 'yasr_style_options_init' ); //This is for auto insert options

	function yasr_style_options_init() {

    	register_setting(
        	'yasr_style_options_group', // A settings group name. Must exist prior to the register_setting call. This must match the group name in settings_fields()
        	'yasr_style_options', //The name of an option to sanitize and save.
        	'yasr_style_options_sanitize'
    	);

    	$style_options = get_option( 'yasr_style_options' );

    	if (!$style_options || !isset($style_options['scheme_color_multiset']) || !isset($style_options['textarea'])) {

    		$style_options = array();
    		$style_options['scheme_color_multiset'] = 'light';
    		$style_options['textarea'] = NULL;

    	}

		//filter $style_options
		$style_options = apply_filters('yasr_filter_style_options', $style_options);

    	add_settings_section( 'yasr_style_options_section_id', __('Style Options', 'yet-another-stars-rating'), 'yasr_style_section_callback', 'yasr_style_tab' );
			do_action('yasr_style_options_add_settings_field', $style_options);
			add_settings_field( 'yasr_color_scheme_multiset', __('Which color scheme do you want to use?', 'yet-another-stars-rating') , 'yasr_color_scheme_multiset_callback', 'yasr_style_tab', 'yasr_style_options_section_id', $style_options);
			add_settings_field( 'yasr_style_options_textarea', __('Custom CSS Styles', 'yet-another-stars-rating'), 'yasr_style_options_textarea_callback', 'yasr_style_tab', 'yasr_style_options_section_id', $style_options );



	}

	function yasr_style_section_callback () {

	}



	function yasr_color_scheme_multiset_callback($style_options) {

    	?>

    	<input type='radio' name='yasr_style_options[scheme_color_multiset]' value='light' class='yasr-general-options-scheme-color' <?php if ($style_options['scheme_color_multiset']==='light') echo " checked=\"checked\" "; ?>  />
			<?php _e('Light', 'yet-another-stars-rating')?>

		&nbsp;&nbsp;&nbsp;

		<input type='radio' name='yasr_style_options[scheme_color_multiset]' value='dark' class='yasr-general-options-scheme-color' <?php if ($style_options['scheme_color_multiset']==='dark') echo " checked=\"checked\" "; ?>  />
			<?php _e('Dark', 'yet-another-stars-rating')?>
			<br />

			<br />

		<a href="#" id="yasr-color-scheme-preview-link"><?php _e("Preview", 'yet-another-stars-rating') ?></a>

		<div id="yasr-color-scheme-preview" style="display:none">
	   			<?php

	   				_e("Light theme", 'yet-another-stars-rating');
	   				echo "<br /><br /><img src=" . YASR_IMG_DIR . "yasr-multi-set.png>";

	   				echo "<br /> <br />";

	   				_e("Dark theme", 'yet-another-stars-rating');
	   				echo "<br /><br /><img src=" . YASR_IMG_DIR . "dark-multi-set.png>";
	   			 ?>
	   	</div>

		<p>

    	<?php
    }

    function yasr_style_options_textarea_callback ($style_options) {

    	_e("Please use text area below to write your own CSS styles to override the default ones.", 'yet-another-stars-rating');
		echo "<br /><strong>";
		_e("Leave it blank if you don't know what you're doing.", 'yet-another-stars-rating');
		echo "</strong><p>";

		echo ("
			<textarea rows=\"17\" cols=\"40\" name=\"yasr_style_options[textarea]\" id=\"yasr_style_options_textarea\">$style_options[textarea]</textarea>
			");


	}

    //sanitize
	function yasr_style_options_sanitize ($style_options) {

		foreach ($style_options as $key => $value) {

			$output[$key] = strip_tags( stripslashes( $style_options[$key] ) );

		}

		return $output;


	}


/*** Facebook sdk, since version  0.8.8 ***/

function yasr_include_fb_sdk () {

	$lang = get_locale();

	$lang = json_encode("$lang");

	?>

	<div id="fb-root"></div>
	<script>
		(function(d, s, id) {
			var lang = <?php echo ($lang); ?>;
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/"+lang+"/sdk.js#xfbml=1&version=v2.3&appId=113845018658519";
			fjs.parentNode.insertBefore(js, fjs);
		}
		(document, 'script', 'facebook-jssdk'));
	</script>

	<?php

}

function yasr_upgrade_pro_box ($position=FALSE) {

	if ( yasr_fs()->is_not_paying() ) {

		if ( $position && $position == "bottom" ) {

			$yasr_upgrade_class = "yasr-donatedivbottom";

		}

		else {

			$yasr_upgrade_class = "yasr-donatedivdx";

		}

		?>

        <div class="<?php echo $yasr_upgrade_class ?>" style="display: none">

            <h2 style="color: #34A7C1">
				<?php _e( 'Upgrade to YASR Pro', 'yet-another-stars-rating' ); ?>
            </h2>

            <div class="yasr-upgrade-to-pro">

                <ul>

                    <li><strong><?php _e( ' User Reviews', 'yet-another-stars-rating' ); ?></strong></li>
                    <li><strong><?php _e( ' Custom Rankings', 'yet-another-stars-rating' ); ?></strong></li>
                    <li><strong><?php _e( ' 20 + ready to use themes', 'yet-another-stars-rating' ); ?></strong></li>
                    <li><strong><?php _e( ' Upload your own theme', 'yet-another-stars-rating' ); ?></strong></li>
                    <li><strong><?php _e( ' Dedicate support', 'yet-another-stars-rating' ); ?></strong></li>


                </ul>

                <a href="<?php echo yasr_fs()->get_upgrade_url(); ?>"><button class="button button-primary">Upgrade Now</button></a>

            </div>

        </div>

		<?php

	}

}

/****** Facebook box, since version 0.8.8 ******/

function yasr_fb_box ($position=FALSE) {

	if ($position && $position == "bottom") {

		$yasr_fb_class = "yasr-donatedivbottom";

	}

	else {

		$yasr_fb_class = "yasr-donatedivdx";

	}

	?>

	<div class="<?php echo $yasr_fb_class; ?>" style="display:none">

	<h2><?php _e('Keep in touch!', 'yet-another-stars-rating'); ?></h2>

		<div class="fb-page" data-href="https://www.facebook.com/yetanotherstarsrating" data-hide-cover="false" data-show-facepile="true" data-show-posts="false">
			<div class="fb-xfbml-parse-ignore">
				<blockquote cite="https://www.facebook.com/yetanotherstarsrating"><a href="https://www.facebook.com/yetanotherstarsrating">YASR - Yet Another Stars Rating</a></blockquote>
			</div>
		</div>

	</div>

	<?php

}

/** Add a box on the right for asking to rate 5 stars on Wordpress.org
*   It must be appear after 10 logged rating, after 100 and after 1000
*   Since version 0.9.0
*/

function yasr_ask_rating ($position=FALSE) {

	$transient = get_site_transient ('yasr_hide_ask_rating');

	if (!$transient) {

		if ($position && $position == "bottom") {

			$yasr_metabox_class = "yasr-donatedivbottom";

		}

		else {

			$yasr_metabox_class = "yasr-donatedivdx";

		}

		$n_stored_ratings = yasr_count_logged_vote ();

		$div = "<div class=\"$yasr_metabox_class\" id=\"yasr-ask-five-stars\" style=\" display:none; border-left: 3px solid #7AD03A; font-size: 14px;\">";

		if($n_stored_ratings > 20) {

			$text = sprintf( __('Hey, seems like you reached %s votes on your site throught YASR! That\'s cool!', 'yet-another-stars-rating'),'<strong>'.$n_stored_ratings.'</strong>');
			$text .= "<br />";
			$text .= __('Can I ask a favor?', 'yet-another-stars-rating');
			$text .= "<br />";
			$text .= __('Can you please rate YASR 5 stars on', 'yet-another-stars-rating');
			$text .= ' <a href="https://wordpress.org/support/view/plugin-reviews/yet-another-stars-rating?filter=5">wordpress.org?</a>';
			$text .= __(' It will require just 1 min but it\'s a HUGE help for me. Thank you.' , 'yet-another-stars-rating');
			$text .= "<br /><br />";
			$text .= "<em>> Dario Curvino</em>";

			$text .= "<ul>

					<li><a href=\"https://wordpress.org/support/view/plugin-reviews/yet-another-stars-rating?filter=5\">" . __("Ok, I'm glad to help!" , 'yet-another-stars-rating') ."</a></li>
					<li><a href=\"#\" id=\"yasr-ask-five-star-later\">" . __("Remind me later!" , 'yet-another-stars-rating') ."</a></li>
					<li><a href=\"#\" id=\"yasr-ask-five-close\">" . __("Don't need to ask, I already did it!" , 'yet-another-stars-rating') ."</a></li>

			</ul>";


			$div_and_text = $div . $text . '</div>';

			echo $div_and_text;

		}

	} //End if (!transient)


}



/** Change default admin footer on yasr settings pages
*       $text is the default wordpress text
*		Since 0.8.9
*/

add_filter( 'admin_footer_text', 'yasr_custom_admin_footer' );

function yasr_custom_admin_footer ($text) {

	if (isset($_GET['page'])) {
    		$yasr_page = $_GET[ 'page' ];

    		if ($yasr_page == 'yasr_settings_page') {

    			$custom_text = ' | <i>';
				$custom_text .= sprintf( __( 'Thank you for using <a href="%s" target="_blank">Yet Another Stars Rating</a>. Please <a href="%s" target="_blank">rate it</a> 5 stars on <a href="%s" target="_blank">WordPress.org</a>', 'yet-another-stars-rating' ), 'https://yetanotherstarsrating.com', 'https://wordpress.org/support/view/plugin-reviews/yet-another-stars-rating?filter=5', 'https://wordpress.org/support/view/plugin-reviews/yet-another-stars-rating?filter=5' );
				$custom_text .= '</i>';

				return $text . $custom_text;

    		}

    		else {

    			return $text;

    		}

		}

	else {

		return $text;

	}

}


?>
