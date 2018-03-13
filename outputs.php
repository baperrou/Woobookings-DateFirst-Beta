<?php //OUTPUTS
// only used for start amd finish times bookings.
function dis_datefirst_check_resource_avail($date, $p_id, $resource_id, $number = 1, $start, $end) {
	// resources have to be checked return for FUTURE update
	
	$booking=new WC_Product_Booking($p_id);
	
		//as long as the booking beginning times do not fall outside of the lower limit the check will work
	$avail = $booking->get_available_bookings($start, $end,  $resource_id, $number );
	
	if(is_int($avail)):$avail = $avail; else: $avail = 0;endif;
	if($avail > 0): $is_available = true; else: $is_available = false; endif;
	$show_avail = array($is_available, $avail);
	return $show_avail;	
	
}
// create a function that usings the booking object to investigate
// persons ticked
//max days allowed
// other restrictions to be added as developed.

function dis_datefirst_get_restrictions($p_id) {
	$booking= new WC_Product_Booking($p_id);
	$person =  $booking->has_persons;
	$max_person =  $booking->max_persons;
	$time_unit = $booking->duration_unit;
	$max_time = $booking->max_duration;
	/*[min_date_unit] => hour
            [min_date_value] => 12
            [min_duration] => 1
            [min_persons] => 1
            [duration_type] => customer
            [duration_unit] => day
            [duration] => 1
            [max_duration] => 7
            [max_persons] => 0
            
            [block_cost] => 65
            [buffer_period] => 0
            [calendar_display_mode] => 
            [cancel_limit_unit] => month
            [cancel_limit] => 1
            [check_start_block_only] => 
            [cost] => 0
            [default_date_availability] => available
            [display_cost] => 
            [duration_type] => customer
            [duration_unit] => day
            [duration] => 1
            [enable_range_picker] => 
            [first_block_time] => 
            [has_person_cost_multiplier] => 1
            [has_person_qty_multiplier] => 1
            [has_person_types] => 
            [has_persons] => 1
            [has_resources] => 1
            [has_restricted_days] => 
            [max_date_unit] => month
            [max_date_value] => 12
            [max_duration] => 7
            [max_persons] => 0
            [min_date_unit] => hour
            [min_date_value] => 12
            [min_duration] => 1
            [min_persons] => 1
            [person_types] => Array

			*/
	//echo '<pre>';print_r($booking);echo '</pre>';
	
	
}

// MOD from dropdown list to checklist with forms. Keep V2
function dis_datefirst_show_single_product() {
	$nonce = wp_create_nonce("new_bike_nonce");
	if ( !wp_verify_nonce( $_REQUEST['nonce'], "get_datefirst_nonce")) {
      exit("There was an error selecting the date.  Please start again.");
	}  
	//needed to test for resource availability 
	
	if($_REQUEST["date"]):$date = $_REQUEST["date"]; endif;
	
	//these are all variables needed to manually add a bookings item to the basket
	
	
	if($_REQUEST["day"]):$day = $_REQUEST["day"];endif;
	if($_REQUEST["month"]):$month = $_REQUEST["month"];endif;
	if($_REQUEST["year"]):$year = $_REQUEST["year"];endif;
	
	if($_REQUEST["p_id"]):$p_id = $_REQUEST["p_id"];endif;
	if($_REQUEST["length"]):$length = $_REQUEST["length"];endif;
	
	//ADD Some customization here		dis_datefirst_show_single_product
	
  
	
	$out .= get_datefirst_product_resources($p_id, $date, $day, $month, $year, $length);	
	
	
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		echo '<div class="dis_wcb_steps">';
		do_action('dis_wcb_step2_hook', $p_id);
		echo ' </div>'	;
		echo $out;
		die();
   }
   else {
      header("Location: ".$_SERVER["HTTP_REFERER"]);
      die();
   }   
		
}
// checking each resource for availability and then building the form to use to add to cart
function get_datefirst_product_resources($p_id, $date, $day, $month, $year, $length) {
	
	//print_r($time_ck);
	//time isn't needed for day
	//may need to change
	$date_start = $date ;
	$date_end = $date + ($length*3600);
	//$date_end = $date + (24*3600 * 2) ;
	
	
	global $wpdb;
	$table_name = $wpdb->prefix . 'wc_booking_relationships';
	$table_posts = $wpdb->prefix .'posts';
	
	
		$closed_out = array();
		
			// select all resources for the DATE FIRST products then test for availability
			$b_resources = $wpdb->get_results($wpdb->prepare("SELECT resource_id, product_id, post_title FROM $table_name LEFT JOIN $table_posts ON $table_posts.ID = $table_name.resource_id  WHERE  product_id = %s ORDER BY product_id, resource_id", $p_id));
			
			$count = count($b_resources);
			//print_r($b_resources);
			  foreach($b_resources as $r):	
			 
			  		
					$resource_id = $r->resource_id;
					
					$datefirst_name = $r->post_title;
					$category = $row['category'];
					$categories[$category][] = $row['product'];
					
						
				 	$result = dis_datefirst_check_resource_avail($date, $p_id, $resource_id, $number = 1, $date_start, $date_end);
		        	$avail = $result[1];
		        	$yes_no = $result[0];
	
		       		$closed_out[] = $avail;
					
					if($avail > 0 && is_int($avail)) :
					$name = $datefirst_name. " (".$avail ." Avail)";
					$out .= dis_datefirst_get_single_to_group_booking($p_id, $time, $day, $month, $year, $resource_id, $avail, $name, $length);
					else:
					$out .= '<div class="warning-datefirst">'.$datefirst_name. " IS NO LONGER AVAILABLE</div>";
					endif;	
			endforeach;
			$out .= '</div>';
			$out .= '</div>';
			
		if(is_array($closed_out)) {
		if(!array_filter($closed_out)) {
			$out ='<h3 class="steps">No '.$datefirst_name.' available on this date</h3>';
	
		}
	}
	return $out;
}

