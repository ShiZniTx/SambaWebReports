<?php
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
				WHERE InventoryTransactionDocuments.Id = 486
				";


$stmt = sqlsrv_query( $conn, $sql, array(), array( "Scrollable" => 'static' ) );
if( $stmt === false) {
    die( print_r( sqlsrv_errors(), true) );
}
if( sqlsrv_fetch( $stmt ) === false) {
     die( print_r( sqlsrv_errors(), true));
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

    echo "<tr><th>".$produs."</th><th>".$unit."</th><th>".$quantity."</th><th class='center'>".$faratva."</th><th class='center'>".$cutva."</th><th class='center randTotal'>".$totalcutva."</th><th class='center'>".$prettva."</th><th class='center'>".$procenttva."</tr>";
	$sumaP[] = $totalcutva;
}
print_r($sumaP);
$row_count = sqlsrv_num_rows( $stmt );
echo $row_count;

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
			<th class="totalul"></th>
		</tr>
	 </table>
</div>
<?php

sqlsrv_free_stmt( $stmt);

?>