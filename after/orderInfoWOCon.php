<?php

//Абсолютно идентичный orderInfo файлу за исключением подключения к БД
//это нужно исключительно в целях демонстрации для вкладки "Методы" на сайте с выполненым заданием
$vars = new Customer(); 
$rate = $vars->rateLimit($link);
if(intval($rate["value"]) <= 10){
    $data = $vars->getOrder($link);
}else{
    echo"<script>alert('Превышено количество запросов в минуту. Ожидайте.')</script>";
}


