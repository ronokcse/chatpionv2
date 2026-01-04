<script type="text/javascript">
	"use strict";
	var base_url=<?php echo json_encode(base_url()); ?>;
	var user_id = <?php echo json_encode(isset($user_id) ? $user_id : 0); ?>;
	var selected_language=<?php echo json_encode(isset($language) ? $language : 'english'); ?>;
	var is_demo = <?php echo json_encode(isset($is_demo) ? $is_demo : '0'); ?>;
	var is_admin = <?php echo json_encode((session()->get('user_type') == "Admin") ? 1:0); ?>;
	var controller_name = <?php echo json_encode(isset($uri) && $uri ? $uri->segment(1) : ''); ?>;
    var function_name = <?php echo json_encode(isset($uri) && $uri ? $uri->segment(2) : ''); ?>;    
    var is_mobile =  <?php echo json_encode(session()->get('is_mobile'));?>;
	var global_lang_video_upload_limit = <?php 
		$myConfig = config('MyConfig');
		$video_limit = '15'; // Default value
		if (property_exists($myConfig, 'video_upload_limit')) {
			$video_limit = $myConfig->video_upload_limit;
		} elseif (property_exists($myConfig, 'facebook_poster_video_upload_limit')) {
			$video_limit = $myConfig->facebook_poster_video_upload_limit;
		}
		echo json_encode($video_limit);
	?>;
	global_lang_video_upload_limit = parseInt(global_lang_video_upload_limit);

	var global_lang_image = <?php echo json_encode(lang('Image')); ?>;
	var global_lang_video = <?php echo json_encode(lang('Video')); ?>;
	var global_lang_report = <?php echo json_encode(lang('Report')); ?>;
	var global_lang_view = <?php echo json_encode(lang('View')); ?>;
	var global_lang_edit = <?php echo json_encode(lang('Edit')); ?>;
	var global_lang_update = <?php echo json_encode(lang('Update')); ?>;
	var global_lang_delete = <?php echo json_encode(lang('Delete')); ?>;
	var global_lang_remove = <?php echo json_encode(lang('Remove')); ?>;
	var global_lang_close = <?php echo json_encode(lang('Close')); ?>;
	var global_lang_copy = <?php echo json_encode(lang('Copy')); ?>;
	var global_lang_submit = <?php echo json_encode(lang('Submit')); ?>;
	var global_lang_add_more = <?php echo json_encode(lang('Add more'));?>;
	var global_lang_play = <?php echo json_encode(lang('Play'));?>;
	var global_lang_visit_channel = <?php echo json_encode(lang('Visit Channel'));?>;
	var global_lang_watch_video = <?php echo json_encode(lang('Watch Video'));?>;
	var global_lang_not_applicable = <?php echo json_encode(lang('N/A'));?>;
	var global_lang_url_copied_clipbloard = <?php echo json_encode(lang('Url Copied to clipboard'));?>;
	var global_lang_pause_campaign =<?php echo json_encode(lang('Pause Campaign'));?>;
	var global_lang_start_campaign = <?php echo json_encode(lang('Start Campaign'));?>;

	var global_lang_active = <?php echo json_encode(lang('Active')); ?>;
	var global_lang_inactive = <?php echo json_encode(lang('Inactive')); ?>;
	var global_lang_processing = <?php echo json_encode(lang('Processing')); ?>;
	var global_lang_completed = <?php echo json_encode(lang('Completed')); ?>;
	var global_lang_pending = <?php echo json_encode(lang('Pending')); ?>;

	var global_lang_success = <?php echo json_encode(lang('Success')); ?>;
	var global_lang_error = <?php echo json_encode(lang('Error')); ?>;
	var global_lang_warning = <?php echo json_encode(lang('Warning')); ?>;
	var global_lang_confirm = <?php echo json_encode(lang('Confirm')); ?>;
	var global_lang_cancel = <?php echo json_encode(lang('Cancel')); ?>;

	var global_lang_last_30_days = <?php echo json_encode(lang('Last 30 Days'));?>;
	var global_lang_this_month = <?php echo json_encode(lang('This Month'));?>;
	var global_lang_last_month = <?php echo json_encode(lang('Last Month'));?>;
	var global_lang_select_from_date = <?php echo json_encode(lang('Please select from date'));?>;
	var global_lang_select_to_date = <?php echo json_encode(lang('Please select to date'));?>;

	var global_lang_try_once_again = <?php echo json_encode(lang('try once again'));?>;
	var global_lang_something_went_wrong = <?php echo json_encode(lang('Something went wrong, please try again.'));?>;
	var global_lang_no_data_found = <?php echo json_encode(lang('No data found')); ?>;
	var global_lang_are_you_sure = <?php echo json_encode(lang('Are you sure?'));?>;
	var global_lang_saved_successfully = <?php echo json_encode(lang('Your data has been successfully saved.')); ?>;
	var global_lang_delete_confirmation = <?php echo json_encode(lang('Do you really want to delete it?'));?>;

	var global_lang_campaign_create = <?php echo json_encode(lang('Create Campaign'));?>;
	var global_lang_campaign_edit = <?php echo json_encode(lang('Edit Campaign'));?>;
	var global_lang_campaign_delete = <?php echo json_encode(lang('Delete Campaign'));?>;
	var global_lang_campaign_delete_confirmation = <?php echo json_encode(lang('Do you really want to delete this campaign?'));?>;
	var global_lang_campaign_campaign_state_confirmation = <?php echo json_encode(lang('Do you really want to change this campaign state?'));?>;
	var global_lang_campaign_name = <?php echo json_encode(lang('Campaign Name'));?>;
	var global_lang_campaign_created_successfully = <?php echo json_encode(lang('Campaign has been created successfully.'));?>;
	var global_lang_campaign_updated_successfully = <?php echo json_encode(lang('Campaign has been updated successfully.'));?>;
	var global_lang_campaign_deleted_successfully = <?php echo json_encode(lang('Campaign has been deleted successfully.'));?>;
	var global_lang_campaign_paused_successfully = <?php echo json_encode(lang('Campaign has been paused successfully.'));?>;
	var global_lang_campaign_started_successfully = <?php echo json_encode(lang('Campaign has been stared successfully.'));?>;
	var global_lang_campaign_force_started_successfully = <?php echo json_encode(lang('Force processing has been enabled successfully.'));?>;
	var global_lang_no_video_found = <?php echo json_encode(lang('We cound not find any video'));?>;

	var global_lang_previous = <?php echo json_encode(lang('Previous')); ?>;
	var global_lang_next = <?php echo json_encode(lang('Next')); ?>;
	var global_lang_this = <?php echo json_encode(lang('This')); ?>;
	var global_lang_today = <?php echo json_encode(lang('Today')); ?>;
	var global_lang_month = <?php echo json_encode(lang('Month')); ?>;
	var global_lang_week = <?php echo json_encode(lang('Week')); ?>;
	var global_lang_day = <?php echo json_encode(lang('Day')); ?>;
	var global_lang_all_day = <?php echo json_encode(lang('All Day')); ?>;
	var global_lang_more = <?php echo json_encode(lang('More')); ?>;
	var global_lang_no_event_found = <?php echo json_encode(lang('No event found')); ?>;

	var upload_lang_drag_drop_files = <?php echo json_encode(lang('Drag & Drop Files'));?>;
	var upload_lang_upload = <?php echo json_encode(lang('Upload'));?>;
	var upload_lang_abort = <?php echo json_encode(lang('Abort'));?>;
	var upload_lang_cancel = <?php echo json_encode(lang('Cancel'));?>;
	var upload_lang_delete = <?php echo json_encode(lang('Delete'));?>;
	var upload_lang_done = <?php echo json_encode(lang('Done'));?>;
	var upload_lang_multiple_file_drag_drop_is_not_allowed = <?php echo json_encode(lang('Multiple File Drag & Drop is not allowed.'));?>;
	var upload_lang_is_not_allowed_allowed_extensions  = <?php echo json_encode(lang('is not allowed. Allowed extensions: '));?>;
	var upload_lang_is_not_allowed_file_already_exists = <?php echo json_encode(lang('is not allowed. File already exists.'));?>;
	var upload_lang_is_not_allowed_allowed_max_size  = <?php echo json_encode(lang('is not allowed. Allowed Max size: '));?>;
	var upload_lang_upload_is_not_allowed = <?php echo json_encode(lang('Upload is not allowed'));?>;
	var upload_lang_is_not_allowed_maximum_allowed_files_are = <?php echo json_encode(lang('is not allowed. Maximum allowed files are:'));?>;
	var upload_lang_download = <?php echo json_encode(lang('Download'));?>;

	var support_lang_success = <?php echo json_encode(lang('Success')); ?>;
	var support_lang_error = <?php echo json_encode(lang('Error')); ?>;
	var support_lang_no_data_found = <?php echo json_encode(lang('No data found')); ?>;
	var support_lang_ticket_delete_confirm = <?php echo json_encode(lang('Do you really want to delete it?'));?>;
	var support_lang_are_you_sure = <?php echo json_encode(lang('Are you sure?'));?>;


	var addon_manager_lang_alert = <?php echo json_encode(lang('Alert'));?>;
	var addon_manager_lang_deactive_addon = <?php echo json_encode(lang('Deactive Add-on?'));?>;
	var addon_manager_lang_deactive_addon_confirmation = <?php echo json_encode(lang('Do you really want to deactive this add-on? Your add-on data will still remain.'));?>;
	var addon_manager_lang_delete_addon = <?php echo json_encode(lang('Delete Add-on?'));?>;
	var addon_manager_lang_delete_addon_confirmation = <?php echo json_encode(lang('Do you really want to delete this add-on? This process can not be undone.'));?>;
	var addon_manager_lang_delete_url = <?php echo json_encode(base_url("addons/delete_uploaded_zip"));?>;

	var announcement_lang_mark_seen_confirmation = <?php echo json_encode(lang('Do you really want to mark all unseen notifications as seen?'));?>;

	var user_manager_lang_not_selected = <?php echo json_encode(lang('You have to select users to send email.'));?>;
	var package_manager_lang_cannot_deleted = <?php echo json_encode(lang('Default package can not be deleted.'));?>;

	var language_manager_lang_alert1 = <?php echo json_encode(lang('Please put a language name & then save.'));?>;
	var language_manager_lang_alert2 = <?php echo json_encode(lang('Please put a language name & save it first.'));?>;
	var language_manager_lang_download = <?php echo json_encode(lang('Download Language'));?>;
	var language_manager_lang_delete = <?php echo json_encode(lang('Delete Language'));?>;
	var language_manager_lang_cannot_delete = <?php echo json_encode(lang('Sorry, english language can not be deleted.'));?>;
	var language_manager_lang_cannot_delete_default = <?php echo json_encode(lang('This is your default language, it can not be deleted.'));?>;
	var language_manager_lang_cannot_delete_confirmation = <?php echo json_encode(lang('Delete Language?'));?>;
	var language_manager_lang_cannot_delete_confirmation_msg = <?php echo json_encode(lang('Do you really want to delete this language? It will delete all files of this language.'));?>;
	var language_manager_lang_cannot_delete_success_msg = <?php echo json_encode(lang('Your language file has been successfully deleted.'));?>;
	var language_manager_lang_only_char_allowed = <?php echo json_encode(lang('Only characters and underscores are allowed.'));?>;
	var language_manager_lang_language_exist = <?php echo json_encode(lang('Sorry, this language already exists, you can not add this again.'));?>;
	var language_manager_lang_language_exist_try = <?php echo json_encode(lang('This language is already exist, please try with different one.'));?>;
	var language_manager_lang_language_exist_update = <?php echo json_encode(lang('This language already exist, no need to update.'));?>;
	var language_manager_lang_update_name_first = <?php echo json_encode(lang('Your given name has not updated, please update the name first.'));?>;
	var language_manager_lang_selected_lang = <?php echo json_encode(session()->get('selected_language'));?>;
	var language_manager_lang_editable_language = <?php echo json_encode(isset($uri) && $uri ? $uri->segment(3) : '');?>;

	var smtp_settings_lang_test_mail_sent = <?php echo json_encode(lang('Test email has been sent successfully.'));?>;

	var import_account_bot_restart = <?php echo json_encode(lang('Re-start Bot Connection'));?>;
	var import_account_bot_restart_confirm = <?php echo json_encode(lang('Do you really want to re-start Bot Connection for this page?'));?>;
	var import_account_bot_enable = <?php echo json_encode(lang('Enable Bot Connection'));?>;
	var import_account_bot_enable_confirm = <?php echo json_encode(lang('Do you really want to enable Bot Connection for this page?'));?>;
	var import_account_bot_disable = <?php echo json_encode(lang('Disable Bot Connection'));?>;
	var import_account_bot_disable_confirm = <?php echo json_encode(lang('Do you really want to disable Bot Connection for this page?'));?>;
	var import_account_bot_delete = <?php echo json_encode(lang('Delete Bot Connection & all settings'));?>;
	var import_account_bot_delete_confirm = <?php echo json_encode(lang('By proceeding, it will delete all settings of messenger bot, auto reply campaign, posting campaign, subscribers and all campaign reports of this page. This data can not be retrived. It will not delete the page itself from the system.'));?>;
	var import_account_group_delete_confirm = <?php echo json_encode(lang('If you delete this group, all the campaigns corresponding to this group will also be deleted. Do you want to delete this group from database?'));?>;
	var import_account_page_delete_confirm = <?php echo json_encode(lang('If you delete this page, all the campaigns corresponding to this page will also be deleted. Do you want to delete this page from database?'));?>;
	var import_account_delete_confirm = <?php echo json_encode(lang('If you delete this account, all the pages, groups and all the campaigns corresponding to this account will also be deleted form database. do you want to delete this account from database?'));?>;
	var import_account_gb_numberic_id = <?php echo json_encode(lang('Please enter your facebook numeric id first'));?>;

	var fb_settings_lang_make_active = <?php echo json_encode(lang('Make this app active'));?>;
	var fb_settings_lang_make_inactive = <?php echo json_encode(lang('Make this app inactive'));?>;
	var fb_settings_lang_add_app = <?php echo json_encode(lang('Add App'));?>;
	var fb_settings_lang_edit_app = <?php echo json_encode(lang('Edit App'));?>;
	var fb_settings_lang_change_app_state_confirmation = <?php echo json_encode(lang('Do you really want to change this apps state?'));?>;
	var fb_settings_lang_delete_app_confirmation = <?php echo json_encode(lang('Do you really want to delete this app?'));?>;
	var google_settings_lang_delete_app_confirmation = <?php echo json_encode(lang('Do you really want to delete this app? Deleting app will delete all related channels and campaigns.'));?>;

	var theme_manager_lang_activation = <?php echo json_encode(lang('Theme Activation'));?>;
	var theme_manager_lang_activation_confirmation = <?php echo json_encode(lang('Do you really want to activate this Theme?'));?>;
	var theme_manager_lang_deactivation = <?php echo json_encode(lang('Theme Deactivation'));?>;
	var theme_manager_lang_deactivation_confirmation = <?php echo json_encode(lang('Do you really want to deactivate this Theme? Your theme data will still remain'));?>;
	var theme_manager_lang_delete_confirmation = <?php echo json_encode(lang('Do you really want to delete this Theme? This process can not be undone.'));?>;

	var account_list_delete_confirmation = <?php echo json_encode(lang('Do you really want to delete this account?'));?>;

	var upload_lang_error_msg1 = <?php echo json_encode(lang('Please provide video title'));?>;
	var upload_lang_error_msg2 = <?php echo json_encode(lang('Please select a youtube Channel'));?>;
	var upload_lang_error_msg3 = <?php echo json_encode(lang('Please select video category'));?>;
	var upload_lang_error_msg4 = <?php echo json_encode(lang('Please select video privacy type'));?>;
	var upload_lang_error_msg5 = <?php echo json_encode(lang('Please select time zone'));?>;
	var upload_lang_error_msg6 = <?php echo json_encode(lang('Please select schedule date time'));?>;
	var upload_lang_error_msg7 = <?php echo json_encode(lang('Please upload video'));?>;
	var upload_lang_error_msg8 = <?php echo json_encode(lang('This video has no title'));?>;
	var upload_lang_error_msg9 = <?php echo json_encode(lang('No title'));?>;
	var upload_lang_success_msg = <?php echo json_encode(lang('Video data has been stored successfully and will be processed at scheduled time.'));?>;
	var upload_lang_update_video = <?php echo json_encode(lang('Update Schedule Video'));?>;


	var menu_manager_all_menu = <?php echo json_encode(isset($all_menu) ? $all_menu : "");?>;
	var menu_manager_restore_confirm = <?php echo json_encode(lang('Are you sure about reseting your menus to default state?'));?>;
	var menu_manager_name_required = <?php echo json_encode(lang('Menu Name is Required'));?>;
	var menu_manager_icon_required = <?php echo json_encode(lang('Menu Icon must not be empty icon'));?>;
	var menu_manager_page_created = <?php echo json_encode(lang('Page has been created successfully.'));?>;
	var menu_manager_page_updated = <?php echo json_encode(lang('Page has been updated successfully.'));?>;
	var menu_manager_page_deleted = <?php echo json_encode(lang('Page has been deleted successfully.'));?>;
	var menu_manager_pages_deleted = <?php echo json_encode(lang('Selected pages has been deleted Successfully'));?>;
	var menu_manager_page_not_selected = <?php echo json_encode(lang('You did not select any page to delete.'));?>;
	var notAllowed = <?php echo json_encode(lang('Menu having link cannot be used as parent.')); ?>;
    var three_level_allowed = <?php echo json_encode(lang('Third level menu is not allowed.')); ?>;
    var drag_drop_not_allowed = <?php echo json_encode(lang('System default menu cannot be re-ordered.')); ?>;

    var payment_is_manaual_payment =  <?php echo json_encode(isset($manual_payment) ? $manual_payment : "");?>;
    var payment_sslcommers_mode =  <?php echo json_encode(isset($sslcommers_mode) ? $sslcommers_mode : "");?>;
    var payment_ssl_post_data = <?php echo json_encode(isset($postdata_array) ? $postdata_array : "");?>;
    var payment_has_reccuring = <?php echo json_encode(isset($has_reccuring) ? $has_reccuring : "");?>;
    var payment_lang_subscription_message = <?php echo json_encode(lang('Subscription Message')); ?>;
    var payment_lang_subscription_message_deatils = <?php echo json_encode(lang('You have already a subscription enabled in paypal. If you want to use different paypal or different package, make sure to cancel your previous subscription from your paypal.')); ?>;


    var facebook_app_delete_confirm = <?php echo json_encode(lang('If you delete this APP then, all the imported Facebook accounts and their Pages and Campaigns will be deleted too corresponding to this APP.')); ?>;
    var google_app_delete_confirm = <?php echo json_encode(lang('If you delete this APP then, all the imported Google accounts and their Pages and Campaigns will be deleted too corresponding to this APP.')); ?>;
    var google_app_status_change_confirm = <?php echo json_encode(lang('If you change this APP status to inactive then, all the imported Google accounts and Campaigns will not work corresponding to this APP.')); ?>;

	var dashboard_step_size = <?php echo json_encode(isset($step_size) ? $step_size : 1);?>;
	var dashboard_image_video_compare_list = <?php echo isset($image_video_compare_list) ? json_encode(array_values($image_video_compare_list)) : '[]';?>;
	var dashboard_image_post_list = <?php echo isset($image_post_list) ? json_encode(array_values($image_post_list)) : '[]';?>;
	var dashboard_video_post_list = <?php echo isset($video_post_list) ? json_encode(array_values($video_post_list)) : '[]';?>;

	var calendar_events = <?php echo isset($calendar_data) ? json_encode($calendar_data) : '[]';?>;

	var instragram_post_post_type =<?php echo json_encode(isset($all_data[0]["post_type"]) ? $all_data[0]["post_type"] : "");?>;
	var instragram_post_warning_upload_message = <?php echo json_encode(lang('Please type a message to post.'));?>;
	var instragram_post_warning_upload_link = <?php echo json_encode(lang('Please paste a link to post.'));?>;
	var instragram_post_warning_upload_image = <?php echo json_encode(lang('Please paste an image url or upload an image to post.'));?>;
	var instragram_post_warning_upload_video = <?php echo json_encode(lang('Please paste an video url or upload an video to post.'));?>;
	var instragram_post_warning_select_account = <?php echo json_encode(lang('Please select any page/group/account to publish this post.'));?>;
	var instragram_post_warning_schedule_timezone = <?php echo json_encode(lang('Please select schedule time/time zone.'));?>;
	var instragram_post_message_see_report = <?php echo json_encode(lang('Click here to see report'));?>;
	var instragram_post_delete_main_confirm = <?php echo json_encode(lang('This is main campaign, if you want to delete it, rest of the sub campaign will be deleted. Do you really want to delete this post from the database?'));?>;
	var instragram_post_message_sorry1 = <?php echo json_encode(lang('Sorry, Only parent campaign has shown report.'));?>;
	var instragram_post_message_sorry2 = <?php echo json_encode(lang('Sorry, this post is not published yet.'));?>;
	var instragram_post_message_sorry3 = <?php echo json_encode(lang('Sorry, Only Pending Campaigns Are Editable.'));?>;
	var instragram_post_message_sorry4 = <?php echo json_encode(lang('Sorry, Processing Campaign Can not be deleted.'));?>;
	var instragram_post_message_sorry5 = <?php echo json_encode(lang('Sorry, Embed code is only available for published video posts.'));?>;

    var instagram_all_auto_comment_report_doyouwanttopausethiscampaign = <?php echo json_encode(lang('do you want to pause this campaign?'));?>;
	var instagram_all_auto_comment_report_doyouwanttostartthiscampaign = <?php echo json_encode(lang('do you want to start this campaign?'));?>;
	var instagram_all_auto_comment_report_doyouwanttodeletethisrecordfromdatabase = <?php echo json_encode(lang('do you want to delete this record from database?'));?>;
	var instagram_all_auto_comment_report_youdidntselectanyoption = <?php echo json_encode(lang('you did not select any option.'));?>;
	var instagram_all_auto_comment_report_youdidntprovideallinformation = <?php echo json_encode(lang('you did not provide all information.'));?>;
	var instagram_all_auto_comment_report_doyouwanttostarthiscampaign = <?php echo json_encode(lang('do you want to start this campaign?'));?>;
	var instagram_all_auto_comment_report_doyoureallywanttoreprocessthiscampaign = <?php echo json_encode(lang('Force Reprocessing means you are going to process this campaign again from where it ended. You should do only if you think the campaign is hung for long time and did not send message for long time. It may happen for any server timeout issue or server going down during last attempt or any other server issue. So only click OK if you think message is not sending. Are you sure to Reprocessing ?'));?>;
	var instagram_all_auto_comment_report_alreadyenabled = <?php echo json_encode(lang('this campaign is already enable for processing.'));?>;
	var instagram_all_auto_comment_report_typeautocampaignname = <?php echo json_encode(lang('You did not Type auto campaign name'));?>;
	var instagram_all_auto_comment_report_youdidnotchosescheduletype = <?php echo json_encode(lang('You did not choose any schedule type'));?>;
	var instagram_all_auto_comment_report_youdidnotchosescheduletime = <?php echo json_encode(lang('You did not select any schedule time'));?>;
	var instagram_all_auto_comment_report_youdidnotchosescheduletimezone = <?php echo json_encode(lang('You did not select any time zone'));?>;
	var instagram_all_auto_comment_report_youdidnotselectperodictime = <?php echo json_encode(lang('You did not select any periodic time'));?>;
	var instagram_all_auto_comment_report_youdidnotselectcampaignstarttime = <?php echo json_encode(lang('You did not choose campaign start time'));?>;
	var instagram_all_auto_comment_report_youdidnotselectcampaignendtime = <?php echo json_encode(lang('You did not choose campaign end time'));?>;
	var instagram_all_auto_comment_report_youdidntselectanytemplate = <?php echo json_encode(lang('you did not select any template.'));?>;
	var instagram_all_auto_comment_report_youdidntselectanyoptionyet = <?php echo json_encode(lang('you did not select any option yet.'));?>;
	var instagram_all_auto_comment_report_please_select_comment_between_times = <?php echo json_encode(lang('Please select comment between times.'));?>;
	var instagram_all_auto_comment_report_comment_between_start_time_must_be_less_than_end_time = <?php echo json_encode(lang('Comment between start time must be less than end time.'));?>;
	var instagram_all_auto_comment_report_post_id = <?php echo json_encode(isset($post_id)?$post_id:'0'); ?>;
	var instagram_all_auto_comment_report_page_id = <?php echo json_encode(isset($page_id)?$page_id:'0'); ?>;

	var instagram_auto_comment_template_youdidntselectanyoption = <?php echo json_encode(lang('you did not select any option.'));?>;
	var instagram_auto_comment_template_youdidntprovideallcomment = <?php echo json_encode(lang('You did not provide comment information '));?>;
	var instagram_auto_comment_template_autocomment = <?php echo json_encode(lang('auto comment'));?>;
	var instagram_auto_comment_template_addcomments = <?php echo json_encode(lang('add comments'));?>;
	var instagram_auto_comment_template_please_give_the_following_information_for_post_auto_comment = <?php echo json_encode(lang('Please Give The Following Information For Post Auto Comment'));?>;
	var instagram_auto_comment_template_can_not_delete_from_admin = <?php echo json_encode(lang('You can not delete templates from admin account'));?>;
	var instagram_auto_comment_template_deleted_successfully = <?php echo json_encode(lang('Template has been deleted successfully.'));?>;


	var instagram_template_manager_youdidntprovideallinformation = <?php echo json_encode(lang("you didn't provide all information."));?>;
	var instagram_template_manager_pleaseprovidepostid = <?php echo json_encode(lang('please provide post id.'));?>;
	var instagram_template_manager_alreadyenabled = <?php echo json_encode(lang('already enabled'));?>;
	var instagram_template_manager_thispostidisnotfoundindatabaseorthispostidisnotassociatedwiththepageyouareworking = <?php echo json_encode(lang('This post ID is not found in database or this post ID is not associated with the page you are working.'));?>;
	var instagram_template_manager_enableautoreply = <?php echo json_encode(lang('enable auto reply'));?>;


	var instagram_hash_tag_select_account = <?php echo json_encode(lang('Please select an instagram account'));?>;
	var instagram_hash_tag_provide_hash_tag = <?php echo json_encode(lang('Please provide hash tag'));?>;

	var instagram_selectanaccount = <?php echo json_encode(lang('Please select an account')); ?>;
	var instagram_selectanaccountfirst = <?php echo json_encode(lang('Please select an account first')); ?>;
	var instagram_meta_info_grabber_url = <?php echo json_encode(site_url('ultrapost/text_image_link_video_meta_info_grabber'));?>;

	var selected_global_page_table_id = <?php echo json_encode(session()->get('selected_global_page_table_id'));?>;
	var selected_global_media_type = <?php echo json_encode(isset($media_type) ? $media_type : 'fb');?>;
</script>
