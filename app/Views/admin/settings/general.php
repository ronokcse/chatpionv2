<section class="section">
	<div class="section-header">
		<h1><i class="fas fa-toolbox"></i> <?php echo $page_title; ?></h1>
		<div class="section-header-breadcrumb">
			<div class="breadcrumb-item"><?php echo lang('System'); ?></div>
			<div class="breadcrumb-item active"><a href="<?php echo base_url('admin/settings'); ?>"><?php echo lang('Settings'); ?></a></div>
			<div class="breadcrumb-item"><?php echo $page_title; ?></div>
		</div>
	</div>

	<?php view('admin/theme/message'); ?>

	<?php $save_button = '<div class="card-footer bg-whitesmoke">
	                      <button class="btn btn-primary btn-lg" id="save-btn" type="submit"><i class="fas fa-save"></i> ' . lang('Save') . '</button>
	                      <button class="btn btn-secondary btn-lg float-right" onclick=\'goBack("admin/settings")\' type="button"><i class="fa fa-remove"></i> ' . lang('Cancel') . '</button>
	                    </div>'; ?>

	<form class="form-horizontal text-c" enctype="multipart/form-data" action="<?php echo site_url() . 'admin/general_settings_action'; ?>" method="POST">

		<input type="hidden" name="csrf_token" id="csrf_token" value="<?php echo session()->get('csrf_token_session'); ?>">

		<div class="section-body">
			<div id="output-status"></div>
			<div class="row">
				<div class="col-md-8">
					<div class="card" id="brand">

						<div class="card-header">
							<h4><i class="fas fa-flag"></i> <?php echo lang('Brand'); ?></h4>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label for=""><i class="fa fa-globe"></i> <?php echo lang('Application Name'); ?> </label>
										<input name="product_name" value="<?php echo config('MyConfig')->product_name; ?>" class="form-control" type="text">
										<span class="red"><?php echo form_error('product_name'); ?></span>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label for=""><i class="fa fa-compress"></i> <?php echo lang('Application Short Name'); ?> </label>
										<input name="product_short_name" value="<?php echo config('MyConfig')->product_short_name; ?>" class="form-control" type="text">
										<span class="red"><?php echo form_error('product_short_name'); ?></span>
									</div>
								</div>
							</div>

							<div class="form-group">
								<label for=""><i class="fas fa-tag"></i> <?php echo lang('Slogan'); ?> </label>
								<input name="slogan" value="<?php echo config('MyConfig')->slogan; ?>" class="form-control" type="text">
								<span class="red"><?php echo form_error('slogan'); ?></span>
							</div>

							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label for=""><i class="fa fa-briefcase"></i> <?php echo lang('Company Name'); ?></label>
										<input name="institute_name" value="<?php echo config('MyConfig')->institute_address1; ?>" class="form-control" type="text">
										<span class="red"><?php echo form_error('institute_name'); ?></span>
									</div>
								</div>

								<div class="col-12 col-md-6">
									<div class="form-group">
										<label for=""><i class="fa fa-map-marker"></i> <?php echo lang('Company Address'); ?></label>
										<input name="institute_address" value="<?php echo config('MyConfig')->institute_address2; ?>" class="form-control" type="text">
										<span class="red"><?php echo form_error('institute_address'); ?></span>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label for=""><i class="fa fa-envelope"></i> <?php echo lang('Company Email'); ?> *</label>
										<input name="institute_email" value="<?php echo config('MyConfig')->institute_email; ?>" class="form-control" type="email">
										<span class="red"><?php echo form_error('institute_email'); ?></span>
									</div>
								</div>

								<div class="col-12 col-md-6">
									<div class="form-group">
										<label for=""><i class="fa fa-mobile"></i> <?php echo lang('Company Phone'); ?></label>
										<input name="institute_mobile" value="<?php echo config('MyConfig')->institute_mobile; ?>" class="form-control" type="text">
										<span class="red"><?php echo form_error('institute_mobile'); ?></span>
									</div>
								</div>
							</div>
						</div>
						<?php echo $save_button; ?>
					</div>

					<div class="card" id="preference">
						<div class="card-header">
							<h4><i class="fas fa-tasks"></i> <?php echo lang('Preference'); ?></h4>
						</div>
						<div class="card-body">

							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label for=""><i class="fa fa-language"></i> <?php echo lang('Language'); ?></label>
										<?php
										$select_lan = "english";
										if (config('MyConfig')->language != "") $select_lan = config('MyConfig')->language;
										echo form_dropdown('language', $language_info, $select_lan, 'class="form-control select2" id="language"');  ?>
										<span class="red"><?php echo form_error('language'); ?></span>
									</div>
								</div>

								<div class="col-12 col-md-6">
									<div class="form-group">
										<label for=""><i class="fa fa-clock-o"></i> <?php echo lang('Time Zone');
																					echo " (" . date("Y-m-d H:i:s") . ")"; ?></label>
										<?php $time_zone[''] = lang('Time Zone');
										echo form_dropdown('time_zone', $time_zone, config('MyConfig')->time_zone, 'class="form-control select2" id="time_zone"');  ?>
										<span class="red"><?php echo form_error('time_zone'); ?></span>
									</div>
								</div>
							</div>


							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group">
										<?php
										$is_rtl = config('MyConfig')->is_rtl;
										if ($is_rtl == '') $is_rtl = '0';
										?>
										<label class="custom-switch mt-2">
											<input type="checkbox" name="is_rtl" value="1" class="custom-switch-input" <?php if ($is_rtl == '1') echo 'checked'; ?>>
											<span class="custom-switch-indicator"></span>
											<span class="custom-switch-description"><?php echo lang('RTL'); ?></span>
											<span class="red"><?php echo form_error('is_rtl'); ?></span>
										</label>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group">
										<?php
										$force_https = config('MyConfig')->force_https;
										if ($force_https == '') $force_https = '0';
										?>
										<label class="custom-switch mt-2">
											<input type="checkbox" name="force_https" value="1" class="custom-switch-input" <?php if ($force_https == '1') echo 'checked'; ?>>
											<span class="custom-switch-indicator"></span>
											<span class="custom-switch-description"><?php echo lang('Force HTTPS'); ?>?</span>
											<span class="red"><?php echo form_error('force_https'); ?></span>
										</label>
									</div>
								</div>
							</div>




							<div class="form-group">
								<!-- <label for="email_sending_option"><i class="fa fa-at"></i> <?php echo lang('Email Sending Option'); ?></label>  -->
								<?php
								$email_sending_option = config('MyConfig')->email_sending_option;
								if ($email_sending_option == '') $email_sending_option = 'php_mail';
								?>
								<div class="row">
									<div class="col-12 col-md-6">
										<label class="custom-switch">
											<input type="radio" name="email_sending_option" value="php_mail" class="custom-switch-input" <?php if ($email_sending_option == 'php_mail') echo 'checked'; ?>>
											<span class="custom-switch-indicator"></span>
											<span class="custom-switch-description"><?php echo lang('Use PHP Email Function'); ?></span>
										</label>
									</div>
									<div class="col-12 col-md-6">
										<label class="custom-switch">
											<input type="radio" name="email_sending_option" value="smtp" class="custom-switch-input" <?php if ($email_sending_option == 'smtp') echo 'checked'; ?>>
											<span class="custom-switch-indicator"></span>
											<span class="custom-switch-description"><?php echo lang('Use SMTP Email'); ?>
												&nbsp;:&nbsp;<a href="<?php echo base_url('admin/smtp_settings'); ?>" class="float-right"> <?php echo lang('SMTP Setting'); ?> </a></span>
										</label>
									</div>
								</div>
								<span class="red"><?php echo form_error('email_sending_option'); ?></span>
							</div>

							<div class="row">

								<div class="col-12 col-md-6">
									<div class="form-group">
										<?php
										$enable_signup_form = config('MyConfig')->enable_signup_form;
										if ($enable_signup_form == '') $enable_signup_form = '1';
										?>
										<label class="custom-switch mt-2">
											<input type="checkbox" name="enable_signup_form" value="1" class="custom-switch-input" <?php if ($enable_signup_form == '1') echo 'checked'; ?>>
											<span class="custom-switch-indicator"></span>
											<span class="custom-switch-description"><?php echo lang('Display Signup Page'); ?></span>
											<span class="red"><?php echo form_error('enable_signup_form'); ?></span>
										</label>
									</div>
								</div>

								<div class="col-12 col-md-6">
									<div class="form-group">
										<?php
										$enable_signup_activation = config('MyConfig')->enable_signup_activation;
										if ($enable_signup_activation == '') $enable_signup_activation = '1';
										?>
										<label class="custom-switch mt-2">
											<input type="checkbox" name="enable_signup_activation" value="1" class="custom-switch-input" <?php if ($enable_signup_activation == '1') echo 'checked'; ?>>
											<span class="custom-switch-indicator"></span>
											<span class="custom-switch-description"><?php echo lang('Signup Email Activation'); ?></span>
											<span class="red"><?php echo form_error('enable_signup_activation'); ?></span>
										</label>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group">
										<?php
										$instagram_reply_enable_disable = config('MyConfig')->instagram_reply_enable_disable;
										if ($instagram_reply_enable_disable == '') $instagram_reply_enable_disable = '0';
										?>
										<label class="custom-switch mt-2">
											<input type="checkbox" name="instagram_reply_enable_disable" value="1" class="custom-switch-input" <?php if ($instagram_reply_enable_disable == '1') echo 'checked'; ?>>
											<span class="custom-switch-indicator"></span>
											<span class="custom-switch-description"><?php echo lang('Enable Instagram Features'); ?></span>
											<span class="red"><?php echo form_error('instagram_reply_enable_disable'); ?></span>
										</label>
									</div>
								</div>
							</div>

						</div>
						<?php echo $save_button; ?>
					</div>

					<div class="card" id="logo-favicon">
						<div class="card-header">
							<h4><i class="fas fa-images"></i> <?php echo lang('Logo & Favicon'); ?></h4>
						</div>
						<div class="card-body">

							<div class="row">
								<div class="col-6">
									<label for=""><i class="fas fa-image"></i> <?php echo lang('logo'); ?> (png)</label>
									<div class="custom-file">
										<input type="file" name="logo" class="custom-file-input">
										<label class="custom-file-label"><?php echo lang('Choose File'); ?></label>
										<small><?php echo lang('Max Dimension'); ?> : 700 x 200, <?php echo lang('Max Size'); ?> : 500KB </small>
										<span class="red"> <?php echo session()->get('logo_error');
															session()->remove('logo_error'); ?></span>
									</div>
								</div>
								<div class="col-6 my-auto text-center">
									<img class="img-fluid" src="<?php echo base_url() . 'assets/img/logo.png'; ?>" alt="Logo" />
								</div>
							</div>

							<div class="row">
								<div class="col-6">
									<label for=""><i class="fas fa-portrait"></i> <?php echo lang('Favicon'); ?> (png)</label>
									<div class="custom-file">
										<input type="file" name="favicon" class="custom-file-input">
										<label class="custom-file-label"><?php echo lang('Choose File'); ?></label>
										<small><?php echo lang('Dimension'); ?> : 100 x 100, <?php echo lang('Max Size'); ?> : 50KB </small>
										<span class="red"> <?php echo session()->get('favicon_error');
															session()->remove('favicon_error'); ?></span>
									</div>
								</div>
								<div class="col-6 my-auto text-center">
									<img class="img-fluid" src="<?php echo base_url() . 'assets/img/favicon.png'; ?>" alt="Favicon" style="max-width:50px;" />
								</div>
							</div>
						</div>
						<?php echo $save_button; ?>
					</div>

					<div class="card" id="master-password">
						<div class="card-header">
							<h4><i class="fab fa-keycdn"></i> <?php echo lang('Master Password'); ?></h4>
						</div>
						<div class="card-body">
							<div class="form-group">
								<label for=""><i class="fa fa-key"></i> <?php echo lang('Master Password (will be used for login as user)'); ?></label>
								<input name="master_password" value="******" class="form-control" type="text">
								<span class="red"><?php echo form_error('master_password'); ?></span>
								<div class="text-danger mt-1"><?php echo lang('Set different than admin password.'); ?></div>
							</div>
							<div class="row d-none">
								<div class="col-12 col-md-6">
									<div class="form-group">
										<?php
										$backup_mode = config('MyConfig')->backup_mode;
										if ($backup_mode == '') $backup_mode = '0';
										?>
										<label class="custom-switch mt-2">
											<input type="checkbox" name="backup_mode" value="1" class="custom-switch-input" <?php if ($backup_mode == '1') echo 'checked'; ?>>
											<span class="custom-switch-indicator"></span>
											<span class="custom-switch-description"><?php echo lang('Give access to user to set their own Facebook APP'); ?>?</span>
											<span class="red"><?php echo form_error('backup_mode'); ?></span>
										</label>
									</div>
								</div>
							</div>
						</div>
						<?php echo $save_button; ?>
					</div>
					<div class="card" id="pusher-menu">
						<div class="card-header">
							<h4><i class="fab fa-pushed"></i> <?php echo lang('Pusher App Key'); ?></h4>
						</div>
						<?php $this->config->load('pusher'); ?>
						<div class="card-body">
							<div class="form-group">
								<label for=""><i class="fas fa-id-card"></i> <?php echo lang('Pusher App Id'); ?></label>
								<input name="pusher_app_id" value="<?php echo config('MyConfig')->pusher_app_id; ?>" class="form-control" type="text">
								<span class="red"><?php echo form_error('pusher_app_id'); ?></span>
							</div>
							<div class="form-group">
								<label for=""><i class="fa fa-key"></i> <?php echo lang('Pusher App Key (will be used for livechat message send)'); ?></label>
								<input name="pusher_app_key" value="<?php echo config('MyConfig')->pusher_app_key; ?>" class="form-control" type="text">
								<span class="red"><?php echo form_error('pusher_app_key'); ?></span>
							</div>
							<div class="form-group">
								<label for=""><i class="fas fa-mask"></i> <?php echo lang('Pusher App Secret'); ?></label>
								<input name="pusher_app_secret" value="<?php echo config('MyConfig')->pusher_app_secret; ?>" class="form-control" type="text">
								<span class="red"><?php echo form_error('pusher_app_secret'); ?></span>
							</div>
							<div class="form-group">
								<label for=""><i class="fas fa-mask"></i> <?php echo lang('Pusher Cluster'); ?></label>
								<input name="pusher_cluster" value="<?php echo config('MyConfig')->pusher_cluster; ?>" class="form-control" type="text">
								<span class="red"><?php echo form_error('pusher_cluster'); ?></span>
							</div>
						</div>
						<?php echo $save_button; ?>
					</div>

					<div class="card" id="subscriber">
						<div class="card-header">
							<h4><i class="fas fa-user-circle"></i> <?php echo lang('Subscriber'); ?></h4>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-12 col-md-6 d-none">
									<div class="form-group">
										<label for=""><i class="fa fa-sort-numeric-asc"></i> <?php echo lang('Avatar download limit per cron job'); ?></label>
										<?php
										$messengerbot_subscriber_avatar_download_limit_per_cron_job = config('MyConfig')->messengerbot_subscriber_avatar_download_limit_per_cron_job;
										if ($messengerbot_subscriber_avatar_download_limit_per_cron_job == "") $messengerbot_subscriber_avatar_download_limit_per_cron_job = 25;
										?>
										<input name="messengerbot_subscriber_avatar_download_limit_per_cron_job" value="<?php echo $messengerbot_subscriber_avatar_download_limit_per_cron_job; ?>" class="form-control" type="number" min="1">
										<span class="red"><?php echo form_error('messengerbot_subscriber_avatar_download_limit_per_cron_job'); ?></span>
									</div>
								</div>
								<div class="col-12">
									<div class="form-group">
										<label for=""><i class="fas fa-edit"></i> <?php echo lang('Profile information update limit per cron job'); ?></label>
										<?php
										$messengerbot_subscriber_profile_update_limit_per_cron_job = config('MyConfig')->messengerbot_subscriber_profile_update_limit_per_cron_job;
										if ($messengerbot_subscriber_profile_update_limit_per_cron_job == "") $messengerbot_subscriber_profile_update_limit_per_cron_job = 100;
										?>
										<input name="messengerbot_subscriber_profile_update_limit_per_cron_job" value="<?php echo $messengerbot_subscriber_profile_update_limit_per_cron_job; ?>" class="form-control" type="number" min="1">
										<span class="red"><?php echo form_error('messengerbot_subscriber_profile_update_limit_per_cron_job'); ?></span>
									</div>
								</div>
								<div class="col-12">

									<div class="form-group">
										<?php
										$enable_tracking_subscribers_last_interaction = config('MyConfig')->enable_tracking_subscribers_last_interaction;
										if ($enable_tracking_subscribers_last_interaction == '') $enable_tracking_subscribers_last_interaction = 'yes';
										?>
										<label class="custom-switch mt-2">
											<input type="checkbox" name="enable_tracking_subscribers_last_interaction" value="yes" class="custom-switch-input" <?php if ($enable_tracking_subscribers_last_interaction == 'yes') echo 'checked'; ?>>
											<span class="custom-switch-indicator"></span>
											<span class="custom-switch-description"><?php echo lang('Enable Tracking of Subscribers Last Interaction'); ?></span>
											<span class="red"><?php echo form_error('enable_tracking_subscribers_last_interaction'); ?></span>
										</label>
									</div>

								</div>
							</div>
						</div>
						<?php echo $save_button; ?>
					</div>


					<div class="card" id="persistent-menu">
						<div class="card-header">
							<h4><i class="fas fa-bars"></i> <?php echo lang('Persistent Menu'); ?></h4>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label for=""><i class="fas fa-copyright"></i> <?php echo lang('Copyright text'); ?></label>
										<?php
										$persistent_menu_copyright_text = config('MyConfig')->persistent_menu_copyright_text;
										if ($persistent_menu_copyright_text == "") $persistent_menu_copyright_text = config('MyConfig')->product_name;
										?>
										<input name="persistent_menu_copyright_text" value="<?php echo $persistent_menu_copyright_text; ?>" class="form-control" type="text">
										<span class="red"><?php echo form_error('persistent_menu_copyright_text'); ?></span>
									</div>
								</div>
								<div class="col-12 col-md-6">
									<div class="form-group">
										<label for=""><i class="fa fa-link"></i> <?php echo lang('Copyright URL'); ?></label>
										<?php
										$persistent_menu_copyright_url = config('MyConfig')->persistent_menu_copyright_url;
										if ($persistent_menu_copyright_url == "") $persistent_menu_copyright_url = base_url();
										?>
										<input name="persistent_menu_copyright_url" value="<?php echo $persistent_menu_copyright_url; ?>" class="form-control" type="text">
										<span class="red"><?php echo form_error('persistent_menu_copyright_url'); ?></span>
									</div>
								</div>
							</div>

						</div>
						<?php echo $save_button; ?>
					</div>

					<?php if ($this->is_broadcaster_exist) : ?>
						<div class="card" id="messenger-broadcast">
							<div class="card-header">
								<h4><i class="fas fa-mail-bulk"></i> <?php echo lang('Messenger Broadcast'); ?></h4>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-12 hidden">
										<div class="row">
											<div class="col-12">
												<div class="form-group">
													<label for=""><i class="fa fa-sort-numeric-asc"></i> <?php echo lang('Conversation Broadcast - number of message send per cron job'); ?></label>
													<?php
													$number_of_message_to_be_sent_in_try = config('MyConfig')->number_of_message_to_be_sent_in_try;
													if ($number_of_message_to_be_sent_in_try == "") $number_of_message_to_be_sent_in_try = 10;
													?>
													<input name="number_of_message_to_be_sent_in_try" value="<?php echo $number_of_message_to_be_sent_in_try; ?>" class="form-control" type="number" min="1">
													<span class="red"><?php echo form_error('number_of_message_to_be_sent_in_try'); ?></span>
												</div>
											</div>
											<div class="col-12">
												<div class="form-group">
													<label for=""><i class="fas fa-edit"></i> <?php echo lang('Conversation Broadcast - message sending report update frequency'); ?></label>
													<?php
													$update_report_after_time = config('MyConfig')->update_report_after_time;
													if ($update_report_after_time == "") $update_report_after_time = 5;
													?>
													<input name="update_report_after_time" value="<?php echo $update_report_after_time; ?>" class="form-control" type="number" min="1">
													<span class="red"><?php echo form_error('update_report_after_time'); ?></span>
												</div>
											</div>
											<div class="col-12">
												<div class="form-group">
													<label for=""><i class="far fa-hand-paper"></i> <?php echo lang('Conversation Broadcast - hold after number of errors'); ?></label>
													<?php
													$conversation_broadcast_hold_after_number_of_errors = config('MyConfig')->conversation_broadcast_hold_after_number_of_errors;
													if ($conversation_broadcast_hold_after_number_of_errors == "") $conversation_broadcast_hold_after_number_of_errors = 10;
													?>
													<input name="conversation_broadcast_hold_after_number_of_errors" value="<?php echo $conversation_broadcast_hold_after_number_of_errors; ?>" class="form-control" type="number" min="1">
													<span class="red"><?php echo form_error('conversation_broadcast_hold_after_number_of_errors'); ?></span>
												</div>
											</div>
										</div>
									</div>
									<div class="col-12">
										<div class="row <?php if (!$this->is_broadcaster_exist) echo 'hidden'; ?>">
											<div class="col-12">
												<div class="form-group">
													<label for=""><i class="fa fa-sort-numeric-asc"></i> <?php echo lang('Subscriber Broadcast - number of message send per cron job'); ?></label>
													<?php
													$broadcaster_number_of_message_to_be_sent_in_try = config('MyConfig')->broadcaster_number_of_message_to_be_sent_in_try;
													if ($broadcaster_number_of_message_to_be_sent_in_try == "") $broadcaster_number_of_message_to_be_sent_in_try = 120;
													?>
													<input name="broadcaster_number_of_message_to_be_sent_in_try" value="<?php echo $broadcaster_number_of_message_to_be_sent_in_try; ?>" class="form-control" type="number" min="1">
													<span class="red"><?php echo form_error('broadcaster_number_of_message_to_be_sent_in_try'); ?></span>
												</div>
											</div>
											<div class="col-12">
												<div class="form-group">
													<label for=""><i class="fas fa-edit"></i> <?php echo lang('Subscriber Broadcast - message sending report update frequency'); ?></label>
													<?php
													$broadcaster_update_report_after_time = config('MyConfig')->broadcaster_update_report_after_time;
													if ($broadcaster_update_report_after_time == "") $broadcaster_update_report_after_time = 20;
													?>
													<input name="broadcaster_update_report_after_time" value="<?php echo $broadcaster_update_report_after_time; ?>" class="form-control" type="number" min="1">
													<span class="red"><?php echo form_error('broadcaster_update_report_after_time'); ?></span>
												</div>
											</div>
											<div class="col-12">
												<div class="form-group">
													<label for=""><i class="far fa-hand-paper"></i> <?php echo lang('Subscriber Broadcast - hold after number of errors'); ?></label>
													<?php
													$subscriber_broadcaster_hold_after_number_of_errors = config('MyConfig')->subscriber_broadcaster_hold_after_number_of_errors;

													if ($subscriber_broadcaster_hold_after_number_of_errors == "") $subscriber_broadcaster_hold_after_number_of_errors = 30;
													?>
													<input name="subscriber_broadcaster_hold_after_number_of_errors" value="<?php echo $subscriber_broadcaster_hold_after_number_of_errors; ?>" class="form-control" type="number" min="1">
													<span class="red"><?php echo form_error('subscriber_broadcaster_hold_after_number_of_errors'); ?></span>
												</div>
											</div>
										</div>
									</div>
								</div>





							</div>
							<?php echo $save_button; ?>
						</div>
					<?php endif; ?>

					<div class="card" id="group-posting">
						<div class="card-header">
							<h4><i class="fas fa-share-square"></i> <?php echo lang('Facebook Poster'); ?></h4>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-12">
									<div class="form-group">
										<?php
										$facebook_poster_botenabled_pages = config('MyConfig')->facebook_poster_botenabled_pages;
										if ($facebook_poster_botenabled_pages == '') $facebook_poster_botenabled_pages = '0';
										?>
										<label class="custom-switch mt-2">
											<input type="checkbox" name="facebook_poster_botenabled_pages" value="1" class="custom-switch-input" <?php if ($facebook_poster_botenabled_pages == '1') echo 'checked'; ?>>
											<span class="custom-switch-indicator"></span>
											<span class="custom-switch-description"><?php echo lang('Use only bot connection enabled pages for posting.'); ?></span>
											<span class="red"><?php echo form_error('facebook_poster_botenabled_pages'); ?></span>
										</label>
									</div>
								</div>
							</div>
						</div>
						<?php echo $save_button; ?>
					</div>


					<div class="card" id="email_auto_responder">
						<div class="card-header">
							<h4><i class="fas fa-envelope-open"></i> <?php echo lang('Email Auto Responder'); ?></h4>
						</div>
						<div class="card-body">
							<div class="row mb-3">
								<div class="col-12 col-md-4">
									<ul class="nav nav-pills flex-column" id="myTab4" role="tablist">
										<li class="nav-item">
											<a class="nav-link active" id="mailchimp_content" data-toggle="tab" href="#mailchimp" role="tab" aria-controls="home" aria-selected="true"><?php echo lang('MailChimp Integration'); ?></a>
											<span style="font-size: 12px !important;"><a href="<?php echo base_url('email_auto_responder_integration/mailchimp_list'); ?>" target="_BLANK"><?php echo lang('Add MailChimp API'); ?></a></span>
										</li>
									</ul>
								</div>
								<div class="col-12 col-md-8">
									<div class="tab-content no-padding" id="">

										<div class="tab-pane fade show active" id="mailchimp" role="tabpanel" aria-labelledby="mailchimp_content">
											<div class="form-group">
												<label><i class="fab fa-mailchimp"></i> <?php echo lang('Select MailChimp List where email will be sent when user signup. sign-up-{product short name} will be used as Tag Name in your MailChimp list.'); ?></label>
												<select class="form-control select2" id="mailchimp_list_id" name="mailchimp_list_id[]" multiple="">
													<?php
													echo "<option value='0'>" . lang('Choose a List') . "</option>";
													foreach ($mailchimp_list as $key => $value) {
														echo '<optgroup label="' . addslashes($value['tracking_name']) . '">';
														foreach ($value['data'] as $key2 => $value2) {
															if (in_array($value2['table_id'], $selected_mailchimp_list_ids)) $selected = 'selected';
															else $selected = '';
															echo "<option value='" . $value2['table_id'] . "' " . $selected . ">" . $value2['list_name'] . "</option>";
														}
														echo '</optgroup>';
													} ?>
												</select>
											</div>
										</div>

									</div>
								</div>
							</div>
							<div class="row mb-3">
								<div class="col-12 col-md-4">
									<ul class="nav nav-pills flex-column" id="myTab4" role="tablist">
										<li class="nav-item">
											<a class="nav-link active" id="sendinblue_content" data-toggle="tab" href="#sendinblue" role="tab" aria-controls="home" aria-selected="true"><?php echo lang('Sendinblue Integration'); ?></a>
											<span style="font-size: 12px !important;"><a href="<?php echo base_url('email_auto_responder_integration/sendinblue_list'); ?>" target="_BLANK"><?php echo lang('Add Sendinblue API'); ?></a></span>
										</li>
									</ul>
								</div>
								<div class="col-12 col-md-8">
									<div class="tab-content no-padding" id="">

										<div class="tab-pane fade show active" id="sendinblue" role="tabpanel" aria-labelledby="sendinblue_content">
											<div class="form-group">
												<label><i class="fas fa-atom"></i> <?php echo lang('Select Sendinblue list where email will be sent when user signup.'); ?></label>
												<select class="form-control select2" id="sendinblue_list_id" name="sendinblue_list_id[]" multiple="">
													<?php
													echo "<option value='0'>" . lang('Choose a List') . "</option>";
													foreach ($sendinblue_list as $key => $value) {
														echo '<optgroup label="' . addslashes($value['tracking_name']) . '">';
														foreach ($value['data'] as $key2 => $value2) {
															if (in_array($value2['table_id'], $selected_sendinblue_list_ids)) $selected = 'selected';
															else $selected = '';
															echo "<option value='" . $value2['table_id'] . "' " . $selected . ">" . $value2['list_name'] . "</option>";
														}
														echo '</optgroup>';
													} ?>
												</select>
											</div>
										</div>

									</div>
								</div>
							</div>

							<!-- activecampaign integration -->
							<div class="row">
								<div class="col-12 col-md-4">
									<ul class="nav nav-pills flex-column" id="myTab4" role="tablist">
										<li class="nav-item">
											<a class="nav-link active" id="activecampaign_content" data-toggle="tab" href="#activecampaign" role="tab" aria-controls="home" aria-selected="true"><?php echo lang('Activecampaign Integration'); ?></a>
											<span style="font-size: 12px !important;"><a href="<?php echo base_url('email_auto_responder_integration/activecampaign_list'); ?>" target="_BLANK"><?php echo lang('Add Activecampaign API'); ?></a></span>
										</li>
									</ul>
								</div>
								<div class="col-12 col-md-8">
									<div class="tab-content no-padding" id="">

										<div class="tab-pane fade show active" id="activecampaign" role="tabpanel" aria-labelledby="activecampaign_content">
											<div class="form-group">
												<label><i class="fab fa-buffer"></i> <?php echo lang('Select Activecampaign list where email will be sent when user signup.'); ?></label>
												<select class="form-control select2" id="activecampaign_list_id" name="activecampaign_list_id[]" multiple="">
													<?php
													echo "<option value='0'>" . lang('Choose a List') . "</option>";
													foreach ($activecampaign_list as $key => $value) {
														echo '<optgroup label="' . addslashes($value['tracking_name']) . '">';
														foreach ($value['data'] as $key2 => $value2) {
															if (in_array($value2['table_id'], $selected_activecampaign_list_ids)) $selected = 'selected';
															else $selected = '';
															echo "<option value='" . $value2['table_id'] . "' " . $selected . ">" . $value2['list_name'] . "</option>";
														}
														echo '</optgroup>';
													} ?>
												</select>
											</div>
										</div>

									</div>
								</div>
							</div>

							<!-- mautic integration -->
							<div class="row">
								<div class="col-12 col-md-4">
									<ul class="nav nav-pills flex-column" id="myTab4" role="tablist">
										<li class="nav-item">
											<a class="nav-link active" id="mautic_content" data-toggle="tab" href="#mautic" role="tab" aria-controls="home" aria-selected="true"><?php echo lang('Mautic Integration'); ?></a>
											<span style="font-size: 12px !important;"><a href="<?php echo base_url('email_auto_responder_integration/mautic_list'); ?>" target="_BLANK"><?php echo lang('Add Mautic API'); ?></a></span>
										</li>
									</ul>
								</div>
								<div class="col-12 col-md-8">
									<div class="tab-content no-padding" id="">

										<div class="tab-pane fade show active" id="mautic" role="tabpanel" aria-labelledby="mautic_content">
											<div class="form-group">
												<label><i class="fas fa-mail-bulk"></i> <?php echo lang('Select Mautic list where email will be sent when user signup. sign-up-{product short name} will be used as tag name in your Mautic list.'); ?></label>
												<select class="form-control select2" id="mautic_list_id" name="mautic_list_id[]" multiple="">
													<?php
													echo "<option value='0'>" . lang('Choose a List') . "</option>";
													foreach ($mautic_list as $key => $value) {
														echo '<optgroup label="' . addslashes($value['tracking_name']) . '">';
														foreach ($value['data'] as $key2 => $value2) {
															if (in_array($value2['table_id'], $selected_mautic_list_ids)) $selected = 'selected';
															else $selected = '';
															echo "<option value='" . $value2['table_id'] . "' " . $selected . ">" . $value2['list_name'] . "</option>";
														}
														echo '</optgroup>';
													} ?>
												</select>
											</div>
										</div>

									</div>
								</div>
							</div>

							<!-- acelle integration -->
							<div class="row">
								<div class="col-12 col-md-4">
									<ul class="nav nav-pills flex-column" id="myTab4" role="tablist">
										<li class="nav-item">
											<a class="nav-link active" id="acelle_content" data-toggle="tab" href="#acelle" role="tab" aria-controls="home" aria-selected="true"><?php echo lang('Acelle Integration'); ?></a>
											<span style="font-size: 12px !important;"><a href="<?php echo base_url('email_auto_responder_integration/acelle_list'); ?>" target="_BLANK"><?php echo lang('Add Acelle API'); ?></a></span>
										</li>
									</ul>
								</div>
								<div class="col-12 col-md-8">
									<div class="tab-content no-padding" id="">

										<div class="tab-pane fade show active" id="acelle" role="tabpanel" aria-labelledby="acelle_content">
											<div class="form-group">
												<label><i class="fas fa-box-open"></i> <?php echo lang('Select Acelle list where email will be sent when user signup.'); ?></label>
												<select class="form-control select2" id="acelle_list_id" name="acelle_list_id[]" multiple="">
													<?php
													echo "<option value='0'>" . lang('Choose a List') . "</option>";
													foreach ($acelle_list as $key => $value) {
														echo '<optgroup label="' . addslashes($value['tracking_name']) . '">';
														foreach ($value['data'] as $key2 => $value2) {
															if (in_array($value2['table_id'], $selected_acelle_list_ids)) $selected = 'selected';
															else $selected = '';
															echo "<option value='" . $value2['table_id'] . "' " . $selected . ">" . $value2['list_name'] . "</option>";
														}
														echo '</optgroup>';
													} ?>
												</select>
											</div>
										</div>

									</div>
								</div>
							</div>

							<!-- Google Sheet integration -->
							<?php if ( $this->basic->is_exist("add_ons",array("project_id"=>70)) && (session()->get('user_type') == 'Admin' || in_array(351, $this->module_access))){ ?>
								<div class="row">
									<div class="col-12 col-md-4">
										<ul class="nav nav-pills flex-column" id="myTab4" role="tablist">
											<li class="nav-item">
												<a class="nav-link active" id="acelle_content" data-toggle="tab" href="#acelle" role="tab" aria-controls="home" aria-selected="true"><?php echo lang('Google Sheet Integration'); ?></a>
												<span style="font-size: 12px !important;"><a href="<?php echo base_url('google_sheet'); ?>" target="_BLANK"><?php echo lang('Google Sheet Api'); ?></a></span>
											</li>
										</ul>
									</div>
									<div class="col-12 col-md-8">
										<div class="tab-content no-padding" id="">

											<div class="tab-pane fade show active" id="google_sheet" role="tabpanel" aria-labelledby="acelle_content">
												<div class="form-group">
													<label><i class="fas fa-box-open"></i> <?php echo lang('Select Google Shee list where data will be append when user signup.'); ?></label>
													<select class="form-control select2" id="google_sheet_list" name="google_sheet_list[]" multiple="">

													<?php
														echo "<option value='0'>" . lang('Choose a List') . "</option>";
														foreach ($google_sheet_list as $email => $sheets) {
															echo '<optgroup label="' . addslashes($email) . '">';
															foreach ($sheets as $sheet) {
																$sheet_tab_names = explode(',', $sheet['sheet_names']);

																foreach ($sheet_tab_names as $sheet_tab) {
																	$selected = '';
																	if (!empty($google_sheet_selected_value)) {
																		if(in_array($sheet['id'].'-'.$sheet_tab, $google_sheet_selected_value)) {
																			$selected = 'selected';
																		}
																	}
																	echo "<option value='".$sheet['id']."-".$sheet_tab."' ".$selected.">".addslashes($sheet['name']) . " [" . addslashes($sheet_tab)."]</option>";
																}
															}
															echo '</optgroup>';
														}
													?>
													</select>
												</div>
											</div>

										</div>
									</div>
								</div>
							<?php } ?>
						</div>
						<?php echo $save_button; ?>
					</div>

					<?php if (session()->get('license_type') == 'double') { ?>
						<div class="card" id="support-desk">
							<div class="card-header">
								<h4><i class="fas fa-headset"></i> <?php echo lang('Support Desk'); ?></h4>
							</div>
							<div class="card-body">
								<div class="form-group">
									<?php
									$enable_support = config('MyConfig')->enable_support;
									if ($enable_support == '') $enable_support = '1';
									?>
									<label class="custom-switch mt-2">
										<input type="checkbox" name="enable_support" value="1" class="custom-switch-input" <?php if ($enable_support == '1') echo 'checked'; ?>>
										<span class="custom-switch-indicator"></span>
										<span class="custom-switch-description"><?php echo lang('Enable Support Desk for Users'); ?></span>
										<span class="red"><?php echo form_error('enable_support'); ?></span>
									</label>
								</div>
							</div>
							<?php echo $save_button; ?>
						</div>
					<?php } ?>

					<div class="card" id="file-upload">
						<div class="card-header">
							<h4><i class="fas fa-cloud-upload-alt"></i> <?php echo lang('File Upload'); ?></h4>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-12 col-md-4">
									<ul class="nav nav-pills flex-column" id="myTab4" role="tablist">
										<li class="nav-item">
											<a class="nav-link active" id="facebook_poster_content" data-toggle="tab" href="#facebook_poster" role="tab" aria-controls="home" aria-selected="true"><?php echo lang('Facebook Poster'); ?></a>
										</li>

										<li class="nav-item">
											<a class="nav-link" id="auto_reply_content" data-toggle="tab" href="#auto_reply_up" role="tab" aria-controls="profile" aria-selected="false"><?php echo lang('Auto Reply'); ?></a>
										</li>

										<li class="nav-item">
											<a class="nav-link" id="comboposter_content" data-toggle="tab" href="#comboposter" role="tab" aria-controls="contact" aria-selected="false"><?php echo lang('Comboposter'); ?></a>
										</li>

										<li class="nav-item hidden">
											<a class="nav-link" id="vidcaster_content" data-toggle="tab" href="#vidcaster" role="tab" aria-controls="contact" aria-selected="false"><?php echo lang('Vidcaster Live'); ?></a>
										</li>

										<li class="nav-item">
											<a class="nav-link" id="messenger_content" data-toggle="tab" href="#messenger_bot" role="tab" aria-controls="contact" aria-selected="false"><?php echo lang('Messenger Bot') ?></a>
										</li>

									</ul>
								</div>
								<div class="col-12 col-md-8">
									<div class="tab-content no-padding" id="myTab2Content">

										<div class="tab-pane fade show active" id="facebook_poster" role="tabpanel" aria-labelledby="facebook_poster_content">
											<div class="form-group">
												<label for=""><i class="fas fa-image"></i> <?php echo lang('Image Upload Limit (MB)'); ?></label>
												<?php
												$facebook_poster_image_upload_limit = config('MyConfig')->facebook_poster_image_upload_limit;
												if ($facebook_poster_image_upload_limit == "") $facebook_poster_image_upload_limit = 1;
												?>
												<input name="facebook_poster_image_upload_limit" value="<?php echo $facebook_poster_image_upload_limit; ?>" class="form-control" type="number" min="1">

												<span class="red"><?php echo form_error('facebook_poster_image_upload_limit'); ?></span>
											</div>

											<div class="form-group">
												<label for=""><i class="fas fa-video"></i> <?php echo lang('Video Upload Limit (MB)'); ?></label>
												<?php
												$facebook_poster_video_upload_limit = config('MyConfig')->facebook_poster_video_upload_limit;
												if ($facebook_poster_video_upload_limit == "") $facebook_poster_video_upload_limit = 10;
												?>
												<input name="facebook_poster_video_upload_limit" value="<?php echo $facebook_poster_video_upload_limit; ?>" class="form-control" type="number" min="1">

												<span class="red"><?php echo form_error('facebook_poster_video_upload_limit'); ?></span>
											</div>
										</div>

										<div class="tab-pane fade" id="auto_reply_up" role="tabpanel" aria-labelledby="auto_reply_content">
											<div class="form-group">
												<label for=""><i class="fas fa-image"></i> <?php echo lang('Image Upload Limit (MB)'); ?></label>
												<?php
												$autoreply_image_upload_limit = config('MyConfig')->autoreply_image_upload_limit;
												if ($autoreply_image_upload_limit == "") $autoreply_image_upload_limit = 1;
												?>
												<input name="autoreply_image_upload_limit" value="<?php echo $autoreply_image_upload_limit; ?>" class="form-control" type="number" min="1">

												<span class="red"><?php echo form_error('autoreply_image_upload_limit'); ?></span>
											</div>

											<div class="form-group">
												<label for=""><i class="fas fa-video"></i> <?php echo lang('Video Upload Limit (MB)'); ?></label>
												<?php
												$autoreply_video_upload_limit = config('MyConfig')->autoreply_video_upload_limit;
												if ($autoreply_video_upload_limit == "") $autoreply_video_upload_limit = 3;
												?>
												<input name="autoreply_video_upload_limit" value="<?php echo $autoreply_video_upload_limit; ?>" class="form-control" type="number" min="1">

												<span class="red"><?php echo form_error('autoreply_video_upload_limit'); ?></span>
											</div>
										</div>

										<div class="tab-pane fade" id="comboposter" role="tabpanel" aria-labelledby="comboposter_content">
											<div class="form-group">
												<label for=""><i class="fas fa-image"></i> <?php echo lang('Image Upload Limit (MB)'); ?></label>
												<?php
												$comboposter_image_upload_limit = config('MyConfig')->comboposter_image_upload_limit;
												if ($comboposter_image_upload_limit == "") $comboposter_image_upload_limit = 1;
												?>
												<input name="comboposter_image_upload_limit" value="<?php echo $comboposter_image_upload_limit; ?>" class="form-control" type="number" min="1">

												<span class="red"><?php echo form_error('comboposter_image_upload_limit'); ?></span>
											</div>

											<div class="form-group">
												<label for=""><i class="fas fa-video"></i> <?php echo lang('Video Upload Limit (MB)'); ?></label>
												<?php
												$comboposter_video_upload_limit = config('MyConfig')->comboposter_video_upload_limit;
												if ($comboposter_video_upload_limit == "") $comboposter_video_upload_limit = 10;
												?>
												<input name="comboposter_video_upload_limit" value="<?php echo $comboposter_video_upload_limit; ?>" class="form-control" type="number" min="1">

												<span class="red"><?php echo form_error('comboposter_video_upload_limit'); ?></span>
											</div>
										</div>

										<div class="tab-pane fade" id="vidcaster" role="tabpanel" aria-labelledby="vidcaster_content">
											<div class="form-group">
												<label for=""><i class="fas fa-image"></i> <?php echo lang('Image Upload Limit (MB)'); ?></label>
												<?php
												$vidcaster_image_upload_limit = config('MyConfig')->vidcaster_image_upload_limit;
												if ($vidcaster_image_upload_limit == "") $vidcaster_image_upload_limit = 1;
												?>
												<input name="vidcaster_image_upload_limit" value="<?php echo $vidcaster_image_upload_limit; ?>" class="form-control" type="number" min="1">

												<span class="red"><?php echo form_error('vidcaster_image_upload_limit'); ?></span>
											</div>

											<div class="form-group">
												<label for=""><i class="fas fa-video"></i> <?php echo lang('Video Upload Limit (MB)'); ?></label>
												<?php
												$vidcaster_video_upload_limit = config('MyConfig')->vidcaster_video_upload_limit;
												if ($vidcaster_video_upload_limit == "") $vidcaster_video_upload_limit = 30;
												?>
												<input name="vidcaster_video_upload_limit" value="<?php echo $vidcaster_video_upload_limit; ?>" class="form-control" type="number" min="1">

												<span class="red"><?php echo form_error('vidcaster_video_upload_limit'); ?></span>
											</div>
										</div>
										<div class="tab-pane fade" id="messenger_bot" role="tabpanel" aria-labelledby="messenger_content">
											<div class="form-group">
												<label for=""><i class="fas fa-image"></i> <?php echo lang('Image Upload Limit (MB)'); ?></label>
												<?php
												$messengerbot_image_upload_limit = config('MyConfig')->messengerbot_image_upload_limit;
												if ($messengerbot_image_upload_limit == "") $messengerbot_image_upload_limit = 1;
												?>
												<input name="messengerbot_image_upload_limit" value="<?php echo $messengerbot_image_upload_limit; ?>" class="form-control" type="number" min="1">

												<span class="red"><?php echo form_error('messengerbot_image_upload_limit'); ?></span>
											</div>

											<div class="form-group">
												<label for=""><i class="fas fa-video"></i> <?php echo lang('Video Upload Limit (MB)'); ?></label>
												<?php
												$messengerbot_video_upload_limit = config('MyConfig')->messengerbot_video_upload_limit;
												if ($messengerbot_video_upload_limit == "") $messengerbot_video_upload_limit = 5;
												?>
												<input name="messengerbot_video_upload_limit" value="<?php echo $messengerbot_video_upload_limit; ?>" class="form-control" type="number" min="1">

												<span class="red"><?php echo form_error('messengerbot_video_upload_limit'); ?></span>
											</div>

											<div class="form-group">
												<label for=""><i class="fas fa-headset"></i> <?php echo lang('Audio Upload Limit (MB)'); ?></label>
												<?php
												$messengerbot_audio_upload_limit = config('MyConfig')->messengerbot_audio_upload_limit;
												if ($messengerbot_audio_upload_limit == "") $messengerbot_audio_upload_limit = 3;
												?>
												<input name="messengerbot_audio_upload_limit" value="<?php echo $messengerbot_audio_upload_limit; ?>" class="form-control" type="number" min="1">

												<span class="red"><?php echo form_error('messengerbot_audio_upload_limit'); ?></span>
											</div>

											<div class="form-group">
												<label for=""><i class="fas fa-file"></i> <?php echo lang('File Upload Limit (MB)'); ?></label>
												<?php
												$messengerbot_file_upload_limit = config('MyConfig')->messengerbot_file_upload_limit;
												if ($messengerbot_file_upload_limit == "") $messengerbot_file_upload_limit = 2;
												?>
												<input name="messengerbot_file_upload_limit" value="<?php echo $messengerbot_file_upload_limit; ?>" class="form-control" type="number" min="1">

												<span class="red"><?php echo form_error('messengerbot_file_upload_limit'); ?></span>
											</div>
										</div>

									</div>
								</div>
							</div>


						</div>
						<?php echo $save_button; ?>
					</div>

					<div class="card" id="junk_data">
						<div class="card-header">
							<h4><i class="fas fa-trash-alt"></i> <?php echo lang('Junk Data Deletion'); ?></h4>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-12">
									<div class="form-group">
										<label for=""><i class="fa fa-calendar"></i> <?php echo lang('Delete broadcasting / auto-reply report data, log data after how many days?'); ?></label>
										<?php
										$delete_junk_data_after_how_many_days = config('MyConfig')->delete_junk_data_after_how_many_days;
										if ($delete_junk_data_after_how_many_days == "") $delete_junk_data_after_how_many_days = 30;
										?>
										<input name="delete_junk_data_after_how_many_days" value="<?php echo $delete_junk_data_after_how_many_days; ?>" class="form-control" type="number" min="1">
										<span class="red"><?php echo form_error('delete_junk_data_after_how_many_days'); ?></span>
									</div>
								</div>
								<div class="col-12">
									<div class="form-group">
										<label for=""><i class="fa fa-calendar"></i> <?php echo lang('Delete live chat messages older than how many days?'); ?></label>
										<?php
										$delete_livechat_old_message_day = config('MyConfig')->delete_livechat_old_message_day;
										if ($delete_livechat_old_message_day == "") $delete_livechat_old_message_day = 60;
										?>
										<input name="delete_livechat_old_message_day" value="<?php echo $delete_livechat_old_message_day; ?>" class="form-control" type="number" min="1">
										<span class="red"><?php echo form_error('delete_livechat_old_message_day'); ?></span>
									</div>
								</div>
							</div>

						</div>
						<?php echo $save_button; ?>
					</div>

					<?php if ($this->basic->is_exist("add_ons", array("project_id" => 41))) : ?>
						<div class="card" id="fb_live">
							<div class="card-header">
								<h4><i class="fas fa-tv"></i> <?php echo lang('Facebook Live Streaming'); ?></h4>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-12">
										<div class="form-group">
											<label for=""><i class="fas fa-map-signs"></i> <?php echo lang('FFMPEG path'); ?></label>
											<?php if (config('MyConfig')->ffmpeg_path == "") $ffmpeg_path = "ffmpeg";
											else $ffmpeg_path = config('MyConfig')->ffmpeg_path; ?>
											<input name="ffmpeg_path" value="<?php echo $ffmpeg_path; ?>" class="form-control" type="text">
											<small><?php echo lang('It can be different like in many server like this'); ?> : <b>/usr/local/bin/ffmpeg</b></small>
											<span class="red"><?php echo form_error('ffmpeg_path'); ?></span>
										</div>
									</div>

									<div class="col-12">
										<div class="form-group">
											<label for=""><i class="fas fa-expand-arrows-alt"></i> <?php echo lang('Maximum simultaneous live stream'); ?></label>
											<?php if (config('MyConfig')->maximum_simultaneous_live_stream == "") $maximum_simultaneous_live_stream = 10;
											else $maximum_simultaneous_live_stream = config('MyConfig')->maximum_simultaneous_live_stream; ?>
											<input name="maximum_simultaneous_live_stream" value="<?php echo $maximum_simultaneous_live_stream; ?>" class="form-control" type="number" min="1">
											<span class="red"><?php echo form_error('maximum_simultaneous_live_stream'); ?></span>
										</div>
									</div>

									<div class="col-12">
										<div class="form-group">
											<label for=""><i class="far fa-clock"></i> <?php echo lang('maximum length of live stream'); ?> (<?php echo lang('hour'); ?>)</label>
											<?php if (config('MyConfig')->maximum_length_of_live_stream == "") $maximum_length_of_live_stream = 1;
											else $maximum_length_of_live_stream = config('MyConfig')->maximum_length_of_live_stream; ?>
											<input name="maximum_length_of_live_stream" value="<?php echo $maximum_length_of_live_stream; ?>" class="form-control" type="number" min="1">
											<span class="red"><?php echo form_error('maximum_length_of_live_stream'); ?></span>
										</div>
									</div>

									<div class="col-12">
										<div class="form-group">
											<label for=""><i class="fas fa-save"></i> <?php echo lang('maximum allowed video size'); ?> (MB)</label>
											<?php if (config('MyConfig')->allowed_video_size == "") $allowed_video_size = 200;
											else $allowed_video_size = config('MyConfig')->allowed_video_size; ?>
											<input name="allowed_video_size" value="<?php echo $allowed_video_size; ?>" class="form-control" type="number" min="1">
											<span class="red"><?php echo form_error('allowed_video_size'); ?></span>
										</div>
									</div>

								</div>
							</div>
							<?php echo $save_button; ?>
						</div>
					<?php endif; ?>

					<div class="card" id="server-status">
						<div class="card-header">
							<h4><i class="fas fa-server"></i> <?php echo lang('Server Status'); ?></h4>
						</div>
						<div class="card-body">
							<?php

							$sql = "SHOW VARIABLES;";
							$mysql_variables = $this->basic->execute_query($sql);
							$variables_array_format = array();
							foreach ($mysql_variables as $my_var) {
								$variables_array_format[$my_var['Variable_name']] = $my_var['Value'];
							}
							$disply_index = array("version", "innodb_version", "innodb_log_file_size", "wait_timeout", "max_connections", "connect_timeout", "max_allowed_packet", "innodb_lock_wait_timeout");

							$list1 = $list2 = "";
							$make_dir = (!function_exists('mkdir')) ? lang('Disabled') : lang('Enabled');
							$zip_archive = (!class_exists('ZipArchive')) ? lang('Disabled') : lang('Enabled');
							$list1 .= "<li class='list-group-item'><b>mkdir</b> : " . $make_dir . "</li>";
							$list2 .= "<li class='list-group-item'><b>ZipArchive</b> : " . $zip_archive . "</li>";

							if (function_exists('curl_version'))	$curl = "Enabled";
							else $curl = "Disabled";

							if (function_exists('mb_detect_encoding')) $mbstring = "Enabled";
							else $mbstring = "Disabled";

							if (function_exists('set_time_limit')) $set_time_limit = "Enabled";
							else $set_time_limit = "Disabled";

							if (function_exists('exec')) $exec = "Enabled";
							else $exec = "Disabled";

							$list2 .= "<li class='list-group-item'><b>curl</b> : " . $curl . "</li>";
							$list1 .= "<li class='list-group-item'><b>exec</b> : " . $exec . "</li>";
							$list2 .= "<li class='list-group-item'><b>mb_detect_encoding</b> : " . $mbstring . "</li>";
							$list2 .= "<li class='list-group-item'><b>set_time_limit</b> : " . $set_time_limit . "</li>";


							if (function_exists('ini_get')) {
								if (ini_get('safe_mode'))
									$safe_mode = "ON, please set safe_mode=off";
								else $safe_mode = "OFF";

								if (ini_get('open_basedir') == "")
									$open_basedir = "No Value";
								else $open_basedir = "Has value";

								if (ini_get('allow_url_fopen'))
									$allow_url_fopen = "TRUE";
								else $allow_url_fopen = "FALSE";

								$list1 .= "<li class='list-group-item'><b>safe_mode</b> : " . $safe_mode . "</li>";
								$list2 .= "<li class='list-group-item'><b>open_basedir</b> : " . $open_basedir . "</li>";
								$list1 .= "<li class='list-group-item'><b>allow_url_fopen</b> : " . $allow_url_fopen . "</li>";
								$list1 .= "<li class='list-group-item'><b>upload_max_filesize</b> : " . ini_get('upload_max_filesize') . "</li>";
								$list1 .= "<li class='list-group-item'><b>max_input_time</b> : " . ini_get('max_input_time') . "</li>";
								$list2 .= "<li class='list-group-item'><b>post_max_size</b> : " . ini_get('post_max_size') . "</li>";
								$list2 .= "<li class='list-group-item'><b>max_execution_time</b> : " . ini_get('max_execution_time') . "</li>";
							}

							$php_version = (function_exists('ini_get') && phpversion() != FALSE) ? phpversion() : ""; ?>

							<div class="row">
								<div class="col-12 col-lg-6">
									<ul class="list-group">
										<li class='list-group-item active'>PHP</li>
										<li class='list-group-item'><b>PHP version : </b> <?php echo $php_version; ?></li>
										<?php echo $list1; ?>
									</ul>
								</div>
								<div class="col-12 col-lg-6">
									<ul class="list-group">
										<li class='list-group-item active'>PHP</li>
										<?php echo $list2; ?>
									</ul>
								</div>
								<div class="col-12">
									<br>
									<ul class="list-group">
										<li class='list-group-item active'>MySQL</li>

										<?php
										foreach ($disply_index as $value) {
											if (isset($variables_array_format[$value]))
												echo "<li class='list-group-item'><b>" . $value . "</b> : " . $variables_array_format[$value] . "</li>";
										}
										?>
									</ul>
								</div>

								<?php if ($this->basic->is_exist("add_ons", array("project_id" => 41))) : ?>
									<div class="col-12">
										<br>
										<ul class="list-group">
											<li class='list-group-item active'>FFMPEG</li>
											<?php
											if (function_exists('ini_get')) {
												$ffmpeg_path = config('MyConfig')->ffmpeg_path;

												if ($ffmpeg_path == '') $ffmpeg_path = "ffmpeg";
												echo "<li class='list-group-item'><b>exec()</b> function available : ";
												if (function_exists('exec')) echo "<i class='fa fa-check-circle green'></i> yes";
												else echo "<i class='fa fa-remove red'></i> no";
												echo "<li class='list-group-item'><b>FFMPEG version : </b>";

												if (!function_exists('exec')) echo "unknown</li>";
												else {
													$a = exec($ffmpeg_path . " -version -loglevel error 2>&1", $error_message);
													if ($a != '') echo $a . "</li>";
													echo "<li class='list-group-item'>";
													if (isset($error_message) && !empty($error_message))
														echo '<pre class="language-javascript text-left"><code class="dlanguage-javascript"><span class="token keyword">FFMPEG Info :';
													print_r($error_message);
													echo '</span></code></pre>';
													echo "</li>";
												}
											}

											?>
										</ul>
									</div>
								<?php endif; ?>

							</div>

						</div>
					</div>
				</div>

				<div class="col-md-4 d-none d-sm-block">
					<div class="sidebar-item">
						<div class="make-me-sticky">
							<div class="card">
								<div class="card-header">
									<h4><i class="fas fa-columns"></i> <?php echo lang('Sections'); ?></h4>
								</div>
								<div class="card-body">
									<ul class="nav nav-pills flex-column settings_menu">
										<li class="nav-item"><a href="#brand" class="nav-link"><i class="fas fa-flag"></i> <?php echo lang('Brand'); ?></a></li>
										<li class="nav-item"><a href="#preference" class="nav-link"><i class="fas fa-tasks"></i> <?php echo lang('Preference'); ?></a></li>
										<li class="nav-item"><a href="#logo-favicon" class="nav-link"><i class="fas fa-images"></i> <?php echo lang('Logo & Favicon'); ?></a></li>
										<li class="nav-item"><a href="#master-password" class="nav-link"><i class="fab fa-keycdn"></i> <?php echo lang('Master Password'); ?></a></li>
										<li class="nav-item"><a href="#pusher-menu" class="nav-link"><i class="fab fa-pushed"></i> <?php echo lang('Pusher'); ?></a></li>
										<li class="nav-item"><a href="#subscriber" class="nav-link"><i class="fas fa-user-circle"></i> <?php echo lang('Subscriber'); ?></a></li>

										<li class="nav-item"><a href="#persistent-menu" class="nav-link"><i class="fas fa-bars"></i> <?php echo lang('Persistent Menu'); ?></a></li>

										<?php if ($this->is_broadcaster_exist) : ?>
											<li class="nav-item"><a href="#messenger-broadcast" class="nav-link"><i class="fas fa-mail-bulk"></i> <?php echo lang('Messenger Broadcast'); ?></a></li>
										<?php endif; ?>

										<li class="nav-item"><a href="#group-posting" class="nav-link"><i class="fas fa-share-square"></i> <?php echo lang('Facebook Poster'); ?></a></li>

										<li class="nav-item"><a href="#email_auto_responder" class="nav-link"><i class="fas fa-envelope-open"></i> <?php echo lang('Email Auto Responder'); ?></a></li>

										<?php if (session()->get('license_type') == 'double') { ?>
											<li class="nav-item"><a href="#support-desk" class="nav-link"><i class="fas fa-headset"></i> <?php echo lang('Support Desk'); ?></a></li>
										<?php } ?>

										<li class="nav-item"><a href="#file-upload" class="nav-link"><i class="fas fa-cloud-upload-alt"></i> <?php echo lang('File Upload'); ?></a></li>
										<li class="nav-item"><a href="#junk_data" class="nav-link"><i class="fas fa-trash-alt"></i> <?php echo lang('Delete Junk Data'); ?></a></li>

										<?php if ($this->basic->is_exist("add_ons", array("project_id" => 41))) : ?>
											<li class="nav-item"><a href="#fb_live" class="nav-link"><i class="fas fa-tv"></i> <?php echo lang('Facebook Live Streaming'); ?></a></li>
										<?php endif; ?>

										<li class="nav-item"><a href="#server-status" class="nav-link"><i class="fas fa-server"></i> <?php echo lang('Server Status'); ?></a></li>
									</ul>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</section>


<script type="text/javascript">
	$('document').ready(function() {
		$(".settings_menu a").click(function() {
			$(".settings_menu a").removeClass("active");
			$(this).addClass("active");
		});
	});
</script>
<script>
	$('[data-toggle="popover"]').popover();
	$('[data-toggle="popover"]').on('click', function(e) {
		e.preventDefault();
		return true;
	});
</script>