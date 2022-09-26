<!DOCTYPE html>
<html lang="en">

<head>
	<title><?php echo $this->webspice->settings()->domain_name; ?>: Option</title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />

	<?php include(APPPATH . "views/global.php"); ?>
</head>

<body>
	<div id="wrapper">
		<div id="header_container"><?php include(APPPATH . "views/header.php"); ?></div>

		<div id="page_manage_receivable" class="container-fluid page_identifier">
			<div class="page_caption">Manage Receivable</div>
			<div class="page_body table-responsive">
				<div class="row">
					<div class="col-lg-8 col-md-8">
						<!--filter section-->
						<form id="frm_filter" method="get" action="" data-parsley-validate>
							<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">

							<table style="width:auto;">
								<tr>
									<td>Keyword</td>
									<td>Vendor Name</td>
									<td>Date From (Received)</td>
									<td>Date To (Received)</td>
								</tr>
								<tr>
									<td>
										<input type="text" name="SearchKeyword" class="input_style input_full" />
									</td>
									<td>
										<div>
											<select name="VENDOR_ID" id="VENDOR_ID" class="input_full form-control">
												<option value="">Select One</option>
												<?php foreach ($vendors as $val) : ?>
													<option value="<?php echo $val->ID; ?>"><?php echo $val->VENDOR_NAME; ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									</td>
									<td>
										<div>
											<input type="text" name="date_from" class="input_style date_picker" />
										</div>
									</td>
									<td>
										<div>
											<input type="text" name="date_to" class="input_style date_picker" />
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="10">
										<input type="submit" name="filter" class="btn btn-info" value="Filter Data" />
										<a class="btn btn-default" href="<?php echo $url_prefix; ?>manage_receivable">Refresh</a>
										<a class="btn btn-default" href="<?php echo $url_prefix; ?>manage_receivable/print" target="_blank">Print</a>
										<a class="btn btn-default" href="<?php echo $url_prefix; ?>manage_receivable/csv" target="_blank">Export</a>
									</td>
								</tr>
							</table>
						</form>
					</div>

					<div class="col-lg-4 col-md-4">
						<div class="user_tips">
							<div class="title">Tips to All Users.</div>
							<div class="description">
								<ul>
									<li>Filter by Vendor Name, Lease ID, Amount, Date Range in <b>Keyword</b> field.</li>
								</ul>
							</div>
						</div>
					</div>
				</div> <!-- end of the row -->
				<br />
				<?php if (!isset($filter_by) || !$filter_by) {
					$filter_by = 'All Data';
				} ?>
				<div class="breadcrumb">Filter By: <?php echo $filter_by; ?></div>

				<div id="data_table" style="overflow:auto;">
					<table class="table table-bordered table-striped">
						<tr>
							<th>Sl No.</th>
							<th>Vendor Name</th>
							<th>Lease ID</th>
							<th>Lease Name</th>
							<th>Period</th>
							<th>Amount</th>
							<th>Payment Method</th>
							<th>Received Date</th>
							<th>REFERENCE NUMBER</th>
							<th>REMARKS</th>
							<th>RECEIVED BY</th>
							<th>ATTACHMENT</th>
							<th>created By</th>
							<th>created Date</th>
							<th>Updated By</th>
							<th>Updated Date</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
						<?php foreach ($get_record as $k => $v) :
							$paymentMethod = ($v->PAYMENT_METHOD_ID == 1) ? 'Cash' : (($v->PAYMENT_METHOD_ID == 2) ? 'Cheque' : 'Card');
						?>
							<tr>
								<td><?php echo ++$k; ?></td>
								<td><?php echo $v->VENDOR_NAME; ?></td>
								<td><?php echo $v->LEASE_ID; ?></td>
								<td><?php echo $v->LEASE_NAME; ?></td>
								<td><?php echo $v->PERIOD; ?></td>
								<td><?php echo $v->AMOUNT; ?></td>
								<td><?php echo $paymentMethod; ?></td>
								<td><?php echo $v->RECEIVE_DATE; ?></td>
								<td><?php echo $v->REFERENCE_NUMBER; ?></td>
								<td><?php echo $v->REMARKS; ?></td>
								<td><?php echo $this->customcache->user_maker($v->RECEIVED_BY, 'USER_NAME'); ?></td>
								<td>
									<?php if($v->ATTACHMENT) { ?>
									<a href="<?php echo $url_prefix.'global/custom_files/receivable/'.$v->ATTACHMENT;  ?>" target="_blank">attachment</a>
									<?php } ?>
											
								</td>
								<td><?php echo $this->customcache->user_maker($v->CREATED_BY, 'USER_NAME'); ?></td>
								<td><?php echo $this->webspice->formatted_date($v->CREATED_DATE); ?></td>
								<td><?php echo $this->customcache->user_maker($v->UPDATED_BY, 'USER_NAME'); ?></td>
								<td><?php echo $this->webspice->formatted_date($v->UPDATED_DATE); ?></td>
								<td><?php echo $this->webspice->static_status($v->STATUS); ?></td>
								<td>
									<!-- <?php if ($this->webspice->permission_verify('manage_receivable', true)) : ?>
										<a href="<?php echo $url_prefix; ?>manage_receivable/edit/<?php echo $this->webspice->encrypt_decrypt($v->ID, 'encrypt'); ?>" class="btn btn-xs btn-primary" data-featherlight="ajax">Edit</a>
									<?php endif; ?> -->
									<?php if ($this->webspice->permission_verify('manage_receivable', true)) : ?>
										<a href="<?php echo $url_prefix; ?>manage_receivable/delete/<?php echo $this->webspice->encrypt_decrypt($v->ID, 'encrypt'); ?>" class="btn btn-xs btn-danger btn_ajax"> Delete</a>
									<?php endif; ?>
									<!-- <?php if ($this->webspice->permission_verify('manage_receivable', true) && $v->STATUS == 7) : ?>
										<a href="<?php echo $url_prefix; ?>manage_receivable/inactive/<?php echo $this->webspice->encrypt_decrypt($v->ID, 'encrypt'); ?>" class="btn btn-xs btn-warning btn_ajax">Inactive</a>
									<?php endif; ?> -->
									<!-- <?php if ($this->webspice->permission_verify('manage_receivable', true) && $v->STATUS == -7) : ?>
										<a href="<?php echo $url_prefix; ?>manage_receivable/active/<?php echo $this->webspice->encrypt_decrypt($v->ID, 'encrypt'); ?>" class="btn btn-xs btn-success btn_ajax">Active</a>
									<?php endif; ?> -->
									<div class="spinner">&nbsp;</div>
								</td>
							</tr>
						<?php endforeach; ?>
					</table>
				</div>

				<div id="pagination"><?php echo $pager; ?><div class="float_clear_full">&nbsp;</div>
				</div>

			</div>
			<!--end .page_body-->

		</div>

		<div id="footer_container"><?php include(APPPATH . "views/footer.php"); ?></div>
	</div>
</body>

</html>