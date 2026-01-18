<section class="section mt-3">
  <div class="section-header p-0 no_shadow bg-light mb-0">
      <?php 
      $currency = isset($ecommerce_config['currency']) ? $ecommerce_config['currency'] : "USD";
      $currency_icon = isset($currency_icons[$currency]) ? $currency_icons[$currency] : "$";
      $currency_position = isset($ecommerce_config['currency_position']) ? $ecommerce_config['currency_position'] : "left";
      $decimal_point = isset($ecommerce_config['decimal_point']) ? $ecommerce_config['decimal_point'] : 0;
      $thousand_comma = isset($ecommerce_config['thousand_comma']) ? $ecommerce_config['thousand_comma'] : '0';
      $buy_button_title = isset($ecommerce_config['buy_button_title']) ? $ecommerce_config['buy_button_title'] : lang('Buy Now');
      $is_category_wise_product_view = isset($ecommerce_config['is_category_wise_product_view']) ? $ecommerce_config['is_category_wise_product_view'] : "0";
      $product_listing = isset($ecommerce_config['product_listing']) ? $ecommerce_config['product_listing'] : "list";
      $hide_add_to_cart = isset($ecommerce_config['hide_add_to_cart']) ? $ecommerce_config['hide_add_to_cart'] : "0";
      $hide_buy_now = isset($ecommerce_config['hide_buy_now']) ? $ecommerce_config['hide_buy_now'] : "0";

      $currency_left = $currency_right = "";
      if($currency_position=='left') $currency_left = $currency_icon;
      if($currency_position=='right') $currency_right = $currency_icon;

      $subscriber_id=session()->get($store_data['id']."ecom_session_subscriber_id");
      if($subscriber_id=="")  $subscriber_id = isset($_GET['subscriber_id']) ? $_GET['subscriber_id'] : "";
      $pickup = isset($_GET['pickup']) ? $_GET['pickup'] : '';

      $form_action = base_url('ecommerce/store/'.$store_data['store_unique_id']);
      $form_action = mec_add_get_param($form_action,array("subscriber_id"=>$subscriber_id,"pickup"=>$pickup));
      
      $current_cart_id = isset($current_cart['cart_id']) ? $current_cart['cart_id'] : 0;
      $cart_count = isset($current_cart['cart_count']) ? $current_cart['cart_count'] : 0;
      $current_cart_url = base_url("ecommerce/cart/".$current_cart_id);
      $current_cart_url = mec_add_get_param($current_cart_url,array("subscriber_id"=>$subscriber_id,"pickup"=>$pickup));
      $url_cat =  isset($_GET["category"]) ? $_GET["category"] : "";

      $product_list_grouped = array();
      $product_list_grouped_ordered = array();
      if($is_category_wise_product_view=='1')
      {
        foreach ($product_list as $key => $value)
        {
          if(isset($category_list[$value["category_id"]]))
          $product_list_grouped[$value["category_id"]][] = $value;
          else $product_list_grouped["other"][] = $value;
        }
        foreach ($category_list as $key => $value)
        {
          if(isset($product_list_grouped[$key]))
          $product_list_grouped_ordered[$key] = $product_list_grouped[$key];
        }
        if(isset($product_list_grouped["other"])) $product_list_grouped_ordered["other"] = $product_list_grouped["other"];
      }
      else {
        // CI4 fix: Ensure product_list is properly assigned
        if(!empty($product_list) && is_array($product_list)) {
          $product_list_grouped_ordered['none'] = $product_list;
        } else {
          $product_list_grouped_ordered['none'] = array();
        }
      }

      // Debug: Check product_list
      // var_dump('product_list count: ' . (is_array($product_list) ? count($product_list) : 'not array'));
      // var_dump('product_list_grouped_ordered count: ' . count($product_list_grouped_ordered));

      $featured_product_lists = array();
      foreach ($product_list as $feature_product) {
        if($feature_product['is_featured'] == '0') continue;

        array_push($featured_product_lists, $feature_product);
      }
      ?>   
  </div>

  <div class="section-body">
    <div class="category_container xscroll">
      <?php 
      $active_class = $url_cat=='' ? 'border border-primary bg-white' : 'border bg-white';
      echo '<div class="slide"><a class="pointer cat_nav nav-link '.$active_class.'" href="" data-val=""><img class="rounded-circle mx-auto d-block" style="width:40px;height:40px;" src="'. base_url('assets/img/icon/shop.png').'">'.lang('All Items').'</a></div>';
      unset($category_list['']);

      foreach ($category_list_raw as $key => $value)
      {
        // CI4 fix: Prevent directory URL by checking if thumbnail is not empty before concatenating
        $thumb_val = isset($value['thumbnail']) ? trim($value['thumbnail']) : '';
        // Strict validation: ensure thumbnail is not empty, not just '/', and has a valid filename
        if(empty($thumb_val) || $thumb_val == '' || $thumb_val == '/' || !preg_match('/\.(jpg|jpeg|png|gif|webp|svg)$/i', $thumb_val)) {
          $final_url = base_url('assets/img/icon/rocket.png');
        } else {
          $final_url = base_url("upload/ecommerce/".$thumb_val);
        }
        $active_class2 = ($value['id']==$url_cat) ? 'border border-primary bg-white' : 'border bg-white';
        echo '<div class="slide text-center"><a class="pointer cat_nav nav-link '.$active_class2.'" href="" data-val="'.$value['id'].'"><img class="rounded-circle mx-auto d-block" style="width:40px;height:40px;" src="'.htmlspecialchars($final_url).'" onerror="this.src=\''.base_url('assets/img/icon/rocket.png').'\'">'.$value["category_name"].'</a></div>';
      } ?>
      
    </div>
    <?php
    if(empty($product_list))
    { ?>
      <div class="card no_shadow" id="nodata">
        <div class="card-body">
          <div class="empty-state">
            <img class="img-fluid" style="height: 200px" src="<?php echo base_url('assets/img/drawkit/drawkit-full-stack-man-colour.svg'); ?>" alt="image">
             <h2 class="mt-0"><?php echo lang('We could not find any item.');?></h2>
             <?php if($_POST) { ?>
             <a href="<?php echo $_SERVER['QUERY_STRING'] ? current_url().'?'.$_SERVER['QUERY_STRING'] : current_url(); ?>" class="btn btn-outline-primary mt-4"><i class="fas fa-arrow-circle-right"></i> <?php echo lang('Search Again');?></a>
             <?php } ?>
          </div>
        </div>
      </div>
    <?php
    }?>
    <div class="row" id="product-container" style="margin-left: -3px;margin-right: -3px;">

      <?php if(isset($is_ecommerce_related_product_exist) && $is_ecommerce_related_product_exist) : ?>
        <?php if(!empty($featured_product_lists)) : ?>
        <div class="col-12 p-0">
          <div class="section-title mt-3 mb-1"><?php echo lang('Featured Products') ?></div>
        </div>
        <div class="col-12 p-0 mb-3">
          <div class="owl-carousel owl-theme" id="featured-products-carousel">
            <?php foreach($featured_product_lists as $featured) : ?>
              <?php 
                  // CI4 fix: Strict empty check to prevent directory URL
                  $imgSrcs = base_url('assets/img/products/product-1.jpg'); // Default fallback
                  $thumb_val = isset($featured['thumbnail']) ? trim($featured['thumbnail']) : '';
                  if(isset($featured["woocommerce_product_id"]) && !is_null($featured["woocommerce_product_id"]) && !empty($thumb_val) && $thumb_val != '' && $thumb_val != '/') {
                    // WooCommerce product - use external URL directly
                    $imgSrcs = $thumb_val;
                  } elseif(!empty($thumb_val) && $thumb_val != '' && $thumb_val != '/' && preg_match('/\.(jpg|jpeg|png|gif|webp|svg)$/i', $thumb_val)) {
                    // Regular product - use local upload path (ensure thumbnail has valid extension)
                    $imgSrcs = base_url('upload/ecommerce/'.$thumb_val);
                  }

                  $product_url = base_url("ecommerce/product/".$featured['id']);
                  $product_url = mec_add_get_param($product_url,array("subscriber_id"=>$subscriber_id,"pickup"=>$pickup));

                  $display_featured_product_price = mec_display_price($featured['original_price'],$featured['sell_price'],$currency_icon,'1',$currency_position,$decimal_point,$thousand_comma);
                  $display_featured_product_discount = mec_display_price($featured['original_price'],$featured['sell_price'],$currency_icon,'4',$currency_position,$decimal_point,$thousand_comma);
              ?>
              <article class="article article-style-c mb-1 mt-1" data-cat="<?php echo $featured['category_id'];?>">
                <div class="article-header">
                  <a href="<?php echo $product_url; ?>">
                    <?php 
                    // CI4 fix: Final validation - use validated imgSrcs directly
                    $final_imgSrcs = $imgSrcs;
                    ?>
                    <div class="article-image" data-background="<?php echo htmlspecialchars($final_imgSrcs);?>" style="background-image: url('<?php echo htmlspecialchars($final_imgSrcs);?>');"></div>
                  </a>
                  <?php echo $display_featured_product_discount; ?>

                </div>
                <div class="article-details pt-0 pb-0 pl-1 pr-1">
                  <div class="article-category mt-1 mb-0"><?php echo $display_featured_product_price; ?></div>
                  <div class="article-title mb-0">
                    <a href="<?php echo $product_url; ?>" class="text-dark text-small"><?php echo $featured['product_name']; ?></a>
                  </div>
                  <p class="d-none"><?php echo strip_tags($featured['product_description']); ?></p>
                  <p class="d-none"><?php echo isset($category_list[$featured['category_id']]) ? $category_list[$featured['category_id']] : ''; ?></p>
                  &nbsp;   
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      <?php endif; ?>


      <?php
      // CI4 fix: Debug - check if product_list_grouped_ordered is empty
      if(empty($product_list_grouped_ordered)) {
        // If empty, try to use product_list directly
        if(!empty($product_list) && is_array($product_list)) {
          $product_list_grouped_ordered['none'] = $product_list;
        }
      }
      
      foreach($product_list_grouped_ordered as $key_main => $value_main) :   
       if($is_category_wise_product_view=='1')
       {
          $echo_cat = isset($category_list[$key_main]) ? $category_list[$key_main] : lang('Other Items'); 
          echo '<div class="col-12"><div class="section-title mt-3 mb-1">'.$echo_cat.'</div></div>'; 
       }
       
       // CI4 fix: Ensure value_main is an array and not empty
       if(!empty($value_main) && is_array($value_main)) {
       foreach ($value_main as $key => $value) :         
          $product_link = base_url("ecommerce/product/".$value['id']);
          $product_link = mec_add_get_param($product_link,array("subscriber_id"=>$subscriber_id,"pickup"=>$pickup));
          $show_preperation_time = false;
          if(isset($ecommerce_config['is_preparation_time']) && $ecommerce_config['is_preparation_time']=='1' && $value["preparation_time_unit"]!="") $show_preperation_time = true;
          $preperationtime = "";
          if($show_preperation_time)
          {
            $system_preparation_time = isset($ecommerce_config['preparation_time']) ? $ecommerce_config['preparation_time'] : "30";
            $system_preparation_time_unit = isset($ecommerce_config['preparation_time_unit']) ? $ecommerce_config['preparation_time_unit'] : "minutes";
            $preparation_time = $value['preparation_time']=="" ? $system_preparation_time : $value['preparation_time'];
            $preparation_time_unit = $value['preparation_time_unit']=="" ? $system_preparation_time_unit : $value['preparation_time_unit'];
            $preparation_time_unit = str_replace(array("minutes","hours","days"), array("m","h","d"), $preparation_time_unit);
            $preperationtime = $value["preparation_time_unit"]!="" ? $preparation_time."".$preparation_time_unit : "";
          }

          // CI4 fix: Strict empty check to prevent directory URL
          $imgSrc = base_url('assets/img/products/product-1.jpg'); // Default fallback
          $thumb_val = isset($value['thumbnail']) ? trim($value['thumbnail']) : '';
          if(isset($value["woocommerce_product_id"]) && !is_null($value["woocommerce_product_id"]) && !empty($thumb_val) && $thumb_val != '' && $thumb_val != '/') {
            // WooCommerce product - use external URL directly
            $imgSrc = $thumb_val;
          } elseif(!empty($thumb_val) && $thumb_val != '' && $thumb_val != '/' && preg_match('/\.(jpg|jpeg|png|gif|webp|svg)$/i', $thumb_val)) {
            // Regular product - use local upload path (ensure thumbnail has valid extension)
            $imgSrc = base_url('upload/ecommerce/'.$thumb_val);
          }

          $display_price = mec_display_price($value['original_price'],$value['sell_price'],$currency_icon,'1',$currency_position,$decimal_point,$thousand_comma);
          $display_discount = mec_display_price($value['original_price'],$value['sell_price'],$currency_icon,'4',$currency_position,$decimal_point,$thousand_comma);

          $display_review = "";
          $rating = "";
          if(isset($ecommerce_review_comment_exist) && $ecommerce_review_comment_exist && isset($review_data[$value['id']])) : 
            $float_review = 'float-right';
            $rating = mec_average_rating($review_data[$value['id']]['total_point'],$review_data[$value['id']]['total_review']);
            $review_star = mec_display_rating_starts($rating,'text-small');
            $display_review = '<span class="'.$float_review.'">'.$review_star.'</span>';
          endif;

          $lim = $hide_add_to_cart=='1' && $hide_buy_now=='1' ? 75 : 30;

          $display_short_description = strlen(strip_tags($value['product_description']))>$lim?substr(strip_tags($value['product_description']), 0, $lim).'..':strip_tags($value['product_description']);

          // $cart_lang = $cart_count>0 ? lang('Add to Cart') : lang('Cart');
          $cart_lang = $product_listing=='list' ? lang('Add to Cart') : '';
          $buy_button_title = $product_listing=='list' ? lang($buy_button_title) : '';
          $is_float = $product_listing=='list' ? 'float-right' : 'float-right';
          $display_buy_button = $display_add_to_cart = "";
          $btn_size = $product_listing=='list' ? 'btn-sm' : '';

          if($hide_add_to_cart=='1') $is_float = '';

          if($hide_buy_now=='0')
          {
            if($value['attribute_ids']=='')
            $display_buy_button = '<a href="" class="btn '.$btn_size.' btn-primary '.$is_float.' add_to_cart buy_now" data-attributes="'.$value['attribute_ids'].'" data-product-id="'.$value['id'].'" data-action"add"><i class="fas fa-credit-card"></i> '.$buy_button_title.'</a>';
            else
            $display_buy_button = '<a href="" class="btn '.$btn_size.' btn-primary '.$is_float.' add_to_cart_modal buy_now" data-product-id="'.$value['id'].'"><i class="fas fa-credit-card"></i> '.$buy_button_title.'</a>';
          }


          if($hide_add_to_cart=='0')
          {            
            if($value['attribute_ids']=='') 
            {
              $display_add_to_cart =  '<a href="" class="btn  '.$btn_size.' btn-outline-primary add_to_cart" data-attributes="'.$value['attribute_ids'].'" data-product-id="'.$value['id'].'" data-action="add"><i class="fas fa-shopping-cart"></i> '.$cart_lang.'</a>';                   
            } 
            else 
            { 
              $display_add_to_cart =  '<a href="" data-product-id="'.$value['id'].'" class="btn '.$btn_size.' btn-outline-primary add_to_cart_modal"><i class="fas fa-shopping-cart"></i> '.$cart_lang.'</a>';
            }
          }
          ?>

          <?php 
          if($product_listing=='list')
          { ?>
            <div class="col-12 col-sm-12 col-md-6 col-lg-4 product-single pl-1 pr-1" data-cat="<?php echo $value['category_id'];?>">
              <ul class="list-unstyled list-unstyled-border bg-white mb-2 mt-1 rounded bordered">
                  <li class="media align-items-center">                 
                     <?php 
                     // CI4 fix: Final validation - use validated imgSrc directly
                     $final_imgSrc_list = $imgSrc;
                     ?>
                     <a href="<?php echo $product_link;?>"><img width="110" height="110" class="mr-2 rounded-left bordered-right" src="<?php echo htmlspecialchars($final_imgSrc_list); ?>" onerror="this.src='<?php echo base_url('assets/img/products/product-1.jpg'); ?>'"/>
                     </a>
                    <?php echo $display_discount; ?>

                    <?php  if($show_preperation_time): ?>
                    <span class="badge badge-dark rounded preparation_time"><i class="fas fa-clock"></i> <?php echo $preperationtime; ?></span>
                    <?php endif; ?>
                    
                    <div class="media-body pl-0 pr-2">
                      <div class="media-title mb-1">
                        <a href="<?php echo $product_link;?>" class="text-dark text-small"><?php echo $value['product_name'];?></a><br>
                        <span class="mt-1 text-small"><?php echo $display_price;?></span>
                        <?php echo $display_review;?>              
                      </div>
                      <p class="text-small text-muted m-0 mb-2" style="line-height: normal !important;font-size: 11px;">
                        <?php 
                        echo $display_short_description;
                        if(empty($value['product_description'])) echo "&nbsp;";
                        ?>                     
                      </p>
                      <p class="d-none"><?php echo strip_tags($value['product_description']); ?></p>
                      <p class="d-none"><?php echo isset($category_list[$value['category_id']]) ? $category_list[$value['category_id']] : ''; ?></p>
                      <?php
                        echo $display_add_to_cart;
                        echo $display_buy_button;
                      ?> 
                    </div>
                  </li>                
              </ul>
            </div>
          <?php
          }
          else
          { ?>
            <div class="col-4 col-sm-4 col-md-3 col-lg-2 product-single pl-1 pr-1" data-cat="<?php echo $value['category_id'];?>">
              <article class="article article-style-c mb-1 mt-1">
                <div class="article-header">
                   <a href="<?php echo $product_link;?>">
                    <?php 
                    // CI4 fix: Final validation - use validated imgSrc directly
                    $final_imgSrc = $imgSrc;
                    ?>
                    <div class="article-image" data-background="<?php echo htmlspecialchars($final_imgSrc);?>" style="background-image: url('<?php echo htmlspecialchars($final_imgSrc);?>');"></div>
                   </a>
                   <?php echo $display_discount; ?>
                   <?php  if($show_preperation_time): ?>
                   <span class="badge badge-dark rounded preparation_time2">
                    <i class="fas fa-clock"></i> <?php echo $preperationtime; ?>
                    <span class="float-right"><?php echo $rating!="" && $rating!=0 ? $rating." <i class='fas fa-star orange text-small'></i>" : ""; ?></span>
                    </span>
                   <?php endif; ?>                   
                </div>
                <div class="article-details pt-0 pb-0 pl-1 pr-1">
                  <div class="article-category mt-1 mb-0"><?php echo $display_price;?></div>
                  <div class="article-title mb-2">
                    <a href="<?php echo $product_link;?>" class="text-dark text-small"><?php echo $value['product_name'];?></a>
                  </div>

                  <p class="d-none"><?php echo strip_tags($value['product_description']); ?></p>
                  <p class="d-none"><?php echo isset($category_list[$value['category_id']]) ? $category_list[$value['category_id']] : ''; ?></p>
                  <div>
                    <?php
                      echo $display_add_to_cart;
                      echo $display_buy_button;
                    ?>  
                  </div>
                  &nbsp;   
                </div>
              </article>
            </div>
          <?php
          }
          ?>
       <?php
       endforeach;
       } // End CI4 fix: is_array check
      endforeach; ?>       
    </div>

    <div class="card no_shadow d-none w-100" id="nodata_search">
      <div class="card-body">
        <div class="empty-state">
          <img class="img-fluid" style="height: 200px" src="<?php echo base_url('assets/img/drawkit/drawkit-full-stack-man-colour.svg'); ?>" alt="image">
           <h2 class="mt-0"><?php echo lang('We could not find any item.');?></h2>
           <?php if($_POST) { ?>
           <a href="<?php echo $_SERVER['QUERY_STRING'] ? current_url().'?'.$_SERVER['QUERY_STRING'] : current_url(); ?>" class="btn btn-outline-primary mt-4"><i class="fas fa-arrow-circle-right"></i> <?php echo lang('Search Again');?></a>
           <?php } ?>
        </div>
      </div>
    </div> 
  </div>

