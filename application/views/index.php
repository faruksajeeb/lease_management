<!DOCTYPE html>
<html lang="en">
<head>
	<title><?php echo $this->webspice->settings()->domain_name; ?>: Welcome</title>
	<meta name="keywords" content="" />
	<meta name="description" content="" />
	
	<?php include("global.php"); ?>
	
	<style>
		
		#page_index .info_box { 
			padding:10px;
			/*background-image: linear-gradient(to right, #FF512F 0%, #F09819 100%);*/
			background-image: linear-gradient(to right, #e52d27 0%, #b31217 51%, #e52d27 100%);
			background-size: 200% auto;
			border-radius:5px;
		}
		#page_index .info_box:first-child {
			margin-left: -3%; 
		}
		#page_index .info_total {
			float:left;
			padding: 0px 15px;
			border-right:1px dotted #eeeeee;
			color:#ffffff;
			font-size:140%;
			text-align:center;
			line-height:25px;
			vertical-align:middle;
		}
		#page_index .info_detail {
			float:right;
			padding: 5px;
			color:#ffffff;
			line-height:20px;
			vertical-align:middle;
		}
		#page_index .info_title {
			clear:both;
			color:#ffffff;
			text-align:center;
			font-weight:bold;
			padding:8px 3px 3px 3px;
		}
		
		#page_index .side-padding {
		    padding-left: 4%;
		    padding-right: 4%;
		    padding-top: 2%;
		}

		#page_index .side-padding-box {
		    padding-left: 1%;
		    padding-right: 1%;
		    padding-top: 1%;
		}


	</style>
</head>

<body> 
	<div id="wrapper">
		<div id="header_container"><?php include("header.php"); ?></div>
		
		<div id="page_index" class="container-fluid page_identifier">
			<div class="page_caption">Welcome</div>
			<div class="page_body">
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" id="token" value="<?php echo $this->security->get_csrf_hash(); ?>">
<!--
<div class="row">
	<div class="col-md-6">
		  <select class="form-control" style="padding:5px" >
		    <option value="daily" selected>Daily</option>
		  </select>
			<br>
		<div id="chartContainer" style="height: 300px; width: 100%;"></div>
	</div>
	<div class="col-md-6">
		<div id="barchartContainer" style="height: 300px; width: 100%;"></div>

	</div>
</div>
-->
			</div><!--end .page_body-->

		</div>
		
		<div id="footer_container"><?php include("footer.php"); ?></div>
	</div>
	<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<script>
	
/*
window.onload = function () {

var chart = new CanvasJS.Chart("chartContainer", {
	animationEnabled: true,
	title:{
		text: "Status wise Requisition",
		horizontalAlign: "left"
	},
	data: [{
		type: "doughnut",
		startAngle: 60,
		//innerRadius: 60,
		indexLabelFontSize: 17,
		indexLabel: "{label} - #percent",
		toolTipContent: "<b>{label}:</b> {y} (#percent)",
		dataPoints: [
			{ y: 6, label: "Pending Requisition" },
			{ y: 6, label: "Approved" },
			{ y: 6, label: "Assinged Vehicle" },
			{ y: 6, label: "Loaded"},
			{ y: 6, label: "Received"},
			{ y: 10, label: "Unloaded"}
		]
	}]
});
var barchart = new CanvasJS.Chart("barchartContainer", {
	animationEnabled: true,
	
	title:{
		text:"Month wise Report on Costing"
	},
	axisX:{
		interval: 1
	},
	axisY2:{
		interlacedColor: "rgba(1,77,101,.2)",
		gridColor: "rgba(1,77,101,.1)",
		title: "Cost"
	},
	data: [{
		type: "bar",
		name: "companies",
		axisYType: "secondary",
		color: "#014D65",
		dataPoints: [
			{ y: 500, label: "Jan" },
			{ y: 1500, label: "Feb" },
			{ y: 1200, label: "Mar" },
			{ y: 2500, label: "Apr" },
			{ y: 3500, label: "May" },
			{ y: 3200, label: "Jun" },
			{ y: 3000, label: "Jul" },
			{ y: 2500, label: "Aug" },
			{ y: 1200, label: "Sep" },
			{ y: 2900, label: "Oct" },
			{ y: 2600, label: "Nov" },
			{ y: 3600, label: "Dec" }
		]
	}]
});
chart.render();
barchart.render();

}
*/
</script>
</body>
</html>