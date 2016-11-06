<?php
/* Server Configuration */
$serverHost = "90ss.go.ro"; 
$databaseName = "RockCaffe";
$userName = "sa";
$password = "ro92rzbr";
/* End Server Configuration */

$connInfo = array("Database"=>$databaseName, "UID"=>$userName, "PWD"=>$password);
$conn = sqlsrv_connect($serverHost, $connInfo);
if($conn){
 echo "Connected.<br />";
}else{
 echo "Something in your setup is not setup well.<br />";
 die( print_r(sqlsrv_errors(), true));
}
?>