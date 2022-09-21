<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $this->webspice->settings()->domain_name; ?>: Consolidate Summary</title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	
	<?php include(APPPATH."views/global.php"); ?>
</head>

<body>
	<div id="wrapper">
		<div id="header_container"><?php include(APPPATH."views/header.php"); ?></div>
		
		<div id="page_manage_lease_onboarding" class="container-fluid page_identifier">
			<div class="page_caption">Consolidate Summary</div>
			<div class="page_body table-responsive">
				<div class="breadcrumb">Date: <?php echo date("d F, Y"); ?></div>
				
				<div id="data_table" style="overflow:auto;">
					<table class="table table-bordered table-striped">
						<tr>
							<th class="text-center" style="padding:3px !important;">SL</th>
							<th>Branch</th>
							<th class="text-center" style="padding:3px !important;">Tenor</th>
							<th class="text-right" style="padding:3px !important;">ROU</th>
							<th class="text-right" style="padding:3px !important;">Rent</th>
							<th class="text-right" style="padding:3px !important;">Advance</th>
							<th class="text-right" style="padding:3px !important;">Present Value</th>
							<th class="text-right" style="padding:3px !important;">Lease Payment</th>
							<th class="text-right" style="padding:3px !important;">Lease Interest</th>
							<th class="text-right" style="padding:3px !important;">Depreciation</th>
						</tr>
						<?php if($get_record && $get_record_2 && count($get_record)==count($get_record_2)): ?>
						<?php foreach($get_record as $k=>$v): ?>
						<tr>
							<td class="text-center" style="padding:3px !important;"><?php echo $k+1; ?></td>
							<td><?php echo $this->customcache->option_maker($v->BRANCH_ID, 'OPTION_VALUE'); ?></td>
							<td class="text-center" style="padding:3px !important;"><?php echo $v->STARTING_MONTH.' to '.$v->ENDING_MONTH; ?></td>
							<td class="text-right" style="padding:3px !important;"><?php echo $this->webspice->tk(round($v->TOTAL_PRESENT_VALUE + $v->TOTAL_ADVANCE,2)); ?></td>
							<td class="text-right" style="padding:3px !important;"><?php echo $this->webspice->tk(round($v->TOTAL_RENT,2)); ?></td>
							<td class="text-right" style="padding:3px !important;"><?php echo $this->webspice->tk(round($v->TOTAL_ADVANCE,2)); ?></td>
							<td class="text-right" style="padding:3px !important;"><?php echo $this->webspice->tk(round($v->TOTAL_PRESENT_VALUE,2)); ?></td>
							<td class="text-right" style="padding:3px !important;"><?php echo $this->webspice->tk(round($get_record_2[$k]->TOTAL_LEASE_PAYMENT,2)); ?></td>
							<td class="text-right" style="padding:3px !important;"><?php echo $this->webspice->tk(round($get_record_2[$k]->TOTAL_INTEREST,2)); ?></td>
							<td class="text-right" style="padding:3px !important;"><?php echo $this->webspice->tk(round($get_record_2[$k]->TOTAL_DEPRECIATION,2)); ?></td>
						</tr>
						<?php endforeach; ?>
						<?php endif; ?>
					</table>
				</div>
				<a class="btn btn-default" href="<?php echo $url_prefix; ?>consolidate_summary/print" target="_blank">Print</a>
				<a class="btn btn-default" href="<?php echo $url_prefix; ?>consolidate_summary/csv" target="_blank">Export</a>
				
			</div><!--end .page_body-->

		</div>
		
		<div id="footer_container"><?php include(APPPATH."views/footer.php"); ?></div>
	</div>
</body>
</html>