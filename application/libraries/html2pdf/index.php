<html>
<head>
<script type="text/javascript" src="jquery-3.1.1.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#btnPrint').on('click', function(){
			var html = encodeURIComponent($('body').html());

			window.open('http://localhost:90/test/html2pdf/pdf_generator.php?fn=report_name&html=' + html, '_blank');

			return false;
		});
	});
</script>
</head>
<body>
<div style="z-index: 9999; border: 1px solid rgb(74, 174, 222); visibility: hidden;" class="selection_bubble fontSize13 noSelect" id="dic_bubble"></div><h1>Object not found!</h1>
<p>
    The requested URL was not found on this server.

    The link on the
    <a href="http://103.247.238.115/bundle/master_controller/view_configuration_data">referring
    page</a> seems to be wrong or outdated. Please inform the author of
    <a href="http://103.247.238.115/bundle/master_controller/view_configuration_data">that page</a>
    about the error.
</p>
<p>
If you think this is a server error, please contact
the <a href="mailto:postmaster@localhost">webmaster</a>.

</p>

<h2>Error 404</h2>
<address>
  <a href="/">103.247.238.115</a><br>
  <span>Apache/2.4.25 (Win32) OpenSSL/1.0.2j PHP/5.6.30</span>
</address>


<a id="btnPrint" target="_new" href="#"><strong>Print</strong></a>
</body>
</html>