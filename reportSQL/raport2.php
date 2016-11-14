<?php
$data = $_POST["texttoshow"];
$dataInceput = date('Y-m-d',date(strtotime("+0 day", strtotime($data ))));
$dataFinal = date('Y-m-d',date(strtotime("+1 day", strtotime($data ))));
?>
<div class="dataCrt">S-au afisat vanzarile facute intre Data: <?php echo $dataInceput; ?> Ora: 6:00 si Data: <?php echo $dataFinal; ?> Ora: 6:00</div>
<?php
$sql = "
declare @dat_WorkPeriod_Beg Datetime = '".$dataInceput."T06:00:00.000'
declare @dat_WorkPeriod_End Datetime = '".$dataFinal."T06:00:00.000'

SELECT
  [GroupCode] as [Group]
--, m.[Tag]
, [MenuItemName] as [Item]
, CONVERT(INT,SUM([Quantity])) as [Qty]
--, [Price]
, [Price]*SUM([Quantity]) as [TAmt]

FROM [RockCaffe].[dbo].[Orders] o
LEFT JOIN [RockCaffe].[dbo].[MenuItems] m on m.[Id] = o.[MenuItemId]

WHERE 1=1
and [CreatedDateTime] >= @dat_WorkPeriod_Beg
and [CreatedDateTime] <= @dat_WorkPeriod_End
and DecreaseInventory = 1
and CalculatePrice <> 0
GROUP BY m.[GroupCode], [MenuItemName], [Price]
ORDER BY m.[GroupCode], [MenuItemName]";
$stmt = sqlsrv_query( $conn, $sql );
if( $stmt === false) {
    die( print_r( sqlsrv_errors(), true) );
}

while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) {
      echo "<tr><th>".$row[1]."</th><th>".$row[2]."</th><th class='randTotal'>".number_format($row[3],2)."</tr>";
}
	
sqlsrv_free_stmt( $stmt);

?>


