<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $this->webspice->settings()->domain_name; ?>: Welcome</title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	
	<?php include(APPPATH."views/global.php"); ?>
</head>

<body>
	<div id="wrapper">
		<div id="header_container"><?php include(APPPATH."views/header.php"); ?></div>
		
		<div id="page_manage_user" class="container-fluid page_identifier">
			<div class="page_caption">Manage User</div>
			<div class="page_body table-responsive">
				<div class="row">
					<div class="col-lg-8 col-md-8">		
						<!--filter section-->
						<form id="frm_filter" method="get" action="" data-parsley-validate>
							<input type="hidden" id="token" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
							<div class="row">
								<div class="col-md-12 col-sm-12">
									<table style="width:auto;">
										<tr>
											<td>Keyword</td>
											<td>Tenors</td>
											<td>Role</td>
											<td>Branch</td>
										<tr>
											<td>
												<input type="text" name="SearchKeyword" class="input_style input_full" />
											</td>
											<td>
												<select name="tenor" class="input_style input_full">
				                  <option value="">Select One</option>
				                  <option value="today">Today</option>
				                  <option value="yesterday">Yesterday</option>
				                  <option value="current_month">Current Month</option>
				                  <option value="previous_month">Previous Month</option>
				                  <option value="last_3_month">Last 3 Month</option>
				                  <option value="last_6_month">Last 6 Month</option>
				                  <option value="current_year">Current Year</option>
				                  <option value="previous_year">Previous Year</option>
				                </select>
											</td>
											<td>
												<select name="ROLE_ID" class="input_style input_full">
													<option value="">Choose One</option>
													<?php echo $this->customcache->get_user_role('option_mix'); ?>
												</select>
											</td>
											<td>
												<select name="BRANCH_ID" class="input_style input_full">
													<option value="">Choose One</option>
													<?php echo $this->customcache->get_user_branch('option_mix'); ?>
												</select>
											</td>
										</tr>
										<tr>
											<td colspan="10">
												<input type="submit" name="filter" class="btn btn-info" value="Filter Data" />
												<a class="btn btn-default" href="<?php echo $url_prefix; ?>manage_user">Refresh</a>
												<a class="btn btn-default" href="<?php echo $url_prefix; ?>manage_user/print" target="_blank">Print</a>
												<a class="btn btn-default" href="<?php echo $url_prefix; ?>manage_user/csv" target="_blank">Export</a>
											</td>
										</tr>
									</table>
								</div>
								
							</div>
						</form>
					</div>
					<div class="col-lg-4 col-md-4">
						<div class="user_tips">
							<div class="title">Tips to All Users.</div>
							<div class="description">
								<ul>
									<li>Filter by User ID, User Name, User Email, User Phone in <b>Keyword</b> field.</li>
								</ul>
							</div>
						</div>
					</div>
				</div> <!-- end of the row -->
				<br />
				
				<?php if( !isset($filter_by) || !$filter_by ){$filter_by = 'All Data';} ?>
				<div class="breadcrumb">Filter By: <?php echo $filter_by; ?></div>
				
				<table id="data_table" class="table table-bordered table-striped">
					<tr>
						<th>Status</th>
						<th>Is Logged</th>
						<th>Employee ID</th>
						<th>Employee Name</th>
						<th>Email Address</th>
						<th>User Type</th>
		  			<th>Branch</th>
		  			<th>Assign Role</th>
		  			<th>Action</th>
					</tr>
					<?php foreach($get_record as $k=>$v): ?>
					<tr>
						<td><?php if($v->STATUS==9){echo 'ADMIN';} else {echo $this->webspice->static_status($v->STATUS);} ?></td>
						<td>
							<?php if($v->IS_LOGGED==1): ?>
							<span class="label label-success">Logged</span>
							<?php else: ?>
							<span class="label label-default">Not Logged</span>
							<?php endif; ?>
						</td>
						<td><?php echo $v->EMPLOYEE_ID; ?></td>
						<td><?php echo $v->USER_NAME; ?></td>
						<td><?php echo $v->USER_EMAIL; ?></td>
						<td><?php echo $v->USER_TYPE; ?></td>
						<td><?php echo $this->customcache->option_maker($v->BRANCH_ID, 'OPTION_VALUE'); ?></td>
						<td><?php echo $v->ROLE_NAME; ?></td>
						<td>
							<?php if( $this->webspice->permission_verify('manage_user',true) && $v->STATUS==7 ): ?>
							<a href="<?php echo $url_prefix; ?>manage_user/edit/<?php echo $this->webspice->encrypt_decrypt($v->USER_ID,'encrypt'); ?>" class="btn btn-xs btn-primary" data-featherlight="ajax">Edit</a>
							<?php endif; ?>
							
							<?php if( $this->webspice->permission_verify('manage_user',true) && $v->STATUS==7 ): ?>
							<a href="<?php echo $url_prefix; ?>manage_user/inactive/<?php echo $this->webspice->encrypt_decrypt($v->USER_ID,'encrypt'); ?>" class="btn btn-xs btn-warning btn_ajax">Inactive</a>
							<?php endif; ?>
							
							<?php if( $this->webspice->permission_verify('manage_user',true) && $v->STATUS==-7 ): ?>
							<a href="<?php echo $url_prefix; ?>manage_user/active/<?php echo $this->webspice->encrypt_decrypt($v->USER_ID,'encrypt'); ?>" class="btn btn-xs btn-success btn_ajax">Active</a>
							<?php endif; ?>
							<div class="spinner">&nbsp;</div>
						</td>
					</tr>
					<?php endforeach; ?>
				</table>
				
				<div id="pagination"><?php echo $pager; ?><div class="float_clear_full">&nbsp;</div></div>
				
			</div><!--end .page_body-->

		</div>
		
		<div id="footer_container"><?php include(APPPATH."views/footer.php"); ?></div>
	</div>
</body>
</html>