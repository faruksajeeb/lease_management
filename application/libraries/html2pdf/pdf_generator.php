<?php
set_time_limit(600);
ini_set("memory_limit","256M");
//
$timeo_start = microtime(true);

include("mpdf60/mpdf.php");
$mpdf=new mPDF();

$header = array(
	'L' => array(
	'content' => 'LOGO'
	),
	'C' => array(
	),
	'R' => array(
		'content' => '{PAGENO}|{nbpg}',
		'font-family' => 'sans',
		'font-style' => '',
		'font-size' => '9',	/* gives default */
	),
	'line' => 1,		/* 1 or 0 to include line above/below header/footer */
);

# get query string
$file_name = $_REQUEST['fn'].'_'.date("Y_m_d_h_i").'.pdf';
$html = urldecode($_REQUEST['html']);

$mpdf->SetHeader($header,'O');
$mpdf->SetDisplayMode('fullpage');
$mpdf->mirrorMargins = 1;
/*
$html = '<h3>Header 3</h3>';
$html .= '<h4>Header 4</h4>';
$html .= '<table style="width:100%; border:1px solid red;"><tr><td style="width:20%;">td 1</td><td>td 2</td></tr></table>';
*/
$mpdf->WriteHTML($html);
#$mpdf->Output('test.pdf');
$mpdf->Output($file_name);

echo '<script type="text/javascript" src="jquery-3.1.1.min.js"></script>';
echo '<script type="text/javascript">';
echo '$(document).ready(function(){';
echo 'location.href = "http://localhost:90/test/html2pdf/'.$file_name.'";';
echo '});';
echo '</script>';

exit;
?>