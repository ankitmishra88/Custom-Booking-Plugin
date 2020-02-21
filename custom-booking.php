<?php
/**
 * Plugin Name: Custom Booking
 * Plugin URI : http://abc.com
 * Description: Custom-booking is a easily integrable booking system plugin.
 * Version: 1.0.0
 * Author: Ankit Mishra
 */
class CD{
	public $aParam;
	
	public function __construct(){
global $wpdb;
		$this->aParam['cd_table']   = $wpdb->prefix . "custom_addons";
		$this->aParam['cd_slots']   = $wpdb->prefix . "custom_slots";
		$this->aParam['cd_availabilty']   = $wpdb->prefix . "custom_availability";
		add_action( 'admin_menu', array($this, 'add_cd_admin_menu'));
		//add_action( 'init', array($this,'register_booking_product_type') );
         add_action( 'admin_footer', array(&$this,'admin_enqueue_scripts' ));
         add_action( 'wp_footer', array(&$this,'enqueue_scripts' ));
         register_activation_hook( __FILE__, array($this,'activation_cd_plugin') );
	}
	
	public function activation_cd_plugin(){
		
		 global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();				

		$sql = "CREATE TABLE {$this->aParam['cd_table']} (	
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		title varchar(200) NOT NULL,
		description varchar(200),
		thumbnail varchar(200),
		price float(9),
		is_global int(1) NOT NULL DEFAULT 0,
		UNIQUE KEY id (id)
		) $charset_collate;";

		$sql = "CREATE TABLE {$this->aParam['cd_slots']} (	
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		title varchar(200) NOT NULL,
		slots varchar(5000),
		UNIQUE KEY id (id)
		) $charset_collate;";
	
		$sql1 = "CREATE TABLE {$this->aParam['availability']} (	
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		product_id int(9),
		available varchar(2000),
		unavailable varchar(2000),
		UNIQUE KEY id (id)
		) $charset_collate;";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta($sql2);
		dbDelta($sql);
		dbDelta($sql1);
		
	}
	public function add_cd_admin_menu(){
		add_menu_page(
        'Custom Global Addons',
        'Custom Global Addons',
        'manage_options',
        plugin_dir_path(__FILE__) . 'admin/custom-global-addon.php',
        null,
        plugin_dir_url(__FILE__) . 'images/addons.png',
        20
    );
		 add_submenu_page(
        plugin_dir_path(__FILE__) . 'admin/custom-global-addon.php',
        'Edit Addons',
        'All Addons',
        'manage_options',
        plugin_dir_path(__FILE__) . 'admin/custom-global-addon.php',
        null
    );
			 add_submenu_page(
         plugin_dir_path(__FILE__) . 'admin/custom-global-addon.php',
        'Edit Time Slots',
        'All Time Slots',
        'manage_options',
        plugin_dir_path(__FILE__) . 'admin/custom-time-slots.php',
        null
    );

	}
	public function admin_enqueue_scripts(){
		//admin scripts here
wp_enqueue_style( 'cd_jquery_ui_style', plugins_url('form/css/jquery-ui.min.css', __FILE__), array(), '' );
wp_enqueue_style( 'cd_form_multidate_style', plugins_url('assets/css/jquery-ui.multidatespicker.css', __FILE__), array(), '' );
wp_enqueue_script( 'cd_product_form_jquery_step1admin', plugins_url('form/js/jquery-ui.min.js', __FILE__), array(), '' );
wp_enqueue_script( 'cd_product_form_jquery_multidatesadmin', plugins_url('assets/js/jquery-ui.multidatespicker.js', __FILE__), array(), '' );
//wp_enqueue_script( 'cd_product_form_jquery_multiidates', plugins_url('assets/js/jquery-ui.multidatespicker.js', __FILE__), array(), '' );
	}
	public function enqueue_scripts(){
		//scripts here
//Form Styles
wp_enqueue_style( 'cd_opensans_font_style', plugins_url('form/css/opensans-font.css', __FILE__), array(), '' );
wp_enqueue_style( 'cd_mat_font_style', plugins_url('form/fonts/material-design-iconic-font/css/material-design-iconic-font.min.css', __FILE__), array(), '' );
wp_enqueue_style( 'cd_roboto_font_style', plugins_url('form/css/roboto-font.css', __FILE__), array(), '' );
wp_enqueue_style( 'cd_mat_design_style', plugins_url('form/css/jquery-ui.min.css', __FILE__), array(), '' );
wp_enqueue_style( 'cd_form_style', plugins_url('form/css/style.css', __FILE__), array(), '' );
wp_enqueue_style( 'cd_form_book_style', plugins_url('form/css/booking.css', __FILE__), array(), '' );
//wp_enqueue_style( 'cd_product_form_bootstrap_script', plugins_url('form/vendor/bootrap/css/bootstrap.min.css', __FILE__), array(), '' );
//form ends
//wp_enqueue_script( 'cd_product_form_jquery_step3', plugins_url('form/js/jquery-3.3.1.min.js', __FILE__), array(), '' );
wp_enqueue_script( 'cd_product_form_jquery_step2', plugins_url('form/js/jquery.steps.js', __FILE__), array(), '' );
wp_enqueue_script( 'cd_product_form_jquery_step1', plugins_url('form/js/jquery-ui.min.js', __FILE__), array(), '' );

wp_enqueue_script( 'cd_product_form_booking', plugins_url('form/js/booking.js', __FILE__), array(), '' );
wp_enqueue_script( 'cd_product_form_main4', plugins_url('form/js/main.js', __FILE__), array(), '' );
//wp_enqueue_script( 'cd_product_form_bootstrap_script', plugins_url('form/vendor/bootrap/js/bootstrap.min.js', __FILE__), array(), '' );
		
	}
	

}
$cd=new CD;
add_filter( 'product_type_selector', 'cd_add_booking_product_type' );
 
