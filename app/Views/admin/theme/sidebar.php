<?php
$sidebar_user_type = session()->get('user_type');
?>
<div class="main-sidebar">
  <aside id="sidebar-wrapper">
    <div class="sidebar-brand">
      <a href="<?php echo base_url(); ?>">
        <img src="<?php echo base_url(); ?>assets/img/logo.png" alt='<?php echo config('MyConfig')->product_short_name; ?>'>
      </a>
    </div>
    <ul class="sidebar-menu mt-3">
      <?php
      
        $admin_double_level2=array('admin/activity_log','payment/accounts','payment/earning_summary','payment/transaction_log','blog/posts');
        $all_links=array();
        foreach($menus as $single_menu) 
        {          
            $menu_html= '';
            $only_admin = $single_menu['only_admin'];
            $only_member = $single_menu['only_member']; 
            $module_access = explode(',', $single_menu['module_access']);
            $module_access = array_filter($module_access);
            $color = $single_menu['color'] ?? 'var(--blue)';

            if($single_menu['header_text']!='') echo '<li class="menu-header">'.lang($single_menu['header_text']).'</li>';

            if($single_menu['url']=='social_apps/index' && $single_menu['only_member']=='1' && config('MyConfig')->backup_mode==='0' && $sidebar_user_type=='Member') continue; // static condition not to

            // team user can not access team features
            if($single_menu['module_access']=='325' && isset($is_manager) && $is_manager==1) continue;
            if($single_menu['url']=='integration' && isset($is_manager) && $is_manager==1) continue;
            if($single_menu['url']=='integration' && isset($is_manager) && $is_manager==1) continue;

            if($single_menu['module_access']=='278,279' && (config('MyConfig')->instagram_reply_enable_disable==='0' || config('MyConfig')->instagram_reply_enable_disable=='')) continue;
            if($single_menu['module_access']=='296' && (config('MyConfig')->instagram_reply_enable_disable==='0' || config('MyConfig')->instagram_reply_enable_disable=='')) continue;

            if(!addon_exist($module_id=315,$addon_unique_name="visual_flow_builder") && $single_menu['module_access']=='315') continue;

            $extraText='';
            if($single_menu['add_ons_id']!='0' && isset($is_demo) && $is_demo=='1') $extraText=' <label class="label label-warning" style="font-size:9px;padding:4px 3px;">Addon</label>';

            if($single_menu['have_child']=='1') 
            {
              $dropdown_class1="nav-item dropdown";
              $dropdown_class2="has-dropdown";
            }
            else 
            {
              $dropdown_class1="";
              $dropdown_class2="";
            }
            if($single_menu['is_external']=='1') $site_url1=""; else $site_url1=site_url(); // if external link then no need to add site_url()
            if($single_menu['is_external']=='1') $parent_newtab=" target='_BLANK'"; else $parent_newtab=''; // if external link then open in new tab
            
           
            if($color=='' && (str_ends_with($single_menu['url'], '=ig') || str_ends_with($single_menu['url'], '/ig'))){
              $color_css = 'background: -webkit-linear-gradient(45deg, #405DE6, #5851DB, #833AB4, #C13584, #E1306C, #FD1D1D, #F56040, #F77737,#FCAF45, #FFDC80);background-clip: text;-webkit-background-clip: text;color: transparent;';
            }
            else if($color=='' && (str_ends_with($single_menu['url'], '=fb') || str_ends_with($single_menu['url'], '/fb'))){
              $color_css = 'background: -webkit-linear-gradient(45deg, #1877f2, #1877f2, #0a63bf, #2A4480, #1F3D73);background-clip: text;-webkit-background-clip: text;color: transparent;'; 
            }
            else{
              $color_css = "background: -webkit-linear-gradient(45deg,".adjustBrightness($color,-0.85).",".adjustBrightness($color,-0.65).",".adjustBrightness($color,-0.45).",".adjustBrightness($color,-0.25).",".$color.");-webkit-background-clip: text;-webkit-text-fill-color: transparent;";
            }

            $menu_html .= "<li class='".$dropdown_class1."'><a {$parent_newtab} href='".$site_url1.$single_menu['url']."' class='nav-link ".$dropdown_class2."'><i class= '".$single_menu['icon']."' style='".$color_css."'></i> <span>".lang($single_menu['name']).$extraText."</span></a>"; 

            array_push($all_links, $site_url1.$single_menu['url']);  

            if(isset($menu_child_1_map[$single_menu['id']]) && count($menu_child_1_map[$single_menu['id']]) > 0)
            {
              $menu_html .= '<ul class="dropdown-menu">';
              foreach($menu_child_1_map[$single_menu['id']] as $single_child_menu)
              {                  

                  $only_admin2 = $single_child_menu['only_admin'];
                  $only_member2 = $single_child_menu['only_member']; 
                  $color2 = $single_child_menu['color'] ?? '';
                  if(empty($color2)) $color2 = $color;
                  
                  if($sidebar_user_type == 'Admin' && session()->get('license_type') != 'double' && in_array($single_child_menu['url'], $admin_double_level2)) continue;

                  if(($only_admin2 == '1' && $sidebar_user_type == 'Member') || ($only_member2 == '1' && $sidebar_user_type == 'Admin')) 
                  continue;

                  // team user can not access
                  if($only_admin2 == '1' && isset($is_manager) && $is_manager == 1) 
                  continue;


                  if($single_child_menu['is_external']=='1') $site_url2=""; else $site_url2=site_url(); // if external link then no need to add site_url()
                  if($single_child_menu['is_external']=='1') $child_newtab=" target='_BLANK'"; else $child_newtab=''; // if external link then open in new tab

                  if($single_child_menu['have_child']=='1') $second_menu_href = '';
                  else $second_menu_href = "href='".$site_url2.$single_child_menu['url']."'";

                  $module_access2 = explode(',', $single_child_menu['module_access']);
                  $module_access2 = array_filter($module_access2);

                  
                  $hide_second_menu = '';
                  if($sidebar_user_type != 'Admin' && !empty($module_access2) && isset($module_access) && count(array_intersect($module_access, $module_access2))==0) $hide_second_menu = 'hidden';
                  
                  $menu_html .= "<li class='".$hide_second_menu."'><a {$child_newtab} {$second_menu_href} class='nav-link'><i style='color:".$color2."' class='".$single_child_menu['icon']."'></i><span>".lang($single_child_menu['name'])."</span></a>";

                  array_push($all_links, $site_url2.$single_child_menu['url']);

                  if(isset($menu_child_2_map[$single_child_menu['id']]) && count($menu_child_2_map[$single_child_menu['id']]) > 0)
                  {
                    $menu_html .= "<ul class='dropdown-menu2'>";
                    foreach($menu_child_2_map[$single_child_menu['id']] as $single_child_menu_2)
                    { 
                      $only_admin3 = $single_child_menu_2['only_admin'];
                      $only_member3 = $single_child_menu_2['only_member'];
                      if(($only_admin3 == '1' && $sidebar_user_type == 'Member') || ($only_member3 == '1' && $sidebar_user_type == 'Admin'))
                        continue;

                      // team user can not access
                      if($only_admin3 == '1' && isset($is_manager) && $is_manager == 1) 
                      continue;

                      if($single_child_menu_2['is_external']=='1') $site_url3=""; else $site_url3=site_url(); // if external link then no need to add site_url()
                      if($single_child_menu_2['is_external']=='1') $child2_newtab=" target='_BLANK'"; else $child2_newtab=''; // if external link then open in new tab   

                      $menu_html .= "<li><a {$child2_newtab} href='".$site_url3.$single_child_menu_2['url']."' class='nav-link'><i class='".$single_child_menu_2['icon']."'></i><span> ".lang($single_child_menu_2['name'])."</span></a></li>";

                      array_push($all_links, $site_url3.$single_child_menu_2['url']);
                    }
                    $menu_html .= "</ul>";
                  }
                  $menu_html .= "</li>";
              }
              $menu_html .= "</ul>";
            }

            $menu_html .= "</li>";
            
            if($only_admin == '1') 
            {
              if($sidebar_user_type == 'Admin' && (!isset($is_manager) || $is_manager != 1)) 
              echo $menu_html;
            }
            else if($only_member == '1') 
            {
              if($sidebar_user_type == 'Member') 
              echo $menu_html;
            }
            else 
            {
              if($sidebar_user_type=="Admin" || empty($module_access) || (isset($module_access) && count(array_intersect($module_access, $module_access))>0) ) 
              echo $menu_html;
            }             
        }

        if(session()->get('license_type') == 'double' && $sidebar_user_type=='Member' && (!isset($is_manager) || $is_manager!=1))
        {
          echo'
          <li class="menu-header">'.lang('Payment').'</li>
          <li class="nav-item dropdown">
            <a href="#" class="nav-link has-dropdown"><i class="fa fa-coins"></i> <span>'.lang('Payment').'</span></a>
            <ul class="dropdown-menu">
              <li class=""><a href="'.base_url("payment/buy_package").'" class="nav-link"><i class="fa fa-cart-plus"></i><span>'.lang('Renew Package').'</span></a></li>
              <li class=""><a href="'.base_url("payment/transaction_log").'" class="nav-link"><i class="fa fa-history"></i><span>'.lang('Transaction Log').'</span></a></li>
              <li class=""><a href="'.base_url("payment/usage_history").'" class="nav-link"><i class="fa fa-user-clock"></i><span>'.lang('Usage Log').'</span></a></li>
            </ul>
          </li>
          ';
        }
      ?>
    </ul>

    <?php
    if(session()->get('license_type') == 'double')
      if(config('MyConfig')->enable_support == '1')
        {
          $support_menu = lang('Support Desk');
          $support_icon = "fa fa-headset";
          $support_url = base_url('simplesupport/tickets');
          
          echo '
          <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
            <a href="'.$support_url.'" class="btn btn-primary btn-lg btn-block btn-icon-split">
              <i class="'.$support_icon.'"></i> '.$support_menu.'
            </a>
          </div>';
        }
    ?>

    
  </aside>
