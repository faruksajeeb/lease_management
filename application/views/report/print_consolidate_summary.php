<?php 
$url_prefix = $this->webspice->settings()->site_url_prefix;
$site_url = $this->webspice->settings()->site_url;
$domain_name = $this->webspice->settings()->domain_name;
$site_title = $this->webspice->settings()->site_title;
$total_column = 9;
$report_name = 'Consolidate Summary';

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

			<table id="data_table" class="table" style="overflow:auto; margin:auto;" border="1" cellpadding="0" cellspacing="0">
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
	</body>
</html>