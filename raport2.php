<?php
$sql = "declare @dat_WorkPeriod_Beg Datetime = '2016-11-02T06:00:00.000'
declare @dat_WorkPeriod_End Datetime = '2016-11-02T18:00:00.000'

declare @tbl_Orders table
    (
    [ID]  INT IDENTITY(1,1) NOT NULL 
   ,[Group] varchar(255)
   ,[Item] varchar(255)
   ,[Qty] int
   ,[TAmt] money
    )

INSERT INTO @tbl_Orders
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
ORDER BY m.[GroupCode], [MenuItemName]

INSERT INTO @tbl_Orders
SELECT
 '',''
,SUM([Qty]) as [Qty]
,SUM([Tamt]) as [Tamt]
FROM @tbl_Orders

SELECT
[Group]
,[Item]
,[Qty]
,[TAmt]
FROM @tbl_Orders";
$stmt = sqlsrv_query( $conn, $sql );
if( $stmt === false) {
    die( print_r( sqlsrv_errors(), true) );
}

while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) {
      echo "<tr><th>".$row[0]."</th><th>".$row[1]."</th><th>".$row[2]."</th><th>".$row[3]."</tr>";
}
sqlsrv_free_stmt( $stmt);
?>


