<!DOCTYPE html>
<html>
<head>
    <title></title>
	<link href="./css/vanzari.css" rel="stylesheet" media="screen">
    <link href="./bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
	<link href="./css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
	<script type="text/javascript" src="./bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="./jquery/jquery-1.8.3.min.js" charset="UTF-8"></script>
	<script type="text/javascript" src="./js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
	
</head>
<body>
<?php require 'config.php';?>
<?php $reportName = "Stoc ".$BusinessName;?>
<div class="col-md-12 top-container">
	<fieldset>
        <?php include 'header.php';?>
    </fieldset>
</div>
<div class="tabel col-md-12">
	<table id='sum_table_stoc'>  
		<tr class="randTitlu">
			<th>Denumire Produs</th>
			<th>Cantitate</th>
			<th>U/M</th>
			<th>Pret U/M</th>
			<th>Pret Total</th>
		</tr>
		
		<?php include './reportSQL/raport1.php';?>
		<tr class="totalRand">
			<th>Total</th>
			<th></th>
			<th></th>
			<th></th>
			<th class="totalul"></th>
		</tr>
	 </table>
</div>

<script type="text/javascript" src="./js/reportJS.js" charset="UTF-8"></script>
</body>
</html>
