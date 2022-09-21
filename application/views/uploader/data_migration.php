<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $this->webspice->settings()->domain_name; ?>: Welcome</title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	
	<?php include(APPPATH."views/global.php"); ?>
	<style>
		label {
		    font-weight: normal !important;
		    cursor:pointer;
		}
	</style>
</head>

<body>
	<div id="wrapper">
		<div id="header_container"><?php include(APPPATH."views/header.php"); ?></div>
		
		<div id="page_accounts_journal" class="container-fluid page_identifier">
			<div class="page_caption">Data Migration</div>
			<div class="page_body">
				
				<!--file upload error-->
				<?php if( isset($error) && $error ): ?>
				<div class="message_board"><?php echo $error; ?></div>
				<?php endif; ?>
					
				<div class="row">
				<div class="col-md-6">
					<fieldset class="divider"><legend>Please select a file!</legend></fieldset>
					
					<div class="stitle">* Mandatory Field</div>
					
					<form id="frm_upload_ic_data" method="post" action="" enctype="multipart/form-data" data-parsley-validate>
						<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
		            
						<table width="100%">

							<tr>
								<td>
									<br />
									<div class="form_label">Select File* <small>Format (.xlsx)</small></div>
									<div>
										<input type="file" class="" id="attachment_file" name="attachment_file" accept=".xlsx" required />
									</div>
								</td>
							</tr>
							<tr>
								<td>
									<div><input type="submit" class="btn btn-success" value="Submit Data" /></div>
								</td>
							</tr>
						</table>
					</form>
				</div>
				
				<div class="col-md-6">
					<fieldset class="divider"><legend>Please download the formatted file</legend></fieldset>
					<a href="<?php echo $this->webspice->get_path('custom'); ?>data_migration.xlsx">Download</a>
				</div>
				</div>

		</div>
		
		<div id="footer_container"><?php include(APPPATH."views/footer.php"); ?></div>
		
	</div>
</body>
</html>