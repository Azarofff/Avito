<!DOCTYPE html>
<html lang="en">
<head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Main</title>
	<?php require_once "blocks/navigation.php"?>
	<?php require_once "after/orderList.php"?>
	<?php require_once "after/orderInfoWOCon.php"?>

	
</head>
<body>

 

<div class="h3 text-center" style="font-family: cursive;">Подробная информация о заказах:</div>

<div class="text-center" style="font-family: cursive;">
    <?php for($i=0;$i<count($data);$i++){ ?>
        <br>
        <hr>
        <input class="my_check" type="checkbox" data-name="<?=$i?>" style="cursor: pointer;">
        
        <label class="h3 text-bold">
            Заказ № <?=$i+1?>
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




	<hr style='border-color: black;'>

	<div class="row justify-content-center">
		<div class="col-sm-4">        
			<div class="h3 text-center">Расчитать стоимость доставки</div>
			<hr>
			<form action="after/countAsCust.php"  id="count" method="post">
				<div class="form-group">
					<label for="lat">Широта</label>
					<input type="text" name="latitudeTo" class="form-control" placeholder="Широта" value="" id ="lat">
				</div>
				<div class="form-group">
					<label for="lng">Долгота</label>
					<input type="text" name="longitudeTo" class="form-control" placeholder="Долгота" value="" id ="lng">                    
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
				<div class="text-center">
				<button type="submit" id="cnt" name="cnt" class="buttonSend btn btn-primary"> Рассчитать</button>
				</div>
			</form> 
			<div id="distance">  </div>
			<div id="cost">  </div>
		</div>
	</div>
	<script>
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
                        // delete [] from string
                        
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



	<hr style='border-color: black;'>
	<div class="row text-center justify-content-center">
		<div class="col-sm-4">
			<div class="h3 text-center">Создать заказ</div>
			<hr>
			<form action="after/orderCreate.php"  method="post" style="text-align: -webkit-center;" >
				<div class="form-group">
					<label for="name">Имя</label>
					<input type="text" class="form-control" name ="name" id="name" placeholder="Имя">
				</div>
				<input type="text" name ="uri" style="display:none" value="<?= $_SERVER['REQUEST_URI']?>">
				<div class="form-group">
					<label for="phone">Телефон</label>
					<input type="text" class="form-control" name ="phone" id="phone" placeholder="Пример: 9119876543">
				</div>
				<div class="form-group">
					<label for="latitudeTo">Широта</label>
					<input type="text" name="latitudeTo" class="form-control" placeholder="Широта" value="" id ="latitudeTo">
				</div>
				<div class="form-group">
					<label for="longitudeTo">Долгота</label>
					<input type="text" name="longitudeTo" class="form-control" placeholder="Долгота" value="" id ="longitudeTo">                    
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
				<div class="text-center">
					<button name="submit_create" type="submit" id="submit_create" class="buttonSend btn btn-primary">Оформить</button>
				</div>
			</form>

			<?php if ($_COOKIE['success'] == 1):?>
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


	<hr style='border-color: black;'>

	
		
	<div class="h3 text-center">Список заказов</div>
	<div class="row text-center justify-content-center" style="display:block">
		<div>
			<button type="submit" name="submit_list" onclick="showOrder()" class="buttonSend btn btn-primary"> Открыть</button>
		</div><br>
		<div id="orderLst">
			<?php for ($i=0;$i<count($list);$i++) {?>
				<label id="<?=$i?>">
					<span class="h4">  Заказ № <?= $i?></span><br>
					<span class="h4">  Широта: <?= $list[$i]["toLat"]?></span><br>
					<span class="h4">  Долгота: <?= $list[$i]["toLong"]?></span><br>
					<span class="h4">  Вещей: <?= $list[$i]["items"]?></span><br>
					<span class="h4">  Доставить: <?= $list[$i]["time"]?></span><br>
				</label>
				<hr>
			<?php }?>
		</div>
	</div>
	<script>
		function showOrder(){
			var x = document.getElementById("orderLst");
			if (x.style.display === "none") {
				x.style.display = "block";
			} else {
				x.style.display = "none";
			}
		}
		$(document).ready(function() {
			$('#orderLst').hide()
		})
	</script>



</body>
</html>