<?php
$sql = "SELECT [Id]
      ,[GroupCode]
      ,[BaseUnit]
      ,[Name]
  FROM [RockCaffe].[dbo].[InventoryItems]";
$stmt = sqlsrv_query( $conn, $sql );
if( $stmt === false) {
    die( print_r( sqlsrv_errors(), true) );
}

while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) {
      echo "<tr><th>".$row[0]."</th><th>".$row[1]."</th><th>".$row[2]."</th><th>".$row[3]."</tr>";
}
sqlsrv_free_stmt( $stmt);
?>