</div>



<?php 
$all_links=array_unique($all_links);
$unsetkey = array_search (base_url().'#', $all_links); 
if($unsetkey!=FALSE)
unset($all_links[$unsetkey]); // removing links without a real url

/* 
links that are not in database [custom link = sibebar parent]
No need to add a custom link if it's parent is controller/index
*/
$custom_links=array
(
  base_url("admin/general_settings")=>base_url("admin/settings"),
  base_url("admin/frontend_settings")=>base_url("admin/settings"),
  base_url("admin/smtp_settings")=>base_url("admin/settings"),
  base_url("admin/email_template_settings")=>base_url("admin/settings"),
  base_url("admin/analytics_settings")=>base_url("admin/settings"),
  base_url("admin/advertisement_settings")=>base_url("admin/settings"),
  base_url("admin/add_user")=>base_url("admin/user_manager"),
  base_url("admin/edit_user")=>base_url("admin/user_manager"),
  base_url("admin/login_log")=>base_url("admin/user_manager"),
  base_url("payment/add_package")=>base_url("payment/package_manager"),
  base_url("payment/update_package")=>base_url("payment/package_manager"),
  base_url("payment/details_package")=>base_url("payment/package_manager"),
  base_url("announcement/add")=>base_url("announcement/full_list"),
  base_url("announcement/edit")=>base_url("announcement/full_list"),
  base_url("announcement/details")=>base_url("announcement/full_list"),
  base_url("addons/upload")=>base_url("addons/lists"),
  base_url("comment_automation/all_auto_comment_report?media_type=".(isset($using_media_type) ? $using_media_type : 'fb'))=>base_url("comment_automation/comment_section_report?media_type=".(isset($using_media_type) ? $using_media_type : 'fb')),
  base_url("comment_automation/all_auto_comment_report/0/0")=>base_url("instagram_reply/reports"),
  base_url("comment_automation/all_auto_reply_report?media_type=".(isset($using_media_type) ? $using_media_type : 'fb'))=>base_url("comment_automation/comment_section_report?media_type=".(isset($using_media_type) ? $using_media_type : 'fb')),
  base_url("comment_reply_enhancers/bulk_tag_campaign_list?media_type=".(isset($using_media_type) ? $using_media_type : 'fb'))=>base_url("comment_automation/comment_section_report?media_type=".(isset($using_media_type) ? $using_media_type : 'fb')),
  base_url("comment_reply_enhancers/bulk_comment_reply_campaign_list?media_type=".(isset($using_media_type) ? $using_media_type : 'fb'))=>base_url("comment_automation/comment_section_report?media_type=".(isset($using_media_type) ? $using_media_type : 'fb')),
  base_url("comment_reply_enhancers/all_response_report?media_type=".(isset($using_media_type) ? $using_media_type : 'fb'))=>base_url("comment_automation/comment_section_report?media_type=".(isset($using_media_type) ? $using_media_type : 'fb')),
  base_url("comment_reply_enhancers/all_like_share_report?media_type=".(isset($using_media_type) ? $using_media_type : 'fb'))=>base_url("comment_automation/comment_section_report?media_type=".(isset($using_media_type) ? $using_media_type : 'fb')),
  base_url("messenger_bot_enhancers/checkbox_plugin_list")=>base_url("messenger_bot"),
  base_url("messenger_bot_enhancers/checkbox_plugin_add")=>base_url("messenger_bot"),
  base_url("messenger_bot_enhancers/checkbox_plugin_edit")=>base_url("messenger_bot"),
  base_url("messenger_bot_enhancers/send_to_messenger_list")=>base_url("messenger_bot"),
  base_url("messenger_bot_enhancers/send_to_messenger_add")=>base_url("messenger_bot"),
  base_url("messenger_bot_enhancers/send_to_messenger_edit")=>base_url("messenger_bot"),
  base_url("messenger_bot_enhancers/mme_link_list")=>base_url("messenger_bot"),
  base_url("messenger_bot_enhancers/mme_link_add")=>base_url("messenger_bot"),
  base_url("messenger_bot_enhancers/mme_link_edit")=>base_url("messenger_bot"),
  base_url("messenger_bot_enhancers/customer_chat_plugin_list")=>base_url("messenger_bot"),
  base_url("messenger_bot_enhancers/customer_chat_add")=>base_url("messenger_bot"),
  base_url("messenger_bot_enhancers/customer_chat_edit")=>base_url("messenger_bot"),  
  base_url("messenger_bot_enhancers/subscriber_broadcast_campaign")=>base_url("messenger_bot_broadcast"),
  base_url("messenger_bot_enhancers/create_subscriber_broadcast_campaign")=>base_url("messenger_bot_broadcast"),
  base_url("messenger_bot_enhancers/edit_subscriber_broadcast_campaign")=>base_url("messenger_bot_broadcast"),
  base_url("livachat/load_livechat?media_type=fb")=>base_url("subscriber_manager/livechat?media_type=fb"),
  base_url("livachat/load_livechat?media_type=ig")=>base_url("subscriber_manager/livechat?media_type=ig"),
  base_url("messenger_bot/tree_view")=>base_url("messenger_bot"),
  base_url("messenger_bot_connectivity/analytics")=>base_url("messenger_bot"),
  base_url("messenger_bot_connectivity/saved_template_view")=>base_url("messenger_bot"),
  base_url("webview_builder")=>base_url("messenger_bot"),
  base_url("webview_builder/manager")=>base_url("messenger_bot"),
  base_url("autoposting/settings")=>base_url("ultrapost"),
  base_url("instagram_poster")=>base_url("ultrapost"),
  base_url("themes/upload") => base_url("themes/lists"),
  base_url("messenger_bot_connectivity/webview_builder_manager") => base_url("messenger_bot"),
  base_url("messenger_bot_connectivity") => base_url("messenger_bot"),
  base_url("messenger_bot_connectivity/edit_webview") => base_url("messenger_bot"),
  base_url("sms_email_manager/contact_group_list") => base_url("subscriber_manager"),
  base_url("sms_email_manager/contact_list") => base_url("subscriber_manager"),
  base_url("sms_email_manager/sms_campaign_lists") => base_url("messenger_bot_broadcast"),
  base_url("sms_email_manager/create_sms_campaign") => base_url("messenger_bot_broadcast"),
  base_url("sms_email_manager/edit_sms_campaign") => base_url("messenger_bot_broadcast"),
  base_url("sms_email_manager/email_campaign_lists") => base_url("messenger_bot_broadcast"),
  base_url("sms_email_manager/create_email_campaign") => base_url("messenger_bot_broadcast"),
  base_url("sms_email_manager/edit_email_campaign") => base_url("messenger_bot_broadcast"),

  base_url("comment_automation/comment_growth_tools/fb") => base_url("comment_automation/comment_growth_tools/fb"),
  base_url("comment_automation/comment_template_manager?media_type=fb") => base_url("comment_automation/comment_growth_tools/fb"),
  base_url("comment_automation/template_manager?media_type=fb") => base_url("comment_automation/comment_growth_tools/fb"),
  base_url("comment_automation/index?media_type=fb") => base_url("comment_automation/comment_growth_tools/fb"),
  base_url("comment_automation/comment_section_report?media_type=fb") => base_url("comment_automation/comment_growth_tools/fb"),
  base_url("comment_automation/all_auto_comment_report?media_type=fb") => base_url("comment_automation/comment_growth_tools/fb"),
  base_url("comment_automation/all_auto_reply_report?media_type=fb") => base_url("comment_automation/comment_growth_tools/fb"),
  base_url("comment_reply_enhancers/bulk_tag_campaign_list?media_type=fb") => base_url("comment_automation/comment_growth_tools/fb"),
  base_url("comment_reply_enhancers/bulk_comment_reply_campaign_list?media_type=fb") => base_url("comment_automation/comment_growth_tools/fb"),
  base_url("comment_reply_enhancers/all_response_report?media_type=fb") => base_url("comment_automation/comment_growth_tools/fb"),
  base_url("comment_reply_enhancers/all_like_share_report?media_type=fb") => base_url("comment_automation/comment_growth_tools/fb"),
  base_url("comment_reply_enhancers/post_list?media_type=fb") => base_url("comment_automation/comment_growth_tools/fb"),
  base_url("comment_automation/all_auto_comment_report?media_type=fb") => base_url("comment_automation/comment_growth_tools/fb"),
  
  base_url("comment_automation/comment_growth_tools/ig") => base_url("comment_automation/comment_growth_tools/ig"),
  base_url("comment_automation/comment_template_manager?media_type=ig") => base_url("comment_automation/comment_growth_tools/ig"),
  base_url("comment_automation/template_manager?media_type=ig") => base_url("comment_automation/comment_growth_tools/ig"),
  base_url("comment_automation/index?media_type=ig") => base_url("comment_automation/comment_growth_tools/ig"),
  base_url("comment_automation/comment_section_report?media_type=ig") => base_url("comment_automation/comment_growth_tools/ig"),
  base_url("comment_automation/all_auto_comment_report?media_type=ig") => base_url("comment_automation/comment_growth_tools/ig"),
  base_url("comment_automation/all_auto_reply_report?media_type=ig") => base_url("comment_automation/comment_growth_tools/ig"),
  base_url("comment_reply_enhancers/bulk_tag_campaign_list?media_type=ig") => base_url("comment_automation/comment_growth_tools/ig"),
  base_url("comment_reply_enhancers/bulk_comment_reply_campaign_list?media_type=ig") => base_url("comment_automation/comment_growth_tools/ig"),
  base_url("comment_reply_enhancers/all_response_report?media_type=ig") => base_url("comment_automation/comment_growth_tools/ig"),
  base_url("comment_reply_enhancers/all_like_share_report?media_type=ig") => base_url("comment_automation/comment_growth_tools/ig"),
  base_url("comment_reply_enhancers/post_list?media_type=ig") => base_url("comment_automation/comment_growth_tools/ig"),
  base_url("comment_automation/all_auto_comment_report?media_type=ig") => base_url("comment_automation/comment_growth_tools/ig"),
  

  base_url("instagram_reply/template_manager") => base_url("comment_automation/comment_growth_tools/ig"),
  base_url("instagram_reply/get_account_lists") => base_url("comment_automation/comment_growth_tools/ig"),
  base_url("instagram_reply/instagram_autoreply_report/post") => base_url("comment_automation/comment_growth_tools/ig"),
  base_url("instagram_reply/instagram_autoreply_report/full") => base_url("comment_automation/comment_growth_tools/ig"),
  base_url("instagram_reply/instagram_autoreply_report/mention") => base_url("comment_automation/comment_growth_tools/ig"),


  base_url("affiliate_system/request_info") => base_url("affiliate_system/affiliate_users"),
  base_url("affiliate_system/add_affiliate") => base_url("affiliate_system/affiliate_users"),
  base_url("affiliate_system/edit_affiliate") => base_url("affiliate_system/affiliate_users"),


  base_url("comboposter/text_post/campaigns") => base_url("ultrapost"),
  base_url("comboposter/image_post/campaigns") => base_url("ultrapost"),
  base_url("comboposter/video_post/campaigns") => base_url("ultrapost"),
  base_url("comboposter/link_post/campaigns") => base_url("ultrapost"),
  base_url("comboposter/html_post/campaigns") => base_url("ultrapost"),

  base_url("comboposter/text_post/create") => base_url("ultrapost"),
  base_url("comboposter/image_post/create") => base_url("ultrapost"),
  base_url("comboposter/video_post/create") => base_url("ultrapost"),
  base_url("comboposter/link_post/create") => base_url("ultrapost"),
  base_url("comboposter/html_post/create") => base_url("ultrapost"),  

  base_url("comboposter/text_post/edit") => base_url("ultrapost"),
  base_url("comboposter/image_post/edit") => base_url("ultrapost"),
  base_url("comboposter/video_post/edit") => base_url("ultrapost"),
  base_url("comboposter/link_post/edit") => base_url("ultrapost"),
  base_url("comboposter/html_post/edit") => base_url("ultrapost"),

  base_url("comboposter/text_post/clone") => base_url("ultrapost"),
  base_url("comboposter/image_post/clone") => base_url("ultrapost"),
  base_url("comboposter/video_post/clone") => base_url("ultrapost"),
  base_url("comboposter/link_post/clone") => base_url("ultrapost"),
  base_url("comboposter/html_post/clone") => base_url("ultrapost"),

  base_url("blog/add_post") => base_url("blog/posts"),
  base_url("blog/edit_post") => base_url("blog/posts"),
  base_url("blog/tag") => base_url("blog/posts"),
  base_url("blog/category") => base_url("blog/posts"),

  base_url("menu_manager/custom_page") => "",

  base_url("payment/accounts") => base_url("integration"),
  base_url("social_apps") => base_url("integration"),
  base_url("comboposter/social_accounts") => base_url("integration"),
  base_url("email_auto_responder_integration") => base_url("integration"),
  base_url("messenger_bot_connectivity/json_api_connector") => base_url("integration"),
  base_url("sms_email_manager/sms_api_lists") => base_url("integration"),
  base_url("sms_email_manager/smtp_config") => base_url("integration"),
  base_url("sms_email_manager/mandrill_api_config") => base_url("integration"),
  base_url("sms_email_manager/sendgrid_api_config") => base_url("integration"),
  base_url("sms_email_manager/mailgun_api_config") => base_url("integration"),
  base_url("woocommerce_abandoned_cart") => base_url("integration"),
  base_url("woocommerce_integration") => base_url("integration"),
  base_url("http_api") => base_url("integration")

);

