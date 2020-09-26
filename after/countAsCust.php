<?php
//Импортируем классы для подключения к БД, заданные параметры подключения и класс ограничения запросов
require $_SERVER['DOCUMENT_ROOT']."/connection/connection.php";
require $_SERVER['DOCUMENT_ROOT']."/connection/config.php";
require $_SERVER['DOCUMENT_ROOT']."/customer.php";

//Создаём экземляр класса и подключаемся в БД
$conn = new Database(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
$link = $conn->connect();
//Создаём экземпляр класса и вызываем метод подсчёта количества запросов
$vars = new Customer();
$rateNum = $vars->rateLimit($link);
   
//Проверяем количество запросов (менее 10 или нет)
if(intval($rateNum['value']) <= 10){ 
    $latitudeTo = $_POST['latitudeTo'];
    $longitudeTo = $_POST['longitudeTo'];
    $items = $_POST['items'];

    $sql_from = "SELECT from_lat, from_long FROM salery";
    $dataFrom = $link->query($sql_from);
    $from = $dataFrom->fetch_assoc();

    $dist = $vars->coordinatesToDistance($from['from_lat'], $from['from_long'], $latitudeTo, $longitudeTo);  
    $count = $vars->deliveryCount($link, $dist, $items);
    $list = [
        0 => $dist,
        1 => $count,
    ];

    $list = json_encode( $list );

    echo $list;
    
}else{
    echo "Вы превысили максимальное количество запросов в минуту ($rate), ожидайте";
}


 







