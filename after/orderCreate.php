<?php
require $_SERVER['DOCUMENT_ROOT']."/connection/connection.php";
require $_SERVER['DOCUMENT_ROOT']."/connection/config.php";
require $_SERVER['DOCUMENT_ROOT']."/customer.php";


$conn = new Database(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
$link = $conn->connect();
//Проверка на нажатие кнопки
if (isset($_POST['submit_create'])){
    $uri = $_POST['uri'];
    //Проверка на наличие данных в форме
    if (!empty($_POST['name']) && !empty($_POST['phone']) &&
        !empty($_POST['latitudeTo']) && !empty($_POST['longitudeTo']) && !empty($_POST['items'])){
        $name = $_POST['name']; 
        $phone = $_POST['phone'];  
        $latitudeTo = $_POST['latitudeTo'];
        $longitudeTo = $_POST['longitudeTo'];
        $items = $_POST['items'];
       //Проверка времени (должно быть более 30 минут, ограничение времени по доставке)
        if(strtotime($_POST['time'])<=strtotime("+ 1800 seconds")){
            echo "Время доставки не менее 30 минут".$_POST['time'];
            var_dump(strtotime($_POST['time']));         
            die;
        }else{
            $time = strtotime($_POST['time']);
        }
        $sql_lat = "SELECT from_lat, from_long FROM salery";
        $lat = $link->query($sql_lat);
        $from = $lat->fetch_assoc();

        
        $vars = new Customer(); 
        $rate = $vars->rateLimit($link);
            if (intval($rate["value"]) <= 10) {
                $dist = $vars->coordinatesToDistance($from['from_lat'], $from['from_long'], $latitudeTo, $longitudeTo);  
                $count = $vars->deliveryCount($link, $dist, $items);
                $create = $vars->createOrder($link, $name, $phone, $items, $latitudeTo, $longitudeTo, $count, $time);
                setcookie("success", "1", time() + 3, '/');
                header("Location: /$uri");
            } else {
                echo"<script>alert('Превышено количество запросов в минуту. Ожидайте.')</script>";
            }
    } else {
        // Если поля не заполнены, появляется алерт и при нажатии на кнопку возвращает на предыдущую страницу
        //setcookie("pageUri", $_SERVER['REQUEST_URI'], time() + 15, "/after/orderCreate.php")
        
        echo "<script>
            if (confirm('Заполните все поля') == true) {
                window.location.replace(''+'$uri');
            } else {
                window.location.replace(''+'$uri');
            }
        </script>";
 
    }
}else{
    echo "ooops";
    echo "<br>";
}