<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $this->webspice->settings()->domain_name; ?>: Welcome</title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	
	<?php include("global.php"); ?>
</head>

<body>
	<div id="wrapper">
		<div id="header_container"><?php include("header.php"); ?></div>
		
		<div id="page_login" class="container-fluid page_identifier">
			<div class="page_caption">
				Authentication
			</div>
			<div class="page_body">
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-12">
						<fieldset class="divider"><legend>Please enter your credential to login</legend></fieldset>
						
						<form id="frm_login" action="" method="post" data-parsley-validate>
							<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
							
							<table>
								<tr>
									<td>
										<div class="form_label">User Email</div>
										<div>
											<input type="email" class="input_full form-control" id="user_email" name="user_email" value="" placeholder="" required />
											<span class="fred"><?php echo form_error('user_email'); ?></span>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="form_label">User Password</div>
										<div>
											<input type="password" class="input_full form-control" id="user_password" name="user_password" value="" placeholder="" required />
											<span class="fred"><?php echo form_error('user_email'); ?></span>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div><input type="submit" class="btn btn-info" value="Login" /></div>
									</td>
								</tr>
							</table>
						</form>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-12">
						<fieldset class="divider"><legend>Forgot your password?</legend></fieldset>
						
						<form id="frm_forgot_password" action="<?php echo $url_prefix; ?>forgot_password" method="post" data-parsley-validate>
							<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
							
							<table>
								<tr>
									<td>
										<div class="form_label">User Email</div>
										<div>
											<input type="email" class="input_full form-control" id="user_email_address" name="user_email" value="" required />
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div><input type="submit" class="btn btn-info" value="Send Query" /></div>
									</td>
								</tr>
							</table>
						</form>
					</div>
				</div>

<br />
<div class="user_tips">
	<div class="title">Notice to All Users (Authorized or Unauthorized).</div>
	<div class="description">
	This computer system is for authorized use only. Users have no explicit or implicit expectation of privacy.<br />
	Any or all uses of this system and all data on this system may be intercepted, monitored, recorded, copied, audited, inspected, and disclosed to authorized sites and law enforcement personnel, as well as authorized officials of other agencies. By using this system, the user consent to such disclosure at the discretion of authorized site personnel. Unauthorized or improper use of this system may result in administrative disciplinary action, civil and criminal penalties. By continuing to use this system you indicate your awareness of and consent to these terms and conditions of use. STOP IMMEDIATELY!!! if you do not agree to the conditions stated in this warning.
	</div>
</div>
				
			</div><!--end .page_body-->

		</div>
		
		<div id="footer_container"><?php include("footer.php"); ?></div>
	</div>
</body>
</html>