<?php
$sql = "SELECT [InventoryItemName] as ItemName,
(CASE WHEN [BaseUnit] = [TransactionUnit] THEN
(ISNULL([PhysicalInventory],([InStock]+[Added]-[Removed]-[Consumption]))+
ISNULL((SELECT TOP 1 [Quantity] FROM [InventoryTransactions]
WHERE [InventoryItem_Id] = [PeriodicConsumptionItems].InventoryItemId AND [Date] >= CONVERT(varchar(10),GETDATE(),121)
ORDER BY [Id] DESC),0))
      ELSE
        ISNULL([PhysicalInventory],([InStock]+[Added]-[Removed]-[Consumption]))*[UnitMultiplier]
	  END) as InStock,
	  [BaseUnit] as Unit,
	  CONVERT(numeric(10,2),([Cost]/[UnitMultiplier])) as UnitCost,
	  CONVERT(numeric(10,2),ROUND([Cost]*ISNULL([PhysicalInventory],([InStock]+[Added]-[Removed]-[Consumption])),2)) as TotalCost
	,[InventoryItems].[Id]
FROM [PeriodicConsumptionItems]

LEFT OUTER JOIN [InventoryItems] ON [InventoryItemId] = [InventoryItems].[Id]

WHERE [PeriodicConsumptionId] = (SELECT TOP 1 [Id] FROM [PeriodicConsumptions] ORDER BY [Id] DESC)

ORDER BY [InventoryItemName]";
/* Cauta in Transaction Documents */
$sql3 = "
SELECT        
	SUM(InventoryTransactions.Quantity) as Cantitate, 
	SUM(InventoryTransactions.TotalPrice) PretTotal, 
	InventoryTransactions.InventoryItem_Id, 
	InventoryItems.DefaultBaseUnitCost, 
	InventoryItems.DefaultTransactionUnitCost, 
	InventoryItems.Id
FROM            
	InventoryItems INNER JOIN
	InventoryTransactions ON InventoryItems.Id = InventoryTransactions.InventoryItem_Id
GROUP BY 
	InventoryTransactions.InventoryItem_Id, 
	InventoryItems.DefaultBaseUnitCost, 
	InventoryItems.DefaultTransactionUnitCost, 
	InventoryItems.Id
ORDER BY 
	InventoryItems.Id
";
$sql4 = "
SELECT [Id]
      ,[DefaultBaseUnitCost]
      ,[DefaultTransactionUnitCost]
      ,[Name]
  FROM [InventoryItems]
";
$stmt4 = sqlsrv_query( $conn, $sql4 );
if( $stmt4 === false) {
    die( print_r( sqlsrv_errors(), true) );
}
while( $row4 = sqlsrv_fetch_array( $stmt4, SQLSRV_FETCH_NUMERIC) ) {
	$relux2[] = $row4;
}
$stmt3 = sqlsrv_query( $conn, $sql3 );
if( $stmt3 === false) {
    die( print_r( sqlsrv_errors(), true) );
}
while( $row3 = sqlsrv_fetch_array( $stmt3, SQLSRV_FETCH_NUMERIC) ) {
	$relux[] = $row3;
}
/* SEARCH ARRAY FCT */
function multidimensional_search($parents, $searched) { 
  if (empty($searched) || empty($parents)) { 
    return false; 
  } 

  foreach ($parents as $key => $value) { 
    $exists = true; 
    foreach ($searched as $skey => $svalue) { 
      $exists = ($exists && IsSet($parents[$key][$skey]) && $parents[$key][$skey] == $svalue); 
    } 
    if($exists){ return $key; } 
  } 

  return false; 
} 
/* END SEACH Function */
/* SUm Array Function */
function sumArray($array, $min, $max) {
   $sum = 0;
   foreach ($array as $k => $a) {
      if ($k >= $min && $k <= $max) {
         $sum += $a;
      }
   }
   return $sum;
}
/* END SUM */
/* END TRANSACTION DOC SEARCH */

$stmt = sqlsrv_query( $conn, $sql );
if( $stmt === false) {
    die( print_r( sqlsrv_errors(), true) );
} ?>
<div class="infoTitlu">
	<div class="titluRaport">Stoc Actual</div>
	<div class="subtitluRaport">S.C. RockCaffe Grecu SRL-D</div>
</div>
<div class="tabel col-md-12">
	<table id='sum_table_stoc'>  
		<tr class="randTitlu">
			<th>Denumire Produs</th>
			<th>Cantitate</th>
			<th>U/M</th>
			<th>Pret U/M (fara TVA)</th>
			<th>Pret U/M (cu TVA)</th>
			<th>Pret Total (cu TVA)</th>
		</tr>
<?php
while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) {
	
	$findarray2 = multidimensional_search($relux2, array('0'=>$row[5]));
	$pretFix = $relux2[$findarray2][2];
	
	$findarray = multidimensional_search($relux, array('2'=>$row[5]));
	if ($findarray) {
		$pretunit = $relux[$findarray][1] / $relux[$findarray][0];
	}
	else {
		$pretunit = $pretFix;
	}
	$pretunitTVA = $pretunit * $relux[$findarray][3] + $pretunit;
	$pretTotalTVA = $pretunitTVA * $row[1];
	if ($row[5] !== 246 && $row[5] !== 247) {	
		echo "<tr><th>".$row[0]."</th><th>".number_format($row[1],2)."</th><th>".$row[2]."</th><th class='center'>".number_format($pretunit,4)."</th><th class='center randTotal'>".number_format($pretunitTVA,4)."</th><th class='center'>".number_format($pretTotalTVA,2)."</th></tr>";
	}
	if ($row[5] !== 246 && $row[5] !== 247) {
		$suma[] = $pretTotalTVA;
	}
}
$sumaTotal = array_sum($suma);
sqlsrv_free_stmt( $stmt);
?>
		<tr class="totalRand">
			<th>Total</th>
			<th class="totalulx" colspan="5"><?php echo number_format($sumaTotal,0).' LEI';?></th>
		</tr>
	 </table>