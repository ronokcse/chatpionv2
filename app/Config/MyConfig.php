<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class MyConfig extends BaseConfig
{
    public string $default_page_url = 'page/blank';
    public string $product_name = 'ChatPion';
    public string $product_short_name = 'ChatPion';
    public string $product_version = '9.2.6';
    public string $slogan = '#1 Messenger Marketing Solutions for Facebook';

    public string $institute_address1 = 'Xerone IT';
    public string $institute_address2 = 'Holding No. 127, 1st Floor, Gonok Para';
    public string $institute_email = 'mostofa.ru22@gmail.com';
    public string $institute_mobile = '01729853645';

    public string $time_zone = 'Asia/Dhaka';
    public string $language = 'english';

    public string $facebook_poster_image_upload_limit = '5';
    public string $facebook_poster_video_upload_limit = '15';
    public string $autoreply_image_upload_limit = '10';
    public string $autoreply_video_upload_limit = '10';
    public string $comboposter_image_upload_limit = '5';
    public string $comboposter_video_upload_limit = '8';
    public string $vidcaster_image_upload_limit = '3';
    public string $vidcaster_video_upload_limit = '30';
    public string $messengerbot_image_upload_limit = '2';
    public string $messengerbot_video_upload_limit = '15';
    public string $messengerbot_audio_upload_limit = '1';
    public string $messengerbot_file_upload_limit = '15';

    public string $email_sending_option = 'smtp';
    public string $enable_tracking_subscribers_last_interaction = 'no';
    public string $is_rtl = '0';
    public string $force_https = '0';
    public string $enable_signup_form = '1';
    public string $instagram_reply_enable_disable = '1';
    public string $enable_signup_activation = '0';
    public string $enable_support = '1';
    public string $developer_access = '0';
    public string $backup_mode = '0';

    public string $master_password = '';

    public string $messengerbot_subscriber_avatar_download_limit_per_cron_job = '25';
    public string $messengerbot_subscriber_profile_update_limit_per_cron_job = '100';

    public string $number_of_message_to_be_sent_in_try = '10';
    public string $update_report_after_time = '5';
    public string $conversation_broadcast_hold_after_number_of_errors = '10';
    public string $broadcaster_number_of_message_to_be_sent_in_try = '120';
    public string $broadcaster_update_report_after_time = '20';
    public string $subscriber_broadcaster_hold_after_number_of_errors = '30';

    public string $sms_api_access = '';
    public string $email_api_access = '';
    public string $enable_open_rate = '';
    public string $enable_click_rate = '';

    public string $persistent_menu_copyright_text = 'XeroChat';
    public string $persistent_menu_copyright_url = 'http://localhost/ezsoci/';

    public string $facebook_poster_group_enable_disable = '';
    public string $facebook_poster_botenabled_pages = '1';
    public string $central_webhook_verify_token = '2069446245';
    public bool $sess_use_database = false;
    public string $sess_table_name = 'ci_sessions';
    public string $mail_service_id = '{"mailchimp":[],"sendinblue":["64","63"],"activecampaign":["68","73"],"mautic":["97","96"],"acelle":["122"],"google_sheet":["110-Sheet1","111-Sheet1"]}';
    public string $delete_junk_data_after_how_many_days = '32';
    public string $delete_livechat_old_message_day = '60';
    
    // Pusher Configuration
    public string $pusher_app_id = '1899396';
    public string $pusher_app_key = 'd11176dd83ffbcd62d65';
    public string $pusher_app_secret = '1f6592a7bec59c076a0e';
    public string $pusher_cluster = 'ap1';
    public bool $pusher_debug = false;
    
    public string $current_theme = 'modern';
    public string $theme_front = 'purple';
    public string $display_landing_page = '1';
    public string $display_review_block = '1';
    public string $display_video_block = '1';
    public string $facebook = 'https://www.facebook.com/mostofa.ru';
    public string $twitter = 'https://twitter.com/mostofa.ru';
    public string $linkedin = 'https://www.linkedin.com/';
    public string $youtube = 'https://www.youtube.com/';
    public string $promo_video = 'https://www.youtube.com/watch?v=OObcKRImhi0';
    public string $customer_review_video = 'https://www.youtube.com/watch?v=gmWVpo1vmhw';
    public array $customer_review = [
        '0' => [
            'Ava Adams',
            'Artist',
            'assets/site_new/img/client/thumb-1.jpg',
            'Lorem ipsum sit amet, consectetur adipisicing elit. cing elit dsasa asas asaewfsf asas Lorem ipsum sit amet, consectetur adipisicing elit.',
        ],
        '1' => [
            'John Roger',
            'Designer',
            'assets/site_new/img/client/thumb-2.jpg',
            'Lorem ipsum sit amet, consectetur adipisicing elit. Harum maxime voluptate optio saepe omnis eum dolor.Vitae quidem nostrum necessitatibus distinctio, quis consequuntur similique.Lorem ipsum sit amet, consectetur adipisicing elit. Harum maxime voluptate optio saepe omnis eum dolor.Vitae quidem nostrum necessitatibus distinctio, quis consequuntur similique.',
        ],
        '2' => [
            'Albert Einstein',
            'Digital Scientist',
            'assets/site_new/img/client/thumb-3.jpg',
            'Lorem ipsum sit amet, consectetur adipisicing elit. Harum maxime voluptate optio saepe omnis eum dolor.Vitae quidem nostrum necessitatibus distinctio, quis consequuntur similique.Vitae quidem nostrum necessitatibus distinctio, quis consequuntur similique.',
        ],
    ];
    public array $custom_video = [
        '0' => [
            'assets/site_new/img/tutorial/blog-1.jpg',
            'Facebook App Configuration',
            'https://www.youtube.com/watch?v=6jiNS_4CEug',
        ],
        '1' => [
            'assets/site_new/img/tutorial/blog-2.jpg',
            'Page Posting Feature',
            'https://www.youtube.com/watch?v=6jiNS_4CEug',
        ],
        '2' => [
            'assets/site_new/img/tutorial/blog-3.jpg',
            'Announcement',
            'https://www.youtube.com/watch?v=6jiNS_4CEug',
        ],
        '3' => [
            'assets/site_new/img/tutorial/blog-4.jpg',
            'Full Demo',
            'https://www.youtube.com/watch?v=6jiNS_4CEug',
        ],
    ];
}

