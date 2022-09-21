<?php 
$url_prefix = $this->webspice->settings()->site_url_prefix;
$site_url = $this->webspice->settings()->site_url;
$domain_name = $this->webspice->settings()->domain_name;
$site_title = $this->webspice->settings()->site_title;
$total_column = 15;
$report_name = 'Consolidate Summary by Year';

# don't edit the below area (csv)
if( $this->uri->segment(2)=='csv' ){
	$file_name = strtolower(str_replace(array('_',' '),'',$report_name)).'_'.date('Y_m_d_h_i').'.xls';
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=".$file_name);
	header("Pragma: no-cache");
	header("Expires: 0");
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $report_name; ?></title>

		<style type="text/css">
			#printArea { width:1024px; margin:auto; }
			body, table {font-family:tahoma; font-size:13px;}
			table td { padding:8px; }
		</style>
		
		<?php if( $this->uri->segment(2)=='print'): ?>
		<script type="text/javascript" src="<?php echo $url_prefix; ?>global/js/jquery-3.1.1.min.js"></script>
		
		<link rel="stylesheet" href="<?php echo $url_prefix; ?>global/css/styles.css">
		
    <!-- Bootstrap -->
		<link rel="stylesheet" href="<?php echo $url_prefix; ?>global/bootstrap_3_2/css/bootstrap.min.css">
		<link rel="stylesheet" href="<?php echo $url_prefix; ?>global/bootstrap_3_2/css/bootstrap-theme.min.css">
		<script src="<?php echo $url_prefix; ?>global/bootstrap_3_2/js/bootstrap.min.js"></script>
    
		<!-- print plugin -->
		<script src="<?php echo $url_prefix; ?>global/js/jquery.jqprint.js"></script>
		
		<script type="text/javascript">
			$(document).ready(function(){
				$('#printArea').jqprint();
			  setTimeout(function () {
			    window.close();
			  }, 1000);
			});
		</script>
		<?php endif; ?>
  </head>
  
  <body>
		<!--<a id="print_icon" href="#">Print</a>-->
		
		<div id="printArea">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td align="center" colspan="<?php echo $total_column; ?>">
						<div style="font-size:24px; font-weight:bold; color:#111842; text-align:center; padding:2px;""><?php echo $site_title ?></div>
					</td>
				</tr>
			</table>

			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr style="border-top:1px solid #ccc;">
					<td colspan="<?php echo $total_column; ?>" align="center" style="font-size:17px; font-weight:bold; color:#d04e2a; text-align:center; padding:0px;"><?php echo $report_name; ?></td>
				</tr>
				<tr>
					<td colspan="<?php echo $total_column; ?>" align="center" style="text-align:center; padding:0px;">Report Date: <?php echo date("d F, Y"); ?></td>
				</tr>
				<tr><td style="padding:0px;">&nbsp;</td></tr>
			</table>
			
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
				
			<table id="data_table" class="table" style="overflow:auto; margin:auto;" border="1" cellpadding="0" cellspacing="0">
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
	</body>
</html>