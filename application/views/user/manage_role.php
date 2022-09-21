<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $this->webspice->settings()->domain_name; ?>: Manage Role</title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	
	<?php include(APPPATH."views/global.php"); ?>
</head>

<body>
	<div id="wrapper">
		<div id="header_container"><?php include(APPPATH."views/header.php"); ?></div>
		
		<div id="page_manage_role" class="container-fluid page_identifier">
			<div class="page_caption">Manage Role</div>

			<div class="page_body table-responsive">
				<div class="row">
					<div class="col-lg-8 col-md-8">						
						<!--filter section-->
						<form id="frm_filter" method="get" action="" data-parsley-validate>
							<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
							
							<table style="width:auto;">
								<tr>
									<td>Keyword</td>
								</tr>
								<tr>
									<td>
			              <input type="text" name="SearchKeyword" class="input_style input_full" />
									</td>
								</tr>
								<tr>
									<td colspan="10">
										<input type="submit" name="filter" class="btn btn-info" value="Filter Data" />
										<a class="btn btn-default" href="<?php echo $url_prefix; ?>manage_role">Refresh</a>
										<a class="btn btn-default" href="<?php echo $url_prefix; ?>manage_role/print" target="_blank">Print</a>
										<a class="btn btn-default" href="<?php echo $url_prefix; ?>manage_role/csv" target="_blank">Export</a>
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
									<li>Filter by Role ID, Role Name, Permission Name in <b>Keyword</b> field.</li>
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
							<th>Role ID</th>
							<th>Role Name</th>
							<th>Permission Name</th>
							<th>Created By</th>
							<th>Created Date</th>
							<th>Updated By</th>
							<th>Updated Date</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
						<?php foreach($get_record as $k=>$v): ?>
						<tr>
							<td><?php echo $v->ROLE_ID; ?></td>
							<td><?php echo $v->ROLE_NAME; ?></td>
							<td><?php echo ucwords(str_replace(',',', ', str_replace('_',' ',$v->PERMISSION_NAME))); ?></td>
							<td><?php echo $this->customcache->user_maker($v->CREATED_BY,'USER_NAME'); ?></td>
							<td><?php echo $this->webspice->formatted_date($v->CREATED_DATE); ?></td>
							<td><?php echo $this->customcache->user_maker($v->UPDATED_BY,'USER_NAME'); ?></td>
							<td><?php echo $this->webspice->formatted_date($v->UPDATED_DATE); ?></td>
							<td><?php echo $this->webspice->static_status($v->STATUS); ?></td>
							<td>
								<?php if( $this->webspice->permission_verify('manage_role',true) && $v->STATUS!=9 ): ?>
								<a href="<?php echo $url_prefix; ?>manage_role/edit/<?php echo $this->webspice->encrypt_decrypt($v->ROLE_ID,'encrypt'); ?>" class="btn btn-xs btn-primary btn_orange edit_btn" data-featherlight="ajax">Edit</a>
								<?php endif; ?>
								
								<?php if( $this->webspice->permission_verify('manage_role',true) && $v->STATUS==7 ): ?>
								<a href="<?php echo $url_prefix; ?>manage_role/inactive/<?php echo $this->webspice->encrypt_decrypt($v->ROLE_ID,'encrypt'); ?>" class="btn btn-xs btn-danger btn_orange btn_ajax">Inactive</a>
								<?php endif; ?>
								
								<?php if( $this->webspice->permission_verify('manage_role',true) && $v->STATUS==-7 ): ?>
								<a href="<?php echo $url_prefix; ?>manage_role/active/<?php echo $this->webspice->encrypt_decrypt($v->ROLE_ID,'encrypt'); ?>" class="btn btn-xs btn-success btn_orange btn_ajax">Active</a>
								<?php endif; ?>
							</td>
						</tr>
						<?php endforeach; ?>
					</table>
				</div>
			</div><!--end .page_body-->

		</div>
		
		<div id="footer_container"><?php include(APPPATH."views/footer.php"); ?></div>
	</div>
</body>
</html>