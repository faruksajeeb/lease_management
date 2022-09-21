<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $this->webspice->settings()->domain_name; ?>: Region Wise Lease Information</title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	
	<?php include(APPPATH."views/global.php"); ?>
</head>

<body>
	<div id="wrapper">
		<div id="header_container"><?php include(APPPATH."views/header.php"); ?></div>
		
		<div id="page_manage_lease_onboarding" class="container-fluid page_identifier">
			<div class="page_caption">Region Wise Lease Information</div>
			<div class="page_body table-responsive">
				<a class="btn btn-default" href="<?php echo $url_prefix; ?>region_wise_lease_information/print" target="_blank">Print</a>
				<a class="btn btn-default" href="<?php echo $url_prefix; ?>region_wise_lease_information/csv" target="_blank">Export</a>				
				<div id="data_table" style="overflow:auto;">
					<table class="table table-bordered table-striped">
						<tr>
							<th rowspan="2" class="text-center">Sl. No.</th>
							<th rowspan="2">Branch Name</th>
							<th rowspan="2">License No. & Issuing Date</th>
							<th rowspan="2">Branch Opening Date</th>
							<th colspan="4" class="text-center">Full Address</th>
							<th rowspan="2">District</th>
							<th rowspan="2">Floor Space (SQFT)</th>
							<th rowspan="2">Rent per SQFT</th>
							<th colspan="2" class="text-center">Loan & Advances</th>
							<th colspan="2" class="text-center">Deposit</th>
							<th rowspan="2">Profit/Loss (In Crore)</th>
						</tr>
						<tr>
							<th>Type (Urban/Rural)</th>
							<th>Address</th>
							<th>Name of the City Corporation/Pouoshova/union)</th>
							<th>Thana/ Upazila</th>
							<th>Total Amount (In Crore)</th>
							<th>No. of Customers</th>
							<th>Total Amount (In Crore)</th>
							<th>No. of Customers</th>
						</tr>
						<?php $temp_region = null; ?>
						<?php foreach($get_record as $k=>$v): ?>
						<?php
							if($temp_region != $v->REGION_NAME){
								$temp_region = $v->REGION_NAME;
								echo '<tr><td colspan="16"><strong>'.$v->REGION_NAME.'</strong></td></tr>';
							}
						?>
						<tr>
							<td class="text-center">
								<?php echo $k+1; ?><br />
							</td>
							<td><?php echo $this->customcache->option_maker($v->BRANCH_ID, 'OPTION_VALUE'); ?></td>
							<td><?php echo $v->LICENSE_NO.', '.$v->LICENSE_ISSUE_DATE; ?></td>
							<td><?php echo $v->BRANCH_OPENING_DATE; ?></td>
							<td><?php echo ucwords($v->TYPE); ?></td>
							<td><?php echo $v->ADDRESS; ?></td>
							<td><?php echo $v->CITY; ?></td>
							<td><?php echo $v->THANA_UPAZILLA; ?></td>
							<td><?php echo $v->DISTRICT; ?></td>
							<td><?php echo $v->FLOOR_SPACE; ?></td>
							<td><?php echo $v->RENT_PER_SQFT; ?></td>
							<td><?php echo $v->TOTAL_AMOUINT_LOAN; ?></td>
							<td><?php echo $v->NO_OF_CUSTOMER_LOAN; ?></td>
							<td><?php echo $v->TOTAL_AMOUNT_DEPOSIT; ?></td>
							<td><?php echo $v->NO_OF_CUSTOMER_DEPOSIT; ?></td>
							<td><?php echo $v->PROFIT_LOSS; ?></td>
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