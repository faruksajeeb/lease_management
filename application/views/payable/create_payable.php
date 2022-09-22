<?php $page_title = 'Create Payament'; ?>
<?php $route = 'create_payable';

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<title><?php echo $this->webspice->settings()->domain_name; ?>: <?php echo $page_title; ?></title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />

	<?php include(APPPATH . "views/global.php"); ?>
</head>

<body>
	<div id="wrapper">
		<?php if ($this->uri->segment(2) != "edit") : ?>
			<div id="header_container"><?php include(APPPATH . "views/header.php"); ?></div>
		<?php endif; ?>

		<div id="page_<?php echo str_replace(' ', '_', strtolower($page_title)); ?>" class="container-fluid page_identifier">
			<div class="page_caption"><?php echo $page_title; ?></div>
			<div class="page_body stamp">
				<div class="row">
					<div class="col-md-4">
						<div class="input-group">
							<span class="input-group-addon "> Vendor Name</span>
							<select name="VENDOR_ID" id="VENDOR_ID" class="input_full form-control" required>
								<option value="">Select One</option>
								<?php foreach ($vendors as $val) : ?>
									<option value="<?php echo $val->ID; ?>" <?php if (set_value('VENDOR_ID', $edit['VENDOR_ID']) == $val->ID) {
																				echo 'selected="selected"';
																			} ?>><?php echo $val->VENDOR_NAME; ?></option>
								<?php endforeach; ?>
							</select>
						</div>

					</div>
					<div class="col-md-3">
						<div class="input-group">
							<span class="input-group-addon glyphicon glyphicon-calendar"> Month/Year</span>
							<input type="text" class="form-control monthpicker" name="MONTH_YEAR" id="MONTH_YEAR" aria-label="" required>
						</div>
					</div>
					<div class="col-md-3">
						<button class="btn btn-success full_paid" type="button">Full Paid</button>
						<button class="btn btn-default reset" type="button">Reset</button>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12 content_section" style='padding-top:10px'>
					<form id='payment_form' class='form_post_ajax' form-route='create_payable' method='post' action=''>
       				<input type='hidden' name='<?php echo $this->security->get_csrf_token_name() ?>' value='<?php echo $this->security->get_csrf_hash() ?>'>
							<div class="dynamic_slot">
								
							</div>
					</form>
					</div>
				</div>



			</div>

		</div>

		<?php if ($this->uri->segment(2) != "edit") : ?>
			<div id="footer_container"><?php include(APPPATH . "views/footer.php"); ?></div>
		<?php endif; ?>

	</div>
	<script type="text/javascript">
		$(document).ready(function() {
			
			$('.full_paid').on('click', function() {
				$('.payable').each(function() {
					var payable = Number($(this).val());
					var tr = $(this).parent().parent();
					tr.find('.pay').val(payable);
				});
				totalPay();
				
			});
			$(".reset").on('click',function() {
				$('.pay').each(function() {
					$(this).val("");
				});
				$('#pay_total').text(0);
				$(".submit_button").prop("disabled", true);
			});
			$('.dynamic_slot').on('keyup', '.pay', function() {				
                var tr = $(this).parent().parent();
				var payable = Number(tr.find('.payable').val());
				var pay = Number($(this).val());
				totalPay();
				if(pay>payable){
					Swal.fire({
						allowOutsideClick: false,
						icon: 'warning',
						title: 'Alert!',
						text: "Pay must be less than or equal to payable.",
					});
					tr.find('.pay').css('border', '1px solid red').focus();
					$(".submit_button").prop("disabled", true);
				}else{
					tr.find('.pay').css('border', '1px solid green');
					$(".submit_button").prop("disabled", false);
				}				
            });
			function totalPay(){
				var totalPay =0;
				var pay;
				$('.pay').each(function() {
					pay = Number($(this).val());
					totalPay += pay;
				});
				$('#pay_total').text(totalPay);
				if(totalPay>0){
					$(".submit_button").prop("disabled", false);
				}else{
					$(".submit_button").prop("disabled", true);
				}				
			}
			$(".monthpicker").MonthPicker({
				ShowIcon: false,
				// Button: '<button>...</button>',
				OnAfterChooseMonth: function() {
					var vendorId = $('#VENDOR_ID').val();
					var monthYear = $(this).val();
					if (!vendorId) {
						Swal.fire('Warning', 'Please select vendor first.', 'warning');
						return false;
					}
					if (vendorId && monthYear) {
						$.ajax({
							url: '<?php echo $url_prefix; ?>PayableController/getLeaseIdByVendor',
							method: 'post',
							data: {
								'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
								vendor_id: vendorId,
								month_year: monthYear,
							},
							dataType: 'html',
							success: function(response) {
								// Remove options 
								// $('#LEASE_ID').find('option').not(':first').remove();
								$('.dynamic_slot').html(response);
								$(".submit_button").prop("disabled", true);
							}
						});
					}
				}
			});
			$('#VENDOR_ID').change(function() {
				var vendorId = $(this).val();
				var monthYear = $("#MONTH_YEAR").val();
				if (vendorId && monthYear) {
					$.ajax({
						url: '<?php echo $url_prefix; ?>PayableController/getLeaseIdByVendor',
						method: 'post',
						data: {
							'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
							vendor_id: vendorId,
							month_year: monthYear,
						},
						dataType: 'html',
						success: function(response) {
							// Remove options 
							// $('#LEASE_ID').find('option').not(':first').remove();
							$('.dynamic_slot').html(response);
						}
					});
				}
			});

			// if edit
			var key = $('#key').val();
			if (key) {
				var desc = $('#group_name').find('option:selected').attr('data-description');

				if (!desc) {
					$('#right_part').html("");
					return false;
				}

				$('#right_part').html('<br /><br /><br /><span style="color:red;">Instruction:</span><br />' + desc).css('font-weight', 'bold');
			}

		});
	</script>
</body>

</html>