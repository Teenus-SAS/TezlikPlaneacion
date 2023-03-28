<?php
require_once __DIR__."/env.php";

//$conn = mysqli_connect($servername, $username, $password, $database);
//mysqli_set_charset($conn, "utf-8");

/*Array con todo lo necesario para la configuración*/

$arrOptions = array(
  PDO::ATTR_EMULATE_PREPARES => FALSE, 
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
  PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"
);

// Crear conexion

try{
  $conn = new PDO("mysql:dbname=$database;host=localhost", "$username", "$password", $arrOptions);
  $conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch(Exception $e){
  echo $e->getMessage();
}


