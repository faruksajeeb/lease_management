//<!--
var url_prefix = '/fda/'; /*should be start and end with / (slash)*/

$(document).ready(function(){
	init();
});

init = function(){
	common_functions();
	
	if( $("#header_container").length ){
		header_footer();
	}
	
	if( $("#page_header_visitor").length ){
		page_header_visitor();
	}
	
	var page = $('#wrapper').find('.page_identifier').attr('id');
	switch(page){
		case 'page_index': page_index(); break;
		case 'page_create_user': page_create_user(); break;
		//case 'page_manage_user': page_manage_user(); break;
		case 'page_manage_role': page_manage_role(); break;
		case 'page_create_requisition': page_create_requisition(); break;
		case 'page_manage_requisition': page_manage_requisition(); break;
		case 'page_card_receive': page_card_receive(); break;
		case 'page_visitor_center_form': page_visitor_center_form(); break;
		case 'page_create_request': page_create_request(); break;
		case 'page_manage_request': page_manage_request(); break;
	}
	
	$("input[type=submit]").click(function(){
		$('.chzn-select,#nic_editor').hide();
	});
};


common_functions = function(){
	/*action confirmation*/
	$('.confirmation').on('click', function(){
		if(confirm('Do you really want to perform this action?')){
			return true;
		}else{
			return false;	
		}
	});
	
	/*chosen select box*/
	$('.chosen').chosen();
	
	//$('#data_table').DataTable();
	$('body').delegate('#data_table tr', 'click', function (evt) {
		$('#data_table tr').removeClass('row_selected');
		$(this).addClass('row_selected');
	});
	
	/*date picker*/
	$('.date_time_picker_24').datetimepicker({
		format:'Y-m-d H:i:s',
		 allowTimes:[
		  '00:00', '00:30', '01:00', '01:30',
		  '02:00', '02:30', '03:00', '03:30',
		  '04:00', '04:30', '05:00', '05:30',
		  '06:00', '06:30', '07:00', '07:30',
		  '08:00', '08:30', '09:00', '09:30',
		  '10:00', '10:30', '11:00', '11:30',
		  '12:00', '12:30', '13:00', '13:30',
		  '14:00', '14:30', '15:00', '15:30',
		  '16:00', '16:30', '17:00', '17:30',
		  '18:00', '18:30', '19:00', '19:30',
		  '20:00', '20:30', '21:00', '21:30',
		  '22:00', '22:30', '23:00', '23:30'
 		]
	});
	$('.time_picker_24').datetimepicker({
		datepicker:false,
		format:'H:i:s',
		 allowTimes:[
		  '00:00', '00:30', '01:00', '01:30',
		  '02:00', '02:30', '03:00', '03:30',
		  '04:00', '04:30', '05:00', '05:30',
		  '06:00', '06:30', '07:00', '07:30',
		  '08:00', '08:30', '09:00', '09:30',
		  '10:00', '10:30', '11:00', '11:30',
		  '12:00', '12:30', '13:00', '13:30',
		  '14:00', '14:30', '15:00', '15:30',
		  '16:00', '16:30', '17:00', '17:30',
		  '18:00', '18:30', '19:00', '19:30',
		  '20:00', '20:30', '21:00', '21:30',
		  '22:00', '22:30', '23:00', '23:30'
 		]
	});
	
	$('.date_picker').datetimepicker({
		timepicker:false,
		format:'Y-m-d',
		scrollMonth : false,
		scrollInput : false
	});
	$('.date_time_picker_24,.date_picker,.date_picker_end').on('change', function(){
		$('.xdsoft_datetimepicker').hide();
	});
	$(document).on("change","#date_from", function(){
		$('#date_end').val('');
		var start_date = $(this).val();
		if(start_date)
		{ 
			var date_picker_end = new Date(start_date);
			date_picker_end.setDate(date_picker_end.getDate());
			$(".date_picker_end").datetimepicker("destroy");
			$(".date_picker_end").datetimepicker({
				format:'Y-m-d',
				timepicker:false,
				scrollMonth : false,
				scrollInput : false,
				minDate: new Date(date_picker_end)
			});
		}
	});
	
	$('.month_picker').monthpicker({'minYear' : 2010, 'maxYear' : 2025, 'class':'input_style'});
	if( $('.month_picker').attr('required') ){
		$('.monthpick, .yearpick').attr('required',true);
	}
	
	/*meassge show and hide after few milli-second*/
	if( $("#message_board").length ){
		$('#message_board').delay(9000).hide('slow');	
	}
	
	$('.refresh').on('click', function(){
		location.href = location.href;
	});
	
	// remove any file with attribute data-path="ABSOLUTE_PATH_SHORT" data-name="FILE_NAME"
	$('.btn_file_delete').click(function(){
		if(confirm('Do you really want to remove the file?')){
			//return true;
		}else{
			return false;	
		}
		
		var me = $(this);
		my_parent = me.parent();
		//var token = $('#token').val();
		var data_path = me.attr('data-path');
		var data_name = me.attr('data-name');
		my_parent.append('<div class="spinner">&nbsp;</div>');
		var spinner = my_parent.find('.spinner');
		spinner.css({"display":"inline-block","vertical-align":"middle"});

		var jqxhr = $.ajax({
		 type: "GET",
		 url: url_prefix + "file_delete?data_path="+data_path+"&data_name="+data_name
		}).done(function(msg){
		 switch(msg.trim()){
		  case 'error':
		   alert( "Error! Please report to Admin." );
		   spinner.hide();
		   break;
		   
		  case 'success':
		  	my_parent.remove();
		   	break;
		   	
		  default:
		  	alert( "We could not execute your request. Please try again later or report to authority.");
		   	spinner.hide();
		   	break;
		 }
		}).fail(function() {
			alert( "We could not execute your request. Please try again later or report to authority." );
			spinner.hide();
		});
		
		return false;
		
	});
	
	// form submit by ajax
	$('.form_post_ajax').submit(function(){
		var me = $(this);
		var myFormData = new FormData($(this)[0]);
		//myFormData.append('EmployeeID', UserID);
		var data = myFormData;
		
		var route = $(this).attr('form-route');
		if( !data || !route ){ 
			alert('Invalid Request!');
			return false; 
		}
		
		if( !$(this).parsley().validate() ){
			//alert('Parsley Error!');
			return false;
		}

		var me = $(this);
		var token = $('#token').val();
		var spinner = me.find('.spinner');
		spinner.show();

		var jqxhr = $.ajax({
			type: "POST",
		 	url: url_prefix + route,
		 	cache: false,
			processData: false, // important
			contentType: false, // important
			data: data
		}).done(function(msg){
			//example - update_success, update_success:maname_author -- after : the value will be treated as route
			var identifier = '';
			var param = msg.trim().split(":");
			if( param[0] ){ identifier = param[0]; }
			
			switch(identifier.trim()){
				case 'success':
				 alert("Your request has been completed successfully!");
				 spinner.hide();
				 break;
				 
				case 'error':
				 alert("Error! Please report to Admin.");
				 spinner.hide();
				 break;
				 
				case 'denied':
				 alert("Unauthorized Access!");
				 spinner.hide();
				 break;
				 
				case 'success':
					alert("Your request has been executed successfully!");
					if( param[1] ){ location.href = url_prefix + param[1]; return false; }
					location.href=location.href;
				 	break;
				 	
				case 'update_success':
					alert("Your record has been updated successfully!");
					if( param[1] ){ location.href = url_prefix + param[1]; return false; }
					location.href=location.href;
				 	break;
				 	
				case 'insert_success':
					alert("New record has been created successfully!");
					if( param[1] ){ location.href = url_prefix + param[1]; return false; }
					location.href=location.href;
				 	break;
				 	
				case 'request':
					if( param[1] ){ location.href = url_prefix + param[1]; return false; }
					location.href=location.href;
				 	break;
				 	
				default:
					alert(msg);
				 	spinner.hide();
				 	break;
			}
		}).fail(function() {
			alert( "We could not execute your request. Please try again later or report to authority." );
			spinner.hide();
		});
		
		return false;
	});
	
	// ajax call for action button
	$('.btn_ajax').click(function(){
		if(confirm('Do you really want to perform this action?')){
			// route must be href of a tag and it must be full route address including url_prefix
			var route = $(this).attr('href');
			if( !route ){ 
				alert('Invalid Request!');
				return false; 
			}

			var me = $(this);
			var spinner = $(this).parent().find('.spinner');
			spinner.show();
	
			var jqxhr = $.ajax({
				type: "GET",
			 	url: route
			}).done(function(msg){
				switch(msg.trim()){
					case 'success':
					 alert("Your request has been completed successfully!");
					 location.href=location.href;
					 break;
					 
					case 'error':
					 alert("Error! Please report to Admin.");
					 spinner.hide();
					 break;
					 
					case 'denied':
					 alert("Unauthorized Access!");
					 spinner.hide();
					 break;
					 
					case 'update_success':
						alert("Your record has been updated successfully!");
						location.href=location.href;
					 	break;
					 	
					case 'insert_success':
						alert("New record has been created successfully!");
						location.href=location.href;
					 	break;
					 	
					default:
						alert(msg.trim());
					 	spinner.hide();
					 	break;
				}
			}).fail(function(){
				alert( "We could not execute your request. Please try again later or report to authority." );
				spinner.hide();
			});
		}
		return false;
	});
	
	$('.spin').hover(function(){
		$(this).animateRotate(360, 300);
	},function(){
		$(this).animateRotate(-360, 300);	
	});
	
	/*drop-down box -> for small screen*/
	$('.list_for_small_screen').change(function(){
		var get_title = $(this).val();
		if(get_title){
			location.href = get_title;
		}
	});
	
	/*custom place holder for input box*/
	$('.placeholder .caption').each(function(){
		var pre_obj = $(this).prev();
		var pos = pre_obj.position();
		$(this).css('top', (pos.top+4) );
		$(this).css('padding-top', pre_obj.css('padding-top'));
		$(this).css('padding-right', pre_obj.css('padding-right'));
		$(this).css('padding-bottom', pre_obj.css('padding-bottom'));
		$(this).css('padding-left', pre_obj.css('padding-left'));
	});
	
	//adding caption in image gallery
	$.featherlightGallery.prototype.afterContent = function() {
	  var caption = this.$currentTarget.find('img').attr('image-caption');
	  if(!caption){return false;}
	  
	  this.$instance.find('.caption').remove();
	  $('<div class="featherlight_image_caption">').text(caption).appendTo(this.$instance.find('.featherlight-content'));
	};

};

