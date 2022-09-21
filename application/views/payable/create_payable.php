<?php $page_title = 'Create Payable'; ?>
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
						<button class="btn btn-success">Full Paid</button>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12 content_section"  style='padding-top:10px'>

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
								$('.content_section').html(response);
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
							$('.content_section').html(response);
						}
					});
				}
			});
			$('#AMOUNT').on('keyup', function() {
				$("#submit_button").prop("disabled", false);
				var VendorId = $('#VENDOR_ID').val();
				var Amount = $(this).val();
				var LeaseId = $('#LEASE_ID').val();
				if (!VendorId || !LeaseId) {
					Swal.fire('ERROR', 'Please select vendor & lease first then enter amount.', 'warning');
					return false;
				}
				if (Amount) {
					$.ajax({
						url: '<?php echo $url_prefix; ?>PayableController/getOutstanding',
						method: 'post',
						data: {
							'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
							amount: Amount,
							vendor_id: VendorId,
							lease_id: LeaseId
						},
						dataType: 'text',
						success: function(response) {
							if (Number(Amount) > Number(response)) {
								$("#submit_button").prop("disabled", true);
								Swal.fire(
									'Forbidden!',
									'Amount shoud be less than or equal to outstanding balance (' + response + ')! ',
									'warning'
								)
								//alert('Amount shoud be less than outstanding!');
							} else {
								$("#submit_button").prop("disabled", false);
							}
						}
					})
				} else {
					alert('Please enter number.');
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