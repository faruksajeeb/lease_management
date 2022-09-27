<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $this->webspice->settings()->domain_name; ?>: Option</title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	
	<?php include(APPPATH."views/global.php"); ?>
</head>

<body>
	<div id="wrapper">
		<div id="header_container"><?php include(APPPATH."views/header.php"); ?></div>
		
		<div id="page_manage_option" class="container-fluid page_identifier">
			<div class="page_caption">Manage Option</div>
			<div class="page_body table-responsive">
				<div class="row">
					<div class="col-lg-8 col-md-8">	
						<!--filter section-->
						<form id="frm_filter" method="get" action="" data-parsley-validate>
							<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
							
							<table style="width:auto;">
								<tr>
									<td>Keyword</td>
									<td>Group Name</td>
								</tr>
								<tr>
									<td>
			              <input type="text" name="SearchKeyword" class="input_style input_full" />
									</td>
									<td>
										<div>
      								 <select id="group_name" name="GROUP_NAME" class="input_style input_full">
													<option value="">Select One</option>
													<option value="region">Region</option>
													<option value="branch">Branch</option>
													<option value="cost_center">Cost Center</option>
													<option value="effective_interest_rate">Effective Interest Rate</option>
											</select>
 											</div>
									</td>
								</tr>
								<tr>
									<td colspan="10">
										<input type="submit" name="filter" class="btn btn-info" value="Filter Data" />
										<a class="btn btn-default" href="<?php echo $url_prefix; ?>manage_option">Refresh</a>
										<a class="btn btn-default" href="<?php echo $url_prefix; ?>manage_option/print" target="_blank">Print</a>
										<a class="btn btn-default" href="<?php echo $url_prefix; ?>manage_option/csv" target="_blank">Export</a>
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
									<li>Filter by Group Name, Option Value, Option Value Bangla, Option Value 2 and Option Value 2 Bangla in <b>Keyword</b> field.</li>
								</ul>
							</div>
						</div>
					</div>
				</div> <!-- end of the row -->				
				<br />
				<?php if( !isset($filter_by) || !$filter_by ){$filter_by = 'All Data';} ?>
				<div class="breadcrumb">Filter By: <?php echo $filter_by; ?></div>
				
				<div id="data_table" style="overflow:auto;">
					<table class="table table-bordered table-striped">
						<tr>
							<th>Option ID</th>
							<th>Group Name</th>
							<th width="30" style="width:30px;">Option Value</th>
							<th>Option Value Bangla</th>
							<th>Option Value 2</th>
							<th>Option Value 2 Bangla</th>
							<th>Created By</th>
							<th>Created Date</th>
							<th>Updated By</th>
							<th>Updated Date</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
						<?php foreach($get_record as $k=>$v): ?>
						<tr>
							<td><?php echo $v->OPTION_ID; ?></td>
							<td><?php echo ucwords(str_replace('_',' ',$v->GROUP_NAME)); ?></td>
							<td><?php echo $v->OPTION_VALUE; ?></td>
							<td><?php echo $v->OPTION_VALUE_BANGLA; ?></td>
							<td><?php echo $v->OPTION_VALUE_2; ?></td>
							<td><?php echo $v->OPTION_VALUE_2_BANGLA; ?></td>
							<td><?php echo $this->customcache->user_maker($v->CREATED_BY,'USER_NAME'); ?></td>
							<td><?php echo $this->webspice->formatted_date($v->CREATED_DATE); ?></td>
							<td><?php echo $this->customcache->user_maker($v->UPDATED_BY,'USER_NAME'); ?></td>
							<td><?php echo $this->webspice->formatted_date($v->UPDATED_DATE); ?></td>
							<td><?php echo $this->webspice->static_status($v->STATUS); ?></td>
							<td>
								<?php if( $this->webspice->permission_verify('manage_option',true) ): ?>
								<a href="<?php echo $url_prefix; ?>manage_option/edit/<?php echo $this->webspice->encrypt_decrypt($v->OPTION_ID,'encrypt'); ?>" class="btn btn-xs btn-primary" data-featherlight="ajax">Edit</a>
								<?php endif; ?>
								
								<?php if( $this->webspice->permission_verify('manage_option',true) && $v->STATUS==7 ): ?>
								<a href="<?php echo $url_prefix; ?>manage_option/inactive/<?php echo $this->webspice->encrypt_decrypt($v->OPTION_ID,'encrypt'); ?>" class="btn btn-xs btn-warning btn_ajax">Inactive</a>
								<?php endif; ?>
								
								<?php if( $this->webspice->permission_verify('manage_option',true) && $v->STATUS==-7 ): ?>
								<a href="<?php echo $url_prefix; ?>manage_option/active/<?php echo $this->webspice->encrypt_decrypt($v->OPTION_ID,'encrypt'); ?>" class="btn btn-xs btn-success btn_ajax">Active</a>
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