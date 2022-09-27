
<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $this->webspice->settings()->domain_name; ?>: Manage Lease Onboarding</title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	
	<?php include(APPPATH."views/global.php"); ?>
</head>

<body>
	<div id="wrapper">
		<div id="header_container"><?php include(APPPATH."views/header.php"); ?></div>
		
		<div id="page_manage_lease_onboarding" class="container-fluid page_identifier">
			<div class="page_caption">Manage Lease Onboarding</div>
			<div class="page_body table-responsive">
				<div class="row">
					<div class="col-lg-8 col-md-8">
						<!--filter section-->
						<form id="frm_filter" method="get" action="" data-parsley-validate>
							<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
							
							<table style="width:auto;">
								<tr>
									<td>Lease Name/Branch Code/SOL</td>
									<td>Region</td>
									<td>Branch List</td>
									<td>Vendor List</td>
								</tr>
								<tr>
									<td><input type="text" name="SearchKeyword" class="input_style input_full" /></td>
									<td>
										<select class="form-control choosen" id="REGION" name="REGION">
											<option value="">Select One</option>
											<?php echo $this->customcache->get_region('option_mix'); ?>
										</select>
									</td>
									<td>
										<select class="form-control choosen" id="BRANCH_ID" name="BRANCH_ID">
											<option value="">Select One</option>
											<?php echo $this->customcache->get_user_branch('option_mix'); ?>
										</select>
									</td>
									<td>
										<select class="form-control choosen" id="VENDOR_ID" name="VENDOR_ID">
											<option value="">Select One</option>
											<?php echo $this->customcache->get_vendor_list('option_mix'); ?>
										</select>
									</td>
								</tr>
								<tr>
									<td colspan="10">
										<input type="submit" name="filter" class="btn btn-info" value="Filter Data" />
										<a class="btn btn-default" href="<?php echo $url_prefix; ?>manage_lease_onboarding">Refresh</a>
										<a class="btn btn-default" href="<?php echo $url_prefix; ?>manage_lease_onboarding/print" target="_blank">Print</a>
										<a class="btn btn-default" href="<?php echo $url_prefix; ?>manage_lease_onboarding/csv" target="_blank">Export</a>
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
									<li>Filter by Lease Name in <b>Keyword</b> field.</li>
								</ul>
							</div>
						</div>
					</div>

				</div> <!-- end of the row -->
				
				<br />
				<?php if( !isset($filter_by) || !$filter_by ){$filter_by = 'All Data';} ?>
				<div class="breadcrumb">Filter By: <?php echo str_replace(array('Branch Id','Vendor Id'),array('Branch','Vendor'),$filter_by); ?></div>
				
				<div id="data_table" style="overflow:auto;">
					<table class="table table-bordered table-striped">
						<tr>
							<th>ID</th>
							<th>Region</th>
							<th>Branch Name</th>
							<!--<th>Branch Code/SOL</th>-->
							<th>Lease Name</th>
							<th>Lease Type</th>
							<th>Lease Term</th>
							<th>Vendor</th>
							<th>License No.</th>
							<th>License Issue Date</th>
							<th>Branch Opening Date</th>
							<th>Type</th>
							<th>Address</th>
							<th>City</th>
							<th>District</th>
							<th>Thana/Upzilla</th>
							<th>Floor Space (SQFT)</th>
							<th>Rent per SQFT</th>
							<!--<th>Contact Person</th>
							<th>Contact Mobile</th>
							<th>Contact Email</th>-->
							<th>Agreement Date</th>
							<th>Agreement Expiry</th>
							<th>Agreement Document</th>
							<th>Cost Center Details</th>
							<th>Rent & Advance</th>
							<!--<th>Total Amount (Loan)</th>
							<th>No. of Customer (Loan)</th>
							<th>Total Amount (Deposit)</th>
							<th>No. of Customer (Deposit)</th>
							<th>Profit/Loss (Crore)</th>-->
							<th>Created By</th>
							<th>Created Date</th>
							<th>Updated By</th>
							<th>Updated Date</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
						<?php foreach($get_record as $k=>$v): ?>
						<tr>
							<td class="text-center">
								<?php echo $v->ID; ?><br />
								<?php if( $this->webspice->permission_verify('manage_lease_onboarding',true) && $this->webspice->get_user('USER_TYPE')!='branch_user' ): ?>
								<a href="<?php echo $url_prefix; ?>manage_lease_onboarding/edit/<?php echo $this->webspice->encrypt_decrypt($v->ID,'encrypt'); ?>" class="btn btn-xs btn-primary edit_btn" data-featherlight="ajax">Edit</a>
								<?php endif; ?>
							</td>
							<td><?php echo $v->REGION_NAME; ?></td>
							<td><?php echo $this->customcache->option_maker($v->BRANCH_ID, 'OPTION_VALUE'); ?></td>
							<!--<td><?php echo $v->BRANCH_CODE; ?></td>-->
							<td><?php echo $v->LEASE_NAME; ?></td>
							<td><?php echo ucfirst($v->LEASE_TYPE); ?></td>
							<td><?php echo ucwords(str_replace('_',' ',$v->LEASE_TERM)); ?></td>
							<td><?php echo $v->VENDOR_NAME.' &raquo; '.$v->EMAIL; ?></td>
							<td><?php echo $v->LICENSE_NO; ?></td>
							<td><?php echo $v->LICENSE_ISSUE_DATE; ?></td>
							<td><?php echo $v->BRANCH_OPENING_DATE; ?></td>
							<td><?php echo ucwords($v->TYPE); ?></td>
							<td><?php echo $v->ADDRESS; ?></td>
							<td><?php echo $v->CITY; ?></td>
							<td><?php echo $v->DISTRICT; ?></td>
							<td><?php echo $v->THANA_UPAZILLA; ?></td>
							<td><?php echo $v->FLOOR_SPACE; ?></td>
							<td><?php echo $v->RENT_PER_SQFT; ?></td>
							<!--<td><?php echo $v->CONTACT_PERSON; ?></td>
							<td><?php echo $v->CONTACT_MOBILE_NO; ?></td>
							<td><?php echo $v->CONTACT_EMAIL; ?></td>-->
							<td><?php echo $v->AGREEMENT_DATE; ?></td>
							<td><?php echo $v->AGREEMENT_EXPIRY; ?></td>
							<td><a target="_blank" href="<?php echo $this->webspice->get_path('agreement').'/'.$v->AGREEMENT_DOCUMENT; ?>"><?php echo $v->AGREEMENT_DOCUMENT; ?></a></td>
							<td>
							<?php
								$get_slab = $this->db->query("
								SELECT TBL_COST_CENTER_DETAILS.*,TBL_OPTION.OPTION_VALUE as COST_CENTER
								FROM TBL_COST_CENTER_DETAILS 
								LEFT JOIN TBL_OPTION on TBL_OPTION.OPTION_ID = TBL_COST_CENTER_DETAILS.COST_CENTER_ID
								WHERE LEASE_ID = ?
								", 
								array(
								$v->ID
								))->result();
								
								echo '<table class="table table-borderd table-striped" style="margin-bottom:0px;">';
									echo '<tr>';
										echo '<th>Cost Center</th>';
										echo '<th>Amount (%)</th>';
									echo '</tr>';
									foreach($get_slab as $k1=>$v1){
									echo '<tr>';
										echo '<td>'.ucwords($v1->COST_CENTER).'</td>';
										echo '<td>'.$v1->PERCENTAGE.'</td>';
									echo '</tr>';
									}
								echo '</table>';
								?>
							</td>
							<td>
								<?php
								$get_slab = $this->db->query("
								SELECT TBL_LEASE_AGREEMENT.* 
								FROM TBL_LEASE_AGREEMENT
								WHERE LEASE_ID = ?
								", 
								array(
								$v->ID
								))->result();
								
								echo '<table class="table table-borderd table-striped" style="margin-bottom:0px;">';
									echo '<tr>';
										echo '<th>From</th>';
										echo '<th>To</th>';
										echo '<th>Type</th>';
										echo '<th>Amt. ex. TAX & VAT</th>';
										echo '<th>Amt. with TAX</th>';
										echo '<th>Amt. with VAT</th>';
									echo '</tr>';
									foreach($get_slab as $k1=>$v1){
									echo '<tr>';
										echo '<td>'.$v1->DATE_FROM.'</td>';
										echo '<td>'.$v1->DATE_TO.'</td>';
										echo '<td>'.ucfirst($v1->TYPE).'</td>';
										echo '<td>'.$v1->AMOUNT.'</td>';
										echo '<td>'.$v1->AMOUNT_WITH_TAX.'</td>';
										echo '<td>'.$v1->AMOUNT_WITH_VAT.'</td>';
									echo '</tr>';
									}
								echo '</table>';
								?>
							</td>
							<!--<td><?php echo $v->TOTAL_AMOUINT_LOAN; ?></td>
							<td><?php echo $v->NO_OF_CUSTOMER_LOAN; ?></td>
							<td><?php echo $v->TOTAL_AMOUNT_DEPOSIT; ?></td>
							<td><?php echo $v->NO_OF_CUSTOMER_DEPOSIT; ?></td>
							<td><?php echo $v->PROFIT_LOSS; ?></td>-->
							<td><?php echo $this->customcache->user_maker($v->CREATED_BY,'USER_NAME'); ?></td>
							<td><?php echo $this->webspice->formatted_date($v->CREATED_DATE, null, 'full'); ?></td>
							<td><?php echo $this->customcache->user_maker($v->UPDATED_BY,'USER_NAME'); ?></td>
							<td><?php echo $this->webspice->formatted_date($v->UPDATED_DATE, null, 'full'); ?></td>
							<td><?php echo $this->webspice->static_status($v->STATUS); ?></td>
							<td>
								<?php if( $this->webspice->permission_verify('manage_lease_onboarding',true) && $v->STATUS==7 && $this->webspice->get_user('USER_TYPE')!='branch_user' ): ?>
								<a href="<?php echo $url_prefix; ?>manage_lease_onboarding/inactive/<?php echo $this->webspice->encrypt_decrypt($v->ID,'encrypt'); ?>" class="btn btn-xs btn-danger btn_orange btn_ajax">Inactive</a>
								<?php endif; ?>
								
								<?php if( $this->webspice->permission_verify('manage_lease_onboarding',true) && $v->STATUS==-7 && $this->webspice->get_user('USER_TYPE')!='branch_user' ): ?>
								<a href="<?php echo $url_prefix; ?>manage_lease_onboarding/active/<?php echo $this->webspice->encrypt_decrypt($v->ID,'encrypt'); ?>" class="btn btn-xs btn-success btn_orange btn_ajax">Active</a>
								<?php endif; ?>
								<div class="spinner">&nbsp;</div>
							</td>
						</tr>
						<?php endforeach; ?>
					</table>
				</div>
				
				<div id="pagination"><?php echo $pager; ?><div class="float_clear_full">&nbsp;</div></div>
				
			</div><!--end .page_body-->

		</div>
		
		<div id="footer_container"><?php include(APPPATH."views/footer.php"); ?></div>
	</div>
</body>
</html>