<style>
  /* Manual payment style */
  #manual-payment-modal #additional-info {
    height: 160px !important;
  }

  /* Clean Modern Cart Design */
  .aliexpress-cart {
    background: #f8f9fa;
    padding: 20px;
  }

  .aliexpress-cart .row {
    margin-left: 0;
    margin-right: 0;
  }

  .aliexpress-cart .row > [class*="col-"] {
    padding-left: 0;
    padding-right: 0;
  }

  .cart-main-wrapper {
    display: flex;
    gap: 20px;
    align-items: flex-start;
  }

  .cart-items-container {
    flex: 1;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
    min-width: 0;
  }

  .cart-left-section {
    flex: 1;
    min-width: 0;
  }

  .cart-items-header {
    background: #fff;
    padding: 16px 20px;
    border-bottom: 2px solid #ff6a00;
    font-size: 18px;
    font-weight: 600;
    color: #333;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .cart-items-header i {
    color: #ff6a00;
    font-size: 20px;
  }

  .cart-item-row {
    display: flex;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.2s;
  }

  .cart-item-row:hover {
    background: #fafafa;
  }

  .cart-item-row:last-child {
    border-bottom: none;
  }

  .cart-item-image-wrapper {
    width: 100px;
    height: 100px;
    margin-right: 16px;
    flex-shrink: 0;
  }

  .cart-item-image-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border: 1px solid #e5e5e5;
  }

  .cart-item-info {
    flex: 1;
    min-width: 0;
    margin-right: 16px;
  }

  .cart-item-name {
    font-size: 14px;
    color: #333;
    margin-bottom: 8px;
    line-height: 1.5;
  }

  .cart-item-name a {
    color: #333;
    text-decoration: none;
  }

  .cart-item-name a:hover {
    color: #ff6a00;
  }

  .cart-item-specs {
    font-size: 12px;
    color: #999;
    margin-bottom: 8px;
  }

  .cart-item-discount-tag {
    display: inline-block;
    background: #ff6a00;
    color: #fff;
    padding: 2px 6px;
    font-size: 11px;
    border-radius: 2px;
    margin-left: 8px;
  }

  .cart-item-price-col {
    width: 120px;
    text-align: right;
    margin-right: 16px;
  }

  .cart-item-price-main {
    font-size: 16px;
    color: #ff6a00;
    font-weight: 500;
    margin-bottom: 4px;
  }

  .cart-item-price-unit {
    font-size: 12px;
    color: #999;
  }

  .cart-item-quantity-col {
    width: 120px;
    text-align: center;
    margin-right: 16px;
  }

  .quantity-control-wrapper {
    display: inline-flex;
    align-items: center;
    border: 1px solid #e5e5e5;
    border-radius: 4px;
  }

  .quantity-btn {
    width: 32px;
    height: 32px;
    border: none;
    background: #fff;
    cursor: pointer;
    font-size: 16px;
    color: #666;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .quantity-btn:hover {
    background: #f5f5f5;
  }

  .quantity-input {
    width: 50px;
    height: 32px;
    border: none;
    border-left: 1px solid #e5e5e5;
    border-right: 1px solid #e5e5e5;
    text-align: center;
    font-size: 14px;
  }

  .cart-item-total-col {
    width: 120px;
    text-align: right;
    margin-right: 16px;
  }

  .cart-item-total-price {
    font-size: 16px;
    color: #ff6a00;
    font-weight: 500;
  }

  .cart-item-delete-col {
    width: 40px;
    text-align: center;
  }

  .delete-item-btn {
    color: #999;
    font-size: 18px;
    cursor: pointer;
    padding: 8px;
    border: none;
    background: none;
  }

  .delete-item-btn:hover {
    color: #ff6a00;
  }

  .cart-summary-wrapper {
    width: 300px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    gap: 20px;
  }

  .cart-summary-container {
    width: 100%;
  }

  .summary-box {
    background: #fff;
    border: 1px solid #e5e5e5;
    margin-bottom: 16px;
  }

  .summary-box-header {
    background: #fafafa;
    padding: 12px 16px;
    border-bottom: 1px solid #e5e5e5;
    font-size: 14px;
    font-weight: 500;
    color: #333;
  }

  .summary-box-body {
    padding: 16px;
  }

  .summary-row-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    font-size: 14px;
    color: #666;
  }

  .summary-row-item.total-row {
    border-top: 1px solid #e5e5e5;
    margin-top: 8px;
    padding-top: 12px;
  }

  .summary-row-item.total-row .summary-label,
  .summary-row-item.total-row .summary-value {
    font-size: 18px;
    font-weight: 600;
    color: #ff6a00;
  }

  .summary-label {
    color: #666;
  }

  .summary-value {
    color: #333;
    font-weight: 500;
  }

  .coupon-discount-item {
    background: #e8f5e9;
    border: 1px solid #4caf50;
    padding: 10px 14px;
    border-radius: 6px;
    margin: 8px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .coupon-discount-item .summary-label {
    color: #0277bd;
    font-size: 13px;
  }

  .coupon-discount-item .summary-value {
    color: #0277bd;
    font-weight: 600;
  }

  .coupon-input-wrapper {
    display: flex;
    gap: 8px;
    margin-top: 12px;
    align-items: stretch;
  }

  .coupon-input-field {
    flex: 1;
    padding: 8px 12px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 14px;
    height: 38px;
    min-width: 0;
  }

  .coupon-input-field:focus {
    outline: none;
    border-color: #ff6a00;
  }

  .coupon-apply-button {
    padding: 8px 20px;
    background: #ff6a00;
    color: #fff;
    border: none;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    height: 38px;
    white-space: nowrap;
    flex-shrink: 0;
  }

  .coupon-apply-button:hover {
    background: #e55a00;
  }

  .coupon-success-badge {
    background: #e8f5e9;
    color: #2e7d32;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 12px;
    margin-top: 10px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
  }

  .coupon-remove-btn {
    background: #dc3545;
    color: #fff;
    border: none;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 11px;
    cursor: pointer;
    white-space: nowrap;
  }

  .coupon-remove-btn:hover {
    background: #c82333;
  }

  .delivery-section-wrapper {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
    margin-top: 20px;
  }

  .cart-left-section .delivery-section-wrapper {
    width: 100%;
    margin-left: 0;
    margin-right: 0;
  }

  #delivery_address_select {
    margin: 0;
    padding: 0;
    width: 100%;
  }

  #delivery_address_select .list-unstyled {
    margin: 0;
    padding: 0;
    width: 100%;
  }

  #delivery_address_select .list-unstyled-border {
    border: none !important;
  }

  #delivery_address_select .bordered {
    border: none !important;
  }

  .checkout-button {
    width: 97%;
    margin-left: 18px;
    padding: 14px;
    background: #ff6a00;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }

  @media (min-width: 993px) {
    .checkout-button {
      padding: 12px 20px;
      font-size: 15px;
    }
  }

  .checkout-button:hover {
    background: #e55a00;
  }

  .checkout-button.btn-progress {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
  }
  
  #cod-payment-button.btn-progress {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
    color: #000 !important;
    background-color: #000 !important;
    border-color: #000 !important;
  }
  
  #cod-payment-button.btn-progress * {
    color: #fff !important;
  }

  .checkout-button i {
    font-size: 16px;
  }

  .cart-checkout-section {
    width: 100%;
    margin-top: 20px;
  }

  @media (max-width: 992px) {
    .cart-main-wrapper {
      flex-direction: column;
    }
    .cart-summary-wrapper {
      width: 100%;
      order: 2;
    }
    .cart-left-section {
      order: 1;
    }
    .cart-checkout-section {
      order: 3;
      width: 100%;
      margin-top: 20px;
    }
  }

  .delivery-section {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 18px;
    margin-bottom: 20px;
  }

  .delivery-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 14px;
    padding-bottom: 12px;
    border-bottom: 1px solid #e9ecef;
  }

  .delivery-section-title {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .delivery-section-title i {
    color: #ff6a00;
    font-size: 16px;
  }

  .delivery-address-content {
    background: #f8f9fa;
    padding: 12px 14px;
    border-radius: 6px;
    margin-bottom: 12px;
    font-size: 14px;
    color: #495057;
    line-height: 1.6;
    border-left: 3px solid #ff6a00;
  }

  .manage-address-btn {
    padding: 6px 12px;
    font-size: 12px;
    border-radius: 4px;
  }

  .store-pickup-toggle {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 0;
  }

  .store-pickup-toggle label {
    margin: 0;
    cursor: pointer;
  }

  @media (max-width: 768px) {
    .cart-item-row {
      flex-wrap: wrap;
    }
    .cart-item-image-wrapper {
      width: 80px;
      height: 80px;
    }
    .cart-item-price-col,
    .cart-item-quantity-col,
    .cart-item-total-col {
      width: auto;
      margin-right: 12px;
    }
    .cart-checkout-section {
      margin-top: 16px;
    }
  }
</style>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<?php
$pickup = isset($_GET['pickup']) ? $_GET['pickup'] : '';
$sslcommerz_mode = isset($ecommerce_config['sslcommerz_mode']) ? $ecommerce_config['sslcommerz_mode'] : "live";
$is_order_schedule = isset($ecommerce_config['is_order_schedule']) ? $ecommerce_config['is_order_schedule'] : "0";
$order_schedule = isset($ecommerce_config['order_schedule']) ? $ecommerce_config['order_schedule'] : "any";
$is_delivery_note = isset($ecommerce_config['is_delivery_note']) ? $ecommerce_config['is_delivery_note'] : "0";
$store_pickup_title = isset($ecommerce_config['store_pickup_title']) ? $ecommerce_config['store_pickup_title'] : "Store Pickup";
$is_store_pickup = isset($ecommerce_config['is_store_pickup']) ? $ecommerce_config['is_store_pickup'] : "0";
$is_home_delivery = isset($ecommerce_config['is_home_delivery']) ? $ecommerce_config['is_home_delivery'] : "1";
$currency_position = isset($ecommerce_config['currency_position']) ? $ecommerce_config['currency_position'] : "left";
$decimal_point = isset($ecommerce_config['decimal_point']) ? $ecommerce_config['decimal_point'] : 0;
$thousand_comma = isset($ecommerce_config['thousand_comma']) ? $ecommerce_config['thousand_comma'] : '0';
$manual_payment_instruction = isset($ecommerce_config['manual_payment_instruction']) ? $ecommerce_config['manual_payment_instruction'] : '';
$order_title = lang('Checkout');
$order_date = date("jS M,Y",strtotime($webhook_data_final['updated_at']));      
$wc_first_name = $webhook_data_final['first_name'];
$wc_last_name = $webhook_data_final['last_name'];
// $confirmation_response = json_decode($webhook_data_final['confirmation_response'],true);
$wc_buyer_location = json_decode($webhook_data_final['user_location'],true);
if(!is_array($wc_buyer_location)) $wc_buyer_location = array(); 
$currency = $webhook_data_final['currency'];
$currency_icon = isset($currency_icons[$currency])?$currency_icons[$currency]:'$';
$wc_email_bill = $webhook_data_final['email'];
$wc_phone_bill = $webhook_data_final['phone_number'];
$shipping_cost = $webhook_data_final["shipping"];
$total_tax = $webhook_data_final["tax"];     
$checkout_amount  = $webhook_data_final['payment_amount'];
$delivery_time = $webhook_data_final['delivery_time'];
if($delivery_time='0000-00-00 00:00:00') $delivery_time = '';
$coupon_code = $webhook_data_final['coupon_code'];
$coupon_type = $webhook_data_final['coupon_type'];
$coupon_amount =  $webhook_data_final['discount'];
$subtotal =  $webhook_data_final['subtotal'];
$currency_left = $currency_right = "";
if($currency_position=='left') $currency_left = $currency_icon;
if($currency_position=='right') $currency_right = $currency_icon;

$payment_method =  $webhook_data_final['payment_method'];
if($payment_method=='') $payment_method =  '<span class="badge badge-danger">'.lang('Incomplete').'</span>';      
else $payment_method =  $payment_method." ".$webhook_data_final['card_ending'];

$order_no =  $webhook_data_final['id'];
$order_url =  base_url("ecommerce/order/".$order_no);

$buyer_country = isset($country_names[$webhook_data_final["buyer_country"]]) ? ucwords(strtolower($country_names[$webhook_data_final["buyer_country"]])) : $webhook_data_final["buyer_country"];
$store_country = $webhook_data_final["store_country"];
$store_country_formatted = isset($country_names[$webhook_data_final["store_country"]]) ? ucwords(strtolower($country_names[$webhook_data_final["store_country"]])) : $webhook_data_final["store_country"];
// $buyer_address = $webhook_data_final["buyer_address"]."<br>".$webhook_data_final["buyer_state"]." ".$webhook_data_final["buyer_zip"]."<br>".$buyer_country;
$store_name = $webhook_data_final['store_name'];
$store_address = "<i class='fas fa-map-marker-alt'></i> ".$webhook_data_final["store_address"].", ".$webhook_data_final["store_state"].", ".$webhook_data_final["store_city"].", ".$store_country_formatted." ".$webhook_data_final["store_zip"]." <br><i class='fas fa-paper-plane'></i> ".$webhook_data_final['store_phone'].", ".$webhook_data_final['store_email'];
$store_phone = $webhook_data_final["store_phone"];
$store_email = $webhook_data_final["store_email"];
$subscriber_id_database = $webhook_data_final["subscriber_id"];
$store_unique_id = $webhook_data_final["store_unique_id"];

$table_data ='';
$i=0;
// $subtotal_count = 0;
foreach ($product_list as $key => $value) 
{        
  $title = isset($value['product_name']) ? $value['product_name'] : "";
  $quantity = isset($value['quantity']) ? $value['quantity'] : 1;
  $price = isset($value['unit_price']) ? $value['unit_price'] : 0;
  $item_total = $price*$quantity;
  // $subtotal_count+=$item_total;
  $item_total = mec_number_format($item_total,$decimal_point,$thousand_comma);
  $price = mec_number_format($price,$decimal_point,$thousand_comma);
  $image_url = (isset($value['thumbnail']) && !empty($value['thumbnail'])) ? base_url('upload/ecommerce/'.$value['thumbnail']) : base_url('assets/img/example-image.jpg');
  if(isset($value["woocommerce_product_id"]) && !is_null($value["woocommerce_product_id"]) && isset($value['thumbnail']) && !empty($value['thumbnail']))
  $image_url = $value["thumbnail"];     
  $permalink = base_url("ecommerce/product/".$value['product_id']);
  $attribute_info = (is_array(json_decode($value["attribute_info"],true))) ? json_decode($value["attribute_info"],true) : array();

  $attribute_query_string_array = array();
  $attribute_query_string = "";
  foreach ($attribute_info as $key2 => $value2) 
  {
    $urlencode = is_array($value2) ? implode(',', $value2) : $value2;
    $attribute_query_string_array[]="option".$key2."=".urlencode($urlencode);
  }
  $attribute_query_string = implode("&", $attribute_query_string_array);
  if(!empty($attribute_query_string_array)) $attribute_query_string = "&quantity=".$quantity."&".$attribute_query_string;

  $attribute_print = "";
  if(!empty($attribute_info))
  {
    $attribute_print_tmp = array();
    foreach ($attribute_info as $key2 => $value2)
    {
      $attribute_print_tmp[] = is_array($value2) ? implode('+', array_values($value2)) : $value2;
    }
    $attribute_print = "<small class='text-muted'>".implode(', ', $attribute_print_tmp)."</small>";
  }
  // if($subscriber_id!='') $permalink.="?subscriber_id=".$subscriber_id.$attribute_query_string;

  if($subscriber_id!='' || $pickup!="")
  $permalink = mec_add_get_param($permalink,array("subscriber_id"=>$subscriber_id,"pickup"=>$pickup)).$attribute_query_string;

  $break_class= (empty($attribute_info)) ? 'pt-2' : '';

  $i++;
  $off = $value["coupon_info"];
  $discount_badge = "";
  if($off!="") {
    $discount_badge = '<span class="cart-item-discount-tag">'.$off.'</span>';
  }

  $table_data.='
  <div class="cart-item-row">
    <div class="cart-item-image-wrapper">
      <a href="'.$permalink.'">
        <img src="'.$image_url.'" alt="'.$title.'">
      </a>
    </div>
    <div class="cart-item-info">
      <div class="cart-item-name">
        <a href="'.$permalink.'">'.$title.'</a>
        '.$discount_badge.'
      </div>
      '.($attribute_print != "" ? '<div class="cart-item-specs">'.$attribute_print.'</div>' : '').'
    </div>
    <div class="cart-item-price-col">
      <div class="cart-item-price-main">'.$currency_left.$item_total.$currency_right.'</div>
      <div class="cart-item-price-unit">'.$currency_left.$price.$currency_right.' each</div>
    </div>
    <div class="cart-item-quantity-col">
      <div class="quantity-control-wrapper">
        <button class="quantity-btn add_to_cart2" data-quantity="'.$quantity.'" data-id="'.$value["id"].'" data-action="remove" type="button">-</button>
        <input type="text" class="quantity-input" value="'.$quantity.'" readonly>
        <button class="quantity-btn add_to_cart2" data-quantity="'.$quantity.'" data-id="'.$value["id"].'" data-action="add" type="button">+</button>
      </div>
    </div>
    <div class="cart-item-total-col">
      <div class="cart-item-total-price">'.$currency_left.$item_total.$currency_right.'</div>
    </div>
    <div class="cart-item-delete-col">
      <button class="delete-item-btn pointer delete_item" data-id="'.$value['id'].'" title="Delete">
        <i class="fas fa-trash-alt"></i>
      </button>
    </div>
  </div>';
}  

$coupon_info2 = "";
if($coupon_code!='' && $coupon_amount>0)
$coupon_info2 =
'<div class="coupon-discount-item">
  <span class="summary-label">'.lang('Coupon Discount').' ('.$coupon_code.')</span>
  <span class="summary-value">-'.$currency_left.mec_number_format($coupon_amount,$decimal_point,$thousand_comma).$currency_right.'</span>
</div>';

$tax_info = "";
if($total_tax>0)
$tax_info =
'<div class="summary-row-item">
  <span class="summary-label">'.lang('Tax').'</span>
  <span class="summary-value">+'.$currency_left.mec_number_format($total_tax,$decimal_point,$thousand_comma).$currency_right.'</span>
</div>';

$shipping_info = "";
if($shipping_cost>0)
$shipping_info =
'<div class="summary-row-item">
  <span class="summary-label">'.lang('Delivery Charge').'</span>
  <span class="summary-value">+'.$currency_left.mec_number_format($shipping_cost,$decimal_point,$thousand_comma).$currency_right.'</span>
</div>';
// $coupon_code." (".$currency_icon.$coupon_amount.")";      

//if($webhook_data_final['action_type']!='checkout') $subtotal = $subtotal_count;
$subtotal = mec_number_format($subtotal,$decimal_point,$thousand_comma);
$checkout_amount = mec_number_format($checkout_amount,$decimal_point,$thousand_comma);
$coupon_amount = mec_number_format($coupon_amount,$decimal_point,$thousand_comma);

$output = "";
$after_checkout_details = 
'<div class="summary-row-item total-row">
  <span class="summary-label">'.lang('Total').'</span>
  <span class="summary-value">'.$currency_left.$checkout_amount.$currency_right.'</span>
</div>';

$coupon_applied_badge = "";
if($coupon_code!='' && $coupon_amount>0) {
  $coupon_applied_badge = '
  <div class="coupon-applied-badge">
    <i class="fas fa-check-circle"></i>
    <span>'.lang('Coupon Applied').': '.$coupon_code.' - '.$currency_left.mec_number_format($coupon_amount,$decimal_point,$thousand_comma).$currency_right.' '.lang('discount').'</span>
  </div>';
}

$apply_coupon = '
<div class="coupon-section">
  <div class="d-flex justify-content-between align-items-center">
    <h6>'.lang('Have a coupon code?').'</h6>
    <a class="text-dark" id="showProfile" href="#" style="font-size: 12px;">
      <i class="fas fa-map-marker-alt"></i> '.lang('Billing Address').'
    </a>
  </div>
  <div class="collapse'.($coupon_code=='' ? '' : ' show').'" id="collapsecoupon">
    <div class="coupon-input-group">
      <input type="text" class="coupon-input" id="coupon_code" name="coupon_code" placeholder="'.lang('Enter coupon code').'" value="'.$coupon_code.'">
      <button class="coupon-apply-btn" type="button" id="apply_coupon">'.lang('Apply').'</button>
    </div>
    '.$coupon_applied_badge.'
  </div>
</div>';

$delivery_note = '';
if($is_delivery_note=='1')
$delivery_note = '<div class="mt-3 pb-2">
<a class="collpase_link text-dark" data-toggle="collapse" href="#collapsenote" role="button" aria-expanded="false" aria-controls="collapsenote"><i class="fas fa-sticky-note ml-0"></i> '.lang('Write a note').'</a>
<div class="input-group collapse pt-2 show" id="collapsenote">
  <textarea class="form-control" id="delivery_note" name="delivery_note">'.$webhook_data_final["delivery_note"].'</textarea>
</div>';

$seller_info = 
'<h6 class="mt-3">'.lang('Seller').'</h6>
<p class="section-lead ml-0">
'.$store_address.'<br>
'.$store_email.'<br>'.$store_phone.'
</p>
';


$coupon_details =
'<div class="col-12 mt-2 mb-2">
  '.$apply_coupon.'                   
</div>';

$seller_details =
'<div class="col-8 col-md-5">
  '.$seller_info.'                     
</div>';
?>

    
<div class="invoice p-0 pt-2 no_shadow bg-light">
  <?php if($i>0) : ?>
  <div class="invoice-print"> 
    <div class="aliexpress-cart">
      <?php
      // Define variables before using them
      $hide_both = '';
      $hide_delivery_address = '';
      $hide_store_pickup = '';
      $check_store_pickup = '';
      if($is_store_pickup=='0' && $is_home_delivery=='0')
      {
        $hide_both = 'd-none';
      }
      else if($is_store_pickup=='0' && $is_home_delivery=='1')
      {
        $hide_store_pickup = 'd-none';
      }
      else if($is_store_pickup=='1' && $is_home_delivery=='0')
      {
        $hide_delivery_address = 'd-none';
        $check_store_pickup = 'checked';
      }
      else if($is_store_pickup=='1' && $is_home_delivery=='1')
      {
        if($webhook_data_final['store_pickup']=='1' || $pickup!='')
        {
          $check_store_pickup = 'checked';
          $hide_delivery_address = 'd-none';
        }
      }

      $order_schedule_html = $order_schedule_button = '';
      if($is_order_schedule=='1')
      {
        $order_schedule_button = '
        <br><a class="collpase_link text-dark" data-toggle="collapse" href="#collapsetime" role="button" aria-expanded="false" aria-controls="collapsetime"><i class="fas fa-clock ml-0"></i> '.lang('Choose delivey time').'</a>';

        $show_time = $delivery_time!='' ? 'show' : '';

        $order_schedule_html = '   
        <div class="input-group collapse pt-2 '.$show_time.'" id="collapsetime">
          <input type="text" class="form-control" id="delivery_time" readonly name="delivery_time" style="height:50px;" placeholder="'.lang('Set delivery time').'" value="'.$delivery_time.'">
        </div>';
      }
      ?>

      <div class="cart-main-wrapper">
        <div class="cart-left-section">
          <div class="cart-items-container">
            <div class="cart-items-header">
              <i class="fas fa-shopping-cart"></i> <?php echo lang('Shopping Cart'); ?>
            </div>
            <?php echo $table_data;?>
          </div>

          <?php if($webhook_data_final['store_type'] == "physical") : ?>
          <div class="delivery-section-wrapper">
            <ul class="list-unstyled bg-white w-100 pt-3 pr-3 pb-2 pl-3" id="delivery_address_select">
              <li class="media">
                <div class="media-body">
                  
                  <div class='delivery_block <?php echo $hide_both;?>'>
                    <div class="btn btn-sm btn-outline-dark mb-1 float-right pointer <?php echo $hide_delivery_address; ?>" data-close='1' id="showAddress"><i class="fas fa-edit"></i> <?php echo lang('Manage Address'); ?></div>
                    <h6 class="media-title">
                      <a class="text-primary pointer"><i class="fas fa-truck"></i> <?php echo lang('Deliver to'); ?></a>
                    </h6>
                    <div class="text-small text-muted <?php echo $hide_delivery_address; ?>" id="put_delivery_address_list">
                        
                    </div>
                    
                    <div class="form-group mb-0 <?php echo $hide_store_pickup; ?>">
                      <label class="custom-switch mt-2">
                        <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input" id="store_pickup" <?php echo $check_store_pickup; ?>>
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description mb-0"><?php echo $this->lang->line($store_pickup_title); ?></span>
                      </label>
                      <select class="form-control <?php if($check_store_pickup=='') echo 'd-none'; ?>" name="pickup_point_details" id="pickup_point_details">
                        <option value="<?php echo lang('Counter')." : ".$store_address; ?>"><?php echo lang('Counter')." : ".$store_address; ?></option>
                        <?php
                        foreach ($pickup_point_list as $key => $value)
                        {
                          $tmp = $value['point_name'].' : '.$value['point_details'];
                          $select_it = '';
                          if($pickup!="" && $pickup==$value['id']) $select_it = 'selected';
                          else if($webhook_data_final['pickup_point_details'] == $tmp) $select_it = 'selected';
                          echo '<option value="'.$tmp.'" '.$select_it.'>'.$tmp.'</option>';
                        }
                        ?>
                      </select>
                    </div>
                  </div>

                  <?php echo $order_schedule_button; ?>
                  <?php echo $order_schedule_html; ?>
                  <?php echo $delivery_note;?>
                </div>
              </li>
            </ul>
          </div>
          <?php endif; ?>
        </div>

        <div class="cart-summary-wrapper">
          <div class="cart-summary-container">
            <div class="summary-box">
              <div class="summary-box-header">
                <?php echo lang('Order Summary'); ?>
              </div>
              <div class="summary-box-body">
                <div class="summary-row-item">
                  <span class="summary-label"><?php echo lang('Subtotal');?></span>
                  <span class="summary-value"><?php echo $currency_left.$subtotal.$currency_right;?></span>
                </div>
                
                <?php if($shipping_info != "" || $tax_info != ""): ?>
                <?php echo $shipping_info.$tax_info; ?> 
                <?php endif; ?>
                
                <?php echo $coupon_info2; ?>
                
                <?php echo $after_checkout_details; ?>
              </div>
            </div>

            <div class="summary-box">
              <div class="summary-box-header">
                <div class="d-flex justify-content-between align-items-center">
                  <span><?php echo lang('Have a coupon code?'); ?></span>
                  <a class="text-dark" id="showProfile" href="#" style="font-size: 12px;">
                    <i class="fas fa-map-marker-alt"></i> <?php echo lang('Billing Address'); ?>
                  </a>
                </div>
              </div>
              <div class="summary-box-body">
                <div class="collapse show" id="collapsecoupon">
                  <div class="coupon-input-wrapper">
                    <input type="text" class="coupon-input-field" id="coupon_code" name="coupon_code" placeholder="<?php echo lang('Enter coupon code'); ?>" value="<?php echo $coupon_code; ?>">
                    <button class="coupon-apply-button" type="button" id="apply_coupon"><?php echo lang('Apply'); ?></button>
                  </div>
                  <?php if($coupon_code!='' && $coupon_amount>0): ?>
                  <div class="coupon-success-badge">
                    <div style="display: flex; align-items: center; gap: 6px;">
                      <i class="fas fa-check-circle"></i>
                      <span><?php echo lang('Coupon Applied'); ?>: <?php echo $coupon_code; ?> - <?php echo $currency_left.mec_number_format($coupon_amount,$decimal_point,$thousand_comma).$currency_right; ?> <?php echo lang('discount'); ?></span>
                    </div>
                    <button type="button" class="coupon-remove-btn" id="remove_coupon">
                      <i class="fas fa-times"></i> <?php echo lang('Remove'); ?>
                    </button>
                  </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
          </div>
        </div>
      </div>

      <div class="cart-checkout-section">
        <button id="proceed_checkout" class="checkout-button">
          <i class="fas fa-credit-card"></i> <?php echo lang('Proceed Checkout'); ?>
        </button>
      </div>
  <?php endif; ?>
  <?php if($i==0) :?>
      <div class="empty-state">
        <img class="img-fluid" style="height: 200px" src="<?php echo base_url('assets/img/drawkit/drawkit-full-stack-man-colour.svg'); ?>" alt="image">
         <h2 class="mt-0"><?php echo lang('Cart is empty');?></h2>
         <p class="lead"><?php echo lang('There is no product added to cart. Please browse our store and add them to cart to continue.'); ?></p>
         <?php 
         $browse =  base_url('ecommerce/store/'.$store_unique_id);
         $browse = mec_add_get_param($browse,array("subscriber_id"=>$subscriber_id,"pickup"=>$pickup));
         ?>
         <a href="<?php echo $browse;?>" class="btn btn-outline-primary mt-4"><i class="fas fa-store-alt"></i> <?php echo lang('Browse Store');?></a>
      </div>
  <?php endif; ?>
</div>


<?php $buyerphone =  !empty($webhook_data_final['buyer_mobile']) ? $webhook_data_final['buyer_mobile'] : $wc_phone_bill; ?>
<?php 
$current_url = current_url();
$current_url = mec_add_get_param($current_url,array("subscriber_id"=>$subscriber_id));
?>
<script>
  var current_url="<?php echo $current_url; ?>";
  var base_url="<?php echo site_url(); ?>";
  var cart_id = '<?php echo $order_no;?>';
  // Store store_id - explicitly get from webhook_data_final
  // IMPORTANT: Use webhook_data_final['store_id'] directly, not from cart table
  // Use a unique variable name to avoid conflict with bare-theme.php
  var cart_page_store_id = '<?php echo isset($webhook_data_final["store_id"]) && !empty($webhook_data_final["store_id"]) ? $webhook_data_final["store_id"] : "";?>';
  
  // Store the correct store_id in a closure-safe variable
  // Don't use 'store_id' as variable name to avoid conflict with bare-theme.php
  var correct_store_id = (cart_page_store_id && cart_page_store_id != '' && cart_page_store_id != '0' && cart_page_store_id != cart_id) ? cart_page_store_id : '';
  
  // Prevent store_id from being overwritten
  if(correct_store_id == cart_id || correct_store_id == '' || correct_store_id == '0') {
    correct_store_id = '';
  }
  
  var store_type = '<?php echo $webhook_data_final["store_type"];?>';
  var subscriber_id = '<?php echo $subscriber_id;?>';
  
  var order_schedule = '<?php echo $order_schedule;?>';
  var today = new Date();
  var maxday = new Date();
  if(order_schedule=='today') maxday = today;
  else if(order_schedule=='tomorrow') maxday.setDate(maxday.getDate() + 1);
  else if(order_schedule=='week') maxday.setDate(maxday.getDate() + 6);
  else maxday = false; 

  function load_address_list()
  {
    // Use the closure-safe correct_store_id variable
    var function_store_id = correct_store_id;
    
    // Ensure store_id is not empty or same as cart_id
    var send_store_id = function_store_id;
    if(!send_store_id || send_store_id == '' || send_store_id == '0' || send_store_id == cart_id) {
      send_store_id = ''; // Let backend get store_id from cart_id
    }
    
     $("#proceed_checkout").addClass('btn-progress');
     $.ajax({
      context: this,
      type:'POST',
      url:"<?php echo site_url();?>ecommerce/get_buyer_address_list/1",
      data:{subscriber_id:subscriber_id,store_id:send_store_id,cart_id:cart_id},
      success:function(response){
        $("#put_delivery_address_list").html(response);
        $("#proceed_checkout").removeClass('btn-progress');
      }
    });
  }

  $("document").ready(function()  {

    setTimeout(function(){
     load_address_list();
    }, 500);
    
    $('#delivery_time').datetimepicker({
     theme:'light',
     format:'Y-m-d H:i:s',
     formatDate:'Y-m-d H:i:s',
     minDate: today,
     maxDate: maxday
    });

    $(document).on('click','#apply_coupon',function(e){
     e.preventDefault();
     var coupon_code = $("#coupon_code").val();
     
     $("#apply_coupon").addClass("btn-progress");
     $.ajax({
       type: 'POST',
       dataType: 'JSON',
       data: {coupon_code,cart_id,subscriber_id},
       url: '<?php echo base_url('ecommerce/apply_coupon'); ?>',
       success: function(response) {
        $("#apply_coupon").removeClass("btn-progress");
        if(response.status=='0') swal("<?php echo lang('Error'); ?>", response.message, 'error');        
        else 
        {
          swal("<?php echo lang('Success'); ?>", response.message, 'success');
          window.location.replace(current_url);
        }  
       }
     });

    });

    $(document).on('click','#remove_coupon',function(e){
     e.preventDefault();
     
     $("#remove_coupon").addClass("btn-progress");
     $.ajax({
       type: 'POST',
       dataType: 'JSON',
       data: {coupon_code:'',cart_id,subscriber_id},
       url: '<?php echo base_url('ecommerce/apply_coupon'); ?>',
       success: function(response) {
        $("#remove_coupon").removeClass("btn-progress");
        if(response.status=='0') swal("<?php echo lang('Error'); ?>", response.message, 'error');        
        else 
        {
          swal("<?php echo lang('Success'); ?>", response.message, 'success');
          window.location.replace(current_url);
        }  
       }
     });

    });

    $(document).on('change','#store_pickup',function(e){   
     var store_pickup =  '0';
     if ($(this).is(':checked')) store_pickup='1';
     $.ajax({
       type: 'POST',
       data: {cart_id,subscriber_id,store_pickup},
       url: '<?php echo base_url('ecommerce/apply_store_pickup'); ?>',
       success: function(response) {        
          window.location.replace(current_url);
       }
     });

    });

    $(document).on('click','#proceed_checkout',function(e){
    e.preventDefault();
    $("#payment_options").html('');
    var input_name;
    var address_data = new Object();
    var pickup_point_details = $("#pickup_point_details").val();
    var delivery_address_id = $("#select_delivery_address").val();
    var delivery_note = $("#delivery_note").val();
    var delivery_time = $("#delivery_time").val();
    var store_pickup = '0';
    if($("#store_pickup").is(':checked')) store_pickup = '1';

    if(store_type == 'physical') {
      if(!delivery_address_id && $("#store_pickup").is(':checked')==false)
      {      
        swal("<?php echo lang('Error'); ?>", "<?php echo lang('Please select delivery address or pickup point before you proceed.');?>", 'error');
        return false;      
      }
    }
    var subscriber_first_name = '<?php echo $wc_first_name;?>';
    var subscriber_last_name = '<?php echo $wc_last_name;?>';
    var subscriber_auto_id = '<?php echo $webhook_data_final['subscriber_auto_id'] ?? 0 ?>';
    var subscriber_country = '<?php echo $store_country;?>';
    var param = {cart_id:cart_id,subscriber_id:subscriber_id,subscriber_first_name:subscriber_first_name,subscriber_last_name:subscriber_last_name,delivery_address_id:delivery_address_id,store_pickup:store_pickup,pickup_point_details:pickup_point_details,delivery_note:delivery_note,subscriber_country:subscriber_country,store_id:correct_store_id,delivery_time:delivery_time,subscriber_auto_id:subscriber_auto_id};
    var mydata = JSON.stringify(param);
    $("#proceed_checkout").addClass("btn-progress");
    $.ajax({
      type: 'POST',
      dataType: 'JSON',
      data: {mydata:mydata},
      url: '<?php echo base_url('ecommerce/proceed_checkout'); ?>',
      success: function(response) {
       $("#proceed_checkout").removeClass("btn-progress");
       if(response.status=='0')
       {
          var span = document.createElement("span");
          span.innerHTML = response.message;
          if(response.login_popup)
            swal({ title:'<?php echo lang('Error'); ?>', content:span,icon:'error'}).then((value) => {             
             $("#login_form").trigger('click');
            });
          else swal({ title:'<?php echo lang('Error'); ?>', content:span,icon:'error'});            
       }
       else if(response.status=='2')
       {
          var span = document.createElement("span");
          span.innerHTML = response.message;
          swal({ title:'<?php echo lang('Oops!'); ?>', content:span,icon:'warning'});                      
       }     
       else 
       {
         $("#payment_options").html(response.html);
         $("#payment-options-modal").modal();
         // $("#manual-payment-ins-modal .modal-body").html(response.manual_payment_instruction);
         // $("html, body").animate({ scrollTop: $(document).height() }, 100);
         // $("#proceed_checkout").parent().hide();
       }  
      }
    });

    });

    $('.modal').on("hidden.bs.modal", function (e) { 
      if ($('.modal:visible').length) { 
        $('body').addClass('modal-open');
      }
    });

  });
</script>


<script>
  $(document).ready(function() {

    $(document).on('click', '#manual-payment-button', function() {
      $('#payment_modal').modal('toggle');
      $('#manual-payment-modal').modal();
    });   

    $(document).on('click', '#mollie-payment-button', function(e) {
      e.preventDefault();
      var redirect_url=$(this).attr('href');
      window.top.location.href=redirect_url;
    });

    $(document).on('click', '#cod-payment-button', function(e) {
      e.preventDefault();
      var cart_id = '<?php echo $order_no;?>';
      var subscriber_id = '<?php echo $subscriber_id;?>';
      $("#cod-payment-button").addClass("btn-progress");
      $.ajax({
        type: 'POST',
        dataType: 'JSON',
        data: {cart_id,subscriber_id},
        url: '<?php echo base_url('ecommerce/cod_payment'); ?>',
        success: function(response) {
         $("#cod-payment-button").removeClass("btn-progress");
         if (response.error)  swal("<?php echo lang('Error'); ?>", response.error, 'error');        
         else window.location.href = response.redirect;
         } 
        
      });
    });


    // Handles form submit
    $(document).on('click', '#manual-payment-submit', function() {
      
      // Reference to the current el
      var that = this;
      
      // Shows spinner
      $(that).addClass('btn-progress');
      var formData = new FormData($("#manaul_payment_data")[0]);

      $.ajax({
        type: 'POST',
        enctype: 'multipart/form-data',
        dataType: 'JSON',
        url: '<?php echo base_url('ecommerce/manual_payment'); ?>',
        data: formData,
        processData: false,
        contentType: false,
        cache: false,
        success: function(response) {
          if (response.success) {

            $(that).removeClass('btn-progress');
            empty_form_values();
            $('#manual-payment-modal').modal('hide');
            window.location.href = response.redirect;
          }

          if (response.error) {

            $(that).removeClass('btn-progress');

            var span = document.createElement("span");
            span.innerHTML = response.error;

            swal({
              icon: 'error',
              title: '<?php echo lang('Error'); ?>',
              content:span,
            });
          }
        },
        error: function(xhr, status, error) {
          $(that).removeClass('btn-progress');
        },
      });
    });

    $('#manual-payment-modal').on('hidden.bs.modal', function (e) {
        $('#manaul_payment_data').trigger("reset");  
    });

    // Empties form values
    function empty_form_values() {
      $('#paid-amount').val('');
      $('#additional-info').val('');
      $('#paid-currency').prop("selectedIndex", 0);
      $("#manual-payment-file").val('');
      // Clears added file
    }


    $(document).on('click','.delete_item',function(e){
       e.preventDefault();
       var id = $(this).attr("data-id");
       var subscriber_id = '<?php echo $subscriber_id;?>';
       var cart_id = '<?php echo $order_no;?>';
       $.ajax({
         type: 'POST',
         dataType: 'JSON',
         data: {id,cart_id,subscriber_id},
         url: '<?php echo base_url('ecommerce/delete_cart_item'); ?>',
         success: function(response)
         {
            if(response.status=='0') 
            swal("<?php echo lang('Error'); ?>", response.message, 'error');          
            else window.location.replace(current_url);
         }
       });

    });

    $(document).on('click','.add_to_cart2',function(e){
     e.preventDefault();
     var id = $(this).attr("data-id");
     var action = $(this).attr("data-action");
     var quantity = $(this).attr("data-quantity");
     quantity = parseInt(quantity);
     if(quantity<=1 && action=='remove'){
      $('.delete_item[data-id='+id+']').trigger('click');
      return;
     }

     $(".add_to_cart").addClass("btn-progress");
     $.ajax({
       type: 'POST',
       data: {id,action,cart_id,store_id:correct_store_id,subscriber_id},
       url: '<?php echo base_url('ecommerce/update_cart_item_checkout'); ?>',
       success: function(response) {
        window.location.replace(current_url);
       }
     });
    });   

  });
</script>



<div class="modal fade" role="dialog" id="payment-options-modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-credit-card"></i> <?php echo lang('Payment Options');?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-chevron-circle-left"></i></span>
        </button>
      </div>
      <div class="modal-body" id="payment_options">

      </div>
    </div>
  </div>
</div>

<div class="modal fade" role="dialog" id="manual-payment-modal" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-file-invoice-dollar"></i> <?php echo lang('Manual payment');?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="fas fa-chevron-circle-left"></i></span>
        </button>
      </div>
      <div class="modal-body">
        <div class="container p-0">

          <form action="#" method="POST" id="manaul_payment_data" enctype="multipart/form-data">
            <?php if (isset($manual_payment_instruction) && ! empty($manual_payment_instruction)): ?>
            <div class="alert alert-light alert-has-icon">
              <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
              <div class="alert-body">
                <div class="alert-title"><?php echo lang('Instructions'); ?></div>
               <?php echo $manual_payment_instruction; ?>
              </div>
            </div>
            <?php endif; ?>

            <input type="hidden" name="cart_id" id="cart_id" value="<?php echo $order_no;?>">
            <input type="hidden" name="subscriber_id" id="subscriber_id" value="<?php echo $subscriber_id;?>">

            <!-- Paid amount and currency -->
            <div class="row">
              <div class="col-lg-6 mb-2">
                <div class="form-group">
                  <label for="paid-amount"><?php echo lang('Paid Amount'); ?></label>
                  <input type="number" name="paid-amount" id="paid-amount" class="form-control" min="1">
                  <input type="hidden" id="selected-package-id">
                </div>
              </div>
              <div class="col-lg-6 mb-2">
                <div class="form-group">
                  <label for="paid-currency"><?php echo lang('Currency'); ?></label>              
                  <?php echo form_dropdown('paid-currency', $currency_list, $currency, ['id' => 'paid-currency', 'class' => 'form-control select2','style'=>'width:100%']); ?>
                </div>
              </div>
            </div>          
            
            <div class="row">
              <!-- Additional Info -->
              <div class="col-12 mb-2">
                <div class="form-group">
                  <label for="paid-amount"><?php echo lang('Additional Info'); ?></label>
                  <textarea name="additional-info" id="additional-info" class="form-control"></textarea>
                </div>
              </div>
              <!-- Image upload - Dropzone -->
              <div class="col-12">
                <div class="form-group">
                  <label style="width:100%;">
                    <?php echo lang('Attachment'); ?> <?php echo lang('(Max 5MB)');?> 
                    <span class="red float-right"><?php echo lang('Allowed types');?> : pdf, doc, txt, png, jpg & zip</span>
                  </label>
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" id="manual-payment-file" name="manual-payment-file">
                    <label class="custom-file-label" for="manual-payment-file">Choose file</label>
                  </div>
                  
                </div>
              </div>
            </div>
            <button type="button" id="manual-payment-submit" class="btn btn-primary btn-lg mt-2"><i class="fas fa-check-circle"></i> <?php echo lang('Submit'); ?></button> 
          </form>

        </div><!-- ends container -->
      </div><!-- ends modal-body -->
    </div>
  </div>
</div>


<script>

$(".custom-file-input").on("change", function() {
  var fileName = $(this).val().split("\\").pop();
  $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
});
</script>


<style type="text/css">
  .delete_item i{font-size: 14px !important;}
  .cart_actions .input-group-text,.cart_actions button{font-size: 14px !important;font-weight: normal;}
  .collpase_link:hover,#showProfile:hover{text-decoration: none;}
  .form-group{margin-bottom: 10px;}
  .section .section-title{margin:20px 0 20px 0;}  
  #payment_options .list-group-item-action{margin-bottom: 30px;}

  #payment_options .list-group-item-action{margin-bottom: 30px;}
  #payment_options img{margin-right: 20px;}
  @media (max-width: 978px) {
    /* Mobile styles */
  }
  
  .tingle-modal{backdrop-filter:none;}
 .tingle-modal{z-index: 1100;}
</style>

<?php include(APPPATH."views/ecommerce/common_style.php"); ?>

<div class="d-none"><?php echo $mercadopago_button; ?></div>
<div class="d-none"><?php echo $sslcommerz_button; ?></div>
<?php 
if(isset($postdata_array) && !empty($postdata_array)): 
$postdata_array = json_encode($postdata_array);
if($sslcommerz_mode=='live') $direct_api_url = "https://seamless-epay.sslcommerz.com/embed.min.js";
else $direct_api_url = "https://sandbox.sslcommerz.com/embed.min.js";
?>
<script>
   $("document").ready(function()  {
      var direct_api_url = '<?php echo $direct_api_url; ?>';
      var ssl_post_data = '<?php echo $postdata_array; ?>';
      var ssl_post_json_data = JSON.parse(ssl_post_data);
      $('#sslczPayBtn').prop('postdata', ssl_post_json_data);
      (function (window, document) {
          var loader = function () {
              var script = document.createElement("script"), tag = document.getElementsByTagName("script")[0];
              script.src = direct_api_url+'?'+Math.random().toString(36).substring(7);
              tag.parentNode.insertBefore(script, tag);
          };
          window.addEventListener ? window.addEventListener("load", loader, false) : window.attachEvent("onload", loader);
      })(window, document);
    });
</script>
<?php endif; ?>