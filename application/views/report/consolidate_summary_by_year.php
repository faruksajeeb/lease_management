<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $this->webspice->settings()->domain_name; ?>: Consolidate Summary By Year</title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	
	<?php include(APPPATH."views/global.php"); ?>
</head>

<body>
	<div id="wrapper">
		<div id="header_container"><?php include(APPPATH."views/header.php"); ?></div>
		
		<div id="page_manage_lease_onboarding" class="container-fluid page_identifier">
			<div class="page_caption">Consolidate Summary by Year</div>
			<div class="page_body table-responsive">
				<div class="breadcrumb">Date: <?php echo date("d F, Y"); ?></div>
				
				<!--prepare data-->
				<?php
				$smallest_year = 2040;
				$largest_year = 0;
				$dataset = array();
				foreach($get_record as $k=>$v){
					$dataset[$v->BRANCH_ID][$v->YEAR]['TOTAL_RENT'] = $v->TOTAL_RENT;
					$dataset[$v->BRANCH_ID][$v->YEAR]['TOTAL_ADVANCE'] = $v->TOTAL_ADVANCE;
					$dataset[$v->BRANCH_ID][$v->YEAR]['TOTAL_PRESENT_VALUE'] = $v->TOTAL_PRESENT_VALUE;
					$dataset[$v->BRANCH_ID][$v->YEAR]['STARTING_MONTH'] = $v->STARTING_MONTH;
					$dataset[$v->BRANCH_ID][$v->YEAR]['ENDING_MONTH'] = $v->ENDING_MONTH;
					
					if( $smallest_year > $v->YEAR ){ $smallest_year = $v->YEAR; }
					if( $largest_year < $v->YEAR ){ $largest_year = $v->YEAR; }
				}
				
				foreach($get_record_2 as $k=>$v){
					$dataset[$v->BRANCH_ID][$v->YEAR]['TOTAL_LEASE_PAYMENT'] = $v->TOTAL_LEASE_PAYMENT;
					$dataset[$v->BRANCH_ID][$v->YEAR]['TOTAL_INTEREST'] = $v->TOTAL_INTEREST;
					$dataset[$v->BRANCH_ID][$v->YEAR]['TOTAL_DEPRECIATION'] = $v->TOTAL_DEPRECIATION;
					
					if( $smallest_year > $v->YEAR ){ $smallest_year = $v->YEAR; }
					if( $largest_year < $v->YEAR ){ $largest_year = $v->YEAR; }
				}
				
				$year_difference = ($largest_year - $smallest_year) + 1; # including start year
				?>
				
				<div id="data_table" style="overflow:auto;">
					<table class="table table-bordered table-striped">
						<tr>
							<th class="text-center" style="padding:3px !important;" rowspan="2">SL</th>
							<th rowspan="2">Branch</th>
							<th class="text-center" style="padding:3px !important;" rowspan="2">Tenor</th>
							<th rowspan="2">Particular</th>
							<th colspan="<?php echo $year_difference; ?>">Lease Year</th>
							<th class="text-center" style="padding:3px !important;" rowspan="2"><strong>Consolidate</strong></th>
						</tr>
						<tr>
							<?php
							for($i=$smallest_year; $i<=$largest_year; $i++){
								echo '<th class="text-center" style="padding:3px !important;">'.$i.'</th>';
							}
							?>
						</tr>
						<?php if($get_record && $get_record_2 && count($get_record)==count($get_record_2)): ?>
						<?php $sl = 0; foreach($dataset as $k=>$v): ?>
						<?php
						$keys = array_keys($v);
						$tenor_start = $keys[0];
						$tenot_end = end($keys);
						?>
						<tr>
							<td class="text-center" style="padding:3px !important;"><?php echo $sl = $sl+1; ?></td>
							<td><?php echo $this->customcache->option_maker($k, 'OPTION_VALUE'); ?></td>
							<td class="text-center" style="padding:3px !important;"><?php echo $this->webspice->formatted_date($v[$tenor_start]['STARTING_MONTH'],'F Y').' -<br /> '.$this->webspice->formatted_date($v[$tenot_end]['ENDING_MONTH'],'F Y'); ?></td>
							<td class="text-right" style="padding:3px !important;">
								<table class="table table-bordered" style="margin-bottom:3px;">
									<tr><td style="padding:3px !important;">ROU</td>
									<tr><td style="padding:3px !important;">Rent</td>
									<tr><td style="padding:3px !important;">Advance</td>
									<tr><td style="padding:3px !important;">Present Value</td>
									<tr><td style="padding:3px !important;">Lease Payment</td>
									<tr><td style="padding:3px !important;">Interest</td>
									<tr><td style="padding:3px !important;">Depreciation</td>
								</table>
							</td>
							<?php
							$c_rou = $c_rent = $c_advance = $c_present_value = $c_lease_payent = $c_interest = $c_depreciation = 0;
							$col_count = 0;
							foreach($v as $k1=>$v1){
								$col_count++;
								$c_rou += ($v1['TOTAL_PRESENT_VALUE'] + $v1['TOTAL_ADVANCE']);
								$c_rent += $v1['TOTAL_RENT'];
								$c_advance += $v1['TOTAL_ADVANCE'];
								$c_present_value += $v1['TOTAL_PRESENT_VALUE'];
								$c_lease_payent += $v1['TOTAL_LEASE_PAYMENT'];
								$c_interest += $v1['TOTAL_INTEREST'];
								$c_depreciation += $v1['TOTAL_DEPRECIATION'];					
								echo '<td class="text-right" style="padding:3px !important;">';
								echo '<table class="table table-bordered" style="margin-bottom:3px;">';
								echo '<tr><td class="text-right" style="padding:3px !important;">'.$this->webspice->tk(round($v1['TOTAL_PRESENT_VALUE'] + $v1['TOTAL_ADVANCE'],2)).'</td></tr>';
								echo '<tr><td class="text-right" style="padding:3px !important;">'.$this->webspice->tk(round($v1['TOTAL_RENT'],2)).'</td></tr>';
								echo '<tr><td class="text-right" style="padding:3px !important;">'.$this->webspice->tk(round($v1['TOTAL_ADVANCE'],2)).'</td></tr>';
								echo '<tr><td class="text-right" style="padding:3px !important;">'.$this->webspice->tk(round($v1['TOTAL_PRESENT_VALUE'],2)).'</td></tr>';
								echo '<tr><td class="text-right" style="padding:3px !important;">'.$this->webspice->tk(round($v1['TOTAL_LEASE_PAYMENT'],2)).'</td></tr>';
								echo '<tr><td class="text-right" style="padding:3px !important;">'.$this->webspice->tk(round($v1['TOTAL_INTEREST'],2)).'</td></tr>';
								echo '<tr><td class="text-right" style="padding:3px !important;">'.$this->webspice->tk(round($v1['TOTAL_DEPRECIATION'],2)).'</td></tr>';
								echo '</table>';
								echo '</td>';
							}
							
							if($col_count < $year_difference){
								$tmp_add_col = $year_difference - $col_count;
								for($j=1; $j<=$tmp_add_col; $j++){
									echo '<td>&nbsp;</td>';
								}
							}
							
							echo '<td class="text-right" style="padding:3px !important;">';
							echo '<table class="table table-bordered" style="margin-bottom:3px;">';
							echo '<tr><td class="text-right" style="padding:3px !important;">'.$this->webspice->tk(round($c_rou,2)).'</td></tr>';
							echo '<tr><td class="text-right" style="padding:3px !important;">'.$this->webspice->tk(round($c_rent,2)).'</td></tr>';
							echo '<tr><td class="text-right" style="padding:3px !important;">'.$this->webspice->tk(round($c_advance,2)).'</td></tr>';
							echo '<tr><td class="text-right" style="padding:3px !important;">'.$this->webspice->tk(round($c_present_value,2)).'</td></tr>';
							echo '<tr><td class="text-right" style="padding:3px !important;">'.$this->webspice->tk(round($c_lease_payent,2)).'</td></tr>';
							echo '<tr><td class="text-right" style="padding:3px !important;">'.$this->webspice->tk(round($c_interest,2)).'</td></tr>';
							echo '<tr><td class="text-right" style="padding:3px !important;">'.$this->webspice->tk(round($c_depreciation,2)).'</td></tr>';
							echo '</table>';
							echo '</td>';
							?>
						</tr>
						<?php endforeach; ?>
						<?php endif; ?>
					</table>
				</div>
				<a class="btn btn-default" href="<?php echo $url_prefix; ?>consolidate_summary_by_year/print" target="_blank">Print</a>
				<a class="btn btn-default" href="<?php echo $url_prefix; ?>consolidate_summary_by_year/csv" target="_blank">Export</a>
				
			</div><!--end .page_body-->

		</div>
		
		<div id="footer_container"><?php include(APPPATH."views/footer.php"); ?></div>
	</div>
</body>
</html>