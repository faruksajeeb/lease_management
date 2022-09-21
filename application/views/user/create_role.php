<?php $page_title = 'Create Role'; ?>
<?php $route = 'create_role'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $this->webspice->settings()->domain_name; ?>: Create Role</title>
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
					<div class="col-lg-6 col-md-6 col-sm-12" id="form_width_full">
						<fieldset class="divider"><legend>Please enter role name and select permission(s)</legend></fieldset>
					
						<div class="stitle">* Mandatory Field</div>
						<form id="frm_<?php echo str_replace(' ','_',strtolower($page_title)); ?>" class="form_post_ajax" form-route="<?php echo $route; ?>" method="post" action="" data-parsley-validate>
							<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
							<input type="hidden" name="ROLE_ID" value="<?php if( isset($edit['ROLE_ID']) && $edit['ROLE_ID'] ){echo $this->webspice->encrypt_decrypt($edit['ROLE_ID'], 'encrypt');} ?>" />
							
							<table width="100%">
								<tr>
									<td>
										<div class="form_label">Role Name*</div>
										<div>
											<input type="text"  class="input_full form-control" id="role_name" name="role_name" value="<?php echo set_value('role_name',$edit['ROLE_NAME']); ?>"  required />
											<span class="fred"><?php echo form_error('role_name'); ?></span>
										</div>
									</td>
								</tr>
								
								<!--permission-->
								<?php if($get_permission_with_group): ?>
								<tr>
									<td>
										<table class="table table-bordered">
										<?php
										$group_name = null;
										$group_count = 0;
										$is_checked = null;
										$edit['PERMISSION_NAME'] ? $edited_permission = explode(',', $edit['PERMISSION_NAME']) : $edited_permission = array();
										foreach($get_permission_with_group as $k=>$v){
											$is_checked = null;
											# for edit - verify that; the permission is selected before or not
											foreach($edited_permission as $k11=>$v11){
												if( $v11==$v->PERMISSION_NAME ){ $is_checked = ' checked="checked"'; }
											}
	
											# get new group name and count by group name
											if( $v->GROUP_NAME != $group_name ){
												$group_name = $v->GROUP_NAME;
												$group_count = 0;
												foreach($get_permission_with_group as $k1=>$v1){
													if($v1->GROUP_NAME == $v->GROUP_NAME){$group_count++;}
												}
	
												echo '<tr>';
													echo '<td rowspan="'.$group_count.'" class="fbold" style="vertical-align:middle;">'.ucwords(str_replace('_',' ',$group_name)).'</td>';
													echo '<td><div><input type="checkbox" name="permission[]" value="'.$v->PERMISSION_NAME.'"'.$is_checked.' />&nbsp;'.ucwords(str_replace('_',' ',$v->PERMISSION_NAME)).'</div></td>';
												echo '</tr>';
												
											}elseif( $v->GROUP_NAME == $group_name ){
												# create checkbox
												echo '<tr><td><div><input type="checkbox" name="permission[]" value="'.$v->PERMISSION_NAME.'"'.$is_checked.' />&nbsp;'.ucwords(str_replace('_',' ',$v->PERMISSION_NAME)).'</div></td></tr>';
											}
											
										} 
										?>
										</table>
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
			
		</div><!--end #page_create_role -->
	
		<div id="footer_container"><?php include(APPPATH."views/footer.php"); ?></div>
		
	</div>
 
</body>
</html>