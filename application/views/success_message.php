<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $this->webspice->settings()->site_title; ?>: Welcome</title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link  type="image/x-icon" rel="Shortcut Icon" href="<?php echo $url_prefix; ?>global/img/nns_logo.png" />
	<link rel="stylesheet" media="screen" type="text/css" href="<?php echo $url_prefix; ?>global/css/start_page.css" />

</head>

<body>
	<div id="mask_bg">&nbsp;</div>
	<div id="wrapper">
		<div id="page_start_page" class="main_container page_identifier">
			<div class="page_body">			
				<div class="left_section">
					<div class="caption">
						<img src="<?php echo $url_prefix; ?>global/img/nns_logo.png" alt="NNS Logo"> 
						<div style="" class="site_title">&nbsp;<?php  echo $site_title?></div> 
					</div>
					<div class="success_message">
						<p>Dear Visitor,<br><br>Your visiting information has been accepted. An OTP will be sent to your E-mail/Phone after approving your request.<br><br>Thank you</p>
					</div>		
					<div class="aggregator_list">						
						<a href="<?php echo $url_prefix;?>create_request" style="text-decoration: none;"> <div class="btn_aggregator btn-grad" data-id="all"> Visitor Request </div> </a>
					</div>
				</div>
				<div style="clear:both; height:0px;">&nbsp;</div>

			</div>
		</div>
		
		<div id="footer_container"><?php /*include(APPPATH."views/footer.php");*/ ?></div>
		
	</div>
	<script src="global/js/jquery-3.1.1.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			
		});
	</script>
</body>
</html>