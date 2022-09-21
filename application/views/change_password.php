<?php $page_title = 'Change Password'; ?>
<?php $route = 'change_password'; ?>

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
		<?php if( $this->uri->segment(2) != "edit" ): ?>
		<div id="header_container"><?php include(APPPATH."views/header.php"); ?></div>
		<?php endif; ?>
		
		<div id="page_<?php echo str_replace(' ','_',strtolower($page_title)); ?>" class="container-fluid page_identifier">
			<div class="page_caption"><?php echo $page_title; ?></div>
			<div class="page_body">
				<!-- show validation error message -->
				<?php if( isset($errors) && $errors ): ?>
					<?php foreach ($errors as $k=>$v): ?>
						<div class="message_board"><?php echo $v; ?><br /></div>
					<?php endforeach; ?>
				<?php endif; ?>
				<!-- end error message -->
				
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-12">
						<fieldset class="divider"><legend>Please enter your credential</legend></fieldset>
						
						<form id="frm_<?php echo str_replace(' ','_',strtolower($page_title)); ?>" class="form_post_ajax" form-route="<?php echo $route; ?>" method="post" action="" data-parsley-validate>
							<input type="hidden" name="user_info" value="<?php echo $this->uri->segment(2); ?>" />
							<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
							<table>
								<tr>
									<td>
										<div class="form_label">New Password*</div>
										<div><input type="password" class="input_full input_style" id="new_password" name="new_password" value="" required data-parsley-minlength="8" /></div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="form_label">Repeat Password*</div>
										<div><input type="password" class="input_full input_style" id="repeat_password" name="repeat_password" value=""  data-parsley-equalto="#new_password" required /></div>
									</td>
								</tr>
								<tr>
									<td>
										<div><input type="submit" class="btn btn-danger" value="Change Password" /></div>
										<div class="spinner">&nbsp;</div>
									</td>
								</tr>
							</table>
						</form>
					</div>
					
					<div class="col-lg-6 col-md-6 col-sm-12">
						<div class="user_tips">
							<div class="title">Please follow the password policy</div>
							<div class="description">
								<ul style="margin-bottom:0px;">
									<li>Password must be minimum 8 characters</li>
									<li>Password must have at least one Capital Letter</li>
									<li>Password must have at least one Small Letter</li>
									<li>Password must have at least one Digit</li>
									<li>Password must have at least one Special Character</li>
									<li>You are not allowed to use your last 2 password</li>
								</ul>
							</div>
						</div><!--end col-lg-6 col-md-6 col-sm-12-->
						
					</div>
				</div>
							
			</div><!--end .page_body-->

		</div>
		
		<div id="footer_container"><?php include("footer.php"); ?></div>
	</div>
</body>
</html>