</section>

<script> 
  var url_cat =  '<?php echo $url_cat;?>';
  var is_category_wise_product_view =  '<?php echo $is_category_wise_product_view;?>';
  $("document").ready(function()  {
    if(url_cat!="" )setTimeout(function(){ $(".cat_nav[data-val="+url_cat+"]").click(); }, 500);
    $(document).on('click','.cat_nav',function(e){
      e.preventDefault();
      $("#search").val('');
      $('.cat_nav').removeClass('border-primary');
      $(this).addClass('border-primary');
      var cat = $(this).attr('data-val');
      if(cat=='0' || cat=='')
      {
        $('.product-single').removeClass('d-none');
        $(".section-title").show();
      }
      else
      {
        $(".section-title").hide();
        $('.product-single').addClass('d-none');
        $('.product-single[data-cat='+cat+']').removeClass('d-none');
      }
      var count = $('.product-single:visible').length;
      if(count==0) $("#nodata_search").removeClass('d-none');
      else $("#nodata_search").addClass('d-none');
    });


    // CI4 fix: Validate all data-background URLs before owl-carousel initialization
    $('#featured-products-carousel [data-background*="upload/ecommerce"]').each(function() {
      var bg = $(this).attr('data-background');
      if(bg && (bg.endsWith('upload/ecommerce/') || bg.endsWith('upload/ecommerce'))) {
        $(this).attr('data-background', '<?php echo base_url('assets/img/products/product-1.jpg'); ?>');
        $(this).css('background-image', 'url(<?php echo base_url('assets/img/products/product-1.jpg'); ?>)');
      }
    });
    
    $("#featured-products-carousel").owlCarousel({
      items: 3,
      rtl : is_rtl,
      margin: 10,
      autoplay: true,
      autoplayTimeout: 4000,
      loop: true,
      responsive: {
        0: {
          items: 4
        },
        768: {
          items: 6
        },
        1200: {
          items: 10
        }
      }
    });
  });

  $(document).on('click','.add_to_cart_modal',function(e){
      e.preventDefault();
      var product_id = $(this).attr('data-product-id');
      var  buy_now = '0';
      var subscriber_id = "<?php echo isset($_GET['subscriber_id']) ? $_GET['subscriber_id'] : ''; ?>";
      if($(this).hasClass('buy_now')) buy_now = '1';
      if(buy_now) $(this).addClass('btn-progress');
      else
      {
        $(this).removeClass('btn-outline-primary');
        $(this).addClass('btn-progress');
        $(this).addClass('btn-primary');
      }
      $("#add_to_cart_modal_view .modal-body").html('<div class="text-center" id="waiting" style="width: 100%;margin: 30px 0;" <i class="fas fa-spinner fa-spin blue" style="font-size:60px;"></i></div>');
      $("#add_to_cart_modal_view").modal();
      $.ajax({
        context : this,
        type: 'POST',
        data: {product_id,buy_now,subscriber_id},
        url: '<?php echo base_url('ecommerce/add_to_cart_modal'); ?>',
        success: function(response)
        {
         $(this).removeClass("btn-progress");
         if(!buy_now)
         {
           $(this).removeClass('btn-primary');
           $(this).addClass('btn-outline-primary');
         }
         $("#add_to_cart_modal_view .modal-body").html(response);
        }
         
      });
  });

  function search_product(obj,div_id){  // obj = 'this' of jquery, div_id = id of the div    
    var filter=$(obj).val().toUpperCase();
    if(filter.length>0)
    {
      $(".section-title").hide();
      $(".cat_nav").removeClass('border-primary');
    }
    else 
    {
      $(".section-title").show();
      $(".slide:first-child .cat_nav").addClass('border-primary');
    }

    $('#'+div_id+" .product-single").each(function(){
      var content=$(this).text().trim();
      if (content.toUpperCase().indexOf(filter) > -1) {
        $(this).removeClass('d-none');
      }
      else $(this).addClass('d-none');
    });    
    var count = $('.product-single:visible').length;
    if(count==0) $("#nodata_search").removeClass('d-none');
    else $("#nodata_search").addClass('d-none');

  }
