 <?php $page_title = 'Lease Onboarding'; ?>
<?php $route = 'lease_onboarding'; ?>

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
			<div class="page_caption"><?php echo $page_title; ?></div>
			<div class="page_body stamp">
				<form id="frm_<?php echo str_replace(' ','_',strtolower($page_title)); ?>" form-route="<?php echo $route; ?>" method="post" action="" autocomplete="off" data-parsley-validate enctype="multipart/form-data">
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-12" id="form_input_id">
						<fieldset class="divider"><legend>Please enter required information</legend></fieldset>
					
						<div class="stitle">* Mandatory Field</div>
						<input type="hidden" id="token" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
						<input type="hidden" name="key" id="key" value="<?php if( isset($edit['ID']) && $edit['ID'] ){echo $this->webspice->encrypt_decrypt($edit['ID'], 'encrypt');} ?>" />
	            
						<div class="form-group">
							<div class="form_label">Region*</div>
							<div>
								<select class="form-control choosen" id="REGION" name="REGION" required>
									<option value="">Select One</option>
									<?php echo str_replace('value="'.set_value('REGION', $edit['REGION']).'"','value="'.set_value('REGION', $edit['REGION']).'" selected="selected"', $this->customcache->get_region()); ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="form_label">Branch Name*</div>
							<div>
								<?php if( isset($edit['ID']) && $edit['ID'] ): ?>
								<input type="hidden" name="BRANCH_ID" value="<?php echo $edit['BRANCH_ID']; ?>">
								<strong><?php echo $this->customcache->option_maker($edit['BRANCH_ID'], 'OPTION_VALUE'); ?></strong>
								<?php else: ?>
								<select class="form-control choosen" id="BRANCH_ID" name="BRANCH_ID" required>
									<option value="">Select One</option>
									<?php echo str_replace('value="'.set_value('BRANCH_ID', $edit['BRANCH_ID']).'"','value="'.set_value('BRANCH_ID', $edit['BRANCH_ID']).'" selected="selected"', $this->customcache->get_user_branch()); ?>
								</select>
								<?php endif; ?>
							</div>
						</div>
						<div class="form-group">
							<div class="form_label">Lease Name*</div>
							<div>
								<input type="text" class="input_full form-control" id="LEASE_NAME" name="LEASE_NAME" value="<?php echo set_value('LEASE_NAME',$edit['LEASE_NAME']); ?>" required />
								<span class="fred"><?php echo form_error('LEASE_NAME'); ?></span>
							</div>
						</div>

						<div class="form-group">
							<div class="form_label">Lease Type*</div>
							<div>
								<select class="form-control choosen" id="LEASE_TYPE" name="LEASE_TYPE" required>
									<option value="">Select One</option>
									<option value="taken" <?php if(set_value('LEASE_TYPE', $edit['LEASE_TYPE']=='taken')){echo 'selected="selected"';} ?>>Taken (ভাড়া নেওয়া) </option>
									<option value="given" <?php if(set_value('LEASE_TYPE', $edit['LEASE_TYPE']=='given')){echo 'selected="selected"';} ?>>Given (ভাড়া দেওয়া) </option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<div class="form_label">Lease Term*</div>
							<div>
								<select class="form-control choosen" id="LEASE_TERM" name="LEASE_TERM" required>
									<option value="">Select One</option>
									<option value="short_term_lease" <?php if(set_value('LEASE_TERM', $edit['LEASE_TERM']=='short_term_lease')){echo 'selected="selected"';} ?>>Short Term Lease</option>
									<option value="long_term_lease" <?php if(set_value('LEASE_TERM', $edit['LEASE_TERM']=='long_term_lease')){echo 'selected="selected"';} ?>>Long Term Lease</option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<div class="form_label">Vendor / Customer*</div>
							<div>
								<select class="form-control choosen" id="VENDOR_ID" name="VENDOR_ID" required>
									<option value="">Select One</option>
									<?php foreach($vendors as $k=>$v): 
										$selected = isset($edit['VENDOR_ID']) && $edit['VENDOR_ID']==$v->ID ? 'selected="selected"' :'';
									?>
										<option value="<?php echo $v->ID;?>" <?php echo $selected; ?>><?php echo $v->VENDOR_NAME.' &raquo; '.$v->EMAIL;?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="form_label">Tax Rate*</div>
							<div>
								<input type="text" class="input_full form-control" id="TAX_RATE" name="TAX_RATE" value="<?php echo set_value('TAX_RATE',$edit['TAX_RATE']); ?>" data-parsley-trigger="keyup" data-parsley-validation-threshold="0" data-parsley-type="number" required />
								<span class="fred"><?php echo form_error('TAX_RATE'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<div class="form_label">VAT Rate*</div>
							<div>
								<input type="text" class="input_full form-control" id="VAT_RATE" name="VAT_RATE" value="<?php echo set_value('VAT_RATE',$edit['VAT_RATE']); ?>" data-parsley-trigger="keyup" data-parsley-validation-threshold="0" data-parsley-type="number" required />
								<span class="fred"><?php echo form_error('VAT_RATE'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<div class="form_label">Address*</div>
							<div>
								<textarea class="form-control" name="ADDRESS" id="ADDRESS" required><?php echo $edit['ADDRESS']; ?></textarea>
								<span class="fred"><?php echo form_error('ADDRESS'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<div class="form_label">District</div>
							<div>
								<input type="text" class="input_full form-control" id="DISTRICT" name="DISTRICT" value="<?php echo set_value('DISTRICT',$edit['DISTRICT']); ?>">
								<span class="fred"><?php echo form_error('DISTRICT'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<div class="form_label">Thana/Upzilla</div>
							<div>
								<input type="text" class="input_full form-control" id="THANA_UPAZILLA" name="THANA_UPAZILLA" value="<?php echo set_value('THANA_UPAZILLA',$edit['THANA_UPAZILLA']); ?>">
								<span class="fred"><?php echo form_error('THANA_UPAZILLA'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<div class="form_label">Floor Space (SQFT)</div>
							<div>
								<input type="text" class="input_full form-control" id="FLOOR_SPACE" name="FLOOR_SPACE" value="<?php echo set_value('FLOOR_SPACE',$edit['FLOOR_SPACE']); ?>">
								<span class="fred"><?php echo form_error('FLOOR_SPACE'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<div class="form_label">Rent per SQFT</div>
							<div>
								<input type="text" class="input_full form-control" id="RENT_PER_SQFT" name="RENT_PER_SQFT" value="<?php echo set_value('RENT_PER_SQFT',$edit['RENT_PER_SQFT']); ?>" data-parsley-trigger="keyup" data-parsley-validation-threshold="0" data-parsley-type="number">
								<span class="fred"><?php echo form_error('RENT_PER_SQFT'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<div class="form_label">Agreement Date*</div>
							<div>
								<input type="text" class="input_full form-control date_picker" id="AGREEMENT_DATE" name="AGREEMENT_DATE" value="<?php echo set_value('AGREEMENT_DATE',$edit['AGREEMENT_DATE']); ?>" required>
								<span class="fred"><?php echo form_error('AGREEMENT_DATE'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<div class="form_label">Agreement Expiry Date*</div>
							<div>
								<input type="text" class="input_full form-control date_picker" id="AGREEMENT_EXPIRY" name="AGREEMENT_EXPIRY" value="<?php echo set_value('AGREEMENT_EXPIRY',$edit['AGREEMENT_EXPIRY']); ?>" required>
								<span class="fred"><?php echo form_error('AGREEMENT_EXPIRY'); ?></span>
							</div>
						</div>
						<div class="form-group">
							<div class="form_label">Agreement Document*</div>
							<div>
								<input type="file" class="input_full form-control" id="agreement_document" value="<?php echo set_value('agreement_document',$edit['AGREEMENT_DOCUMENT']); ?>" name="agreement_document" accept=".pdf,.doc,.docx">
								<?php 
								$file = $this->webspice->get_path('agreement').$edit['AGREEMENT_DOCUMENT'];
								if ($edit['AGREEMENT_DOCUMENT'] && file_exists($this->webspice->get_path('agreement_full').$edit['AGREEMENT_DOCUMENT'])) {
									echo '<a class="bi bi-paperclip btn btn-warning btn-sm p1" href="'. $file.'" target="_blank">' . $edit['AGREEMENT_DOCUMENT'] . '</a>';
								}
								?>
							</div>
						</div>
					</div>
					
					<div class="item_panel col-lg-6 col-md-6 col-sm-12" id="slab_area">
						<fieldset class="divider"><legend>Cost Center Area</legend></fieldset>
						<div class="form-group cost_center_area" id="cost_center_area">
							<table id="item_table">
								<thead>
									<tr>
										<td style="width:50%">Cost Center*</td>
										<td style="width:35%">Amount (%)*</td>
										<td>&nbsp;</td>
									</tr>
								</thead>
								<tbody>
						<?php
						if( isset($edit['ID']) && $edit['ID'] ):
							$cost_center_info = $this->db->query("
							SELECT CCD.*,TBL_OPTION.OPTION_VALUE AS COST_CENTER FROM tbl_cost_center_details CCD
							LEFT JOIN TBL_OPTION ON TBL_OPTION.OPTION_ID = CCD.COST_CENTER_ID
							WHERE CCD.LEASE_ID = ?",$edit['ID'])->result();
							if($cost_center_info):
							foreach($cost_center_info as $ck=>$cv):
						?>
						<tr class="tr_clone">
							<td style="width:50%">
								<select class="input_full form-control cost_center_id" name="cost_center_id[]" required>
									<option value="">Select One</option>
									<?php foreach($cost_centers as $k=>$v): 
										$selected = ($v->OPTION_ID==$cv->COST_CENTER_ID) ? 'selected="selected"' : '';
									?>
										<option value="<?php echo $v->OPTION_ID; ?>" <?php echo $selected; ?>><?php echo $v->OPTION_VALUE; ?></option>
									<?php endforeach; ?>
								</select>
							</td>
							<td style="width:35%"><input type="text" class="input_full form-control cost_center_amount" data-parsley-type="number" data-parsley-trigger="keyup" name="cost_center_amount[]" value="<?php echo $cv->PERCENTAGE; ?>" required></td>
							<td>
								<a href="javascript:void(0);" class="tr_clone_add" title="Add field">
									<span class="glyphicon glyphicon-plus"></span>
								</a>
								<a href="javascript:void(0);" class="tr_clone_remove" title="Remove field">
									<span style="color: #D63939;" class="glyphicon glyphicon-minus"></span>
								</a>
							</td> 
						</tr>
						<?php endforeach;endif; ?>
						<?php else: ?>
							<tr class="tr_clone">
								<td style="width:50%">
									<select class="input_full form-control cost_center_id" name="cost_center_id[]" required>
										<option value="">Select One</option>
										<?php foreach($cost_centers as $k=>$v): ?>
											<option value="<?php echo $v->OPTION_ID; ?>"><?php echo $v->OPTION_VALUE; ?></option>
										<?php endforeach; ?>
									</select>
								</td>
								<td style="width:35%"><input type="text" class="input_full form-control cost_center_amount" data-parsley-type="number" data-parsley-trigger="keyup" name="cost_center_amount[]" required></td>
								<td>
									<a href="javascript:void(0);" class="tr_clone_add" title="Add field">
										<span class="glyphicon glyphicon-plus"></span>
									</a>
									<a href="javascript:void(0);" class="tr_clone_remove" title="Remove field">
										<span style="color: #D63939;" class="glyphicon glyphicon-minus"></span>
									</a>
								</td> 
							</tr>
							<?php endif; ?>
								</tbody>
							</table>
						</div>
						<br />

						<fieldset class="divider"><legend>Rent Slab</legend></fieldset>
						<?php if(isset($edit['ID']) && $edit['ID']):
						$get_slab = $this->db->query("
						SELECT TBL_LEASE_AGREEMENT.* 
						FROM TBL_LEASE_AGREEMENT
						WHERE LEASE_ID = ?
						", 
						array($edit['ID'])
						)->result();
						
						if(count($get_slab)==0): ?>
						<div class="form-group pnl_slab pnl_rent" id="pnl_rent">
							<table>
								<tr>
									<td>From*</td>
									<td>To*</td>
									<td>Amount*</td>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td><input type="text" class="input_full form-control month_picker_x txt_from_rent" name="txt_from_rent[]" required></td>
									<td><input type="text" class="input_full form-control month_picker_x txt_to_rent" name="txt_to_rent[]" required></td>
									<td><input type="text" class="input_full form-control txt_amount_rent" data-parsley-type="number" data-parsley-trigger="keyup" name="txt_amount_rent[]" required></td>
									<td><a class="btn btn-sm btn-danger hide btn_pnl_remove" href="#">X</a></td>
								</tr>
							</table>
						</div>
						
						<?php elseif( isset($edit['ID']) && $edit['ID'] ): ?>
						<?php
						$advance_rcd_db = 0;
						foreach($get_slab as $k=>$v):
						if($v->TYPE=='advance'){$advance_rcd_db++; continue;}
						?>
						<div class="form-group pnl_slab pnl_rent <?php if($k===0){echo 'clone_slab_rent';} ?>" id="<?php if($k===0){echo 'pnl_rent';} ?>">
							<table>
								<tr>
									<td>From*</td>
									<td>To*</td>
									<td>Amount*</td>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td><input type="text" class="input_full form-control month_picker_x txt_from_rent" name="txt_from_rent[]" value="<?php echo $v->DATE_FROM; ?>" required></td>
									<td><input type="text" class="input_full form-control month_picker_x txt_to_rent" name="txt_to_rent[]" value="<?php echo $v->DATE_TO; ?>" required></td>
									<td><input type="text" class="input_full form-control txt_amount_rent" name="txt_amount_rent[]" data-parsley-type="number" data-parsley-trigger="keyup" value="<?php echo $v->AMOUNT; ?>" required></td>
									<td><a class="btn btn-sm btn-danger <?php if($k===0){echo 'hide';} ?> btn_pnl_remove" href="#">X</a></td>
								</tr>
							</table>
						</div>
						<?php endforeach;endif; ?>
						<?php else: ?>
						<div class="form-group pnl_slab pnl_rent" id="pnl_rent">
							<table>
								<tr>
									<td>From*</td>
									<td>To*</td>
									<td>Amount*</td>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td><input type="text" class="input_full form-control month_picker_x txt_from_rent" name="txt_from_rent[]" required></td>
									<td><input type="text" class="input_full form-control month_picker_x txt_to_rent" name="txt_to_rent[]" required></td>
									<td><input type="text" class="input_full form-control txt_amount_rent" data-parsley-type="number" data-parsley-trigger="keyup" name="txt_amount_rent[]" required></td>
									<td><a class="btn btn-sm btn-danger hide btn_pnl_remove" href="#">X</a></td>
								</tr>
							</table>
						</div>
						<?php endif; ?>
						
						<a class="btn btn-sm btn-info btn_add_rent_slab" href="#">Add New Slab</a>
						<br /><br />
						
						<fieldset class="divider"><legend>Advance Payment Slab</legend></fieldset>
						<?php if( isset($edit['ID']) && $edit['ID'] ): 
						if(count($get_slab)==0 OR $advance_rcd_db==0): 
						?>
						<div class="form-group pnl_slab" id="pnl_advance">
							<table>
								<tr>
									<td>From</td>
									<td>To</td>
									<td>Amount</td>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td><input type="text" class="input_full form-control month_picker_x txt_from_advance" name="txt_from_advance[]"></td>
									<td><input type="text" class="input_full form-control month_picker_x txt_to_advance" name="txt_to_advance[]"></td>
									<td><input type="text" class="input_full form-control txt_amount_advance" name="txt_amount_advance[]" data-parsley-type="number" data-parsley-trigger="keyup"></td>
									<td><a class="btn btn-sm btn-danger hide btn_pnl_remove">X</a></td>
								</tr>
							</table>
						</div>
						<?php
						elseif( isset($edit['ID']) && $edit['ID'] ):
						foreach($get_slab as $k=>$v):
						if($v->TYPE=='rent'){continue;}						
						?>
						<div class="form-group pnl_slab <?php if($k===0){echo 'clone_slab_advance';} ?>" id="<?php if($k===0){echo 'pnl_advance';} ?>">
							<table>
								<tr>
									<td>From</td>
									<td>To</td>
									<td>Amount</td>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td><input type="text" class="input_full form-control month_picker_x txt_from_advance" name="txt_from_advance[]" value="<?php echo $v->DATE_FROM; ?>"></td>
									<td><input type="text" class="input_full form-control month_picker_x txt_to_advance" name="txt_to_advance[]" value="<?php echo $v->DATE_TO; ?>"></td>
									<td><input type="text" class="input_full form-control txt_amount_advance" name="txt_amount_advance[]" value="<?php echo $v->AMOUNT; ?>" ></td>
									<td><a class="btn btn-sm btn-danger <?php if($k===0){echo 'hide';} ?> btn_pnl_remove" href="#">X</a></td>
								</tr>
							</table>
						</div>						
						<?php endforeach;endif; ?>
						<?php else: ?>
						<div class="form-group pnl_slab" id="pnl_advance">
							<table>
								<tr>
									<td>From</td>
									<td>To</td>
									<td>Amount</td>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td><input type="text" class="input_full form-control month_picker_x txt_from_advance" name="txt_from_advance[]"></td>
									<td><input type="text" class="input_full form-control month_picker_x txt_to_advance" name="txt_to_advance[]"></td>
									<td><input type="text" class="input_full form-control txt_amount_advance" name="txt_amount_advance[]"></td>
									<td><a class="btn btn-sm btn-danger hide btn_pnl_remove">X</a></td>
								</tr>
							</table>
						</div>
						<?php endif; ?>
						
						<a class="btn btn-sm btn-info btn_add_advance_slab" href="#">Add New Slab</a>
						<div class="p_error_container">&nbsp;</div>
						
						<br />
						<input type="submit" class="btn btn-danger btn-lg btn_submit" value="Submit Data">
						<div class="spinner">&nbsp;</div>
					</div><!--end #slab_area-->
					
				</div><!--end .row-->
				</form>
				
			</div>

		</div>
		
		<?php if( $this->uri->segment(2) != "edit" ): ?>
		<div id="footer_container"><?php include(APPPATH."views/footer.php"); ?></div>
		<?php endif; ?>
		
	</div>
	
	<script type="text/javascript">
		$(document).ready(function(){
			var key_value = $('#key').val();
			$(document).on('focusout',".cost_center_amount",function(){
				cost_center_validation_check(true);
			});
			$(document).on('click', ".tr_clone_add", function() {
				var $tr  = $(this).closest('.tr_clone');
				var newClass='newClass';
				var $clone = $tr.clone().addClass(newClass);
				$clone.find('input').val('');
				$clone.find('select option:selected').prop('selected',false);
				$tr.after($clone);
				$(".date_picker").datetimepicker({timepicker:false,format:'Y-m-d'});
			});
			
			$(document).on('click', ".tr_clone_remove", function() { //Once remove button is clicked
				var rowCount = $('#item_table tr').length;
				if(rowCount > 2){
					$(this).parent().parent().remove(); //Remove field html
				}
			});

			$('.btn_add_rent_slab').click(function(){
				var new_pnl = $('#pnl_rent').clone();
				new_pnl.removeAttr('id');
				new_pnl.addClass('clone_slab_rent');
				new_pnl.find('.btn_pnl_remove').removeClass('hide');
				new_pnl.find('select').remove(); // remove date picker
				new_pnl.find('input').val("");
				new_pnl.find('.month_picker_x')
				.removeClass('hasDatepicker')
				.removeData('datepicker')
				.unbind()
				.datetimepicker('destroy')
				.datetimepicker({closeOnDateSelect:true, timepicker:false, format:'Y-m', scrollMonth : false, scrollInput : false});
				new_pnl.insertBefore(this);
				
				return false;
			});
			
			$('.btn_add_advance_slab').click(function(){
				var new_pnl = $('#pnl_advance').clone();
				new_pnl.removeAttr('id');
				new_pnl.addClass('clone_slab_advance');
				new_pnl.find('.btn_pnl_remove').removeClass('hide');
				new_pnl.find('select,ul').remove(); // remove date picker
				new_pnl.find('input').val("").removeAttr('data-parsley-id');
				new_pnl.find('.month_picker_x')
				.removeClass('hasDatepicker')
				.removeData('datepicker')
				.unbind()
				.datetimepicker('destroy')
				.datetimepicker({closeOnDateSelect:true, timepicker:false, format:'Y-m', scrollMonth : false, scrollInput : false});
				new_pnl.insertBefore(this);
				
				return false;
			});
			
			$('body').delegate('.btn_pnl_remove', 'click', function (evt) {
				var me = $(this);
				me.closest('.pnl_slab').remove();
				return false;
			});


		});

		$(document).on('submit',"#frm_lease_onboarding",function(e){
			var key_value = $('#key').val();
			e.preventDefault();
			var cost_center_validation_return = cost_center_validation_check(false);
			var is_error = false;
			var temp_from_rent = false;
			var temp_to_rent = false;
			var temp_from_advance = false;
			var temp_to_advance = false;
			var agreement_document = $('#agreement_document').get(0).files.length;

			$('.pnl_rent input').each(function(){
				if( !$(this).val() ){
					alert('You must fill-up all slab information.');
					is_error = true;
					return false;
				}
				
				if( $(this).hasClass("txt_from_rent") ){
					var tmp_from = $(this).val();
					var tmp_to = $(this).parent().parent().find('.txt_to_rent').val();

					if( temp_from_rent && tmp_from <= temp_to_rent ){
						alert("From date must be greater than previous slab's (Rent) To date.");
						is_error = true;
						return false;
					}
					
					temp_from_rent = tmp_from;
					temp_to_rent = tmp_to;
					
					if( tmp_from > tmp_to ){
						alert("To date must be greater than From date in a slab (Rent).");
						is_error = true;
						return false;
					}
					
				}else if( $(this).hasClass("txt_from_advance") ){
					var tmp_from = $(this).val();
					var tmp_to = $(this).parent().parent().find('.txt_to_advance').val();
					
					if( temp_from_advance && tmp_from <= temp_to_advance ){
						alert("From date must be greater than previous slab's (Advance) To date.");
						is_error = true;
						return false;
					}
					
					temp_from_advance = tmp_from;
					temp_to_advance = tmp_to;
					
					if( tmp_from > tmp_to ){
						alert("To date must be greater than From date in a slab (Advance).");
						is_error = true;
						return false;
					}
				}
				
			});

			$('.pnl_slab input').each(function(){
				/*if( !$(this).val() ){
					alert('You must fill-up all slab information.');
					is_error = true;
					return false;
				}*/
				
				if( $(this).hasClass("txt_from_advance") ){
					var tmp_from = $(this).val();
					var tmp_to = $(this).parent().parent().find('.txt_to_advance').val();
					
					if( temp_from_advance && tmp_from <= temp_to_advance ){
						alert("From date must be greater than previous slab's (Advance) To date.");
						is_error = true;
						return false;
					}
					
					temp_from_advance = tmp_from;
					temp_to_advance = tmp_to;
					
					if( tmp_from > tmp_to ){
						alert("To date must be greater than From date in a slab (Advance).");
						is_error = true;
						return false;
					}
				}
			});

			if(!cost_center_validation_return){
				return false;
			}

			if(!is_error){
				if(!agreement_document && !key_value){
					alert('Please attach Agreement Document!');
					return false;
				}	
			}
			var me = $(this);
			var myFormData = new FormData($(this)[0]);
			//myFormData.append('EmployeeID', UserID);
			var data = myFormData;

			var route = $(this).attr('form-route');
			if (!data || !route) {
				Swal.fire({
					icon: 'error',
					title: "ERROR!",
					text: 'Invalid Request!',
					type: "error"
				}).then((result) => {
					return false;
				});
			}

			if (!$(this).parsley().validate()) {
				//alert('Parsley Error!');
				return false;
			}

			var me = $(this);
			var token = $('#token').val();
			var spinner = me.find('.spinner');
			spinner.show();

			var jqxhr = $.ajax({
				type: "POST",
				url: url_prefix + route,
				cache: false,
				processData: false, // important
				contentType: false, // important
				data: data
			}).done(function (msg) {
				//example - update_success, update_success:manage_author -- after : the value will be treated as route
				var identifier = '';
				var param = msg.trim().split(":");
				if (param[0]) { identifier = param[0]; }

				switch (identifier.trim()) {
					case 'error':
						Swal.fire({
							icon: 'warning',
							title: "SORRY!",
							text: "System could not execute your request! Please report to Admin.",
							type: "warning"
						}).then((result) => {
							spinner.hide();
						});
						break;

					case 'denied':
						Swal.fire({
							icon: 'warning',
							title: "SORRY!",
							text: "Unauthorized Access!",
							type: "warning"
						}).then((result) => {
							spinner.hide();
						});
						break;

					case 'success':
						Swal.fire({
							icon: 'success',
							title: "Success!",
							text: "Your request has been executed successfully!",
							type: "success"
						}).then((result) => {
							if (param[1]) { location.href = url_prefix + param[1]; return false; }
							location.href = location.href;
						});
						break;

					case 'update_success':
						Swal.fire({
							icon: 'success',
							title: "Updated!",
							text: "Your record has been updated successfully!",
							type: "success"
						}).then((result) => {
							if (param[1]) { location.href = url_prefix + param[1]; return false; }
							location.href = location.href;
						});
						break;

					case 'insert_success':
						Swal.fire({
							icon: 'success',
							title: "Saved!",
							text: 'New record has been created successfully!',
							type: "success"
						}).then((result) => {
							// Reload the Page
							if (param[1]) { location.href = url_prefix + param[1]; return false; }
							location.href = location.href;
						});
						break;

					case 'request':
						if (param[1]) { location.href = url_prefix + param[1]; return false; }
						location.href = location.href;
						break;

					default:
						Swal.fire({
							icon: 'info',
							title: "",
							text: msg,
							type: "info"
						}).then((result) => {
							spinner.hide();
						});
						break;
				}
			}).fail(function () {
				Swal.fire({
					icon: 'error',
					title: "SORRY!",
					text: "We could not execute your request. Please try again later or report to authority.",
					type: "error"
				}).then((result) => {
					spinner.hide();
				});

			});

			return false;
			
		});

		function cost_center_validation_check(return_type){
			var total_cost_center_amount = cost_center_amount = 0;
			var cost_center_selected_values = [];
			var duplicate_validation = false;
			$(".cost_center_amount").each(function(){
				cost_center_amount = Number($(this).val());
				total_cost_center_amount += cost_center_amount;
				var cost_center_id = $(this).parent().parent().find(".cost_center_id").val();
				if(!cost_center_amount || cost_center_amount==0 || !cost_center_id){
					return;
				}
				if(cost_center_id){
					if($.inArray($(this).parent().parent().find(".cost_center_id").val(), Object.values(cost_center_selected_values)) !== -1 ){
						duplicate_validation = true;
					}
					cost_center_selected_values.push($(this).parent().parent().find(".cost_center_id").val());
				}
			});
			cost_center_selected_values = [];
			if(duplicate_validation){
				alert("Duplicate cost center cannot be acceptable!");
				return return_type;
			}
			
			if(total_cost_center_amount > 0 && total_cost_center_amount > 100){
				alert("Cost Center Total Amount(%) must be 100.");
				return return_type;
			}else if(total_cost_center_amount > 0 && total_cost_center_amount != 100){
				var need_more = 100-total_cost_center_amount;
				alert("Cost Center Total Amount(%) must be 100. Need more "+need_more+" (%)");
				return return_type;
			}
			return true;
		}
	</script>
</body>
</html>