header_footer = function(){

};

// General Functions
function duplicate_chack(class_name){
  var selects = $(class_name);
  var values = [];
  for(i=0;i<selects.length;i++) {
    var select = selects[i];
    if(values.indexOf(select.value)>-1) {
    	return true; break;
    }else{
    	if(select.value){
    		values.push(select.value);
    	}
    }
  }
  
  return false;
}

//check the parameter value is number or not
IsNumeric = function(num){
	var ValidChars = "0123456789";
	var IsNumber = true;
	var Char;
	
	for(i = 0; i < num.length && IsNumber == true; i++) { 
		Char = num.charAt(i); 
		if( ValidChars.indexOf(Char) == -1){
			IsNumber = false;
		}
	}
	return IsNumber;
};

IsFloat = function(num){
	var ValidChars = "0123456789.";
	var IsNumber = true;
	var Char;
	
	for(i = 0; i < num.length && IsNumber == true; i++) { 
		Char = num.charAt(i); 
		if( ValidChars.indexOf(Char) == -1){
			IsNumber = false;
		}
	}
	
	if( IsNumber && !isNaN ( parseFloat ( num )) ) {
		return true;
	}
	
	return false;
};

//check email address is valid or not
checkemail = function(val){
	//return (val.indexOf(".") > 2) && (val.indexOf("@") > 0);
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	var address = val;
	if(reg.test(address) == false) {
	  return false;
	}

	return true;
};

