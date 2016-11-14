<?php
$datai = $_POST["startdate"];
$dataf = $_POST["enddate"];
$dataInceput = date('Y-m-d',date(strtotime("+0 day", strtotime($datai ))));
$dataFinal = date('Y-m-d',date(strtotime("+0 day", strtotime($dataf ))));
?>
<div class="dataCrt">S-au afisat vanzarile facute intre Data: <?php echo $dataInceput; ?> Ora: 6:00 si Data: <?php echo $dataFinal; ?> Ora: 6:00</div>
<?php
$sql1 = "
declare @dat_WorkPeriod_Beg Datetime = '".$dataInceput."T06:00:00.000'
declare @dat_WorkPeriod_End Datetime = '".$dataFinal."T06:00:00.000'

SELECT
  [MenuItemName]
,  m.[Id]
FROM [Orders] o
LEFT JOIN [MenuItems] m on m.[Id] = o.[MenuItemId]

WHERE 1=1
and [CreatedDateTime] >= @dat_WorkPeriod_Beg
and [CreatedDateTime] <= @dat_WorkPeriod_End
and DecreaseInventory = 1
and CalculatePrice <> 0
GROUP BY [MenuItemName], m.[Id]
ORDER BY [MenuItemName]
";
$stmt1 = sqlsrv_query( $conn, $sql1 );
if( $stmt1 === false) {
    die( print_r( sqlsrv_errors(), true) );
}
while( $row1 = sqlsrv_fetch_array( $stmt1, SQLSRV_FETCH_NUMERIC) ) {
	$produse[] = $row1;
}
$sql3 = "
SELECT SUM([Quantity]) as Cantitate
      ,SUM([TotalPrice]) as pret
      ,[InventoryItem_Id]
  FROM [RockCaffe].[dbo].[InventoryTransactions]
  GROUP BY [InventoryItem_Id]
  ORDER BY [InventoryItem_Id]
";

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
foreach ($produse as $value) {
    $idprod = $value[1];
	$numeprod = $value[0];

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
, m.[Id]
, [PortionName]
, [Price]

FROM [Orders] o
LEFT JOIN [MenuItems] m on m.[Id] = o.[MenuItemId]

WHERE 1=1
and m.[Id] = ".$idprod."
and [CreatedDateTime] >= @dat_WorkPeriod_Beg
and [CreatedDateTime] <= @dat_WorkPeriod_End
and DecreaseInventory = 1
and CalculatePrice <> 0
GROUP BY m.[GroupCode], [MenuItemName], [Price], m.[Id], [PortionName]
ORDER BY [MenuItemName]";
	if(preg_match("/'/u", $numeprod)) { 
		$numeprod2 = str_replace("'","''",$numeprod);
	}
	else 
	{
		$numeprod2 = $numeprod;
	}
$sql2 = "
SELECT        
 Recipes.Name,
 Recipes.Id, 
 Recipes.Portion_Id, 
 RecipeItems.InventoryItem_Id, 
 RecipeItems.MenuItemPortion_Id, 
 RecipeItems.Quantity, 
 InventoryItems.BaseUnit, 
 InventoryItems.Name AS Expr1, 
 InventoryItems.DefaultBaseUnitCost, 
 InventoryItems.DefaultTransactionUnitCost
FROM            InventoryItems INNER JOIN
                RecipeItems ON InventoryItems.Id = RecipeItems.InventoryItem_Id INNER JOIN
                Recipes ON RecipeItems.RecipeId = Recipes.Id
WHERE Recipes.Name = '".$numeprod2."'
ORDER BY Recipes.Name
";

$stmt2 = sqlsrv_query( $conn, $sql2, array(), array( "Scrollable" => 'static' ) );
if( $stmt2 === false) {
    die( print_r( sqlsrv_errors(), true) );
}
$stmt = sqlsrv_query( $conn, $sql, array(), array( "Scrollable" => 'static' ) );
if( $stmt === false) {
    die( print_r( sqlsrv_errors(), true) );
}
?>
<table id='sum_table_consumm'>  
		<tr class="randTitlu">
			<th>Denumire Produs</th>
			<th>Cod</th>
			<th>U/M</th>
			<th>Cantitate</th>
			<th>Pret</th>
			<th>Valoare</th>
		</tr>
<?php
while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) {
    echo "<tr><th class='center'>".$row[1]."</th><th class='center'>ROCK".$row[4]."</th><th class='center'>".$row[5]."</th><th class='center'>".$row[2]."</th><th class='center'>".number_format($row[6],3)."</th><th class='center randTotal'>".number_format($row[3],3)."</tr>";
	$relu[] = $row;
}
$dimarray = sizeof($relu);
$crtArray = $dimarray - 1;
$cantitate = $relu[$crtArray][2];
?>
</table>
<table id='sum_table_retetar'>  
<tr class="randTitlu">
			<th>Gestiune</th>
			<th>Denumire</th>
			<th>Cod</th>
			<th>U/M</th>
			<th>Cantitate</th>
			<th>Pret</th>
			<th>Valoare</th>
		</tr>
<?php
while( $row2 = sqlsrv_fetch_array( $stmt2, SQLSRV_FETCH_NUMERIC) ) {
	$findarray = multidimensional_search($relux, array('2'=>$row2[3]));
	if ($findarray) {
		$pretunit = $relux[$findarray][1] / $relux[$findarray][0];
	}
	else {
		$pretunit = $row2[9];
	}
	echo '<tr><th>Materie Prima</th><th class="subreteta center">'.$row2[7].'</th><th class="subreteta center">90INV'.$row2[3].'</th><th class="subreteta center">'.$row2[6].'</th><th class="subreteta center">'.$row2[5]*$cantitate.'</th><th class="subreteta center">'.number_format($pretunit,4).'</th><th class="subreteta center">'.number_format($pretunit*$row2[5]*$cantitate,4).'</th>';
	
	$priceTOT[] = $pretunit*$row2[5]*$cantitate;
	}
	$row_count = sqlsrv_num_rows( $stmt2 );
	$dimarray2 = sizeof($priceTOT);
	$crtArray2 = $dimarray2 - 1;
	$pricenr = $priceTOT[$crtArray2];
	if ($dimarray2 == $row_count) {
		$RandMin = 0;
		$RandMax = $dimarray2;
	}
	else {
		$RandMin = $dimarray2 - $row_count;
		$RandMax = $dimarray2;
	}
?>


		<tr class="totalRand">
			<th>Total</th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th class="totalul"><?php echo number_format(sumArray($priceTOT, $RandMin, $RandMax),2); ?></th>
		</tr>
	 </table>
<?php
$echoo[] = sumArray($priceTOT, $RandMin, $RandMax);
sqlsrv_free_stmt( $stmt);
}
?>
<table>
<tr class="totalRand">
			<th>Total General</th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th class="totalulgen"><?php echo number_format(array_sum($echoo),2);?></th>
		</tr>
</table>
