<?php $page_title = 'Create User'; ?>
<?php $route = 'create_user'; ?>

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
		<?php if( $this->uri->segment(2) != "edit" ): ?>
		<div id="header_container"><?php include(APPPATH."views/header.php"); ?></div>
		<?php endif; ?>
		
		<div id="page_<?php echo str_replace(' ','_',strtolower($page_title)); ?>" class="container-fluid page_identifier">
			<div class="page_caption"><?php echo $page_title; ?></div>
			<div class="page_body">
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-12">
						<fieldset class="divider"><legend>Please enter user information</legend></fieldset>
						<div class="stitle">* Mandatory Field</div>
						<form id="frm_<?php echo str_replace(' ','_',strtolower($page_title)); ?>" class="form_post_ajax" form-route="<?php echo $route; ?>" method="post" action="" data-parsley-validate>
							<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
							<input type="hidden" name="user_id" value="<?php if( isset($edit['USER_ID']) && $edit['USER_ID'] ){echo $this->webspice->encrypt_decrypt($edit['USER_ID'], 'encrypt');} ?>" />
							<table width="100%">
								<tr>
									<td>
										<table width="100%">
											<tr>
												<td>
													<div class="form_label">Employee ID*</div>
													<div>
														<input type="text" class="input_full form-control" id="employee_id" name="employee_id" value="<?php echo set_value('employee_id',$edit['EMPLOYEE_ID']); ?>" data-parsley-maxlength="100" data-parsley-validation-threshold="1" data-parsley-trigger="keyup" required />
														<span class="fred"><?php echo form_error('employee_id'); ?></span>
													</div>
												</td>
												<td>
													<div class="form_label">User Name*</div>
													<div>
														<input type="text" class="input_full form-control" id="user_name" name="user_name" value="<?php echo set_value('user_name',$edit['USER_NAME']); ?>" required />
														<span class="fred"><?php echo form_error('user_name'); ?></span>
													</div>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								
								<tr>
									<td>
										<div class="form_label">User Type*</div>
										<div>
											<select name="user_type" class="input_full form-control" id="drp_user_type" required>
											<option value="">Select One</option>
											<option value="admin" <?php if( set_value('user_type', $edit['USER_TYPE']) == 'admin'){echo 'selected="selected"';} ?> data-description="">Admin</option>
											<option value="branch_user" <?php if( set_value('user_type', $edit['USER_TYPE']) == 'branch_user'){echo 'selected="selected"';} ?> data-description="">Branch User</option>
											</select>
										</div>
									</td>
								</tr>
								
								<?php $is_display = 'display:none;'; if( $edit['USER_TYPE'] && $edit['USER_TYPE']=='branch_user' ){ $is_display = ''; } ?>
								<tr id="tr_branch" style="<?php echo $is_display; ?>">
									<td>
										<div class="form_label">User Branch*</div>
										<div>
											<select name="branch_id" class="input_full form-control" id="branch_id">
											<option value="">Select One</option>
											<?php if( set_value('branch_id', $edit['BRANCH_ID']) ): ?>
											<?php echo str_replace('value="'.set_value('branch_id', $edit['BRANCH_ID']).'"','value="'.set_value('branch_id', $edit['BRANCH_ID']).'" selected="selected"', $this->customcache->get_user_branch()); ?>
											<?php else: ?>
											<?php echo $this->customcache->get_user_branch(); ?>
											<?php endif; ?>
											</select>
											<span class="fred"><?php echo form_error('branch_id'); ?></span> 
										</div>
									</td>
								</tr>
								
								<tr>
									<td>
										<table width="100%">
											<tr>
												<td colspan="2">
													<div class="form_label">User Email*</div>
													<div>
														<?php if( $edit['USER_ID'] ): ?>
														<input type="hidden" name="user_email" value="<?php echo $edit['USER_EMAIL']; ?>" />
														<strong><?php echo $edit['USER_EMAIL']; ?></strong>
														<?php else: ?>
														<input type="email" class="input_full form-control" id="user_email" name="user_email" value="<?php echo set_value('user_email',$edit['USER_EMAIL']); ?>" data-parsley-type="email" data-parsley-minlength="5" data-parsley-maxlength="100" data-parsley-validation-threshold="1" data-parsley-trigger="keyup" required />
														<span class="fred"><?php echo form_error('user_email'); ?></span>
														<?php endif; ?>
													</div>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								
								<tr>
									<td>
										<div class="form_label">User Role*</div>
										<div>
											<select name="user_role" class="input_full form-control" required>
											<option value="">Select One</option>
											<?php if( set_value('user_role', $edit['ROLE_ID']) ): ?>
											<?php echo str_replace('value="'.set_value('user_role', $edit['ROLE_ID']).'"','value="'.set_value('user_role', $edit['ROLE_ID']).'" selected="selected"', $this->customcache->get_user_role()); ?>
											<?php else: ?>
											<?php echo $this->customcache->get_user_role(); ?>
											<?php endif; ?>
											</select>
											<span class="fred"><?php echo form_error('user_role'); ?></span> 
										</div>
									</td>
								</tr>

								<?php if( $edit['USER_ID'] ): ?>
								<tr>
									<td>
										<div class="form_label">User Login Status</div>
										<div>
											<input type="text" class="input_full form-control" id="is_logged" name="is_logged" value="<?php echo set_value('is_logged',$edit['IS_LOGGED']); ?>" />
											<span class="fred"><?php echo form_error('is_logged'); ?></span>
										</div>
									</td>
								</tr>
								<?php endif; ?>
								<tr>
									<td>
										<div><input type="submit" class="btn btn-danger" value="Submit Data" /></div>
										<div class="spinner">&nbsp;</div>
									</td>
								</tr>
							</table>
						</form>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-12">
						
					</div>
				</div>
			</div>
		</div>
		<div id="footer_container"><?php include(APPPATH."views/footer.php"); ?></div>
	</div>
	
	<script>
		$(document).ready(function(){
			$('#drp_user_type').change(function(){
				var me = $(this);
				if(me.val()=='branch_user'){
					$('#tr_branch').show('slow');
				}else{
					$('#tr_branch').hide('slow');	
				}
			});
			
			$('.form_post_ajax').submit(function(){
				if( $('#drp_user_type').val()=='branch_user' && !$('#branch_id').val() ){
					alert('Please select a Branch.');
					return false;
				}	
			});
		});
	</script>
</body>
</html>