page_index = function(){


	
};

page_create_user = function(){
	$('#user_type').on('change',function(){
		var user_type = $(this).val();
		if(user_type=='restaurant'){
			$('#restaurant_id,#branch_id').prop('required',true);
			$('#show_restaurant').show(500);
		}else{
			$('#show_restaurant').hide();
			$('#restaurant_id').val('');
			$("#branch_id").val('');
			$('#branch_id').trigger("chosen:updated");
			$('#restaurant_id').prop('required',false);
		}
	});
	
	$("#restaurant_id").change(function(){
		var restaurant_id = $(this).val();
  	var token = $('#token').val();
  	var data_type = "create";
  	if(restaurant_id){
			var jqxhr = $.ajax({
				type: "POST",
				url: url_prefix + "branch_list_by_restaurant",
				data: {restaurant_id:restaurant_id,data_type:data_type, csrf_tkn_bgdjhfodtreylk2a4c: token}
			}).done(function(data){
				$("#branch_id").html(data);
				$('#branch_id').trigger("chosen:updated");
			}).fail(function(){
				alert('We could not execute your request. Please try again later or report to authority.');
		  });
  	}else{
			$("#branch_id").html('');
			$('#branch_id').trigger("chosen:updated");
  	}


	});
	
};

page_manage_user= function(){

  $('.edit_btn').featherlight({
    afterOpen: function(event){
      $("#form_width_full").removeClass('col-lg-6 col-md-6 col-sm-12');
      $("#form_width_full").addClass('col-lg-12 col-md-12 col-sm-12');
    }
  });
  
};

page_manage_role= function(){

  $('.edit_btn').featherlight({
      afterOpen: function(event){
        $("#form_width_full").removeClass('col-lg-6 col-md-6 col-sm-12');
        $("#form_width_full").addClass('col-lg-12 col-md-12 col-sm-12');
      }
  });

};

