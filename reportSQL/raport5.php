<?php
$datai = $_POST["startdate"];
$dataf = $_POST["enddate"];
$dataInceput = date('Y-m-d',date(strtotime("+0 day", strtotime($datai ))));
$dataFinal = date('Y-m-d',date(strtotime("+0 day", strtotime($dataf ))));
$monthC = date("m",strtotime($dataInceput));
$yearC = date("y",strtotime($dataInceput));
?>

<div class="infoTitlu">
	<div class="titluRaport">Vanzari Lunare</div>
	<div class="subtitluRaport">S.C. RockCaffe Grecu SRL-D</div>
</div>
<div class="nrDoc">
	<div class="nrRaport">Nr. Doc.</div>
	<div class="nrEfectivRaport">VZ<?php echo $monthC;?><?php echo $yearC;?></div>
</div>
<div class="infoFirma">
	<div class="CUI"><b>CUI:</b> 33873931</div>
	<div class="nrregcom"><b>Registru Comertului:</b> J17/1297/2014</div>
</div>
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
  FROM [InventoryTransactions]
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
?>
<table id='sum_table_consummx'>  
		<tr class="randTitlux">
			<th>Denumire Produs</th>
			<th>Cod</th>
			<th>U/M</th>
			<th>Cantitate</th>
			<th>Pret U/M Intrare (fara TVA)</th>
			<th>Pret U/M Intrare (cu TVA)</th>
			<th>Pret U/M Vanzare</th>
			<th>Pret Total Intrare (cu TVA)</th>
			<th>Pret Total  Vanzare</th>
			<th>Profit</th>
		</tr>
<?php
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

<?php
while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_NUMERIC) ) {
    echo "<tr><th class='center'>".$row[1]."</th><th class='center'>ROCK".$row[4]."</th><th class='center'>".$row[5]."</th><th class='center'>".$row[2]."</th>";
	$relu[] = $row;
	$pretvanzarearr[] = $row[3];
}
$dimarray = sizeof($relu);
$crtArray = $dimarray - 1;
$cantitate = $relu[$crtArray][2];
$pretpeunitate = $relu[$crtArray][6];
$pretpxtotal = $relu[$crtArray][3];
?>


<?php
while( $row2 = sqlsrv_fetch_array( $stmt2, SQLSRV_FETCH_NUMERIC) ) {
	$findarray = multidimensional_search($relux, array('2'=>$row2[3]));
	if ($findarray) {
		$pretunit = $relux[$findarray][1] / $relux[$findarray][0];
	}
	else {
		$pretunit = $row2[9];
	}
	$pretRETcuTVA = $pretunit * $row2[8] + $pretunit;
	
	$priceTOT[] = $pretunit*$row2[5]*$cantitate;
	$priceTOTTVA[] = $pretRETcuTVA*$row2[5]*$cantitate;
	$priceTOTsg[] = $pretunit*$row2[5];
	$priceTOTTVAsg[] = $pretRETcuTVA*$row2[5];
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
	$pricedivision = sumArray($priceTOTTVA, $RandMin, $RandMax);
	if ($pricedivision != 0 ) {
		$rataprofit = ($pretpeunitate*$cantitate/$pricedivision)*100;
	}
	else 
	{
		$rataprofit = '1';
	}
?>


		
			<th class="totalul"><?php echo number_format(sumArray($priceTOTsg, $RandMin, $RandMax),2).' LEI'; ?></th>
			<th class="totalul"><?php echo number_format(sumArray($priceTOTTVAsg, $RandMin, $RandMax),2). ' LEI'; ?></th>
			<th><?php echo number_format($pretpeunitate,2); ?></th>
			<th class="totalul"><?php echo number_format(sumArray($priceTOTTVA, $RandMin, $RandMax),2). ' LEI'; ?></th>
			<th><?php echo number_format($pretpeunitate*$cantitate,2); ?></th>
			<th><?php echo number_format($rataprofit,2).'%'; ?></th>
			
		</tr>
	
	 
<?php
$echoo[] = sumArray($priceTOT, $RandMin, $RandMax);
$echoo2[] = sumArray($priceTOTTVA, $RandMin, $RandMax);
sqlsrv_free_stmt( $stmt);
}
?>
</table>
<table class="totalTabelVanzari">
<tr class="totalRand">
			<th class="tablettile ep1" rowspan="2">Total Pret Intrare</th>
			<th class="th1TOTAL2">(fara TVA)</th>
			<th  colspan="5" class="th1TOTAL1 totalulgen"><?php echo number_format(array_sum($echoo),2).' LEI';?></th>
		</tr>
		<tr class="totalRand">
			<th class="th1TOTAL2">(cu TVA)</th>
			<th colspan="5"  class="th1TOTAL1 totalulgen"><?php echo number_format(array_sum($echoo2),2).' LEI';?></th>
		</tr>
		<tr class="totalRand">
			<th class="tablettile" rowspan="1">Total Pret Vanzare</th>
			<th class="th1TOTAL1">(fara TVA)</th>
		
			<th colspan="5" class="th1TOTAL1 totalulgen"><?php echo number_format(array_sum($pretvanzarearr),2).' LEI';?></th>
		</tr>
		
		
</table>
