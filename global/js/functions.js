//<!--
var url_prefix = '/dse_lease_mgt/'; /*should be start and end with / (slash)*/
$(document).ready(function () {
	init();
});

init = function () {
	common_functions();

	if ($("#header_container").length) {
		header_footer();
	}

	if ($("#page_header_visitor").length) {
		page_header_visitor();
	}

	var page = $('#wrapper').find('.page_identifier').attr('id');
	switch (page) {
		case 'page_index': page_index(); break;
		case 'page_manage_user': page_manage_user(); break;
		case 'page_manage_role': page_manage_role(); break;
	}

	$("input[type=submit]").click(function () {
		$('.chzn-select,#nic_editor').hide();
	});
};

common_functions = function () {

	/*action confirmation*/
	$('.confirmation').on('click', function () {
		Swal.fire({
			title: 'Do you really want to perform this action?',
			text: "",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, do it!'
		}).then((result) => {
			if (result.isConfirmed) {
				return true;
			} else {
				return false;
			}
		})
		// if (confirm('Do you really want to perform this action?')) {
		// 	return true;
		// } else {
		// 	return false;
		// }
	});

	/*chosen select box*/
	$('.chosen').chosen();

	//$('#data_table').DataTable();
	$('body').delegate('#data_table tr', 'click', function (evt) {
		$('#data_table tr').removeClass('row_selected');
		$(this).addClass('row_selected');
	});

	/*date picker*/
	var now = new Date();
	now.setDate(now.getDate());
	$(".date_picker_from_now").datetimepicker("destroy");
	$(".date_picker_from_now").datetimepicker({
		scrollMonth: false,
		scrollInput: false,
		timepicker: false,
		format: 'Y-m-d',
		minDate: new Date(now)
	});

	$(".date_picker_before_now").datetimepicker("destroy");
	$(".date_picker_before_now").datetimepicker({
		scrollMonth: false,
		scrollInput: false,
		timepicker: false,
		format: 'Y-m-d',
		maxDate: new Date(now)
	});

	$('.date_time_picker_24').datetimepicker({
		format: 'Y-m-d H:i:s',
		allowTimes: [
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
		datepicker: false,
		format: 'H:i:s',
		allowTimes: [
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
		timepicker: false,
		format: 'Y-m-d',
		scrollMonth: false,
		scrollInput: false
	});
	$('.month_picker_x').datetimepicker({
		timepicker: false,
		format: 'Y-m',
		scrollMonth: false,
		scrollInput: false,
		closeOnDateSelect: true
	});
	$(".date_picker_only").datepicker({ timepicker: false, dateFormat: "yy-mm-dd" });
	$('.date_time_picker_24,.date_picker,.date_picker_end,.date_picker_only').on('change', function () {
		$('.xdsoft_datetimepicker').hide();
	});
	$(document).on("change", "#date_from", function () {
		$('#date_end').val('');
		var start_date = $(this).val();
		if (start_date) {
			var date_picker_end = new Date(start_date);
			date_picker_end.setDate(date_picker_end.getDate());
			$(".date_picker_end").datetimepicker("destroy");
			$(".date_picker_end").datetimepicker({
				format: 'Y-m-d',
				timepicker: false,
				scrollMonth: false,
				scrollInput: false,
				minDate: new Date(date_picker_end)
			});
		}
	});

	$('.month_picker').monthpicker({ 'minYear': 2015, 'maxYear': 2030, 'class': 'input_style' });
	if ($('.month_picker').attr('required')) {
		$('.monthpick, .yearpick').attr('required', true);
	}

	$('.time_picker').datetimepicker({
		datepicker: false,
		format: 'H:i',
		allowTimes: ['10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '01:00', '01:30', '02:00', '02:30', '03:00', '03:30', '04:00', '04:30', '05:00', '05:30', '06:00', '06:30', '07:00']
	});

	/*meassge show and hide after few milli-second*/
	if ($("#message_board").length) {
		$('#message_board').delay(9000).hide('slow');
	}

	$('.refresh').on('click', function () {
		location.href = location.href;
	});

	// remove any file with attribute data-path="ABSOLUTE_PATH_SHORT" data-name="FILE_NAME"
	$('.btn_file_delete').click(function () {
		if (confirm('Do you really want to remove the file?')) {
			//return true;
		} else {
			return false;
		}

		var me = $(this);
		my_parent = me.parent();
		//var token = $('#token').val();
		var data_path = me.attr('data-path');
		var data_name = me.attr('data-name');
		my_parent.append('<div class="spinner">&nbsp;</div>');
		var spinner = my_parent.find('.spinner');
		spinner.css({ "display": "inline-block", "vertical-align": "middle" });

		var jqxhr = $.ajax({
			type: "GET",
			url: url_prefix + "file_delete?data_path=" + data_path + "&data_name=" + data_name
		}).done(function (msg) {
			switch (msg.trim()) {
				case 'error':
					Swal.fire({
						icon: 'error',
						title: "ERROR!",
						text: "Error! Please report to Admin.",
						type: "error"
					}).then((result) => {
						spinner.hide();
					});
					break;

				case 'success':
					my_parent.remove();
					break;

				default:
					Swal.fire({
						icon: 'error',
						title: "ERROR!",
						text: "We could not execute your request. Please try again later or report to authority.",
						type: "error"
					}).then((result) => {
						spinner.hide();
					});
					break;
			}
		}).fail(function () {
			Swal.fire({
				icon: 'error',
				title: "ERROR!",
				text: "We could not execute your request. Please try again later or report to authority.",
				type: "error"
			}).then((result) => {
				spinner.hide();
			});
		});

		return false;

	});

	// form submit by ajax
	$('.form_post_ajax').submit(function () {

		var me = $(this);
		var myFormData = new FormData($(this)[0]);
		//myFormData.append('EmployeeID', UserID);
		var data = myFormData;

		var route = $(this).attr('form-route');
		if (!data || !route) {
			Swal.fire({
				icon: 'error',
				title: "ERROR!",
				text: 'Invalid Request!',
				type: "error"
			}).then((result) => {
				return false;
			});
		}

		if (!$(this).parsley().validate()) {
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
		}).done(function (msg) {
			//example - update_success, update_success:manage_author -- after : the value will be treated as route
			var identifier = '';
			var param = msg.trim().split(":");
			if (param[0]) { identifier = param[0]; }

			switch (identifier.trim()) {
				case 'error':
					Swal.fire({
						icon: 'warning',
						title: "SORRY!",
						text: "System could not execute your request! Please report to Admin.",
						type: "warning"
					}).then((result) => {
						spinner.hide();
					});
					break;

				case 'denied':
					Swal.fire({
						icon: 'warning',
						title: "SORRY!",
						text: "Unauthorized Access!",
						type: "warning"
					}).then((result) => {
						spinner.hide();
					});
					break;

				case 'success':
					Swal.fire({
						icon: 'success',
						title: "Success!",
						text: "Your request has been executed successfully!",
						type: "success"
					}).then((result) => {
						if (param[1]) { location.href = url_prefix + param[1]; return false; }
						location.href = location.href;
					});
					break;

				case 'update_success':
					Swal.fire({
						icon: 'success',
						title: "Updated!",
						text: "Your record has been updated successfully!",
						type: "success"
					}).then((result) => {
						if (param[1]) { location.href = url_prefix + param[1]; return false; }
						location.href = location.href;
					});
					break;

				case 'insert_success':
					Swal.fire({
						icon: 'success',
						title: "Saved!",
						text: 'New record has been created successfully!',
						type: "success"
					}).then((result) => {
						// Reload the Page
						if (param[1]) { location.href = url_prefix + param[1]; return false; }
						location.href = location.href;
					});
					break;

				case 'request':
					if (param[1]) { location.href = url_prefix + param[1]; return false; }
					location.href = location.href;
					break;

				default:
					Swal.fire({
						icon: 'info',
						title: "",
						text: msg,
						type: "info"
					}).then((result) => {
						spinner.hide();
					});
					break;
			}
		}).fail(function () {
			Swal.fire({
				icon: 'error',
				title: "SORRY!",
				text: "We could not execute your request. Please try again later or report to authority.",
				type: "error"
			}).then((result) => {
				spinner.hide();
			});

		});

		return false;
	});

	// ajax call for action button
	$('.btn_ajax').click(function () {
		Swal.fire({
			title: 'Do you really want to perform this action?',
			text: "",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, do it!'
		}).then((result) => {
			if (result.isConfirmed) {
				// route must be href of a tag and it must be full route address including url_prefix
				var route = $(this).attr('href');
				if (!route) {
					Swal.fire({
						icon: 'error',
						title: "ERROR!",
						text: 'Invalid Request!',
						type: "error"
					}).then((result) => {
						return false;
					});
				}

				var me = $(this);
				var spinner = $(this).parent().find('.spinner');
				spinner.show();

				var jqxhr = $.ajax({
					type: "GET",
					url: route
				}).done(function (msg) {
					switch (msg.trim()) {
						case 'success':
							Swal.fire({
								icon: 'success',
								title: "Success!",
								text: "Your request has been completed successfully!",
								type: "success"
							}).then((result) => {
								location.href = location.href;
							});
							break;
						case 'error':
							Swal.fire({
								icon: 'error',
								title: "Error!",
								text: "Error! Please report to Admin.",
								type: "error"
							}).then((result) => {
								spinner.hide();
							});
							break;

						case 'denied':
							Swal.fire({
								icon: 'error',
								title: "Error!",
								text: "Unauthorized Access!",
								type: "error"
							}).then((result) => {
								spinner.hide();
							});
							break;

						case 'update_success':
							alert("Your record has been updated successfully!");
							location.href = location.href;
							break;

						case 'insert_success':
							Swal.fire(
								'Saved!',
								'New record has been created successfully!',
								'success'
							)
							//alert("New record has been created successfully!");
							location.href = location.href;
							break;

						default:
							Swal.fire({
								icon: 'info',
								title: "",
								text: msg.trim(),
								type: "info"
							}).then((result) => {
								spinner.hide();
							});
							break;
					}
				}).fail(function () {
					Swal.fire({
						icon: 'error',
						title: "Error",
						text: "We could not execute your request. Please try again later or report to authority.",
						type: "error"
					}).then((result) => {
						spinner.hide();
					});
				});
			} else {
				return false;
			}
		})
		// if (confirm('Do you really want to perform this action?')) {

		// }
		return false;
	});

	$('.spin').hover(function () {
		$(this).animateRotate(360, 300);
	}, function () {
		$(this).animateRotate(-360, 300);
	});

	/*drop-down box -> for small screen*/
	$('.list_for_small_screen').change(function () {
		var get_title = $(this).val();
		if (get_title) {
			location.href = get_title;
		}
	});

	/*custom place holder for input box*/
	$('.placeholder .caption').each(function () {
		var pre_obj = $(this).prev();
		var pos = pre_obj.position();
		$(this).css('top', (pos.top + 4));
		$(this).css('padding-top', pre_obj.css('padding-top'));
		$(this).css('padding-right', pre_obj.css('padding-right'));
		$(this).css('padding-bottom', pre_obj.css('padding-bottom'));
		$(this).css('padding-left', pre_obj.css('padding-left'));
	});

	//adding caption in image gallery
	$.featherlightGallery.prototype.afterContent = function () {
		var caption = this.$currentTarget.find('img').attr('image-caption');
		if (!caption) { return false; }

		this.$instance.find('.caption').remove();
		$('<div class="featherlight_image_caption">').text(caption).appendTo(this.$instance.find('.featherlight-content'));
	};

};

header_footer = function () {

};

// General Functions
function duplicate_chack(class_name) {
	var selects = $(class_name);
	var values = [];
	for (i = 0; i < selects.length; i++) {
		var select = selects[i];
		if (values.indexOf(select.value) > -1) {
			return true; break;
		} else {
			if (select.value) {
				values.push(select.value);
			}
		}
	}

	return false;
}

//check the parameter value is number or not
IsNumeric = function (num) {
	var ValidChars = "0123456789";
	var IsNumber = true;
	var Char;

	for (i = 0; i < num.length && IsNumber == true; i++) {
		Char = num.charAt(i);
		if (ValidChars.indexOf(Char) == -1) {
			IsNumber = false;
		}
	}
	return IsNumber;
};

IsFloat = function (num) {
	var ValidChars = "0123456789.";
	var IsNumber = true;
	var Char;

	for (i = 0; i < num.length && IsNumber == true; i++) {
		Char = num.charAt(i);
		if (ValidChars.indexOf(Char) == -1) {
			IsNumber = false;
		}
	}

	if (IsNumber && !isNaN(parseFloat(num))) {
		return true;
	}

	return false;
};

//check email address is valid or not
checkemail = function (val) {
	//return (val.indexOf(".") > 2) && (val.indexOf("@") > 0);
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	var address = val;
	if (reg.test(address) == false) {
		return false;
	}

	return true;
};

page_index = function () {

};

page_manage_user = function () {
	$('.edit_btn').featherlight({
		afterOpen: function (event) {
			$("#form_width_full").removeClass('col-lg-6 col-md-6 col-sm-12');
			$("#form_width_full").addClass('col-lg-12 col-md-12 col-sm-12');
		}
	});
};

page_manage_role = function () {
	$('.edit_btn').featherlight({
		afterOpen: function (event) {
			$("#form_width_full").removeClass('col-lg-6 col-md-6 col-sm-12');
			$("#form_width_full").addClass('col-lg-12 col-md-12 col-sm-12');
		}
	});

};