page_create_requisition = function(){
	
	var start_time 	 = '';
	var end_time 		 = '';
	var meeting_date = '';
	
	var today_date = new Date();
	$('#meeting_date').datetimepicker({
	  timepicker:false,
	  dateFormat: 'yy-mm-dd',
		minDate: new Date(),
		onSelectDate: function(selectedDate){
			$('#start_time').val('');
			var min_time = '00:00';
			if(selectedDate.getFullYear()+'-'+(selectedDate.getMonth()+1)+'-'+selectedDate.getDate()==today_date.getFullYear()+'-'+(today_date.getMonth()+1)+'-'+today_date.getDate()){
				min_time = today_date.getHours()+':'+today_date.getMinutes();
			}
			meeting_date = $('#meeting_date').val();
			$('#start_time').datetimepicker({
			  datepicker:false,
			  format:'H:i',
			  step: 15,
			  minTime: min_time,
			  //startTime: new Date(0,0,0,15,0,0), // 3:00:00 PM - noon
			  onSelectTime: function(selectedDate){
					$('#end_time').val('');
					
					$('#end_time').datetimepicker({
					  datepicker:false,
					  format:'H:i',
					  step: 15,
					  minTime: selectedDate.getHours()+':'+(selectedDate.getMinutes()+15),
						onSelectTime: function(){
							var start_time 		= $('#start_time').val();
							var end_time 			= $('#end_time').val();
							var meeting_date 	= $('#meeting_date').val();
							
							if(!meeting_date){
								alert("Please select date.");
								$('#end_time').val('');
								$('#start_time').val('');
								return false;
							}
							if(!start_time){
								alert("Please select start time.");
								$('#end_time').val('');
								return false;
							}
							if(start_time >= end_time){
								alert("End time must be greater than start time.");
								$('#end_time').val('');
								return false;
							}
							
							var spinner = $(this).parent().find('.spinner:first');
							spinner.show();
							
							var token = $('#token').val();
							var jqxhr = $.ajax({
								type: "POST",
								//dataType: "json",
								url: url_prefix + "create_requisition",
								data: {purpose:'check_availavility', meeting_date:meeting_date, start_time:start_time, end_time:end_time, csrf_tkn_bgdjhfodtreylk2a4c: token}
							}).done(function(data){
								$("#modal_body").html(data);
								if(data!=''){
									$("#myModal").modal('show');
								}
								spinner.hide();
							}).fail(function(){
								spinner.hide();
								alert('We could not execute your request. Please try again later or report to authority.');
							});
										
						}
					});
					
				}
				
			});
			
		}
	});
	
	$(document).on('click','#start_time',function(){
		meeting_date = $("#meeting_date").val();
		if(meeting_date == ''){
			alert("Please select Date first");
			$('#end_time').val('');
			$('#start_time').val('');
			return false;
		}
		
	});
	
	//add external participents row
	var $tr = $('div.tr_clone:first');
  $('div.tr_clone:first').remove();
  $(document).on('click', ".tr_clone_add", function() {
    var parent = $(this).closest('.tr_clone');
    var newClass='newClass';
    var $clone = $tr.clone().addClass(newClass);
    $clone.find(':text').val('');
    $clone.find(':text').prop("required", false);
    $clone.find('.participant_name,.designation,.organisation').attr('data-parsley-trigger', 'keyup').parsley();
    $clone.find('.participant_name,.email_address,.phone_number,.designation,.organisation').attr('required', 'required').parsley();
		$clone.find('.organisation').autocomplete();
    $clone.insertAfter(parent); 
  });

	//remove external participents row
  $(document).on('click', ".tr_clone_remove", function() {
    var rowCount = $('div.tr_clone').length;
    if(rowCount > 1){
      $(this).parent().parent().parent().remove(); //Remove field html
    }
  });
  
  $(document).on("click",".participents",function(){
		var token = $('#token').val();
		var data_id = $(this).attr('data_id');
		var panel = $(this).parent().parent().next();
		var panel_content = $(this).parent().parent().next().children();

		var spinner = $(this).parent().find('.spinner');
		$('.detail_panel').hide();
		spinner.show();

		var jqxhr = $.ajax({
			type: "POST",
			url: url_prefix + "requisition_participant_list",
			data: { requisition_id:data_id, csrf_tkn_bgdjhfodtreylk2a4c: token }
		}).done(function(msg){
		  $(panel_content).html(msg);
		  $(panel).show('slow');
		  spinner.hide();
		}).fail(function(){
		  alert('Something went wrong!');
		  spinner.hide();
		});
	});
	
 	$('.requisition_form_post_ajax').submit(function(){
		var me = $(this);
		var myFormData = new FormData($(this)[0]);
		//myFormData.append('EmployeeID', UserID);
		var data = myFormData;
		
		var route = $(this).attr('form-route');
		if( !data || !route ){ 
			alert('Invalid Request!');
			return false; 
		}
		
		if( !$(this).parsley().validate() ){
			//alert('Parsley Error!');
			return false;
		}

		var me = $(this);
		var token = $('#token').val();
		var spinner = me.find('.spinner:last');
		spinner.show();
		$("#submit_button").attr("disabled", true);
		
		var jqxhr = $.ajax({
			type: "POST",
		 	url: url_prefix + route,
		 	cache: false,
			processData: false, // important
			contentType: false, // important
			data: data
		}).done(function(msg){
			//example - update_success, update_success:maname_author -- after : the value will be treated as route
			$("#submit_button").attr("disabled", false);
			var identifier = '';
			var param = msg.trim().split(":");
			if( param[0] ){ identifier = param[0]; }
			
			switch(identifier.trim()){
				case 'success':
				 alert("Your request has been completed successfully!");
				 spinner.hide();
				 break;
				 
				case 'error':
				 alert("Error! Please report to Admin.");
				 spinner.hide();
				 break;
				 
				case 'denied':
				 alert("Unauthorized Access!");
				 spinner.hide();
				 break;
				 
				case 'success':
					alert("Your request has been executed successfully!");
					if( param[1] ){ location.href = url_prefix + param[1]; return false; }
					location.href=location.href;
				 	break;
				 	
				case 'update_success':
					alert("Your record has been updated successfully!");
					if( param[1] ){ location.href = url_prefix + param[1]; return false; }
					location.href=location.href;
				 	break;
				 	
				case 'insert_success':
					alert("New record has been created successfully!");
					if( param[1] ){ location.href = url_prefix + param[1]; return false; }
					location.href=location.href;
				 	break;
				 	
				default:
					alert(msg);
				 	spinner.hide();
				 	break;
			}
		}).fail(function() {
			alert( "We could not execute your request. Please try again later or report to authority." );
			spinner.hide();
			$("#submit_button").attr("disabled", false);
		});
		
		return false;
	});
	
	// Jquery ajax auto completed
	$(document).on("input",".organisation", function(){
		var searchRequest = null;
		var token = $('#token').val(); 
		$(".organisation").autocomplete({
		    minLength: 1,
		    source: function(request, response) {
		        if (searchRequest !== null) {
		            searchRequest.abort();
		        }
		        searchRequest = $.ajax({
		            url: url_prefix + "get_organization_list",
		            method: 'post',
		            dataType: "json",
		            data: {organization_name: request.term, csrf_tkn_bgdjhfodtreylk2a4c: token},
		            success: function(data) {
		                searchRequest = null;
		                response($.map(data, function(item) {
		                    return {
		                        value: item.ORGANISATION,
		                        label: item.ORGANISATION
		                    };
		                }));
		            }
		        }).fail(function() {
		        	searchRequest = null;
		        });
		    }
		});
	});
	
	$(document).on("input",".common_phone", function(){
		var token = $('#token').val(); 
		
		var search_phone = $(this).closest('.tr_clone').find('.common_phone').val();
		var search_email = "";
		//var search_email = $(this).closest('.tr_clone').find('.common_email').val();
		
		if(search_phone.length==11){
			var spinner = $(this).closest('div').find('.spinner');
					spinner.show();
			var jqxhr = $.ajax({
				type: "POST",
				context: this,
				dataType: 'json',
				url: url_prefix + "get_participent_information",
				data: {search_phone:search_phone, search_email:search_email, csrf_tkn_bgdjhfodtreylk2a4c: token}
			}).done(function(data){
				if(data){
					$(this).closest('.tr_clone').find('.common_phone').val(data["PHONE_NUMBER"]);
					$(this).closest('.tr_clone').find('.common_email').val(data["EMAIL_ADDRESS"]);
					$(this).closest('.tr_clone').find('.participant_name').val(data["PARTICIPANT_NAME"]);
					$(this).closest('.tr_clone').find('.designation').val(data["DESIGNATION"]);
					$(this).closest('.tr_clone').find('.organisation').val(data["ORGANISATION"]);
				}else{
					$(this).closest('.tr_clone').find('.common_email').val("");
					$(this).closest('.tr_clone').find('.participant_name').val("");
					$(this).closest('.tr_clone').find('.designation').val("");
					$(this).closest('.tr_clone').find('.organisation').val("");
				}
				spinner.hide();
			}).fail(function(){
				spinner.hide();
				alert('We could not execute your request. Please try again later or report to authority.');
			});
		}else{
			$(this).closest('.tr_clone').find('.common_email').val("");
			$(this).closest('.tr_clone').find('.participant_name').val("");
			$(this).closest('.tr_clone').find('.designation').val("");
			$(this).closest('.tr_clone').find('.organisation').val("");
		}
		
	});
	
};