</script>

<style type="text/css">
  .cat_nav{padding: 5px 10px;margin: 5px 0 5px 0;border-radius: 10px !important;margin-right: 5px;white-space: nowrap;font-weight: normal !important;font-size: 12px;}
  .category_container {
    display: inline-flex;
    width: 100%;
    overflow-y: auto;
  }
  .category_container .slide {float: left;}
  .rounded{border-radius: 10px  !important;}
  .rounded-left{border-radius: 10px 0 0 10px ;}
  .nicescroll-cursors{top:5px !important;}
  .preparation_time{opacity:.7;padding:2px 0;border-radius:0 0 0 10px !important;width:70px;text-align:center;position:absolute;bottom:8.5px;left:4.5px;font-size: 10px !important;font-weight: 400;}
  .preparation_time2{opacity:.7;padding:2px 5px 2px 5px;border-radius:0!important;width:100%;text-align:left;position:absolute;bottom:0;font-size: 10px !important;font-weight: 400;}
  /*.display_review{padding:2px 0;border-radius:0!important;width:50px;text-align:right;position:absolute;top:0;right:0;font-size: 10px !important;font-weight: 400;}*/
  .preparation_time2 .orange{font-size:8px !important;line-height: normal;}
  .preparation_time i,.preparation_time2 i,.display_review i{font-size: 8px !important;}
  .article-title a{font-size: 10px !important;font-weight: 500 !important;}
  .article-category{font-size: 9px;}
  .article .article-details,.article .article-details a{line-height: 12px !important;}
  .article.article-style-c .article-header {height: 160px !important; } 

  #featured-products-carousel .article.article-style-c .article-header { height: 80px !important }
  @media (max-width: 575.98px) {  
   .article.article-style-c .article-header{height: 110px !important;}
  }
  </style>


<?php include(APPPATH."Views/ecommerce/cart_js.php"); ?>
<?php include(APPPATH."Views/ecommerce/cart_style.php"); ?>
<?php include(APPPATH."Views/ecommerce/common_style.php"); ?>

<div class="modal fade" id="add_to_cart_modal_view" tabindex="-1" role="dialog" aria-labelledby="add_to_cart_modal_viewLabel" aria-hidden="true" style="height: calc(100% - 79px) !important;" data-backdrop="false">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header p-3">
        <h5 class="modal-title" id="add_to_cart_modal_viewLabel"><i class="fas fa-palette"></i> <?php echo lang('Choose Options'); ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-chevron-circle-left"></i></span>
        </button>
      </div>
      <div class="modal-body text-justify pt-0 pl-3 pr-3 pb-3">

      </div>
    </div>
  </div>
</div>