$custom_links[base_url("payment/transaction_log_manual")]=base_url("payment/transaction_log");

$custom_links_assoc_str="{";
$loop=0;
foreach ($custom_links as $key => $value) 
{
  $loop++;
  array_push($all_links, $key); // adding custom urls in all urls array

  /* making associative link -> parent array for js, js dont support special chars */
  $custom_links_assoc_str.=str_replace(array('/',':','-','.','?','='), array('FORWARDSLASHES','COLONS','DASHES','DOTS','QUESTIONMARKS','EQUALS'), $key).":'".$value."'";
  if($loop!=count($custom_links)) $custom_links_assoc_str.=',';
}
$custom_links_assoc_str.="}";
// echo "<pre style='padding-left:300px;'>";
// print_r($all_links);
// echo "</pre>"; 
?>


<script type="text/javascript">

  var all_links_JS = [<?php echo '"'.implode('","', $all_links).'"' ?>]; // all urls includes database & custom urls
  var custom_links_JS= [<?php echo '"'.implode('","', array_keys($custom_links)).'"' ?>]; // only custom urls
  var custom_links_assoc_JS = <?php echo $custom_links_assoc_str?>; // custom urls associative array link -> parent

  var sideBarURL = window.location;
  sideBarURL=String(sideBarURL).trim();
  sideBarURL=sideBarURL.replace('#_=_',''); // redirct from facebook login return extra chars with url

  function removeUrlLastPart(the_url)   // function that remove last segment of a url
  {
      var theurl = String(the_url).split('/');
      theurl.pop();      
      var answer=theurl.join('/');
      return answer;
  }

  // get parent url of a custom url
  function matchCustomUrl(find)
  {
    var parentUrl='';
    var tempu1=find.replace(/\//g, 'FORWARDSLASHES'); // decoding special chars that was encoded to make js array
    tempu1=tempu1.replace(/:/g, 'COLONS');
    tempu1=tempu1.replace(/-/g, 'DASHES');
    tempu1=tempu1.replace(/\./g, 'DOTS');
    tempu1=tempu1.replace('?', 'QUESTIONMARKS');
    tempu1=tempu1.replace(/\=/g, 'EQUALS');

    if(typeof(custom_links_assoc_JS[tempu1])!=='undefined')
    parentUrl=custom_links_assoc_JS[tempu1]; // getting parent value of custom link

    return parentUrl;
  }

  if(jQuery.inArray(sideBarURL, custom_links_JS) !== -1) // if the current link match custom urls
  {    
    sideBarURL=matchCustomUrl(sideBarURL);
  } 
  else if(jQuery.inArray(sideBarURL, all_links_JS) !== -1) // if the current link match known urls, this check is done later becuase all_links_JS also contains custom urls
  {
     sideBarURL=sideBarURL;
  }
  else // url does not match any of known urls
  {  
    var remove_times=1;
    var temp_URL=sideBarURL;
    var temp_URL2="";
    var tempu2="";
    while(true) // trying to match known urls by remove last part of url or adding /index at the last
    {
      temp_URL=removeUrlLastPart(temp_URL); // url may match after removing last
      temp_URL2=temp_URL+'/index'; // url may match after removing last part and adding /index

      if(jQuery.inArray(temp_URL, custom_links_JS) !== -1) // trimmed url match custom urls
      {
        sideBarURL=matchCustomUrl(temp_URL);
        break;
      }
      else if(jQuery.inArray(temp_URL, all_links_JS) !== -1) //trimmed url match known links
      {
        sideBarURL=temp_URL;
        break;
      }
      else // trimmed url does not match known urls, lets try extending url by adding /index
      {
        if(jQuery.inArray(temp_URL2, custom_links_JS) !== -1) // extended url match custom urls
        {
          sideBarURL=matchCustomUrl(temp_URL2);
          break;
        }
        else if(jQuery.inArray(temp_URL2, all_links_JS) !== -1)  // extended url match known urls
        {
          sideBarURL=temp_URL2;
          break;
        }
      }
      remove_times++;
      if(temp_URL.trim()=="") break;
    }    
  }

  $('ul.sidebar-menu a').filter(function() {
     return this.href == sideBarURL;
  }).parent().addClass('active');
  $('ul.dropdown-menu a').filter(function() {
     return this.href == sideBarURL;
  }).parentsUntil(".sidebar-menu > .dropdown-menu").addClass('active');

  $(document).ready(function() {
      // Select all li elements with class "menu-header"
      var menuHeaders = $(".sidebar-menu li");

      // Loop through each menu header starting from the second one
      for (var i = 1; i < menuHeaders.length; i++) {
          // Check if the current and previous elements both have the class "menu-header"
          if ($(menuHeaders[i]).hasClass("menu-header") && $(menuHeaders[i - 1]).hasClass("menu-header")) {
              // Remove the current and previous <li> elements from the DOM
              $(menuHeaders[i-1]).hide();
          }
      }

      // Select all li elements with class "menu-header"
      var menuHeaders = $(".menu-header");
      // Loop through each menu header
      menuHeaders.each(function() {
          // Check if the current menu header does not have a following <li> sibling
          if ($(this).next("li").length === 0) {
              // Remove the current menu header from the DOM
              $(this).remove();
          }
      });

      $("#collapse_me_plz").on("click", function() {
        if($("body").hasClass("sidebar-mini"))
          $(".main-sidebar").css("height", "auto");
        else
          $(".main-sidebar").css("height", "100%");
      });


      if($("body").hasClass("sidebar-mini"))
      {
        setTimeout(function() {
          $(".main-sidebar").attr("style", "height: auto !important;");
        }, 500);
      }


  });
</script>

<style>
/* Premium Modern Sidebar Design - Standard & Professional */

/* ===== SIDEBAR BASE ===== */
.main-sidebar {
  background: linear-gradient(180deg, #FFFFFF 0%, #F8F9FA 100%) !important;
  border-right: 1px solid rgba(0, 0, 0, 0.06) !important;
  box-shadow: 2px 0 12px rgba(0, 0, 0, 0.04) !important;
  backdrop-filter: blur(10px) !important;
}

/* ===== BRAND/LOGO SECTION ===== */
.main-sidebar .sidebar-brand {
  background: #FFFFFF !important;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
  padding: 20px 24px !important;
  height: auto !important;
  min-height: 68px !important;
  display: flex !important;
  align-items: center !important;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02) !important;
  margin-bottom: 0 !important;
}

.main-sidebar .sidebar-brand a {
  color: #1A202C !important;
  font-size: 22px !important;
  font-weight: 700 !important;
  letter-spacing: -0.3px !important;
  text-transform: none !important;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
}

.main-sidebar .sidebar-brand img {
  transition: transform 0.2s ease !important;
}

.main-sidebar .sidebar-brand:hover img {
  transform: scale(1.02) !important;
}

/* ===== MENU CONTAINER ===== */
.main-sidebar .sidebar-menu {
  padding: 12px 12px !important;
  margin: 0 !important;
}

.main-sidebar .sidebar-menu li {
  margin: 2px 0 !important;
  list-style: none !important;
}

/* ===== MENU ITEMS ===== */
.main-sidebar .sidebar-menu li a {
  color: #2D3748 !important;
  font-size: 15px !important;
  font-weight: 500 !important;
  padding: 11px 16px !important;
  border-radius: 10px !important;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
  height: auto !important;
  min-height: 44px !important;
  display: flex !important;
  align-items: center !important;
  position: relative !important;
  margin: 0 !important;
  text-decoration: none !important;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
}

.main-sidebar .sidebar-menu li a::before {
  content: '' !important;
  position: absolute !important;
  left: 0 !important;
  top: 50% !important;
  transform: translateY(-50%) !important;
  width: 3px !important;
  height: 0 !important;
  background: linear-gradient(180deg, #10B981 0%, #059669 100%) !important;
  border-radius: 0 3px 3px 0 !important;
  transition: height 0.25s ease !important;
}

.main-sidebar .sidebar-menu li a:hover {
  background: linear-gradient(90deg, rgba(16, 185, 129, 0.08) 0%, rgba(16, 185, 129, 0.04) 100%) !important;
  color: #1A202C !important;
  transform: translateX(3px) !important;
  box-shadow: 0 2px 8px rgba(16, 185, 129, 0.12) !important;
}

.main-sidebar .sidebar-menu li a:hover::before {
  height: 60% !important;
}

/* ===== ACTIVE MENU ITEM ===== */
.main-sidebar .sidebar-menu li.active > a {
  background: #021ff2 !important;
  color: #FFFFFF !important;
  font-weight: 600 !important;
  box-shadow: 0 4px 12px rgba(2, 31, 242, 0.35) !important;
  transform: translateX(0) !important;
}

.main-sidebar .sidebar-menu li.active > a::before {
  height: 70% !important;
  background: rgba(255, 255, 255, 0.3) !important;
}

.main-sidebar .sidebar-menu li.active > a span {
  color: #FFFFFF !important;
  font-weight: 600 !important;
}

.main-sidebar .sidebar-menu li.active > a i {
  color: #FFFFFF !important;
  filter: brightness(0) invert(1) !important;
}

/* ===== MENU TEXT ===== */
.main-sidebar .sidebar-menu li a span {
  color: #2D3748 !important;
  font-size: 16px !important;
  font-weight: 500 !important;
  letter-spacing: -0.1px !important;
  margin-left: 14px !important;
  line-height: 1.4 !important;
  flex: 1 !important;
}

.main-sidebar .sidebar-menu li.active a span {
  color: #FFFFFF !important;
  font-weight: 600 !important;
}

/* ===== ICONS ===== */
.main-sidebar .sidebar-menu li a i {
  width: 22px !important;
  min-width: 22px !important;
  font-size: 19px !important;
  margin-right: 0 !important;
  text-align: center !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  transition: transform 0.2s ease !important;
}

.main-sidebar .sidebar-menu li a:hover i {
  transform: scale(1.1) !important;
}

/* ===== DROPDOWN MENU - MODERN CARD STYLE ===== */
.main-sidebar .sidebar-menu li ul.dropdown-menu {
  background: #FFFFFF !important;
  border-radius: 16px !important;
  margin: 6px 0 6px 16px !important;
  padding: 8px 4px !important;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12), 0 2px 8px rgba(0, 0, 0, 0.08) !important;
  border: none !important;
  z-index: 10000 !important;
  position: relative !important;
  pointer-events: auto !important;
  backdrop-filter: blur(10px) !important;
  overflow: hidden !important; /* keep hover effect inside the card */
}

.main-sidebar .sidebar-menu li ul.dropdown-menu li {
  margin: 0 !important;
  padding: 0 4px !important;
}

.main-sidebar .sidebar-menu li ul.dropdown-menu li a {
  color: #2D3748 !important;
  font-size: 13.5px !important;
  font-weight: 500 !important;
  padding: 10px 14px !important;
  padding-left: 48px !important;
  border-radius: 10px !important;
  margin: 2px 0 !important;
  min-height: 38px !important;
  pointer-events: auto !important;
  cursor: pointer !important;
  z-index: 10001 !important;
  position: relative !important;
  display: flex !important;
  align-items: center !important;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
  border: 1px solid transparent !important;
}

.main-sidebar .sidebar-menu li ul.dropdown-menu li a i {
  color: #718096 !important;
  font-size: 16px !important;
  width: 20px !important;
  min-width: 20px !important;
  margin-right: 0 !important;
  opacity: 1 !important;
  filter: none !important;
  transition: all 0.2s ease !important;
}

/* ensure submenu text (span) is visible beside icon */
.main-sidebar .sidebar-menu li ul.dropdown-menu li a span {
  display: inline-block !important;
  margin-left: 12px !important;
  color: #2D3748 !important;
  font-weight: 500 !important;
  line-height: 1.4 !important;
}

.main-sidebar .sidebar-menu li ul.dropdown-menu li a::before {
  display: none !important;
}

/* Hover effect - subtle background with border */
.main-sidebar .sidebar-menu li ul.dropdown-menu li a:hover {
  background: #F7FAFC !important;
  color: #1A202C !important;
  border-color: rgba(16, 185, 129, 0.2) !important;
  transform: translateX(0) !important; /* don't move outside the card */
  box-shadow: none !important; /* keep shadow within dropdown card */
}

.main-sidebar .sidebar-menu li ul.dropdown-menu li a:hover i {
  color: #10B981 !important;
  transform: scale(1.1) !important;
}

/* Active state - modern badge style with very light gradient */
.main-sidebar .sidebar-menu li ul.dropdown-menu li.active > a {
  background: linear-gradient(135deg, #8fa0f9 0%, #6b7af7 50%, #3d4ff5 100%) !important;
  color: #FFFFFF !important;
  font-weight: 600 !important;
  border-color: transparent !important;
  padding-left: 48px !important;
  box-shadow: 0 4px 12px rgba(2, 31, 242, 0.2) !important;
  transform: translateX(0) !important;
}

.main-sidebar .sidebar-menu li ul.dropdown-menu li.active > a i {
  color: #FFFFFF !important;
  filter: brightness(0) invert(1) !important;
}

.main-sidebar .sidebar-menu li ul.dropdown-menu li.active > a span {
  color: #FFFFFF !important;
  font-weight: 600 !important;
}

/* Fix for mini sidebar floating dropdown */
body.sidebar-mini .main-sidebar .sidebar-menu > li ul.dropdown-menu {
  z-index: 10000 !important;
  position: absolute !important;
  left: 100% !important;
  margin-left: 12px !important;
  margin-top: 0 !important;
  min-width: 220px !important;
  pointer-events: auto !important;
  box-shadow: 0 12px 48px rgba(0, 0, 0, 0.15), 0 4px 16px rgba(0, 0, 0, 0.1) !important;
  animation: slideInRight 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

@keyframes slideInRight {
  from {
    opacity: 0;
    transform: translateX(-10px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

body.sidebar-mini .main-sidebar .sidebar-menu > li ul.dropdown-menu li a {
  color: #2D3748 !important;
  background-color: transparent !important;
  opacity: 1 !important;
  filter: none !important;
  pointer-events: auto !important;
  cursor: pointer !important;
  z-index: 10001 !important;
  position: relative !important;
  justify-content: flex-start !important;
  padding-left: 48px !important;
  width: auto !important;
  min-width: 220px !important;
  height: auto !important;
  border-radius: 10px !important;
}

body.sidebar-mini .main-sidebar .sidebar-menu > li ul.dropdown-menu li a i {
  color: #718096 !important;
  opacity: 1 !important;
  filter: none !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
}

/* ===== MENU HEADER ===== */
.main-sidebar .sidebar-menu li.menu-header {
  color: #718096 !important;
  font-size: 11px !important;
  font-weight: 700 !important;
  text-transform: uppercase !important;
  letter-spacing: 1.2px !important;
  padding: 16px 20px 8px 20px !important;
  margin: 8px 0 4px 0 !important;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
}

/* ===== SMOOTH TRANSITIONS ===== */
.main-sidebar .sidebar-menu li a,
.main-sidebar .sidebar-menu li a span,
.main-sidebar .sidebar-menu li a i {
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

/* ===== SUPPORT BUTTON ===== */
.main-sidebar .btn-primary {
  background: linear-gradient(135deg, #10B981 0%, #059669 100%) !important;
  border: none !important;
  border-radius: 10px !important;
  font-weight: 600 !important;
  font-size: 14px !important;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35) !important;
  transition: all 0.25s ease !important;
  padding: 14px 20px !important;
  letter-spacing: 0.2px !important;
}

.main-sidebar .btn-primary:hover {
  transform: translateY(-2px) !important;
  box-shadow: 0 6px 20px rgba(16, 185, 129, 0.45) !important;
  background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
}

.main-sidebar .btn-primary:active {
  transform: translateY(0) !important;
}

/* ===== SCROLLBAR ===== */
.main-sidebar::-webkit-scrollbar {
  width: 8px;
}

.main-sidebar::-webkit-scrollbar-track {
  background: transparent;
}

.main-sidebar::-webkit-scrollbar-thumb {
  background: linear-gradient(180deg, #CBD5E0 0%, #A0AEC0 100%) !important;
  border-radius: 4px;
  border: 2px solid transparent;
  background-clip: padding-box;
}

.main-sidebar::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(180deg, #A0AEC0 0%, #718096 100%) !important;
  background-clip: padding-box;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 1024px) {
  .main-sidebar .sidebar-menu li a {
    padding: 12px 14px !important;
    font-size: 14px !important;
  }
  
  .main-sidebar .sidebar-menu li a span {
    font-size: 14px !important;
  }
}

/* ===== FOCUS STATES ===== */
.main-sidebar .sidebar-menu li a:focus {
  outline: 2px solid rgba(16, 185, 129, 0.3) !important;
  outline-offset: 2px !important;
}

/* ===== ANIMATION FOR DROPDOWN ===== */
.main-sidebar .sidebar-menu li ul.dropdown-menu {
  animation: slideDown 0.3s ease-out !important;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* ===== COLLAPSED (SIDEBAR MINI) FIXES ===== */
/* When sidebar is collapsed (triggered by #collapse_me_plz), hide the text so it doesn't overflow into content area */
body.sidebar-mini .main-sidebar {
  overflow: visible !important;
  z-index: 9999 !important;
}

/* Hide main menu text in collapsed mode, but keep submenu text visible */
body.sidebar-mini .main-sidebar .sidebar-menu > li > a span {
  display: none !important;
  opacity: 0 !important;
  width: 0 !important;
  height: 0 !important;
  overflow: hidden !important;
}

/* Keep submenu text visible in collapsed mode */
body.sidebar-mini .main-sidebar .sidebar-menu li ul.dropdown-menu li a span {
  display: inline-block !important;
  opacity: 1 !important;
  width: auto !important;
  height: auto !important;
  overflow: visible !important;
  margin-left: 12px !important;
  color: #2D3748 !important;
}

body.sidebar-mini .main-sidebar .sidebar-menu li.menu-header {
  display: none !important;
  opacity: 0 !important;
  height: 0 !important;
  margin: 0 !important;
  padding: 0 !important;
  overflow: hidden !important;
}

/* Center icons nicely in collapsed mode */
body.sidebar-mini .main-sidebar .sidebar-menu {
  display: flex !important;
  flex-direction: column !important;
  align-items: center !important;
  padding: 12px 0 !important;
}

body.sidebar-mini .main-sidebar .sidebar-menu li {
  width: 100% !important;
  display: flex !important;
  justify-content: center !important;
  align-items: center !important;
}

body.sidebar-mini .main-sidebar .sidebar-menu li a {
  justify-content: center !important;
  align-items: center !important;
  padding: 0 !important;
  pointer-events: auto !important;
  width: 50px !important;
  height: 50px !important;
  margin: 4px auto !important;
  border-radius: 999px !important; /* perfect circle */
  overflow: hidden !important;
  display: flex !important;
}

/* Ensure ALL icons remain visible & consistent in collapsed mode - perfectly centered */
/* Only apply absolute positioning to main menu items, not submenu */
body.sidebar-mini .main-sidebar .sidebar-menu > li > a i {
  opacity: 1 !important;
  filter: none !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 18px !important;
  margin: 0 auto !important;
  position: absolute !important;
  left: 50% !important;
  top: 50% !important;
  transform: translate(-50%, -50%) !important;
  /* Preserve gradient colors if they exist, otherwise use default color */
  color: #4A5568 !important;
}

/* Submenu icons should stay in normal flow (not absolute positioned) */
body.sidebar-mini .main-sidebar .sidebar-menu li ul.dropdown-menu li a i {
  position: relative !important;
  left: auto !important;
  top: auto !important;
  transform: none !important;
  margin: 0 !important;
}

/* Preserve gradient colors for icons that have them (like fa-paper-plane) */
body.sidebar-mini .main-sidebar .sidebar-menu li a i[style*="gradient"],
body.sidebar-mini .main-sidebar .sidebar-menu li a i[style*="background-clip"],
body.sidebar-mini .main-sidebar .sidebar-menu li a i[style*="background:"] {
  /* Don't override gradient - let inline style work */
  color: transparent !important;
  -webkit-text-fill-color: transparent !important;
}

/* Make fa-user-ninja icon smaller in collapsed mode */
body.sidebar-mini .main-sidebar .sidebar-menu li a i.fa-user-ninja {
  font-size: 14px !important;
}

/* Hide sidebar brand text in collapsed mode */
body.sidebar-mini .main-sidebar .sidebar-brand {
  padding: 20px 12px !important;
  justify-content: center !important;
}

body.sidebar-mini .main-sidebar .sidebar-brand a,
body.sidebar-mini .main-sidebar .sidebar-brand img {
  max-width: 40px !important;
  overflow: hidden !important;
}

/* Make sure parent menu items with dropdowns are clickable and show submenu on hover */
body.sidebar-mini .main-sidebar .sidebar-menu > li {
  position: relative !important;
}

body.sidebar-mini .main-sidebar .sidebar-menu > li:hover > ul.dropdown-menu {
  display: block !important;
  opacity: 1 !important;
  visibility: visible !important;
}
</style>