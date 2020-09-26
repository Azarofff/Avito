<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Main</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
            integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
            crossorigin=""/>
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
            integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
            crossorigin=""></script>
        <?php require_once "blocks/navigation.php"?>
        <style>
            #map {
                height: 70vh;
                width: 50vw;
            }
        </style>
        
    </head>
    <body>
        <div class="h1 text-center">Выберите место доставки</div>
        <hr>

        <div class="row">
            <div class="col-sm-6" id="map"></div>
            <div class="col-sm-3">        
                <div class="h3 text-center">Расчитать стоимость доставки</div>
                <hr>
                <form action="after/countAsCust.php"  id="count" method="post">
                    <div class="form-group">
                        <label for="lat">Широта</label>
                        <input type="text" name="latitudeTo" class="form-control" placeholder="Широта" value="" id ="lat" readonly>
                    </div>
                    <div class="form-group">
                        <label for="lng">Долгота</label>
                        <input type="text" name="longitudeTo" class="form-control" placeholder="Долгота" value="" id ="lng" readonly>                    
                    </div>
                    
                    <div class="form-group">
                        <label for="itemval">Список вещей (количество)</label>
                        <select name="items" id ="itemval" class="form-control">
                            <option value="1">Одна</option>
                            <option value="2">Две</option>
                            <option value="3">Три</option>
                            <option value="4">Четыре</option>
                            <option value="5">Пять</option>
                        </select>
                    </div>

                    <button type="submit" id="cnt" name="cnt" class="buttonSend btn btn-primary"> Рассчитать</button>
                </form> 
                <div id="distance">  </div>
                <div id="cost">  </div>
                <hr>
            </div>
            <div class="col-sm-3">
                <div class="h3 text-center">Создать заказ</div>
                <hr>
                <div class="row text-center justify-content-center">
                    <form action="after/orderCreate.php"  method="post" style="text-align: -webkit-center; width: 80%;">
                        <div class="form-group">
                            <label for="name">Имя</label>
                            <input type="text" class="form-control" name ="name" id="name" placeholder="Имя">
                        </div>
                        <input type="text" name ="uri" style="display:none" value="<?= $_SERVER['REQUEST_URI']?>">
                        <div class="form-group">
                            <label for="phone">Телефон</label>
                            <input type="text" class="form-control" name ="phone" id="phone" placeholder="Пример: 9781111111">
                        </div>
                        <div class="form-group">
                            <label for="latitudeTo">Широта</label>
                            <input type="text" name="latitudeTo" class="form-control" placeholder="Широта" value="" id ="latitudeTo" readonly>
                        </div>
                        <div class="form-group">
                            <label for="longitudeTo">Долгота</label>
                            <input type="text" name="longitudeTo" class="form-control" placeholder="Долгота" value="" id ="longitudeTo" readonly>                    
                        </div>
                            
                        <div class="form-group">
                            <label for="item">Список вещей (количество)</label>
                            <select name="items" id ="item" class="form-control">
                                <option value="1">Одна</option>
                                <option value="2">Две</option>
                                <option value="3">Три</option>
                                <option value="4">Четыре</option>
                                <option value="5">Пять</option>
                            </select>
                        </div>
                        <div class="form-group row">
                           
                            <div class="col-12">
                            <label for="time" class="col-12 col-form-label">День и время доставки заказа</label><br>
                                <input class="form-control" type="datetime-local" name="time" id="time">
                            </div>
                        </div>

                        <br>
                        <button name="submit_create" type="submit" id="submit_create" class="buttonSend btn btn-primary">Оформить</button>
                    </form>

                    <?php if($_COOKIE['success'] == 1):?>
                        <div>
                            <span class="h3 text-center">
                                Заказ Создан
                            </span>
                        </div>
                    <?php else:?>
                        <div>
                            <span>
                                
                            </span>
                        </div>
                    <?php endif;?>
                </div>
            </div> 
        </div>
        <script>

        var map = L.map('map', {
            center: [44.561515, 33.505073],
            zoom: 10
        });
            L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            map.on("click", function (event) {

                let latitudeTo =  document.getElementById('latitudeTo');
                let longitudeTo =  document.getElementById('longitudeTo');
                let lat = document.getElementById('lat');
                let lng = document.getElementById('lng');

                let longVal = event.latlng.lng.toString();
                let latVal = event.latlng.lat.toString();

                latitudeTo.value = latVal.substr(0,8);
                longitudeTo.value = longVal.substr(0,8);
                lat.value = latVal.substr(0,8);
                lng.value = longVal.substr(0,8);

                var marker = L.marker([latVal, longVal]).addTo(map);
                map.addLayer(marker);
                
        

                map.on("click", function (event) {
                    map.removeLayer(marker);
                    });

            });

            //ajax
            var request;

            $(document).ready(function() {
                $("#count").submit(function(event){
                    event.preventDefault();

                    if (request) {
                        request.abort();
                    }
                    var $form = $(this);

                    var $inputs = $form.find("input, select, button, textarea");

                    var serializedData = $form.serialize();
                    $inputs.prop("disabled", true);

                    request = $.ajax({
                        url: "after/countAsCust.php",
                        //contentType: 'application/json',
                        type: "post",
                        data: serializedData
                    });

                    

                    request.done(function (response, textStatus, jqXHR){
                        console.log(typeof response);
                        response = JSON.parse(response);
                        console.log("response"+response[0]);
                       var array0km = response[0]/1000; // метры к км
                        array0km = array0km.toString();
                        response[1] = response[1].toString();
                        $("#distance").html('Расстояние = ' + array0km.substr(0,6) + ' км.'); // ограничение длины строки
                        $("#cost").html('Стоимость = ' + response[1].substr(0,6) + ' руб.');
                    
                    });

                    request.fail(function (jqXHR, textStatus, errorThrown){
                        console.error(
                            "The following error occurred: "+
                            textStatus, errorThrown
                        );
                    });
                    request.always(function () {
                        $inputs.prop("disabled", false);
                    });

                });

            });
            </script>

    </body>
</html>