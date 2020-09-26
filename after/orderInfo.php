<?php
require $_SERVER['DOCUMENT_ROOT']."/connection/connection.php";
require $_SERVER['DOCUMENT_ROOT']."/connection/config.php";
require $_SERVER['DOCUMENT_ROOT']."/customer.php";


$conn = new Database(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
$link = $conn->connect();

$vars = new Customer(); 
$rate = $vars->rateLimit($link);
if(intval($rate["value"]) <= 10){
    $data = $vars->getOrder($link);
}else{
    echo"<script>alert('Превышено количество запросов в минуту. Ожидайте.')</script>";
}


