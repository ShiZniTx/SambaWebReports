<?php
$sql = "SELECT [InventoryItemName] as ItemName,
      CONVERT(INT,ROUND(CASE WHEN [BaseUnit] = [TransactionUnit] THEN
        (ISNULL([PhysicalInventory],([InStock]+[Added]-[Removed]-[Consumption]))+
		ISNULL((SELECT TOP 1 [Quantity] FROM [InventoryTransactions]
	     WHERE [InventoryItem_Id] = [PeriodicConsumptionItems].InventoryItemId AND [Date] >= CONVERT(varchar(10),GETDATE(),121)
	     ORDER BY [Id] DESC),0))
      ELSE
        ISNULL([PhysicalInventory],([InStock]+[Added]-[Removed]-[Consumption]))*[UnitMultiplier]
	  END,0)) as InStock,
	  [BaseUnit] as Unit,
	  CONVERT(numeric(6,2),([Cost]/[UnitMultiplier])) as UnitCost,
	  CONVERT(numeric(6,2),ROUND([Cost]*ISNULL([PhysicalInventory],([InStock]+[Added]-[Removed]-[Consumption])),2)) as TotalCost

FROM [PeriodicConsumptionItems]

LEFT OUTER JOIN [InventoryItems] ON [InventoryItemId] = [InventoryItems].[Id]

WHERE [PeriodicConsumptionId] = (SELECT TOP 1 [Id] FROM [PeriodicConsumptions] ORDER BY [Id] DESC)

ORDER BY [InventoryItemName]";

$stmt = sqlsrv_query( $conn, $sql );
if( $stmt === false) {
    die( print_r( sqlsrv_errors(), true) );
}

while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) {
	if ($row[4] < 1 && $row[4] >= 0) {
		$row[4] = '0'.$row[4];
	}
	if ($row[3] < 1 && $row[3] >= 0) {
		$row[3] = '0'.$row[3];
	}
    echo "<tr><th>".$row[0]."</th><th>".$row[1]."</th><th>".$row[2]."</th><th class='center'>".$row[3]."</th><th class='center randTotal'>".$row[4]."</tr>";
}
sqlsrv_free_stmt( $stmt);
?>