<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <?php require_once "blocks/navigation.php"?>
    <?php require_once "after/orderInfo.php"?>
</head>
<body>
  

<div class="h3 text-center" style="font-family: cursive;">Список заказов:</div>

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


</body>
</html>