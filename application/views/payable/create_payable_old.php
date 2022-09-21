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
					<div class="col-lg-6 col-md-6 col-sm-12">
						<fieldset class="divider">
							<legend>Please enter required information</legend>
						</fieldset>

						<div class="stitle">* Mandatory Field</div>

						<form id="frm_<?php echo str_replace(' ', '_', strtolower($page_title)); ?>" class="form_post_ajax" form-route="<?php echo $route; ?>" method="post" action=""  enctype="multipart/form-data" data-parsley-validate>
							<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
							<input type="hidden" name="key" value="<?php if (isset($edit['ID']) && $edit['ID']) {
																		echo $this->webspice->encrypt_decrypt($edit['ID'], 'encrypt');
																	} ?>" />

							<table width="100%">
								<tr>
									<td>
										<div class="form_label">Vendor Name*</div>
										<div>
											<select name="VENDOR_ID" id="VENDOR_ID" class="input_full form-control" required>
												<option value="">Select One</option>
												<?php foreach ($vendors as $val) : ?>
													<option value="<?php echo $val->ID; ?>" <?php if( set_value('VENDOR_ID', $edit['VENDOR_ID']) == $val->ID){ echo 'selected="selected"';} ?> ><?php echo $val->VENDOR_NAME; ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="form_label">Lease ID*</div>
										<div>
											<select name="LEASE_ID" id="LEASE_ID" class="input_full form-control" required>
												<option value="">Select vendor first</option>
												<?php foreach ($leases as $val) : ?>
													<option value="<?php echo $val->ID; ?>" <?php if (set_value('LEASE_ID', $edit['LEASE_ID']) == $val->ID) {
																								echo 'selected="selected"';
																							} ?>><?php echo $val->LEASE_NAME; ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="form_label">Period (Month/Year) *:</div>
										<div>
											<input type="text" class="input_full form-control monthpicker PERIOD" id="PERIOD" name="PERIOD" value="<?php echo set_value('PERIOD',$edit['PERIOD']); ?>" required>
											<span class="fred"><?php echo form_error('PERIOD'); ?></span>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="form_label">Payment Date:</div>
										<div>
											<input type="text" class="input_full form-control date_picker" id="PAYMENT_DATE" name="PAYMENT_DATE" value="<?php echo set_value('PAYMENT_DATE', $edit['PAYMENT_DATE']); ?>" required />
											<span class="fred"><?php echo form_error('PAYMENT_DATE'); ?></span>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="form_label">Payment Method</div>
										<div>
											<select name="PAYMENT_METHOD_ID" id="PAYMENT_METHOD_ID" class="input_full form-control" required>
												<option value="">Select One</option>
												<option value="1" <?php echo $edit['PAYMENT_METHOD_ID']=='1' ? 'selected' : ''; ?>>Cash</option>
												<option value="2" <?php echo $edit['PAYMENT_METHOD_ID']=='2' ? 'selected' : ''; ?>>Cheque</option>
												<option value="3" <?php echo $edit['PAYMENT_METHOD_ID']=='3' ? 'selected' : ''; ?>>Card</option>
											</select>
											<span class="fred"><?php echo form_error('PAYMENT_METHOD_ID'); ?></span>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="form_label">Reference Number</div>
										<div>
											<input type="text" class="input_full form-control " id="REFERENCE_NUMBER" name="REFERENCE_NUMBER" value="<?php echo set_value('REFERENCE_NUMBER', $edit['REFERENCE_NUMBER']); ?>" required />
											<span class="fred"><?php echo form_error('REFERENCE_NUMBER'); ?></span>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="form_label">Amount</div>
										<div>
											<input type="number" class="input_full form-control " id="AMOUNT" name="AMOUNT" value="<?php echo set_value('AMOUNT', $edit['AMOUNT']); ?>" required />
											<span class="fred"><?php echo form_error('AMOUNT'); ?></span>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="form_label">REMARKS</div>
										<div>
											<textarea name="REMARKS" id="REMARKS" cols="30" rows="5" class="form-control"></textarea>
											<span class="fred"><?php echo form_error('REMARKS'); ?></span>
										</div>
									</td>
								</tr>
								<tr>
									<td>
									<div class="form_label">Attachment</div>
										<div>
										<input type="file" name="attachment" id="attachment" autocomplete="off" class="input-text" accept="image/jpeg,image/gif,image/png,application/pdf">		
										<input type="hidden" name="previous_uploaded_attachment" id="previous_uploaded_attachment" value="<?php if ($edit['ATTACHMENT']) {
																																echo $edit['ATTACHMENT'];
																															} ?>">
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div><input type="submit" class="btn btn-danger" id="submit_button" value="Submit Data" /></div>
										<div class="spinner">&nbsp;</div>
									</td>
								</tr>
							</table>
						</form>
					</div>

					<div id="right_part" class="col-lg-6 col-md-6 col-sm-12">

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
				ShowIcon: false
				// Button: '<button>...</button>'
			});
			$('#VENDOR_ID').change(function() {
				var VendorId = $(this).val();
				if (VendorId) {
					$.ajax({
						url: '<?php echo $url_prefix; ?>PayableController/getLeaseIdByVendor',
						method: 'post',
						data: {
							'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
							vendor_id: VendorId
						},
						dataType: 'json',
						success: function(response) {
							// Remove options 
							// $('#LEASE_ID').find('option').not(':first').remove();
							$('#LEASE_ID').find('option').remove();
							// Add options
							$('#LEASE_ID').append('<option value="">--select lease ID--</option>');
							$.each(response, function(index, data) {
								$('#LEASE_ID').append('<option value="' + data['ID'] + '">' + data['LEASE_NAME'] + '</option>');
							});
						}
					});
				}
			});
			$('#LEASE_ID').change(function() {
				var LEASE_ID = $(this).val();
				
				if (LEASE_ID) {
					$.ajax({
						url: '<?php echo $url_prefix; ?>PayableController/getAdvanceAndPaymentInfoByLeaseId',
						method: 'post',
						data: {
							'<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
							LEASE_ID: LEASE_ID
						},
						dataType: 'html',
						success: function(response) {
							// Remove options 
							$("#right_part").html(response);
						}
					});
				}
			});
			$('#AMOUNT').on('keyup', function() {
				$("#submit_button").prop("disabled", false);
				var VendorId = $('#VENDOR_ID').val();
				var Amount = $(this).val();
				var LeaseId = $('#LEASE_ID').val();
				if(!VendorId || !LeaseId){
					Swal.fire('ERROR','Please select vendor & lease first then enter amount.','warning');
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