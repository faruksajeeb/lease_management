<?php 
$url_prefix = $this->webspice->settings()->site_url_prefix;
$site_url = $this->webspice->settings()->site_url;
$domain_name = $this->webspice->settings()->domain_name;
$site_title = $this->webspice->settings()->site_title;
$total_column = 30;
$report_name = 'Lease Information';

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
					<th>ID</th>
					<th>Region</th>
					<th>Branch Name</th>
					<th>Lease Name</th>
					<th>Lease Type</th>
					<th>Lease Term</th>
					<th>Vendor/ Customer</th>
					<th>Tax Rate</th>
					<th>VAT Rate</th>
					<th>Address</th>
					<th>District</th>
					<th>Thana/Upzilla</th>
					<th>Floor Space (SQFT)</th>
					<th>Rent per SQFT</th>
					<th>Agreement Date</th>
					<th>Agreement Expiry</th>
					<th>Agreement Document</th>
					<th>Cost Center Info.</th>
					<th>Rent & Advance</th>
					<th>Created By</th>
					<th>Created Date</th>
					<th>Updated By</th>
					<th>Updated Date</th>
					<th>Status</th>
				</tr>
				<?php foreach($get_record as $k=>$v): ?>
				<tr>
					<td class="text-center">
						<?php echo $v->ID; ?><br />
					</td>
					<td><?php echo $v->REGION_NAME; ?></td>
					<td><?php echo $this->customcache->option_maker($v->BRANCH_ID, 'OPTION_VALUE'); ?></td>
					<td><?php echo $v->LEASE_NAME; ?></td>
					<td><?php echo ucfirst($v->LEASE_TYPE); ?></td>
					<td><?php echo ucwords(str_replace('_',' ',$v->LEASE_TERM)); ?></td>
					<td><?php echo $v->VENDOR_NAME.' &raquo; '.$v->EMAIL; ?></td>
					<td><?php echo $v->TAX_RATE; ?></td>
					<td><?php echo $v->VAT_RATE; ?></td>
					<td><?php echo $v->ADDRESS; ?></td>
					<td><?php echo $v->DISTRICT; ?></td>
					<td><?php echo $v->THANA_UPAZILLA; ?></td>
					<td><?php echo $v->FLOOR_SPACE; ?></td>
					<td><?php echo $v->RENT_PER_SQFT; ?></td>
					<td><?php echo $v->AGREEMENT_DATE; ?></td>
					<td><?php echo $v->AGREEMENT_EXPIRY; ?></td>
					<td><?php echo $v->AGREEMENT_DOCUMENT; ?></td>
					<td>
						<?php
							$get_slab = $this->db->query("
							SELECT TBL_COST_CENTER_DETAILS.*,TBL_OPTION.OPTION_VALUE as COST_CENTER
							FROM TBL_COST_CENTER_DETAILS 
							LEFT JOIN TBL_OPTION on TBL_OPTION.OPTION_ID = TBL_COST_CENTER_DETAILS.COST_CENTER_ID
							WHERE LEASE_ID = ?
							", 
							array(
							$v->ID
							))->result();
							
							echo '<table class="table table-borderd table-striped" style="margin-bottom:0px;">';
								echo '<tr>';
									echo '<th>Cost Center</th>';
									echo '<th>Amount (%)</th>';
								echo '</tr>';
								foreach($get_slab as $k1=>$v1){
								echo '<tr>';
									echo '<td>'.ucwords($v1->COST_CENTER).'</td>';
									echo '<td>'.$v1->PERCENTAGE.'</td>';
								echo '</tr>';
								}
							echo '</table>';
							?>
					</td>
					<td>
						<?php
						$get_slab = $this->db->query("
						SELECT TBL_LEASE_AGREEMENT.* 
						FROM TBL_LEASE_AGREEMENT
						WHERE LEASE_ID = ?
						", 
						array(
						$v->ID
						))->result();
						
						echo '<table class="table table-borderd table-striped" style="margin-bottom:0px;">';
							echo '<tr>';
								echo '<th>From</th>';
								echo '<th>To</th>';
								echo '<th>Type</th>';
								echo '<th>Amt. ex. TAX & VAT</th>';
								echo '<th>Amt. with TAX</th>';
								echo '<th>Amt. with VAT</th>';
							echo '</tr>';
							foreach($get_slab as $k1=>$v1){
							echo '<tr>';
								echo '<td>'.$v1->DATE_FROM.'</td>';
								echo '<td>'.$v1->DATE_TO.'</td>';
								echo '<td>'.ucfirst($v1->TYPE).'</td>';
								echo '<td>'.number_format($v1->AMOUNT,2).'</td>';
								echo '<td>'.number_format($v1->AMOUNT_WITH_TAX,2).'</td>';
								echo '<td>'.number_format($v1->AMOUNT_WITH_VAT,2).'</td>';
							echo '</tr>';
							}
						echo '</table>';
						?>
					</td>
					<td><?php echo $this->customcache->user_maker($v->CREATED_BY,'USER_NAME'); ?></td>
					<td><?php echo $this->webspice->formatted_date($v->CREATED_DATE, null, 'full'); ?></td>
					<td><?php echo $this->customcache->user_maker($v->UPDATED_BY,'USER_NAME'); ?></td>
					<td><?php echo $this->webspice->formatted_date($v->UPDATED_DATE, null, 'full'); ?></td>
					<td><?php echo $this->webspice->static_status($v->STATUS); ?></td>
				</tr>
				<?php endforeach; ?>
			</table>
		</div>
	</body>
</html>