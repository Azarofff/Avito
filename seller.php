<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
  integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
  crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
  integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
  crossorigin=""></script>
  
    
    <?php require_once "blocks/navigation.php"?>
    <?php require_once "after/updateCoord.php"?>
    <style>
        #map {
            height: 70vh;
            width: 50vw;
        }
    </style>
</head>
<body>

<?php if ($_COOKIE['firstTime'] !== '1'):?>
    <?php setcookie('firstTime', '1', time()+600, '/')?>
    <?php header("Refresh:0");?>
<?php endif; ?>

<div class="h3 text-center">Выберите место заведения на карте</div>
<div class="row">
    <div class="col-sm-6" id="map"></div>
    <div class="col-sm-3">             
        <hr>
        <div class="h3 text-center">Координаты заведения</div>
        <form action="after/updateCoord.php"  id="count" method="post">
            <div class="form-group">
                <label for="lat">Широта</label>
                <input type="text" name="lat" class="form-control" placeholder="Широта" value="" id ="lat" readonly>
            </div>
            <div class="form-group">
                <label for="lng">Долгота</label>
                <input type="text" name="lng" class="form-control" placeholder="Долгота" value="" id ="lng" readonly>                    
            </div>
            <input type="text" name ="uri" style="display:none" value="<?= $_SERVER['REQUEST_URI']?>">
            
            <button type="submit" id="submit" name="submit" class="buttonSend btn btn-primary"> Установить</button>
        </form> 
        <hr>
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
    
        var marker = L.marker([<?=$from["from_lat"]?>, <?=$from["from_long"]?>]).addTo(map);
   
    map.addLayer(marker);

    map.on("click", function (event) {

    let lat =  document.getElementById('lat');
    let lng =  document.getElementById('lng');

    let longVal = event.latlng.lng.toString();
    let latVal = event.latlng.lat.toString();

    lat.value = latVal.substr(0,8);
    lng.value = longVal.substr(0,8);
    map.removeLayer(marker);
    var marker1 = L.marker([latVal, longVal]).addTo(map);
    map.addLayer(marker1);



    map.on("click", function (event) {
        map.removeLayer(marker1);
        });

    });

</script>
    

<div class="h3 text-center" style="font-family: cursive;">Список заказов:</div>

<div class="text-center" style="font-family: cursive;">

    <?php for($i=0;$i<count($data);$i++){ 
        $i1 = $i+1;?>
        <br>
        <hr>
        <input class="my_check" type="checkbox" data-name="<?=$i?>" style="cursor: pointer;">
        
        <label class="h3 text-bold">
            Заказ № <?=$i1?>
        </label>
        <hr>
        
        <br>
        <label id="<?=$i?>">
         <span class="h4"> Широта:  <?= $data[$i]['toLat']?></span><br>
         <span class="h4"> Долгота:  <?= $data[$i]['toLong']?></span><br>
         <span class="h4">  Количество вещей: <?= $data[$i]['items']?></span><br>
         <span class="h4 ">  Имя заказчика: <?= $data[$i]['name']?></span><br>
         <span class="h4 ">  Телефон заказчика: +7<?= $data[$i]['phone']?></span><br>
         <span class="h4">  Доставить: <?= $data[$i]["time"]?></span><br>
         
        </label>
        <br>
        
        <?php } ?>
</div>

<script>
    function update(){
        var checkbox = $(this);
        var name = checkbox.data('name');
        if( checkbox.is(':checked') ) {   
            $( '#' + name ).show();
        } else {                      
            $( '#' + name ).hide();
        }         
	}

	//just setup change and each to use the same function
	$('.my_check').change(update).each(update);
    
</script>




</body>
</html>