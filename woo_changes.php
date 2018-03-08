<?php 
add_action( 'woocommerce_before_single_product', 'dis_wcb_change_single_product_layout' );
function dis_wcb_change_single_product_layout() {
	global $post;
		// check if it is a date first product
	$datefirst_yes = get_post_meta( $post->ID, '_dis_wcb_datefirst_yes', true );
	
	
	
	if($datefirst_yes){
	    // Disable the hooks so that their order can be changed.
	    //remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
	   // remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
	   // remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
	    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	  //  remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
	   // remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
	   add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt');
	   add_action( 'woocommerce_single_product_summary', 'dis_wcb_add_single_product_layout' );
    }
}

function dis_wcb_add_single_product_layout() {
	$nonce = wp_create_nonce("get_datefirst_nonce");
	$booking= new WC_Product_Booking(get_the_ID());
	$max_time = $booking->max_duration;
	echo '<div id="dis_wcb_response"></div><div class="dis_date_product" data-product="'.get_the_ID().'" data-nonce="' . $nonce . '">';
	echo '<div class="dis_wcb_steps">';
	do_action('dis_wcb_step1_hook');echo '</div>';
	if($max_time > 1) {
	echo '<div class="steps days"><label for="wc_bookings_field_days">Choose Total Days:</label>
		<input type="number" value="1" step="1" min="1" max="'.$max_time.'" size ="2" name="wc_bookings_field_duration2" id="wc_bookings_field_duration2"> </	div>';
	}
	echo '<fieldset class="wc-bookings-date-picker wc_bookings_field_start_date dis_wcb_datefirst_fieldset">
			<div class="dis_wcb_datepicker" id="dis_start_date"></div>
		</fieldset>
		<div id="dis_wcb_datefirst_spinner"></div>
		<div id="single-datefirst" class="product"></div>
		</div>';
}
// Adding a tickbox to product type selection area
add_action( 'woocommerce_product_options_general_product_data', 'dis_wcb_custom_add_custom_fields' );

function dis_wcb_custom_add_custom_fields() {
    // Show the checkbox
   woocommerce_wp_checkbox( array(
        'id' => '_dis_wcb_datefirst_yes',
        'label' => 'Change to Date First',
        'description' => 'Ticking this allows a date to be selected first.',
        'desc_tip' => 'true',

    ) );
}
//now save it as post meta then can be retrieved at any point
add_action( 'woocommerce_process_product_meta', 'dis_wcb_custom_save_custom_fields' );
function dis_wcb_custom_save_custom_fields( $post_id ) {
    if ( ! empty( $_POST['_dis_wcb_datefirst_yes'] ) ) {
        update_post_meta( $post_id, '_dis_wcb_datefirst_yes', esc_attr( $_POST['_dis_wcb_datefirst_yes'] ) );
    }
    else {
	    delete_post_meta($post_id, '_dis_wcb_datefirst_yes');
    }
}