page_manage_requisition = function(){
	
	$(".participents").click(function(){
		var token = $('#token').val();
		var data_id = $(this).attr('data_id');
		var panel = $(this).parent().parent().next();
		var panel_content = $(this).parent().parent().next().children();

		var spinner = $(this).parent().find('.spinner');
		$('.detail_panel').hide();
		spinner.show();

		var jqxhr = $.ajax({
			type: "POST",
			url: url_prefix + "requisition_participant_list",
			data: { requisition_id:data_id, csrf_tkn_bgdjhfodtreylk2a4c: token }
		}).done(function(msg){
		  $(panel_content).html(msg);
		  $(panel).show('slow');
		  spinner.hide();
		}).fail(function(){
		  alert('Something went wrong!');
		  spinner.hide();
		});
	});
	
	$('.action_btn_ajax').click(function(){
		if(confirm('Do you really want to perform this action?')){
			var route = $(this).attr('href');
			if( !route ){ 
				alert('Invalid Request!');
				return false; 
			}

			var me = $(this);
			var spinner = $(this).parent().find('.spinner');
			spinner.show();
			$(".action_btn_ajax").attr("disabled", true);
			$(".action_btn_ajax_dec").attr("disabled", true);
			
			var jqxhr = $.ajax({
				type: "GET",
			 	url: route
			}).done(function(msg){
				$(".action_btn_ajax").attr("disabled", false);
				$(".action_btn_ajax_dec").attr("disabled", false);
				switch(msg.trim()){
					case 'success':
					 alert("Request has been approved, Thank you.!");
					 location.href=location.href;
					 break;
					 
				  case 'decline':
					 alert("Request has been declined, Thank you.!");
					 location.href=location.href;
					 break;
					 
					case 'time_expired':
					 alert("Appointment time already expired.!");
					 spinner.hide();
					 //location.href=location.href;
					 break;
					 
					case 'error':
					 alert("Error! Please report to Admin.");
					 spinner.hide();
					 break;
					 
					case 'denied':
					 alert("Unauthorized Access!");
					 spinner.hide();
					 break;
					  	
					default:
						//alert(msg);
						alert("We could not execute your request. Please try again later or report to authority.");
					 	spinner.hide();
					 	break;
				}
			}).fail(function() {
				alert( "We could not execute your request. Please try again later or report to authority." );
				spinner.hide();
				$(".action_btn_ajax").attr("disabled", false);
				$(".action_btn_ajax_dec").attr("disabled", false);
			});
			return false;
		}else{
			return false;	
		}
	});
	
	// Decline request
	$('.action_btn_ajax_dec').click(function(){
		var route = $(this).attr('href');
		if( !route ){ 
			alert('Invalid Request!');
			return false; 
		}
		$('#frm_decline_reason').parsley().reset();
		$("#frm_decline_reason").attr( 'form-route',route);
		$("#decline_modal").modal('show');
		var me = $(this);
		var spinner = $(this).parent().find('.spinner');
		//spinner.show();
		//$(".action_btn_ajax_dec").attr("disabled", true);
		return false;
	});
	
	$('.requisition_decline_reason').submit(function(){
		var me = $(this);
		var myFormData = new FormData($(this)[0]);
		var data = myFormData;
		
		var route = $(this).attr('form-route');
		if( !data || !route ){ 
			alert('Invalid Request!');
			return false; 
		}
		if( !$(this).parsley().validate() ){
			return false;
		}
		
		var me = $(this);
		var token = $('#token').val();
		var spinner = me.find('.spinner');
		spinner.show();
		$("#submit_button").attr("disabled", true);
		$(".action_btn_ajax").attr("disabled", true);
		$(".action_btn_ajax_dec").attr("disabled", true);
		
		var jqxhr = $.ajax({
			type: "GET",
		 	url: route+'?decline_reason='+$("#decline_reason").val(),
		 	cache: false,
			processData: false, // important
			contentType: false, // important
			data: data
		}).done(function(msg){
			$("#submit_button").attr("disabled", false);
			$(".action_btn_ajax").attr("disabled", false);
			$(".action_btn_ajax_dec").attr("disabled", false);
			
			switch(msg.trim()){
				case 'success':
				 alert("Request has been approved, Thank you.!");
				 location.href=location.href;
				 break;
				 
			  case 'decline':
				 alert("Request has been declined, Thank you.!");
				 location.href=location.href;
				 break;
				 
			  case 'time_expired':
				 alert("Appointment time already expired.!");
				 spinner.hide();
				 break;
				 
				case 'error':
				 alert("Error! Please report to Admin.");
				 spinner.hide();
				 break;
				 
				case 'denied':
				 alert("Unauthorized Access!");
				 spinner.hide();
				 break;
				  	
				default:
					//alert(msg);
					alert("We could not execute your request. Please try again later or report to authority.");
				 	spinner.hide();
				 	break;
			}
		}).fail(function() {
			alert( "We could not execute your request. Please try again later or report to authority." );
			spinner.hide();
			$("#submit_button").attr("disabled", false);
			$(".action_btn_ajax").attr("disabled", false);
			$(".action_btn_ajax_dec").attr("disabled", false);
		});
		
		return false;
	});
	
	$('.extend_time_action').click(function(){
		var route = $(this).attr('href');
		if( !route ){ 
			alert('Invalid Request!');
			return false; 
		}
		
		var requisition_id = route.substr(route.lastIndexOf("/")+ 1);
		$("#requisition_id").val("");
		$("#requisition_id").val(requisition_id);
		
		$('#frm_meeting_time_extend').parsley().reset();
		$("#frm_meeting_time_extend").attr( 'form-route',route);
		$("#time_extend_modal").modal('show');
		var me = $(this);
		var spinner = $(this).parent().find('.spinner');
		return false;
	});
	
	$('.frm_meeting_time_extend').submit(function(){
		var me = $(this);
		var myFormData = new FormData($(this)[0]);
		var data = myFormData;
		
		var route = $(this).attr('form-route');
			  route = route.substring(0,route.lastIndexOf("/"));
		
		if( !data || !route ){ 
			alert('Invalid Request!');
			return false; 
		}
		if( !$(this).parsley().validate() ){
			return false;
		}
		
		var me = $(this);
		var token = $('#token').val();
		var spinner = me.find('.spinner');
		spinner.show();
		$("#time_submit_button").attr("disabled", true);
		$(".action_btn_ajax").attr("disabled", true);
		$(".action_btn_ajax_dec").attr("disabled", true);
		
		var jqxhr = $.ajax({
			type: "POST",
		 	url: route,
		 	cache: false,
			processData: false, // important
			contentType: false, // important
			data: data
		}).done(function(msg){
			$("#time_submit_button").attr("disabled", false);
			$(".action_btn_ajax").attr("disabled", false);
			$(".action_btn_ajax_dec").attr("disabled", false);
			
			switch(msg.trim()){
				case 'success':
				 alert("Meeting time has been successfully extended.!");
				 location.href=location.href;
				 break;
				 
			  case 'required_fields':
				 alert("Extend time field is required.!");
				 location.href=location.href;
				 break;
				 
				case 'error':
				 alert("Error! Please report to Admin.");
				 spinner.hide();
				 break;
				 
				case 'denied':
				 alert("Unauthorized Access!");
				 spinner.hide();
				 break;
				  	
				default:
					//alert(msg);
					alert("We could not execute your request. Please try again later or report to authority.");
				 	spinner.hide();
				 	break;
			}
		}).fail(function() {
			alert( "We could not execute your request. Please try again later or report to authority." );
			spinner.hide();
			$("#submit_button").attr("disabled", false);
			$(".action_btn_ajax").attr("disabled", false);
			$(".action_btn_ajax_dec").attr("disabled", false);
		});
		
		return false;
	});
	
};