//Keep V2
// showing forms for each product by resource to be able to add to basket
function dis_datefirst_get_single_to_group_booking($id, $time, $day, $month, $year, $resource=null, $m_avail, $name, $length) {
		
		// calling product object to create manual product page
		$product = new WC_Product($id);
		$booking= new WC_Product_Booking($id);
		$person =  $booking->has_persons;
		$max_person =  $booking->max_persons;
		$time_unit = $booking->duration_unit;
		$max_time = $booking->max_duration;
		//echo '<pre>';print_r($booking); echo '</pre>';
		if (class_exists('Product_Addon_Display')) {
		$addon = new Product_Addon_Display($id);
		}
	//	echo '<pre>';($addon->display());'</pre>';
		if($count):$count=$count;else:$count=1;endif;
		// need to find the limits put on the product such as days available
		
		// get start time and whether half day for full
		//set to midnight for days MAY CHNAGE
		$avail = '01:00';
		
		// now create each product as form group for adding to basket
		$out .= '<form class="cart count-forms manual-mine-'.$count.' " action="'.get_permalink( wc_get_page_id( 'cart' ) ) .'?add-to-cart" method="post" enctype="multipart/form-data">';

	
		$out .='<div itemscope="" itemtype="http://schema.org/Product" id="product-'.$id.'" class="single-product">';
			
		$out .= '<div class="datefirst-name"><p itemprop="name" class="product_title entry-title">'.$name.'</p>';
		$out .= '<ul>';
		// moved days to above calendar for calculation
		//$out .='<li class="days text-right"><label for="wc_bookings_field_days">Total Days:</label><input type="number" value="1" step="1" min="1" max="'.$max_time.'" size ="2" name="wc_bookings_field_duration" id="wc_bookings_field_duration"> </li>';
		if($person) {	
		$out .='<li class="text-right"><label for="wc_bookings_field_persons">Persons:</label> <input type="number" value="1" step="1" min="1" max ="'.$max_person.'" size ="'.($m_avail+1).'" max="'.$m_avail.'" name="wc_bookings_field_persons" id="wc_bookings_field_persons"> </li>';
		}
		
		$out .='</ul>';
		
		$out .='</div>';
		
	
		// find out whether in stock or not (not needed for CURRENT but keep for base functional reasons)
		$link = $product->is_in_stock() ? 'InStock' : 'OutOfStock';
		$out .='<link itemprop="availability" href="http://schema.org/'.$link.'" />';
	
		
		$out .= '<div class="datefirst-detail">';
		if($addon) {
		// add product-addons if they exist
		ob_start();$addon->display($id);$out .= ob_get_clean();
		}

		$out .='<fieldset class="wc-bookings-date-picker wc_bookings_field_start_date" style="border:none;">';
		$out .= '<input type="number" value="'.$length.'" step="1" min="1" max="'.$max_time.'" size ="2" name="wc_bookings_field_duration" id="wc_bookings_field_duration" class="hidden">';

		$out .='<div class="wc-bookings-date-picker-date-fields" style="display: none;">';
		$out .='<input type="text" value="'.$year.'" name="wc_bookings_field_start_date_year" placeholder="YYYY" size="4" class="booking_date_year">';
		$out .='<input type="text" name="wc_bookings_field_start_date_month" placeholder="mm" size="2" value="'.$month.'" class="booking_date_month">';
		$out .='<input type="text" name="wc_bookings_field_start_date_day" placeholder="dd" size="2" value="'.$day.'" class="booking_date_day">
			</div>';
		$out .= '</fieldset>';
			//if($time !='day'){
			//	$out .='<input type="hidden" class="required_for_calculation" name="wc_bookings_field_start_date_time" id="wc_bookings_field_start_date" value="'.$avail.'">';
			//}
		$out .='<input type="hidden" name="add-to-cart" value="'.$id.'">';
		
		$out .='<select name="wc_bookings_field_resource" id="wc_bookings_field_resource" class="hidden"><option value="'.$resource.'"></option></select>';
		
		
		// manual connection text will need to be created before this point
		
		
		


		$out .= '<button type="submit" class="pull-right single_add_to_cart_button btn group-add alt" '.$seat_class.'>';
		$out .= 'Book Now';
		$out .= '</button>';
		$out .= '</div>';

		$out .='</div></form>';
		//$out .= '</div>';
		// start creation  of manual cart			
		
		return $out;  
}
