<?php
$datai = $_POST["startdate"];
$dataf = $_POST["enddate"];
$dataInceput = date('Y-m-d',date(strtotime("+0 day", strtotime($datai ))));
$dataFinal = date('Y-m-d',date(strtotime("+0 day", strtotime($dataf ))));
$sqlID = "
	declare @dat_WorkPeriod_Beg Datetime = '".$dataInceput."T06:00:00.000'
	declare @dat_WorkPeriod_End Datetime = '".$dataFinal."T06:00:00.000'
  SELECT [Id]
  FROM [InventoryTransactionDocuments]
  WHERE 1=1
  and [Date] >= @dat_WorkPeriod_Beg
  and [Date] <= @dat_WorkPeriod_End
  ";
$stmt1 = sqlsrv_query( $conn, $sqlID );
if( $stmt1 === false) {
    die( print_r( sqlsrv_errors(), true) );
}
while( $row1 = sqlsrv_fetch_array( $stmt1, SQLSRV_FETCH_NUMERIC) ) {
	$data[] = $row1;
}
$adunam = 0;
function sumArray($array, $min, $max) {
   $sum = 0;
   foreach ($array as $k => $a) {
      if ($k >= $min && $k <= $max) {
         $sum += $a;
      }
   }
   return $sum;
}

foreach ($data as $value) {
    $iddoc = $value[0];


$sql = "SELECT        InventoryTransactionDocuments.Id,
			  InventoryTransactionDocuments.Date, 
			  InventoryTransactionDocuments.Name, 
			  InventoryItems.DefaultBaseUnitCost, 
              InventoryItems.Name AS Expr1, 
			  InventoryTransactions.Unit,
			  InventoryTransactions.Quantity, 
			  InventoryTransactions.TotalPrice, 
              InventoryTransactions.InventoryItem_Id
FROM            InventoryTransactions INNER JOIN
                InventoryTransactionDocuments ON InventoryTransactions.InventoryTransactionDocumentId = InventoryTransactionDocuments.Id INNER JOIN
                InventoryItems ON InventoryTransactions.InventoryItem_Id = InventoryItems.Id 
				WHERE  InventoryTransactionDocuments.Id = ".$iddoc."
				";


$stmt = sqlsrv_query( $conn, $sql, array(), array( "Scrollable" => 'static' ) );
if( $stmt === false) {
    die( print_r( sqlsrv_errors(), true) );
}
?>
<div class="tabel col-md-12">
	<table id='sum_table_nir'>  
		<tr class="randTitlu">
			<th>Denumire Produs</th>
			<th>U/M</th>
			<th>Cantitate</th>
			<th>Pret fara TVA</th>
			<th>Pret cu TVA</th>
			<th>Pret Total cu TVA</th>
			<th>TVA (RON)</th>
			<th>TVA (%)</th>
		</tr>
<?php

while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) {
	
	$idDoc = $row[0];
	$datanir = $row[1];
	$numeNir = $row[2];
	$tva = $row[3];
	$produs = $row[4];
	$unit = $row[5];
	$quantity = $row[6];
	$pret = $row[7];
	$itemid = $row[8];
	$faratva = number_format((float)($pret / $quantity), 2, '.', '');
	$cutva = number_format((float)(($pret / $quantity) + ($pret / $quantity * $tva)), 4, '.', '');
	$prettva = ($pret * $tva);
	$procenttva = $tva * 100 . "%";
	$totalcutva = $pret + $pret * $tva;
	$sumaP[]= $totalcutva;
	
    echo "<tr><th>".$produs."</th><th>".$unit."</th><th>".$quantity."</th><th class='center'>".$faratva."</th><th class='center'>".$cutva."</th><th class='center randTotal'>".$totalcutva."</th><th class='center'>".$prettva."</th><th class='center'>".$procenttva."</tr>";

}
$row_count = sqlsrv_num_rows( $stmt );
if ($adunam == 0) {
	$RandMin = 0;
	$RandMax = $row_count - 1;
}
else {
	$RandMin = $adunam;
	$RandMax = $RandMin + $row_count;
}
	$adunam = $adunam + $row_count;

$myArray = explode('/', $numeNir);
echo "<div class='dateFacutrare'><div class='nrfact'>Numar Factura: <br>".$myArray[0]."</div><div class='numefirma'>Firma: <br>".$myArray[1]."</div><div class='datafact'>Data: <br>".$myArray[2]."</div>";
echo "<div class='idNir'>Nr. NIR: <br>".$idDoc."</div></div>";

?>

		<tr class="totalRand">
			<th>Total</th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th class="totalul"><?php echo sumArray($sumaP, $RandMin, $RandMax); ?></th>
		</tr>
	 </table>
</div>
<?php

sqlsrv_free_stmt( $stmt);
}

?>