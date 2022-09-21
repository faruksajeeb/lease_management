<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta charset="utf-8" />

<!-- global declaration -->
<?php $url_prefix = $this->webspice->settings()->site_url_prefix; ?>

<link rel="stylesheet" media="screen" type="text/css" href="<?php echo $url_prefix; ?>global/css/jquery-ui.css" />
<link rel="stylesheet" media="screen" type="text/css" href="<?php echo $url_prefix; ?>global/css/styles.css" />

<script type="text/javascript" src="<?php echo $url_prefix; ?>global/js/jquery-3.4.1.min.js"></script>
<script type="text/javascript" src="<?php echo $url_prefix; ?>global/js/jquery-ui.js"></script>

<script type="text/javascript" src="<?php echo $url_prefix; ?>global/js/sweetalert2.all.min.js"></script>
<script type="text/javascript" src="<?php echo $url_prefix; ?>global/js/functions.js?v=3.1"></script>
<script type="text/javascript" src="<?php echo $url_prefix; ?>global/parsley/parsley.min.js"></script> <!--JQuery form validation-->
<!--bootstrap-->
<link rel="stylesheet" type="text/css" href="<?php echo $url_prefix; ?>global/bootstrap_3_2/css/bootstrap.min.css" />

<!--popup could not working after open popup > featherlight -->
<?php if( $this->uri->segment(2) != "edit" ): ?>
<script type="text/javascript" src="<?php echo $url_prefix; ?>global/bootstrap_3_2/js/bootstrap.min.js"></script>
<?php endif; ?>

<link rel="stylesheet" type="text/css" href="<?php echo $url_prefix; ?>global/featherlight/featherlight.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $url_prefix; ?>global/featherlight/featherlight.gallery.min.css" />
<script type="text/javascript" src="<?php echo $url_prefix; ?>global/featherlight/featherlight.min.js"></script>
<script type="text/javascript" src="<?php echo $url_prefix; ?>global/featherlight/featherlight.gallery.min.js"></script>

<!--calendar-->
<link rel="stylesheet" type="text/css" href="<?php echo $url_prefix; ?>global/calendar/jquery.datetimepicker.css" />
<script type="text/javascript" src="<?php echo $url_prefix; ?>global/calendar/jquery.datetimepicker.js"></script>
<script type="text/javascript" src="<?php echo $url_prefix; ?>global/js/jquery.monthpicker.min.js"></script>
<!-- Month Picker -->
<link rel="stylesheet" type="text/css" href="<?php echo $url_prefix; ?>global/monthpicker/MonthPicker.min.css" />
<!-- Month Picker -->
<script type="text/javascript" src="<?php echo $url_prefix; ?>global/monthpicker/MonthPicker.min.js"></script>


<!--[if lt IE 9]>
	<link rel="stylesheet" media="screen" type="text/css" href="<?php echo $url_prefix; ?>global/css/styles_ie.css" />
  <script src="<?php echo $url_prefix; ?>global/js/html5shiv.min.js"></script>
  <script src="<?php echo $url_prefix; ?>global/js/respond.min.js"></script>
<![endif]-->

<link rel="stylesheet" type="text/css" href="<?php echo $url_prefix; ?>global/chosen/chosen.css" />
<script type="text/javascript" src="<?php echo $url_prefix; ?>global/chosen/chosen.jquery.js"></script>
<script type="text/javascript" src="<?php echo $url_prefix; ?>global/js/canvasjs.min.js"></script>

<link  type="image/x-icon" rel="Shortcut Icon" href="<?php echo $url_prefix; ?>global/img/nns_logo.png?v=1.0" />
<link rel="image_src" href="<?php echo $this->webspice->settings()->site_url; ?>global/img/nns_logo.png?v=1.0" />
<meta property="og:image" content="<?php echo $this->webspice->settings()->site_url; ?>global/img/nns_logo.png?v=1.0" />