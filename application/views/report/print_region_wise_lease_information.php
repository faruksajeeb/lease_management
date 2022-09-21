<?php 
$url_prefix = $this->webspice->settings()->site_url_prefix;
$site_url = $this->webspice->settings()->site_url;
$domain_name = $this->webspice->settings()->domain_name;
$site_title = $this->webspice->settings()->site_title;
$total_column = 16;
$report_name = 'Region Wise Lease Information';

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
			#printArea { width:100%; margin:auto; }
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
					<td colspan="<?php echo $total_column; ?>" align="center" style="text-align:center; padding:0px;">Report Date: <?php echo date("d F, Y"); ?>&nbsp;|&nbsp;<?php echo $filter_by; ?></td>
				</tr>
				<tr><td style="padding:0px;">&nbsp;</td></tr>
			</table>

			<table id="data_table" class="table" width="100%" border="1" cellpadding="0" cellspacing="0">
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
	</body>
</html>