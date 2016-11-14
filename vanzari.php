<!DOCTYPE html>
<html>
<head>
    <title></title>
	<link href="./css/vanzari.css" rel="stylesheet" media="screen">
    <link href="./bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
	<link href="./css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
	<script type="text/javascript" src="./jquery/jquery-1.8.3.min.js" charset="UTF-8"></script>
	<script type="text/javascript" src="./bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="./js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
	<script type="text/javascript" src="./js/locales/bootstrap-datetimepicker.ro.js" charset="UTF-8"></script>
</head>
<body>
<?php require 'config.php';?>
<?php $reportName = "Vanzari Zilnice ".$BusinessName;?>
<div class="col-md-12 top-container">
    <form action="vanzari.php" class="form" method="post"  role="form">
        <fieldset>
            <?php include 'header.php';?>
            <div class="form-group">
                <label for="dtp_input1" class="col-md-12 col-sm-12 col-xs-12 col-lg-12 control-label">Alege Data</label>
                <div class="input-group date form_datetime_raport1 col-md-8 col-sm-8 col-xs-8 col-lg-8" data-date="" data-date-format="dd-mm-yyyy" data-link-field="dtp_input1">
                    <input class="form-control" size="16" type="text" placeholder="Apasa aici pentru a introduce data" name="texttoshow" value="" readonly>
                    <span class="input-group-addon"><span class="glyphicon glyphicon-remove"></span></span>
					<div class="spacer"></div>
                </div>
				<button type="submit" class="col-md-4 col-sm-4 col-xs-4 col-lg-4 btn btn-default buton-actiune">Afiseaza Vanzari</button>
				<input type="hidden" id="dtp_input1" /><br/><br/>
				
            </div>
        </fieldset>
    </form>
</div>
<?php 
if(!empty($_POST["texttoshow"])){ ?>

<div class="tabel col-md-12">
	<table id='sum_table_raport1'>  
		<tr class="randTitlu">
			<th>Denumire Produs</th>
			<th>Cantitate</th>
			<th>Pret Total</th>
		</tr>
		<?php include './reportSQL/raport2.php';?>
		<tr class="totalRand">
			<th>Total</th>
			<th></th>
			<th class="totalul"></th>
		</tr>
	 </table>
</div>

<?php
}
?>
<script type="text/javascript" src="./js/reportJS.js" charset="UTF-8"></script>
</body>
</html>
