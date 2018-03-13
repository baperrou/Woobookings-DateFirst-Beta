// Globally scoped object

(function ($) {
		
	
	// days to visually disable COME BACK TO IMPROVE
	var disableddates = ["2016-11-30", "2016-12-07", "2016-12-14", "2016-12-25","2016-12-26", "2017-01-11","2017-01-18", "2017-01-25", "2017-02-01", "2017-02-08"];
	//let's create a calendar to start
	$('.dis_wcb_datepicker').datepicker({
		onSelect: function (dateText, inst) {
	        addOrRemoveDate(dateText);
	    },
		
		beforeShowDay: function(date){
        var string = jQuery.datepicker.formatDate('yy-mm-dd', date);
        return [ disableddates.indexOf(string) == -1, 'Closed-today' ]
    	},
      //  minDate: 1 ,
        dayNamesMin: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
        firstDay: 0,
        altField: ".date_chosen",
		altFormat: "d-M-yy",
        dateFormat: 'dd-mm-yy',
         minDate: 1,

        onSelect: function(date) {
	        var DisFirst = {}; 
	       
	        DisFirst.date = $(this).datepicker('getDate') / 1000; 
	    
	        localStorage.setItem("DisFirstDate", DisFirst.date);
	        
	        var dateChosen = $('.date_chosen').val();
			localStorage.setItem("dateChosen", dateChosen);
			$('.date_chosen').html(dateChosen);
			
			 //dateText is a string matching your desired format
			var dateParts = date.split("-");//spilts string using dash
			 //now set each input field (you can figure out the other three)
		    DisFirst.SelectDay = dateParts[0];
		    DisFirst.SelectMonth = dateParts[1];
		    DisFirst.SelectYear = dateParts[2];
	        
	       localStorage.setItem("DisFirstSelectDay", DisFirst.SelectDay);
		   localStorage.setItem("DisFirstSelectMonth",  DisFirst.SelectMonth);
		   localStorage.setItem("DisFirstSelectYear", DisFirst.SelectYear);
		   var dis_product = $('.dis_date_product').attr("data-product");;
		   
	   		var dis_nonce = $('.dis_date_product').attr("data-nonce");
	   		 
	   		//if(dis_time == 'day') {
				var HireLength = $("#wc_bookings_field_duration2").val();
			//}
	   			DisFirst.date = localStorage.getItem("DisFirstDate");			
				DisFirst.SelectDay = localStorage.getItem("DisFirstSelectDay");
				DisFirst.SelectMonth = localStorage.getItem("DisFirstSelectMonth");
				DisFirst.SelectYear = localStorage.getItem("DisFirstSelectYear");				
				 console.log(DisFirst);
	   		  $('#dis_wcb_datefirst_spinner').show();				
	   		 $.ajax({
	         type : "post",
	         dataType : "html",
	         url : myAjax.ajaxurl,
	        
	         data : {action: 'dis_datefirst_show_single_product', ajax:true, p_id : dis_product, nonce: dis_nonce, date:DisFirst.date,  day:DisFirst.SelectDay, month:DisFirst.SelectMonth, year:DisFirst.SelectYear, length:HireLength},
	         success: function(data) { 
		       		$('#dis_wcb_datefirst_spinner').fadeOut();	
	               $('#single-datefirst').html(data);//.prepend('<p>this from fresh</p>');
	               
					jQuery('.group-add').unbind('click').bind('click',function(e) {
										
					 e.preventDefault();					 
					 //removing the counting of forms to run through
					$('#dis_wcb_datefirst_spinner').show();			
			       	var form = $(this).closest("form");
			       		console.log($(form).serialize());							            
			            $.ajax({
			                type: "post",
			               // async: false,
			                url: $(form).attr("action"), 
			                data: $(form).serialize(), 
			                success: function(data2) { 
				                $('#dis_wcb_datefirst_spinner').fadeOut();		
				                 $('<div class="alert alert-success" style="clear:both;display:block"> Added to basket</div>').appendTo(form).fadeOut(3000);
				                 //now transition to cart
				              	var newURL = $(form).attr("action")
				              	
							  	newURL = newURL.substring(0, newURL.indexOf('?'));
							    window.location.href = (newURL);				            
							}
			            });								  						        

			    });
				
			
        
         	},
	          error: function( jqXhr, textStatus, errorThrown ){
		        	console.log( errorThrown );
				}
		     })   			   		   		
	      	      
		}
    });
    

    function getBaseURL () {
   		 return location.protocol + "//" + location.hostname + location.pathname;
	}    
})(jQuery);