function cd_add_booking_product_type( $types ){
    $types[ 'Booking' ] = 'Booking';
    return $types;
}
 
// --------------------------
// #2 Add New Product Type Class
 
add_action( 'init', 'cd_create_booking_product_type' );
 
function cd_create_booking_product_type(){
    class WC_Product_Booking extends WC_Product {
      public function get_type() {
         return 'Booking';
      }
    }
}
 
// --------------------------
// #3 Load New Product Type Class
 
add_filter( 'woocommerce_product_class', 'cd_woocommerce_product_class', 10, 2 );
 
function cd_woocommerce_product_class( $classname, $product_type ) {
    if ( $product_type == 'Booking' ) { 
        $classname = 'WC_Product_Booking';
    }
    return $classname;
}
add_filter( 'woocommerce_product_data_tabs', 'cd_booking_product_tab' );
 
function cd_booking_product_tab( $tabs) {
		
    $tabs['Booking'] = array(
      'label'	 => __( 'Booking Options', 'dm_product' ),
      'target' => 'booking_product_options',
      'class'  => 'show_if_booking_product',
     );
    return $tabs;
}

add_action( 'woocommerce_product_data_panels', 'cd_booking_product_tab_product_tab_content' );
 
function cd_booking_product_tab_product_tab_content() {
 
 ?><div id='booking_product_options' class='panel woocommerce_options_panel'><?php
 ?>
<div class="options_group">
<?php
	$args = array(
  		'label' => 'Type Of Date', // Text in Label
  		'class' => '',
 		 'style' => '',
		 'wrapper_class' => '',
  		//'value' => '',  if empty, retrieved from post meta where id is the meta_key
  		'id' => 'cd_availability_type', // required
  		'options' => array(
			"0"=>__('Unavailable'),
			"1"=>__('Available')
			), // Options for select, array
  		'desc_tip' => '',
  		'custom_attributes' => '', // array of attributes 
  		'description' => 'Select Type of date(available or unavailable)'
);

woocommerce_wp_radio( $args );
 ?>
</div>
<div class='options_group'><?php
				
    woocommerce_wp_text_input(
	array(
	  'id' => 'cd_dates',
	  'label' => __( 'Dates', 'booking_product' ),
	  'placeholder' => '',
	  'desc_tip' => 'true',
	  'description' => __( 'Select Date Of Availabilty or unavailability', 'booking_product' ),
	  'type' => 'text'
	)
    );
wp_enqueue_script( 'cd_product', plugins_url('assets/js/product.js', __FILE__), array(), '' );
 ?></div>
<div class='options_group'><?php
				
    woocommerce_wp_text_input(
	array(
	  'id' => 'booking_product_info',
	  'label' => __( 'Booking Product Spec', 'booking_product' ),
	  'placeholder' => '',
	  'desc_tip' => 'true',
	  'description' => __( 'Enter Booking product Base Price.', 'booking_product' ),
	  'type' => 'number'
	)
    );
 ?></div>
<div class='options_group'><h3>Addons</h3><?php
	global $post_id;
global $wpdb;
$selected=get_post_meta($post_id,'booking_product_addons');
$selected=unserialize($selected[0]);
$ad_table= $wpdb->prefix . "custom_addons";
$slot_table= $wpdb->prefix . "custom_slots";
$q="SELECT * FROM $ad_table";
$res=$wpdb->get_results($q,"ARRAY_A");
foreach($res as $key=>$val){
$custom_attribute=array();
if(in_array($val['id'],$selected))
$custom_attribute=array('checked'=>true);				
    woocommerce_wp_text_input(
	array(
	id=>'ad'.$val['id'],
	  'name'=>'booking_product_addons[]',
	  'label' => __( $val['title'], 'booking_product' ),
	 'class'=>'checkbox',
	  'placeholder' => '',
	  'desc_tip' => 'true',
	  'description' => __( 'check to select', 'booking_product' ),
	  'type' => 'checkbox',
	  'checked'=>'false',
	  'value'=> $val['id'],
	 'custom_attributes'=>$custom_attribute
	)
    );
}

 ?></div>
<div class="options_group">
<?php
$q="SELECT id,title FROM $slot_table";
$res=$wpdb->get_results($q,"ARRAY_A");
$options=array();
foreach($res as $key=>$val){
$options[$val['id']]= __("{$val['title']}", 'booking_product' );
}
woocommerce_wp_select( array(
        'id'      => 'custom_slot_form',
        'label'   => __( 'Select Slot Form', 'booking_product' ),
        'options' =>  $options, 
    ) );

?>
</div>
 </div><?php
}
add_action( 'woocommerce_process_product_meta', 'save_booking_product_settings' );
	
