<?php
require $_SERVER['DOCUMENT_ROOT']."/connection/connection.php";
require $_SERVER['DOCUMENT_ROOT']."/connection/config.php";
require $_SERVER['DOCUMENT_ROOT']."/customer.php";
$conn = new Database(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
$link = $conn->connect();

$vars = new Customer();
$rateNum = $vars->rateLimit($link);
// проверка на количество запросов
if ($rateNum['value']<=10) {
    $uri = $_POST['uri'];
    $sql_lat = "SELECT from_lat, from_long FROM salery";
    $lat = $link->query($sql_lat);
    $from = $lat->fetch_assoc();
    $data = $vars->getOrder($link); 
        if (!isset($from)) {
            $lat = 44.61421;
            $lng = 33.51849;
            $query = "INSERT INTO salery(from_lat, from_long) VALUE ($lat, $lng)";
            $run_user = mysqli_query($link, $query) or die(mysqli_error());
            header("Location: $uri");
        } else {
            if (isset($_POST['submit'])) {
                if (!empty($_POST['lat']) && !empty($_POST['lng'])) {
                    $lat = $_POST['lat'];
                    $lng = $_POST['lng'];
                    $query = "UPDATE salery SET from_lat = $lat, from_long = $lng WHERE id = 1";
                    $run_user = mysqli_query($link, $query) or die(mysqli_error());
                  
                    header("Location: $uri");
                    
                } else {
                    echo "<script>
                        if (confirm('Координаты не выбраны. Кликните на карту.') == true) {
                            window.location.replace(''+'$uri');
                        } else {
                            window.location.replace(''+'$uri');
                        }
                    </script>";
                }
        }    
    }
} else {
    echo "<script>
            if (confirm('Вы превысили максимальное количество запросов в минуту ($rate), ожидайте') == true) {
                window.location.replace(''+'$uri');
            } else {
                window.location.replace(''+'$uri');
            }
        </script>";
}









