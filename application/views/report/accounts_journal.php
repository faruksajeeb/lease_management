<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $this->webspice->settings()->domain_name; ?>: Journal</title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	
	<?php include(APPPATH."views/global.php"); ?>
</head>

<body>
	<div id="wrapper">
		<div id="header_container"><?php include(APPPATH."views/header.php"); ?></div>
		
		<div id="page_accounts_journal" class="container-fluid page_identifier">
			<div class="page_caption">Journal</div>
			<div class="page_body table-responsive">
				<div class="row">
					<div class="col-lg-8 col-md-8">
						<!--filter section-->
						<form id="frm_filter" method="get" action="" data-parsley-validate>
							<input id="token" type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
							
							<table style="width:auto; width:100%;">
								<tr>
									<td>Year*</td>
									<td>Month</td>
									<td>Branch</td>
								</tr>
								<tr>
									<td>
										<select name="drp_year" id="drp_year" class="form-control" required>
											<option value="">Select Year</option>
											<option value="consolidate" <?php if($this->input->get('drp_year')=='consolidate'){echo 'selected="selected"';} ?>>Consolidate</option>
											<?php for($i=2015; $i<=2030; $i++): ?>
											<option value="<?php echo $i; ?>" <?php if($this->input->get('drp_year')==$i){echo 'selected="selected"';} ?>><?php echo $i; ?></option>
											<?php endfor; ?>
										</select>	
									</td>
									<td>
										<select name="drp_month" id="drp_month" class="form-control">
											<option value="">Select Month</option>
											<?php for($i=1; $i<=12; $i++): ?>
											<option value="<?php echo $i < 10 ? '0'.$i : $i; ?>" <?php if($this->input->get('drp_month')==$i){echo 'selected="selected"';} ?>><?php echo $i < 10 ? '0'.$i : $i; ?></option>
											<?php endfor; ?>
										</select>
									</td>
									<td>
										<?php if( $this->webspice->get_user('USER_TYPE') != 'branch_user' ): ?>
										<select name="drp_branch[]" id="drp_branch" class="form-control chosen" style="width:100%;" multiple>
											<option value="">Select Branch</option>
											<?php echo str_replace('value="'.$this->input->get('drp_branch').'"','value="'.$this->input->get('drp_branch').'" selected="selected"', $this->customcache->get_user_branch()); ?>
										</select>
										<?php else: ?>
										<strong><?php echo $this->customcache->option_maker($this->webspice->get_user('BRANCH_ID'), 'OPTION_VALUE'); ?></strong>
										<?php endif; ?>
									</td>
								</tr>
								<tr>
									<td colspan="10">
										<input type="submit" name="filter" class="btn btn-info" value="Filter Data" />
										<a class="btn btn-default" href="<?php echo $url_prefix; ?>accounts_journal">Refresh</a>
										<a class="btn btn-default" href="<?php echo $url_prefix; ?>accounts_journal/print" target="_blank">Print</a>
										<a class="btn btn-default" href="<?php echo $url_prefix; ?>accounts_journal/csv" target="_blank">Export</a>
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
									<li>Please filter for your report</li>
								</ul>
							</div>
						</div>
					</div>

				</div> <!-- end of the row -->
				
				<br />
				<?php if( !isset($filter_by) || !$filter_by ){$filter_by = '';} ?>
				<div class="breadcrumb">Journal For &raquo; <?php echo $filter_by; ?></div>
				
				<div id="data_table" style="overflow:auto; max-width:600px;">
					<table class="table table-bordered table-striped">
						<tr>
							<th>Journal</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
						</tr>
						<?php if($get_record && $get_record_2): ?>
						<tr>
							<td class="text-center" style="padding:3px !important;">1</td>
							<td>Right of use assets (ROU) as per IFRS 16</td>
							<td class="text-center" style="padding:3px !important;">Dr.</td>
							<td class="text-right"><?php echo $this->webspice->tk(round($get_record->TOTAL_PRESENT_VALUE,2)); ?></td>
						</tr>
						<tr>
							<td class="text-center">&nbsp;</td>
							<td>Leased liabilities as per IFRS 16</td>
							<td class="text-center" style="padding:3px !important;">Cr.</td>
							<td class="text-right"><?php echo $this->webspice->tk(round($get_record->TOTAL_PRESENT_VALUE, 2)); ?></td>
						</tr>
						<tr><td colspan="4">&nbsp;</td></tr>
						<tr>
							<td class="text-center" style="padding:3px !important;">2</td>
							<td>Right of use assets (ROU) as per IFRS 16</td>
							<td class="text-center" style="padding:3px !important;">Dr.</td>
							<td class="text-right"><?php echo $this->webspice->tk(round($get_record->TOTAL_ADVANCE, 2)); ?></td>
						</tr>
						<tr>
							<td class="text-center">&nbsp;</td>
							<td>Advance Rent</td>
							<td class="text-center" style="padding:3px !important;">Cr.</td>
							<td class="text-right"><?php echo $this->webspice->tk(round($get_record->TOTAL_ADVANCE, 2)); ?></td>
						</tr>
						<tr><td colspan="4">&nbsp;</td></tr>
						<tr>
							<td class="text-center" style="padding:3px !important;">3</td>
							<td>Leased liabilities as per IFRS 16</td>
							<td class="text-center" style="padding:3px !important;">Dr.</td>
							<td class="text-right"><?php echo $this->webspice->tk(round($get_record_2->TOTAL_LEASE_PAYMENT, 2)); ?></td>
						</tr>
						<tr>
							<td class="text-center" style="padding:3px !important;">&nbsp;</td>
							<td>Advance Rent</td>
							<td class="text-center" style="padding:3px !important;">Dr.</td>
							<td class="text-right"><?php echo $this->webspice->tk(round($get_record->TOTAL_ADVANCE, 2)); ?></td>
						</tr>
						<tr>
							<td class="text-center">&nbsp;</td>
							<td>Rent Expense</td>
							<td class="text-center" style="padding:3px !important;">Cr.</td>
							<td class="text-right"><?php echo $this->webspice->tk(round($get_record->TOTAL_RENT, 2)); ?></td>
						</tr>
						<tr><td colspan="4">&nbsp;</td></tr>
						<tr>
							<td class="text-center" style="padding:3px !important;">4</td>
							<td>Depreciation (ROU)</td>
							<td class="text-center" style="padding:3px !important;">Dr.</td>
							<td class="text-right"><?php echo $this->webspice->tk(round($get_record_2->TOTAL_DEPRECIATION, 2)); ?></td>
						</tr>
						<tr>
							<td class="text-center">&nbsp;</td>
							<td>Accumulated depreciation (ROU)</td>
							<td class="text-center" style="padding:3px !important;">Cr.</td>
							<td class="text-right"><?php echo $this->webspice->tk(round($get_record_2->TOTAL_DEPRECIATION, 2)); ?></td>
						</tr>
						<tr><td colspan="4">&nbsp;</td></tr>
						<tr>
							<td class="text-center" style="padding:3px !important;">5</td>
							<td>Interest expense for leased liability as per IFRS 16</td>
							<td class="text-center" style="padding:3px !important;">Dr.</td>
							<td class="text-right"><?php echo $this->webspice->tk(round($get_record_2->TOTAL_INTEREST, 2)); ?></td>
						</tr>
						<tr>
							<td class="text-center">&nbsp;</td>
							<td>Leased liabilities as per IFRS 16</td>
							<td class="text-center" style="padding:3px !important;">Cr.</td>
							<td class="text-right"><?php echo $this->webspice->tk(round($get_record_2->TOTAL_INTEREST, 2)); ?></td>
						</tr>
						<?php endif; ?>
					</table>
				</div>
				
				<div id="pagination"><?php echo $pager; ?><div class="float_clear_full">&nbsp;</div></div>
				
			</div><!--end .page_body-->

		</div>
		
		<div id="footer_container"><?php include(APPPATH."views/footer.php"); ?></div>
	</div>
</body>
</html>