function save_booking_product_settings( $post_id ){
		
    $booking_product_info = $_POST['booking_product_info'];
		
    if( !empty( $booking_product_info ) ) {
 
	update_post_meta( $post_id, 'booking_product_info', esc_attr( $booking_product_info ) );
    }
$booking_product_addons=$_POST['booking_product_addons'];
update_post_meta( $post_id, 'booking_product_addons', serialize( $booking_product_addons ) );
$booking_slot_form=$_POST['custom_slot_form'];
update_post_meta( $post_id, 'custom_slot_form', $booking_slot_form );
$availability=$_POST['cd_availability_type'];
update_post_meta( $post_id, 'cd_availability_type', $availability );
$dates=$_POST['cd_dates'];
update_post_meta( $post_id, 'cd_dates', $dates );


}

add_action( 'woocommerce_single_product_summary', 'booking_product_front' );
	
function booking_product_front () {
 //no code
}

//Custom Booking Form

add_action('woocommerce_before_add_to_cart_button','wdm_add_custom_fields');
/**
 * Adds custom field for Product
 * @return [type] [description]
 */
function wdm_add_custom_fields()
{

    global $product,$wpdb;
$id=$product->get_id();
$addons=get_post_meta($id,'booking_product_addons')[0];
$slots=get_post_meta($id,'custom_slot_form')[0];
$dates=get_post_meta($id,'cd_dates')[0];
$type=get_post_meta($id,'cd_availability_type')[0];
//echo $slots;
//echo $addons;
$addons=unserialize($addons);
$condition='';
$count=0;
foreach($addons as $key=>$val){
if($count)
$condition.=" OR id={$val}";
else{
$count++;
$condition.=" id={$val}";
}
}
//echo $condition;
$ad_table= $wpdb->prefix . "custom_addons";
$slot_table= $wpdb->prefix . "custom_slots";
$q="SELECT * FROM $ad_table WHERE {$condition}";
$adds=$wpdb->get_results($q,"ARRAY_A");
$q="SELECT * FROM $slot_table WHERE id={$slots}";
$slots=$wpdb->get_results($q,"ARRAY_A")[0];

$slots=unserialize($slots['slots']);
wp_enqueue_script( 'cd_product_form_jquery_step3', plugins_url('form/js/jquery-3.3.1.min.js', __FILE__), array(), '' );
//print_r($slots);
    ob_start();
include(plugin_dir_path( __FILE__ ) . 'form/index.php');

    $content = ob_get_contents();
    ob_end_flush();
   wc_print_notices();
    return $content;
}
add_filter( 'woocommerce_product_single_add_to_cart_text', 'custom_single_add_to_cart_text' );
function custom_single_add_to_cart_text() {
	return 'BOOK NOW';
}
function so_validate_add_cart_item( $passed, $product_id, $quantity, $variation_id = '', $variations= '' ) {
    // do your validation, if not met switch $passed to false
      if ( !function_exists( 'wc_add_notice' ) ) { 
    require_once '/includes/wc-notice-functions.php'; 
} 
   $message='';
if(!isset($_REQUEST['date'])||$_REQUEST['date']==''){
$message="Date not selected";
$notice_type = 'error'; 
$result = wc_add_notice($message, $notice_type); 
$passed=false;
}
if(!isset($_REQUEST['slot'])||$_REQUEST['slot']==''){
$message="Slot not selected";
$notice_type = 'error'; 
$result = wc_add_notice($message, $notice_type); 
$passed=false;
}
    return $passed;

}
add_filter( 'woocommerce_add_to_cart_validation', 'so_validate_add_cart_item', 10, 5 );
add_filter('woocommerce_add_cart_item_data','wdm_add_item_data',10,3);
function wdm_add_item_data($cart_item_data, $product_id, $variation_id)
{
	$product = wc_get_product( $product_id );
        $cart_item_data['original_price']= $product->get_price();
global $wpdb;
    if(isset($_REQUEST['date']))
    {
        $cart_item_data['date'] = sanitize_text_field($_REQUEST['date']);
    }
   if(isset($_REQUEST['slot']))
    {
        $cart_item_data['slot'] = sanitize_text_field($_REQUEST['slot']);
    }
 if(isset($_REQUEST['custom_note']))
    {
        $cart_item_data['custom_note'] = sanitize_text_field($_REQUEST['custom_note']);
    }
   if(isset($_REQUEST['addons']))
    {
$ads=array();
$ad_table= $wpdb->prefix . "custom_addons";
foreach($_REQUEST['addons'] as $key=>$val){
$q="SELECT * FROM {$ad_table} where id={$val}";
$res=$wpdb->get_results($q,"ARRAY_A");
foreach($res as $key1=>$val1)
$ads[]=array('title'=>$val1['title'],'price'=>$val1['price']);
$cart_item_data['addons'] = serialize($ads);
}

    }

    return $cart_item_data;
}
add_filter('woocommerce_get_item_data','wdm_add_item_meta',10,2);
function wdm_add_item_meta($item_data, $cart_item)
{

     
    if(array_key_exists('date', $cart_item))
    {
        $custom_details = $cart_item['date'];

        $item_data[] = array(
            'key'   => 'Date:',
            'value' => $custom_details
        );
    }
 if(array_key_exists('slot', $cart_item))
    {
        $custom_details = $cart_item['slot'];

        $item_data[] = array(
            'key'   => 'Slot',
            'value' => $custom_details
        );
    }
 if(array_key_exists('original_price', $cart_item))
    {
$custom_details = $cart_item['original_price'];
        $item_data[] = array(
            'key'   => 'Product Price',
            'value' => $custom_details
        );
    }


if(array_key_exists('addons',$cart_item)){
$addons=unserialize($cart_item['addons']);
foreach($addons as $key=>$val){
$item_data[] = array(
            'key'   => $val['title'],
            'value' => 'Rs'.$val['price']
        );
}



}
 if(array_key_exists('custom_note', $cart_item))
    {
        $custom_details = $cart_item['custom_note'];

        $item_data[] = array(
            'key'   => 'Customization Note',
            'value' => $custom_details
        );
    }

    return $item_data;
}
add_action( 'woocommerce_before_calculate_totals', 'cd_recalc_price' );
 
