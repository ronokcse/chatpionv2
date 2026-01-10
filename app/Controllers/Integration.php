<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * class Integration
 * @category controller
 */
class Integration extends Home
{
    protected $form_validation;

    /**
     * Initialize controller
     * @access public
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        if (session()->get('logged_in') != 1) {
            redirect()->to('home/login_page')->send();
            exit();
        }

        helper('form');
        $this->load->library('upload');

        $this->important_feature();
    }

    public function index()
    {
        $this->integration_menu_section();
    }

    public function integration_menu_section()
    {
        $data['body'] = 'api_channels';
        $data['page_title'] = lang('API Channels');
        $data['has_autoresponder_access'] = false;
        $data['has_json_access'] = false;
        $data['has_sms_access'] = true;
        $data['has_email_access'] = true;
        $data['has_http_api_access'] = http_api_exist();
        
        if(session()->get('user_type') == 'Admin' || (isset($this->module_access) && is_array($this->module_access) && in_array(265,$this->module_access))) $data['has_autoresponder_access'] = true;

        if($this->basic->is_exist("add_ons",array("project_id"=>31))) {
            if(session()->get('user_type') == 'Admin' || (isset($this->module_access) && is_array($this->module_access) && in_array(258,$this->module_access))) {
                $data['has_json_access'] = true;
            }

        }

        $data['payment_gateway_url'] = base_url('payment/accounts');
        $data['email_autoresponder_apis'] = $this->get_autoresponders();
        $data['social_medias'] = $this->get_social_medias();
        $data['payment_apis'] = $this->get_payment_apis();
        $data['sms_email_apis'] = $this->get_sms_email();

        $this->_viewcontroller($data);

    }

    public function get_autoresponders()
    {
        $asset_path_common = base_url('assets/img/api_channel_icon/');
        return [
            '0'=>[
                'title'=>lang('MailChimp'),
                'img_path' =>$asset_path_common.'auto_responder/mailchimp.png',
                'action_url'=> base_url('email_auto_responder_integration/mailchimp_list'),
            ],
            '1'=>[
                'title'=>lang('Sendinblue'),
                'img_path' =>$asset_path_common.'auto_responder/sendinblue.png',
                'action_url'=> base_url('email_auto_responder_integration/sendinblue_list'),
            ],
            '2'=>[
                'title'=>lang('Mautic'),
                'img_path' =>$asset_path_common.'auto_responder/mautic.png',
                'action_url'=> base_url('email_auto_responder_integration/mautic_list'),
            ],
            '3'=>[
                'title'=>lang('ActiveCampaign'),
                'img_path' =>$asset_path_common.'auto_responder/activecampaign.png',
                'action_url'=> base_url('email_auto_responder_integration/activecampaign_list'),
            ],
            '4'=>[
                'title'=>lang('Acelle'),
                'img_path' =>$asset_path_common.'auto_responder/acelle.png',
                'action_url'=> base_url('email_auto_responder_integration/acelle_list'),
            ],

        ];
        
    }

    public function get_social_medias()
    {
        $has_access = false;
        $has_facebook_access = false;		
        $has_google_access = false;	
        $has_wpSelf_access = false;
        $asset_path_common = base_url('assets/img/api_channel_icon/');

        if(session()->get('user_type') == 'Admin' || (isset($this->module_access) && is_array($this->module_access) && in_array(65,$this->module_access))) $has_facebook_access = true;
        if(session()->get('user_type') == 'Admin' || (isset($this->module_access) && is_array($this->module_access) && in_array(107,$this->module_access))) $has_google_access = true;
        if(session()->get('user_type') == 'Admin' || (isset($this->module_access) && is_array($this->module_access) && in_array(109,$this->module_access))) $has_wpSelf_access = false;

        return [
            '1'=>[
                'title'=>lang('Facebook'),
                'img_path' =>$asset_path_common.'social_media/facebook.png',
                'action_url'=> base_url('social_apps/facebook_settings'),
                'account_import_url' => base_url('social_accounts/index'),
                'has_access'=> $has_facebook_access,
            ],
            '2'=>[
                'title'=>lang('Google'),
                'img_path' =>$asset_path_common.'social_media/google.png',
                'action_url'=> base_url('social_apps/google_settings'),
                'account_import_url' => base_url('comboposter/social_accounts'),
                'has_access'=> $has_google_access,
            ],
            '3'=>[
                'title'=>lang('WordPress (self)'),
                'img_path' =>$asset_path_common.'social_media/wp.png',
                'action_url'=> base_url('social_apps/wordpress_settings_self_hosted'),
                'account_import_url' => base_url('comboposter/social_accounts'),
                'has_access'=> $has_wpSelf_access,
            ],

        ];
    }

    public function get_payment_apis()
    {
        $asset_path_common = base_url('assets/img/api_channel_icon/');
        return [
            '0'=>[
                'title'=>lang('PayPal'),
                'img_path' =>$asset_path_common.'payment/paypl.png',
            ],
            '1'=>[
                'title'=>lang('Stripe'),
                'img_path' =>$asset_path_common.'payment/stripe.png',
            ],
            '2'=>[
                'title'=>lang('Mollie'),
                'img_path' =>$asset_path_common.'payment/mollie.png',
            ],
            '3'=>[
                'title'=>lang('Razorpay'),
                'img_path' =>$asset_path_common.'payment/razorpay.png',
            ],
            '4'=>[
                'title'=>lang('Paystack'),
                'img_path' =>$asset_path_common.'payment/paystack.png',
            ],
            '5'=>[
                'title'=>lang('Mercadopago'),
                'img_path' =>$asset_path_common.'payment/mercadopago.png',
            ],
            '6'=>[
                'title'=>lang('SSLCOMMERZ'),
                'img_path' =>$asset_path_common.'payment/sslcommerz.png',
            ],
            '7'=>[
                'title'=>lang('Senangpay'),
                'img_path' =>$asset_path_common.'payment/senangpay.png',
            ],
            '8'=>[
                'title'=>lang('Instamojo'),
                'img_path' =>$asset_path_common.'payment/instamojo.png',
            ],
            '9'=>[
                'title'=>lang('Toyyibpay'),
                'img_path' =>$asset_path_common.'payment/toyyibpay.png',
            ],
            '10'=>[
                'title'=>lang('Xendit'),
                'img_path' =>$asset_path_common.'payment/xendit.png',
            ],
            '11'=>[
                'title'=>lang('Myfatoorah'),
                'img_path' =>$asset_path_common.'payment/myfatoorah.png',
            ],
            '12'=>[
                'title'=>lang('Paymaya'),
                'img_path' =>$asset_path_common.'payment/paymaya.png',
            ],
            '13'=>[
                'title'=>lang('Manual'),
                'img_path' =>$asset_path_common.'payment/manualpayment.png',
            ],

        ];
    }

    public function get_sms_email()
    {
        $asset_path_common = base_url('assets/img/api_channel_icon/');
        return [
            'sms' => [
                '0'=>[
                    'title'=>lang('Twilio'),
                    'img_path' =>$asset_path_common.'sms_email/twilio.png',
                    'action_url'=> base_url('sms_email_manager/sms_api_lists')
                ],
                '1'=>[
                    'title'=>lang('Plivo'),
                    'img_path' =>$asset_path_common.'sms_email/plivo.png',
                    'action_url'=> base_url('sms_email_manager/sms_api_lists')
                ],
                '2'=>[
                    'title'=>lang('Clickatell'),
                    'img_path' =>$asset_path_common.'sms_email/clickatell.png',
                    'action_url'=> base_url('sms_email_manager/sms_api_lists')
                ],
                '3'=>[
                    'title'=>lang('Clickatell-platform'),
                    'img_path' =>$asset_path_common.'sms_email/clickatell.png',
                    'action_url'=> base_url('sms_email_manager/sms_api_lists')
                ],
                '4'=>[
                    'title'=>lang('Planet'),
                    'img_path' =>$asset_path_common.'sms_email/planet.png',
                    'action_url'=> base_url('sms_email_manager/sms_api_lists')
                ],
                '5'=>[
                    'title'=>lang('Nexmo'),
                    'img_path' =>$asset_path_common.'sms_email/nexmo.png',
                    'action_url'=> base_url('sms_email_manager/sms_api_lists')
                ],
                '6'=>[
                    'title'=>lang('MSG91'),
                    'img_path' =>$asset_path_common.'sms_email/msg91.png',
                    'action_url'=> base_url('sms_email_manager/sms_api_lists')
                ],
                '7'=>[
                    'title'=>lang('Africastalking'),
                    'img_path' =>$asset_path_common.'sms_email/africastalking.png',
                    'action_url'=> base_url('sms_email_manager/sms_api_lists')
                ],
                '8'=>[
                    'title'=>lang('SemySMS'),
                    'img_path' =>$asset_path_common.'sms_email/semysms.png',
                    'action_url'=> base_url('sms_email_manager/sms_api_lists')
                ],
                '9'=>[
                    'title'=>lang('Routesms.com'),
                    'img_path' =>$asset_path_common.'sms_email/routesms.png',
                    'action_url'=> base_url('sms_email_manager/sms_api_lists')
                ],
                '10'=>[
                    'title'=>lang('HTTP GET/POST'),
                    'img_path' =>$asset_path_common.'sms_email/custom.png',
                    'action_url'=> base_url('sms_email_manager/sms_api_lists')
                ],
            ],
            'email'=> [
                '0' => [
                    'title'=>lang('SMTP'),
                    'img_path' =>$asset_path_common.'sms_email/smtp.png',
                    'action_url'=> base_url('sms_email_manager/smtp_config')
                ],
                '1' => [
                    'title'=>lang('Sendgrid'),
                    'img_path' =>$asset_path_common.'sms_email/sendgrid.png',
                    'action_url'=> base_url('sms_email_manager/sendgrid_api_config')
                ],
                '2' => [
                    'title'=>lang('Mailgun'),
                    'img_path' =>$asset_path_common.'sms_email/mailgun.png',
                    'action_url'=> base_url('sms_email_manager/mailgun_api_config')
                ],
                '3' => [
                    'title'=>lang('Mandrill'),
                    'img_path' =>$asset_path_common.'sms_email/mandrill.png',
                    'action_url'=> base_url('sms_email_manager/mandrill_api_config')
                ],
            ]
        ];
    }

    public function open_ai_api_credentials(){
        if(ai_reply_exist()){
            $data['body'] = "admin/openAI/api_credentials";
            $data['page_title'] = lang('Open AI API Credentials');
            $user_id = session()->get('user_id');
            $get_data = $this->basic->get_data("open_ai_config",array("where"=>array('user_id'=>$user_id)));
            $data['xvalue'] = isset($get_data[0])?$get_data[0]:array();
            if($this->is_demo == '1')
                $data["xvalue"]["open_ai_secret_key"] = "XXXXXXXXXX";
            $this->_viewcontroller($data);
        }
        else {
            redirect()->to('home/access_forbidden')->send();
            exit();
        }
        
    }
    
    public function open_ai_api_credentials_action()
    {

        if($this->is_demo == '1')
        {
            echo "<h2 style='text-align:center;color:red;border:1px solid red; padding: 10px'>This feature is disabled in this demo.</h2>"; 
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            redirect()->to('home/access_forbidden')->send();
            exit();
        }

        if(ai_reply_exist()){
            if ($_POST) {

                $this->form_validation->set_rules('open_ai_secret_key','<b>'.lang("Open Ai Secret Key").'</b>','trim');
                $this->form_validation->set_rules('instruction_to_ai','<b>'.lang("Instruction To AI").'</b>','trim');
                $this->form_validation->set_rules('models','<b>'.lang("Select Models").'</b>','trim');
                $this->form_validation->set_rules('maximum_token','<b>'.lang("Maximum Token").'</b>','trim');
            }

            if ($this->form_validation->run() == false) 
            {
                return $this->open_ai_api_credentials();
            } 
            else{
                $this->csrf_token_check();
                $user_id = session()->get('user_id');
                $open_ai_secret_key = strip_tags($this->request->getPost('open_ai_secret_key'));
                $instruction_to_ai = strip_tags($this->request->getPost('instruction_to_ai'));
                $models = strip_tags($this->request->getPost('models'));
                $maximum_token = strip_tags($this->request->getPost('maximum_token'));
                $update_data = array(
                    'open_ai_secret_key'=>$open_ai_secret_key,
                    'instruction_to_ai'=>$instruction_to_ai,
                    'models'=>$models,
                    'maximum_token'=>$maximum_token,
                    'user_id'=>$user_id
                );
                $get_data = $this->basic->get_data("open_ai_config",array("where"=>array('user_id'=>$user_id)));
                if(!empty($get_data))
                $this->basic->update_data("open_ai_config",array("user_id"=>$user_id),$update_data);
                else $this->basic->insert_data("open_ai_config",$update_data);      
                                 
                session()->setFlashdata('success_message', 1);
                redirect()->to('integration/open_ai_api_credentials')->send();
                exit();
            }
        }
        else {
            redirect()->to('home/access_forbidden')->send();
            exit();
        }
        
    }

}