page_manage_request = function(){
	
	$('.extend_time_action').click(function(){
		var route = $(this).attr('href');
		if( !route ){ 
			alert('Invalid Request!');
			return false; 
		}
		
		var request_id = route.substr(route.lastIndexOf("/")+ 1);
		$("#request_id").val("");
		$("#request_id").val(request_id);
		
		$('#frm_meeting_time_extend').parsley().reset();
		$("#frm_meeting_time_extend").attr( 'form-route',route);
		$("#time_extend_modal").modal('show');
		var me = $(this);
		var spinner = $(this).parent().find('.spinner');
		return false;
	});
	
	$('.frm_meeting_time_extend').submit(function(){
		var me = $(this);
		var myFormData = new FormData($(this)[0]);
		var data = myFormData;
		
		var route = $(this).attr('form-route');
			  route = route.substring(0,route.lastIndexOf("/"));
		if( !data || !route ){ 
			alert('Invalid Request!');
			return false; 
		}
		if( !$(this).parsley().validate() ){
			return false;
		}
		
		var me = $(this);
		var token = $('#token').val();
		var spinner = me.find('.spinner');
		spinner.show();
		$("#time_submit_button").attr("disabled", true);
		
		var jqxhr = $.ajax({
			type: "POST",
		 	url: route,
		 	cache: false,
			processData: false, // important
			contentType: false, // important
			data: data
		}).done(function(msg){
			$("#time_submit_button").attr("disabled", false);
			switch(msg.trim()){
				case 'success':
				 alert("Meeting time has been successfully extended.");
				 location.href=location.href;
				 break;
				 
			  case 'required_fields':
				 alert("Extend time field is required.!");
				 location.href=location.href;
				 break;
				 
				case 'error':
				 alert("Error! Please report to Admin.");
				 spinner.hide();
				 break;
				 
				case 'denied':
				 alert("Unauthorized Access!");
				 spinner.hide();
				 break;
				  	
				default:
					//alert(msg);
					alert("We could not execute your request. Please try again later or report to authority.");
				 	spinner.hide();
				 	break;
			}
		}).fail(function(){
			alert( "We could not execute your request. Please try again later or report to authority." );
			spinner.hide();
			$("#submit_button").attr("disabled", false);
		});
		
		return false;
	});
	
};

