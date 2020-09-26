<?php
//Создаём класс
class Customer
{
    //Создаем метод подсчёта числа запросов в минуту
    public function rateLimit($link){
        // Текущее время
        $currentTime = time(); 
        // В таблицу помещяем значения IP пользователя с которого происходит запрос итекущее время
        $addRateQuery = "INSERT INTO rate (user_IP, request_time) VALUES ('{$_SERVER['REMOTE_ADDR']}', '$currentTime')"; 
        // Отправляем запрос в MySql
        mysqli_query($link, $addRateQuery); 
        // Извлекаем из БД IP и количество запросов за посленюю минуту
        $checkRateQuery = "SELECT COUNT(user_IP) as value FROM rate WHERE user_IP = '{$_SERVER['REMOTE_ADDR']}' AND request_time BETWEEN (UNIX_TIMESTAMP() - 60) AND UNIX_TIMESTAMP()"; 
        $run = $link->query($checkRateQuery);
        // Получаем в rateQuery ассоциативный массив (словарь)
        $rateQuery = $run->fetch_assoc();
        // Возвращаем его
        return $rateQuery;
    }
    // Создаем метод перевода координат в расстояние
    public function coordinatesToDistance(
        $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000){
        // Преобразуем градусы в радианы 
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);
        // определяем разницу значений долготы и широты
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        // рассчитываем расстояние в метрах
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        $distance = $angle * $earthRadius; //metres
        return $distance;
    }


    // Создаём метод рассчёта стоимости доставки
    public function deliveryCount($link, $distance, $items) {
        //Проверка на то, введены ли данные
        if(!empty($items)){
            if(!empty($distance)){
                $sql = "SELECT * FROM delivery_data";
                $result = $link->query($sql);
                if ($result->num_rows > 0) {
        // Создаём ассоциативный массив полученных из запроса данных
                    foreach ($result as $item) {
                        $data[] = [
                                'name' => $item["name"],
                                'value' => $item["value"],
                            ];
                    }
                } else {
                echo "0 results";
                }
        // Проводим рассёт стоимости досавки (параметры сохранены в БД)
        // рассчёт проводился по собственных соображениям исходя из имеющихся данных
        // формулы брались "из головы"
        // конечная стоиместь округлялась до 200, если оказывалась меньше
                $fuelConsumpiton = $data[2]["value"];
                $fuelCost = $data[0]["value"];
                $percentage = $data[1]["value"];
                $fuelPrice = (($distance / 1000) * ($fuelConsumpiton / 100))  * $fuelCost;
                $coef = $items * 0.1;
                $price = $fuelPrice * $coef + $fuelPrice;
                $finalPrice = (($percentage / 100) * $price) + $price;
                if($finalPrice < 200){
                    $finalPrice = 200;
                }
                return $finalPrice;
            }else{
                echo "unknown distance";
            }
        }else{
            echo "choose items";
        }
    }
    // Создаём метод сохранения заказа
    public function createOrder($link, $name, $phone, $items, $latitudeTo, $longitudeTo, $price, $time) {  
        $sql = "SELECT * FROM customer";
        $result = $link->query($sql);
        $user_data = array();
        //Проверка на наличие записей в БД
        if ($result->num_rows > 0) {
        // Создаём массив телефонов, имеющахся в БД
            foreach ($result as $item) {
                array_push($user_data, $item["phone"]);
            }
        }
        //Проверяем имеется ли данный номер телефона в базе
        // если да - приветствуем пользователя
        // если нет - создаем нового пользователя
            if(!in_array($phone, $user_data)){
                $sql_user = "INSERT INTO customer (name, phone) VALUES ('$name', '$phone')";
                $run_user = mysqli_query($link, $sql_user) or die(mysqli_error());
                $user_id = count($user_data) + 1;
                $sql_order = "INSERT INTO orders (customer_id, delivery_price, to_lat, to_long, items, orderTime) 
                                VALUES ('$user_id', '$price', '$latitudeTo', '$longitudeTo', '$items', '$time')";    
                $run_order = mysqli_query($link, $sql_order) or die(mysqli_error());
                return "New user and order created";
            }else{
                $sql_id = "SELECT id FROM customer WHERE phone ='$phone'";
                $user_id = $link->query($sql_id);
                $id = $user_id->fetch_assoc();

                $sql_order = "INSERT INTO orders (customer_id, delivery_price, to_lat, to_long, items, orderTime) 
                                    VALUES ('$id', '$price', '$latitudeTo', '$longitudeTo', '$items', '$time')";    
                $run_order = mysqli_query($link, $sql_order) or die(mysqli_error());
                return "hello dear ".$name. " your order created";
            }
        
    }
    // Создаём метод получения списка заказов (с некоторыми данными)
    public function getList($link) {  
        $sql = "SELECT * FROM orders";
        $result = $link->query($sql);
        if ($result->num_rows > 0) {
        // Создаём ассоциативный массив полученных из запроса данных
            foreach ($result as $item) {
                $list[] = [
                    'toLat' => $item["to_lat"],
                    'toLong' => $item["to_long"],
                    'items' => $item["items"],
                    'time' => date("d-m-Y H:i",$item["orderTime"]),
                        
                ];
            }
        }
        return $list;
    }

    // Создаём метод получения подробной информации о заказе
    public function getOrder($link) {
        $sql = "SELECT orders.customer_id, customer.name, customer.phone, orders.delivery_price, orders.items, orders.orderTime,orders.to_lat,orders.to_long";
        $sql .= " FROM orders INNER JOIN customer ON orders.customer_id = customer.id";
        $result = $link->query($sql);
        
        // Создаём ассоциативный массив полученных из запроса данных
        foreach ($result as $item) {
            $listFullData[] = [
                'deliveryPrice' => $item["delivery_price"],
                'toLat' => $item["to_lat"],
                'toLong' => $item["to_long"],
                'items' => $item["items"],
                'name' => $item["name"],
                'phone' => $item["phone"],
                'time' => date("d-m-Y H:i",$item["orderTime"]),
            ];
        }

        return $listFullData;
    }





}