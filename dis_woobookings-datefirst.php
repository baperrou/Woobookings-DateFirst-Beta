<?php
/*
Plugin Name: Do It Simply Select Date First for Woobookings
Description: WooCommerce Bookings Allow Date first Selection for Products
Version: 1.1.0
Author: DO IT SIMPLY LTD
Author URI: http://doitsimply.co.uk/
GitHub URI: baperrou/WooBooking-DateFiorst
*/

defined( 'ABSPATH' ) or exit;
// decide if the other plugins must be activated to use.
//revisit

define ( 'WCCF_NAME', 'Woocommerce Plugin Example' ) ;
define ( 'WCCF_REQUIRED_PHP_VERSION', '5.4' ) ;                          // because of get_called_class()
define ( 'WCCF_REQUIRED_WP_VERSION', '4.6' ) ;                          // because of esc_textarea()
define ( 'WCCF_REQUIRED_WC_VERSION', '2.6' );                           // because of Shipping Class system

//COMMON IOP
include('outputs.php');
include('woo_changes.php');

// ADD ACTIONS AND ADMIN MENUS

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );


/**
 * Checks if the system requirements are met
 *
 * @return bool True if system requirements are met, false if not
 */
function dis_wcb_datefirst_met () {    
    if ( ! is_plugin_active ( 'woocommerce-bookings/woocommmerce-bookings.php' ) ) {
        return false ;
    }
    

	/* if I want to add version control later    $woocommer_data = get_plugin_data(WP_PLUGIN_DIR .'/woocommerce-bookings/woocommmerce-bookings.php', false, false);

    if (version_compare ($woocommer_data['Version'] , WCCF_REQUIRED_WC_VERSION, '<')){
        return false;
    }
    */

    return true ;
}





//now flush so the tage is activated.
function dis_wcb_datefirst_flush_rewrites() {
	
	flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'dis_wcb_datefirst_flush_rewrites' );

//JAVSCRIPT ACTIONS
add_action('wp_enqueue_scripts','dis_wcb_datefirst_enqueue_script');

function dis_wcb_datefirst_enqueue_script() {
	
	global $post;
	
	$datefirst_yes = get_post_meta( $post->ID, '_dis_wcb_datefirst_yes');
	// post id 17 for my website testing purposes.
	//if($datefirst_yes OR $post->ID == 17) {
	if($datefirst_yes) {
		  wp_enqueue_script( 'jquery-ui-datepicker' );
	    wp_enqueue_script('dis_wcb_datefirst',plugins_url('js/dis_wcb_datefirst.js',__FILE__),array('jquery'), '1.0', true);
	   
		wp_localize_script('dis_wcb_datefirst', 'myAjax', array('ajaxurl' => admin_url('admin-ajax.php'))); 
		// add stylesheet
		 wp_enqueue_style( 'dis_wcb_datefirst_css', plugins_url( '/css/dis_wcb_datefirst_css.css', __FILE__ ) );
	    
	}
    
}
//AJAX ACTIONS
add_action('wp_ajax_nopriv_dis_datefirst_show_single_product', 'dis_datefirst_show_single_product');
add_action('wp_ajax_dis_datefirst_show_single_product', 'dis_datefirst_show_single_product');




//only call these action/filters if the category is set to dropdown and not in admin
if(!is_admin()){
	add_action( 'wp', 'dis_wcb_datefirst_check_product' );
}
function dis_wcb_datefirst_check_product() {
	global $post;
	
	$datefirst_yes = get_post_meta( $post->ID, '_dis_wcb_datefirst_yes', true );
	
	// post id 17 for my website testing purposes.
	//if($datefirst_yes OR $post->ID == 17) {
	if($datefirst_yes) {

			//	add_action('wp_footer','dis_ddd_css');
				
	}
	wp_reset_postdata();

}
/* CREATE some useful hooks
	//add text to.XXXX
	// allow add to cart page transitions
		
*/
// add to cart redirction hook
function dis_wbc_datefirst_after_addtocart_hook($location) {
   // do_action('dis_wbc_datefirst_after_addtocart_hook', $location);
}


add_action('wp_ajax_nopriv_dis_wbc_datefirst_after_addtocart_function', 'dis_wbc_datefirst_after_addtocart_function', 10, 1);
add_action('wp_ajax__dis_wbc_datefirst_after_addtocart_function', 'dis_wbc_datefirst_after_addtocart_function', 10, 1);
 
function dis_wbc_datefirst_after_addtocart_function($location) {
	//options are 'refresh' or 'cart' or do nothing
	$location = 'cart';
	if($location){
		$location = $location;
	}
	else {
		$location = null;
	}
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		echo $location;
		die();
   }
   else {
      header("Location: ".$_SERVER["HTTP_REFERER"]);
      die();
   }   
}
function dis_wcb_step1_hook() {
    do_action('dis_wcb_step1_hook');
}
add_action('dis_wcb_step1_hook', 'dis_wcb_step1_function');
 
function dis_wcb_step1_function() {
	echo '<h3>Step 1 - Choose Your Date</h3>';
}
function dis_wcb_step2_hook($product_id) {
   // do_action('dis_wcb_step2_hook', $product_id);
}
add_action('dis_wcb_step2_hook', 'dis_wcb_step2_function', 10, 1);
 
function dis_wcb_step2_function($product_id) {
	global $post;
	echo '<h3>Step 2 - Choose Your '.get_the_title( $product_id ).'</h3>';
}

function dis_wcb_bookbutton_hook() {
   // do_action('dis_wcb_bookbutton_hook');
}
add_action('dis_wcb_bookbutton_hook', 'dis_wcb_bookbutton_function');
 
function dis_wcb_bookbutton_function() {
	echo 'Book Now';
}
//set basket refresh 
add_action('dis_wbc_datefirst_after_addtocart_hook', 'cart');