page_card_receive = function(){
	$(".card_id").focus();
	
	$("#frm_card_receive").submit(function(e){
		var token = $('#token').val();
		var card_id = $("input[name='card_id']").val();
		if(!card_id){
			alert("Please enter card ID");
			return false;
		}
    e.preventDefault();
    $.ajax({
      url: url_prefix + "card_receive",
      type: "POST", // Can change this to get if required
      data: {card_id:card_id, csrf_tkn_bgdjhfodtreylk2a4c: token}
    }).done(function(data){
    	alert(data);
    	location.href = location.href;
    	$("input[name='card_id']").trigger("onfocus");
    }).fail(function(){
			alert('We could not execute your request. Please try again later or report to authority.');
	  });
	});
};

page_visitor_center_form = function(){
	$("#frm_visitor_center_form").submit(function(e){
		var token = $('#token').val();
		
		var card_id = $("input[name='card_id']").val();
		var otp = $("input[name='otp']").val();
		var participent_image = $("input[name='participent_image']").val();
		
		if(!card_id){
			alert("Please enter card ID");
			return false;
		}
    e.preventDefault();
    $.ajax({
      url: url_prefix + "visitor_center_form",
      type: "POST", // Can change this to get if required
      data: {card_id:card_id, otp:otp, participent_image:participent_image, csrf_tkn_bgdjhfodtreylk2a4c: token}
    }).done(function(data){
    	if(data=='success'){
    		alert("This card has been assigned successfully.");
				location.href = url_prefix+"visitor_center";
    		$("#frm_visitor_center").trigger("reset");
    		return false;
    	}
    	alert(data);
    	return false;
    }).fail(function(){
			alert('We could not execute your request. Please try again later or report to authority.');
	  });
	});
};

