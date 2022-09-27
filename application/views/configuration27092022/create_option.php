<?php $page_title = 'Create Option'; ?>
<?php $route = 'create_option'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $this->webspice->settings()->domain_name; ?>: <?php echo $page_title; ?></title>
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
			<div class="page_caption">Create Option</div>
			<div class="page_body stamp">
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-12">
						<fieldset class="divider"><legend>Please enter required information</legend></fieldset>
					
						<div class="stitle">* Mandatory Field</div>
						
						<form id="frm_<?php echo str_replace(' ','_',strtolower($page_title)); ?>" class="form_post_ajax" form-route="<?php echo $route; ?>" method="post" action="" data-parsley-validate>
							<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
							<input type="hidden" name="key" value="<?php if( isset($edit['OPTION_ID']) && $edit['OPTION_ID'] ){echo $this->webspice->encrypt_decrypt($edit['OPTION_ID'], 'encrypt');} ?>" />
			            
							<table width="100%">
								<tr>
									<td>
										<div class="form_label">Group Name*</div>
										<div>
											<?php $onlyUpdate = array("effective_interest_rate"); ?>
			               	<select name="group_name" id="group_name" class="input_full form-control" required>
					              <option value="">Select One</option>
					              <!--below options can only update, no multiple row/value under the group-->
												<?php if( $edit['OPTION_ID'] && in_array(set_value('group_name', $edit['GROUP_NAME']), $onlyUpdate) ): ?>
												<option value="effective_interest_rate" <?php if( set_value('group_name', $edit['GROUP_NAME']) == 'effective_interest_rate'){echo 'selected="selected"';} ?> data-description="This is a configuration value. It updating might impact your calculation in future (no impact for previous data).">Effective Interest Rate</option>
												<?php else: ?>
												<option value="region" <?php if( set_value('group_name', $edit['GROUP_NAME']) == 'region' ){echo 'selected="selected"';} ?> data-description="You are requested to enter Region Name (English) in Option Value field and Region Name (Bangla) in Option Value (Bangla) field. No entry required for Option Value 2 and Option Value 2 (Bangla) field.">Region</option>
												<option value="branch" <?php if( set_value('group_name', $edit['GROUP_NAME']) == 'branch' ){echo 'selected="selected"';} ?> data-description="You are requested to enter Branch Name (English) in Option Value field and Branch Name (Bangla) in Option Value (Bangla) field. No entry required for Option Value 2 and Option Value 2 (Bangla) field.">Branch Name</option>
												<option value="cost_center" <?php if( set_value('group_name', $edit['GROUP_NAME']) == 'cost_center' ){echo 'selected="selected"';} ?> data-description="You are requested to enter Cost Center (English) in Option Value field and Cost Center (Bangla) in Option Value (Bangla) field. No entry required for Option Value 2 and Option Value 2 (Bangla) field.">Cost Center</option>
												<?php endif; ?>
		             			</select>
		            			<span class="fred"><?php echo form_error('group_name'); ?></span> 
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="form_label">Option Value (one)*</div>
										<div>
											<input type="text"  class="input_full form-control" id="option_value" name="option_value" value="<?php echo set_value('option_value',$edit['OPTION_VALUE']); ?>"  required />
											<span class="fred"><?php echo form_error('option_value'); ?></span>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="form_label">Option Value Bangla (one)*</div>
										<div>
											<input type="text"  class="input_full form-control" id="option_value_bangla" name="option_value_bangla" value="<?php echo set_value('option_value_bangla',$edit['OPTION_VALUE_BANGLA']); ?>"  required />
											<span class="fred"><?php echo form_error('option_value_bangla'); ?></span>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="form_label">Option Value (two)</div>
										<div>
											<input type="text"  class="input_full form-control" id="option_value_2" name="option_value_2" value="<?php echo set_value('option_value_2',$edit['OPTION_VALUE_2']); ?>" />
											<span class="fred"><?php echo form_error('option_value_two'); ?></span>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="form_label">Option Value Bangla (two)</div>
										<div>
											<input type="text"  class="input_full form-control" id="option_value_2_bangla" name="option_value_2_bangla" value="<?php echo set_value('option_value_2_bangla',$edit['OPTION_VALUE_2_BANGLA']); ?>" />
											<span class="fred"><?php echo form_error('option_value_2_bangla'); ?></span>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div><input type="submit" class="btn btn-danger" value="Submit Data" /></div>
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
		
		<?php if( $this->uri->segment(2) != "edit" ): ?>
		<div id="footer_container"><?php include(APPPATH."views/footer.php"); ?></div>
		<?php endif; ?>
		
	</div>
	<script type="text/javascript">
		$(document).ready(function()
		{
			$('#group_name').change(function(){
				var desc = $('option:selected', this).attr('data-description');
				
				if( !desc ){
					$('#right_part').html("");
					return false;
				}
				
				$('#right_part').html('<br /><br /><br /><span style="color:red;">Instruction:</span><br />' + desc).css('font-weight','bold');
			});
			
			// if edit
			var key = $('#key').val();
			if(key)
			{
				var desc = $('#group_name').find('option:selected').attr('data-description');
				
				if( !desc ){
					$('#right_part').html("");
					return false;
				}
				
				$('#right_part').html('<br /><br /><br /><span style="color:red;">Instruction:</span><br />' + desc).css('font-weight','bold');
			}
			
		});
	</script>
</body>
</html>