<?php
/* Server Configuration */
$serverHost = "hostname.for.your.mssql"; 
$databaseName = "DB-Name";
$userName = "sa";
$password = "password";
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