page_create_request = function(){
	$("#phone_number").on("input",function(){
		var phone_number =$(this).val();
		var token = $('#token').val();
		$("#visitor_name").val(null);
		$("#email_address").val(null);
		$("#organisation").val(null);
		$("#designation").val(null);
		if(phone_number.length >= 11){
			var spinner = $(this).parent().parent().find('.form_label div.spinner');
			spinner.show();
			var jqxhr = $.ajax({
				type: "POST",
				dataType: "json",
				url: url_prefix + "participants_record",
				data: {phone_number:phone_number, csrf_tkn_bgdjhfodtreylk2a4c: token}
			}).done(function(data){
					var obj =  data;
				if(data != null){
					$("#visitor_name").val(obj.PARTICIPANT_NAME);
					$("#email_address").val(obj.EMAIL_ADDRESS);
					$("#organisation").val(obj.ORGANISATION);
					$("#designation").val(obj.DESIGNATION);
				}
				spinner.hide();
			}).fail(function(){
				alert('We could not execute your request. Please try again later or report to authority.');
				spinner.hide();
			});
		}
	});
	
	$("#employee_phone").on("input",function(){
		var employee_phone =$(this).val();
		$("#employee").val(null);
		$("#host_email").val(null);
		$("#host_employee").val(null);
		var token = $('#token').val();
		if(employee_phone.length >= 11){
			var spinner = $(this).parent().parent().find('.form_label div.spinner');
			spinner.show();
			var jqxhr = $.ajax({
				type: "POST",
				dataType: "json",
				url: url_prefix + "employee_name_by_number",
				data: {employee_phone:employee_phone, csrf_tkn_bgdjhfodtreylk2a4c: token}
			}).done(function(data){
				if(jQuery.isEmptyObject(data)){
					alert("Please enter correct mobile number.");
					spinner.hide();
					return false;
				}
				var obj =  data;
				$("#employee").val(obj.USER_NAME);
				$("#host_email").val(obj.USER_EMAIL);
				$("#host_employee").val(obj.USER_ID);
				spinner.hide();
			}).fail(function(){
				alert('We could not execute your request. Please try again later or report to authority.');
				spinner.hide();
			});
		}
	});
	
	$("#frm_create_request").submit(function(){
		$(".spinner").not(":last").hide();
	});
};


//-->