function cd_recalc_price( $cart_object ) {
	foreach ( $cart_object->get_cart() as $hash => $value ) {
if(array_key_exists('addons',$value)){
		$extra=unserialize($value['addons']);
	$charges=0;
		foreach($extra as $key=>$add)
		$charges+=$add['price'];
		$value['data']->set_price( $value['data']->get_regular_price()+$charges );}
	}
}
add_action( 'woocommerce_checkout_create_order_line_item', 'wdm_add_custom_order_line_item_meta',10,4 );

function wdm_add_custom_order_line_item_meta($item, $cart_item_key, $values, $order)
{

    if(array_key_exists('date', $values))
    {
        $item->add_meta_data('Date',$values['date']);
    }
if(array_key_exists('date', $values))
    {
        $item->add_meta_data('Customization Note',$values['custom_note']);
    }
if(array_key_exists('slot', $values))
    {
        $item->add_meta_data('Slot Timing',$values['slot']);
    }
if(array_key_exists('original_price', $values))
    {
        $item->add_meta_data('Product Price',$values['original_price']);
    }
if(array_key_exists('addons',$values)){
$add=unserialize($values['addons']);
foreach($add as $key=>$val){

        $item->add_meta_data('Addons',$val['title'].'(Rs'.$val['price'].')');
}
}
}
add_filter( 'woocommerce_is_sold_individually', 'wc_remove_all_quantity_fields', 10, 2 );
function wc_remove_all_quantity_fields( $return, $product ) 
{
    return( true );
}