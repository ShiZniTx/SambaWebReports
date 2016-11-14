<?php
/* Server Configuration */
$serverHost = "90ss.go.ro"; 
$databaseName = "RockCaffe";
$userName = "sa";
$password = "ro92rzbr";
/* End Server Configuration */

/* Business Configuration Strings */
$BusinessName = 'RockCaffe';
/* End Business Configuration Strings */


$connInfo = array("Database"=>$databaseName, "UID"=>$userName, "PWD"=>$password);
$conn = sqlsrv_connect($serverHost, $connInfo);
if($conn){
 echo "<div class='connected'>Connected.</div>";
}else{
 echo "<div class='unconnected'>Conexiunea nu poate fi deschisa</div>";
 die( print_r(sqlsrv_errors(), true));
}
?>