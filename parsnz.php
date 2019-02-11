<?
header('Content-Type: text/html; charset=utf-8');
?>
<html>
<head>
<title>Парсинг сайта Новый Зеленоград</title>
</head>
<body>
<br>
<form action="parsnz.php" method="POST">
<h3>Пример парсинга сайта "Новый Зеленоград"</h3><br>

<? if (!isset($_POST['gobutton'])) {    
echo "<input type=submit name=\"gobutton\" value=\"   Отпарсить   \" onclick=\"document.getElementById('p1').innerHTML='Загрузка...';\">";
} else {echo "<input type=submit disabled name=\"gobutton\" value=\"   Отпарсить   \">";}
?>
<p id="p1"></p>
</form>
<br> 

<?
// если нажали кнопошку
if (isset($_POST['gobutton'])) {     
    
$ttext = "<?xml version='1.0' encoding='utf-8' ?>
<offers>";

// функция парса   
function _pars ($ot, $do, $txxt)    { 
$result = '';    
$ot1 = explode($ot, $txxt);     // от начала тега
$do1 = strpos($ot1[1], $do); // до конца  и начала следующего
$ot1[1] = substr($ot1[1], 0, $do1);    
$result =  $ot1[1];
return $result;   
    
}


// теперь заходим в стройку

$thtmlst = file_get_contents('http://www.newzelenograd.ru/hod-stroitelstva/fotootchet/'); 
// парсим год
$exyear = explode('<div class="b-publ__year', $thtmlst); 
$tyear = _pars('">',"</div>", $exyear[1]);
//echo "$tyear ";                                                     // год

// парсим месяц
$exmess = explode('<div class="l-article-cont">', $thtmlst); 
$tmess = _pars('<h3>','</h3>', $exmess[1]);
// превращаем его в циферку
$nummess =  array("Январь" => 1, "Февраль" => 2, "Март" => 3, "Апрель" => 4, "Май" => 5, "Июнь" => 6, "Июль" => 7, "Август" => 8, "Сентябрь" => 9, "Октябрь" => 10, "Ноябрь" => 11, "Декабрь" => 12 );
//echo "$nummess[$tmess]<br>";  
                                                            // месяц в циферку

// парсим картинки строительства

// сокращаем от самого необходимого
$posim = strpos($thtmlst, 'fotorama is-fotorama');
$thtmlst = substr($thtmlst, $posim);

// сокращаем до самого необходимого
$posim = strpos($thtmlst, '</div>');
$thtmlst = substr($thtmlst, 0, $posim);
//echo "$thtmlst";

$fotorama = explode('<a', $thtmlst);
$endfotorama[1] = _pars(' href="','"><img', $fotorama[1]);                              // сами картинки
$endfotorama[2] = _pars(' href="','"><img', $fotorama[2]);
$endfotorama[3] = _pars(' href="','"><img', $fotorama[3]);

//echo "$endfotorama[1]<br>$endfotorama[2]<br>$endfotorama[3]<br>";




// теперь парсим отделку.
$tohtml = file_get_contents('http://www.newzelenograd.ru/otdelka/');  

// сокращаем от самого необходимого
$pot = strpos($tohtml, 'fotorama is-fotorama');
$tohtml = substr($tohtml, $pot);

// сокращаем до самого необходимого
$pot = strpos($tohtml, '</div>');
$tohtml = substr($tohtml, 0, $pot);

//echo "$tohtml";
$fot = explode('<a', $tohtml);

for ($r = 1; $r < 25; $r++) {

$endfot[$r] = _pars(' href="','"><img', $fot[$r]);                              // сами картинки

}


//$endfot[2] = _pars(' href="','"><img', $fot[2]);
//$endfot[3] = _pars(' href="','"><img', $fot[3]);
//$endfot[4] = _pars(' href="','"><img', $fot[4]);
//$endfot[5] = _pars(' href="','"><img', $fot[5]);
//$endfot[6] = _pars(' href="','"><img', $fot[6]);
//$endfot[7] = _pars(' href="','"><img', $fot[7]);






// подгружаем данные в переменную  -- параметр GET page=500 (означает выборку максимум в 500 квартир, взял с запасом, т.к. на сайте максимум у них 213 имеется)   
$ttable = file_get_contents('http://www.newzelenograd.ru/kvartiry-v-novostroikah/?page=500&price%5Bmin%5D=1+670+000&price%5Bmax%5D=6+770+000&area%5Bmin%5D=21&area%5Bmax%5D=79&sorting=price-asc'); 

// сокращаем от самого необходимого
$pos = strpos($ttable, '<tbody class="js-result-cont"');
$ttable = substr($ttable, $pos);

// сокращаем до самого необходимого
$pos = strpos($ttable, '</tbody');
$ttable = substr($ttable, 0, $pos);

// печатаем, чтобы посмотреть что повылазило
//echo $ttable;
















//делим по позициям
$st = explode('<tr>', $ttable); 





// создаю цикл чтобы выдирать инфу из каждой позиции
for ($i = 1; $i < count($st); $i++) {



// парсим цену
$cena = _pars('<!--', '<br>', $st[$i]);                                                             // $cena  -  цена на кв.
$cena = str_replace ('                                                ', '', $cena);
$cena = str_replace ("\r\n", '', $cena);
$cena = str_replace ("\n", '', $cena);
$cena = str_replace ('', '', $cena);
//echo "$cena <br><br>";

// парсим ссылку на квартиру
$ert = _pars('<a href="', '"><img src="', $st[$i]);
$ssil = "http://www.newzelenograd.ru$ert";                                                                    //$ssil     -  ссылка
//echo "Ссылка: $ssil <br>";  






// пройдем по ссылке и отпарсим остальные данные по каждой позиции
$thtml = file_get_contents($ssil); 
 
// сокращаем от самого необходимого
$pos1 = strpos($thtml, '<div class="b-flat-plan-photo">');
$thtml = substr($thtml, $pos1);

// сокращаем до самого необходимого
$pos1 = strpos($thtml, '<article class="l-article">');
$thtml = substr($thtml, 0, $pos1);
//echo "$thtml<br>";

// парсим картинку
$exkart = explode('<img class="b-flat-plan-photo-big"', $thtml); 
//echo "$exkart[1]<br>$exkart[2]";


$tbigimg = _pars(' src="', '" alt="">', $exkart[1]);
$bigimg= "http://www.newzelenograd.ru$tbigimg";

$tbigimg1 = _pars(' src="', '" alt="">', $exkart[2]); 
if ($exkart[2] != "") {$bigimg1 = "http://www.newzelenograd.ru$tbigimg1";}






// парсим месяц






// парсим картинку
//$kart = _pars('<img src="', '"></a>', $st[$i]);                                                              // $kart  -  kartinka old
//echo "Картинка: http://www.newzelenograd.ru$kart<br>";
//$kart = "http://www.newzelenograd.ru$kart";   // загоняем в понятную для человека переменную



// парсим номер квартиры
$numkv = _pars('№ ', '</a>', $st[$i]);
//echo "Номер квартиры: $numkv<br>";                                                                     // $numkv -  номер квартиры




//  --------- кол-во комнат/студия-----------


$st1 = explode('<td>', $st[$i]); 
$korp = $st1[3];                                                                                    // $korp - корпус
$korp = str_replace ('</td>', '', $korp);
$korp = str_replace (' ', '', $korp);
$korp = str_replace ("\n", '', $korp);
$korp = str_replace ("\r\n", '', $korp);
//echo "Корпус: $st1[3]<br>";                                         
 
$sekc = $st1[4];                                                                                    // $sekc - секций
$sekc = str_replace ('</td>', '', $sekc);
$sekc = str_replace ("\n", '', $sekc);
$sekc = str_replace ("\r\n", '', $sekc);
$sekc = str_replace (' ', '', $sekc);
//echo "Секций: $st1[4]<br>"; 

$komnat = $st1[5];                                                                                    // $komnat - комнат
$komnat = str_replace ('</td>', '', $komnat);
$komnat = str_replace (' ', '', $komnat);
$komnat = str_replace ("\n", '', $komnat);
$komnat = str_replace ("\r\n", '', $komnat);
//echo "Комнат: $st1[5]<br>"; 

$etaj = $st1[6];                                                                                    // $etaj - этаж
$etaj = str_replace ('</td>', '', $etaj);
$etaj = str_replace (' ', '', $etaj);
$etaj = str_replace ("\n", '', $etaj);
$etaj = str_replace ("\r\n", '', $etaj);
//echo "Этаж: $st1[6]<br>"; 

$obplosh = $st1[7];                                                                                    // $obplosh - общ площадь
$obplosh = str_replace ('</td>', '', $obplosh);
$obplosh = str_replace (' ', '', $obplosh);
$obplosh = str_replace ("\n", '', $obplosh);
$obplosh = str_replace ("\r\n", '', $obplosh);
//echo "Общ. площадь: $st1[7]<br>"; 

$plosh = $st1[8];                                                                                    // $plosh - площадь жилая
$plosh = str_replace ('</td>', '', $plosh);
$plosh = str_replace (' ', '', $plosh);
$plosh = str_replace ("\n", '', $plosh);
$plosh = str_replace ("\r\n", '', $plosh);
//echo "Площадь жилая: $st1[8]<br>"; 

$kuch = $st1[9];                                                                                    // $kuch - кухня
$kuch = str_replace ('</td>', '', $kuch);
$kuch = str_replace (' ', '', $kuch);
$kuch = str_replace ("\n", '', $kuch);
$kuch = str_replace ("\r\n", '', $kuch);
//echo "Кухня: $st1[9]<br>"; 



// создаем уникальный ID
$unid = "$numkv$korp$komnat$etaj$plosh";
$unid = str_replace (' ', '', $unid);
$unid = str_replace ('</td>', '', $unid);
$unid = str_replace("\n", "", $unid);
$unid = str_replace("\r\n", "", $unid);
//echo "$unid<br>";
//echo "<br>";
  
// текст нашего XML

$ttext .= " 
<offer>
<build-name>Новый Зеленоград</build-name>
<korps>
<korp>
<num>$korp</num>
<rooms>
<room offer_id='$unid'>
<id>$unid</id>
<number>$numkv</number>
<num>$komnat</num>
<section>$sekc</section>
<floor>$etaj</floor>
<square>$obplosh</square>
<price>$cena</price>
<living_space>$plosh</living_space>";

if ($kuch > 0)  {$ttext .= "<square_kitchen>$kuch</square_kitchen>";} 

$ttext .= "
<link>$ssil</link>
<image tag='plan'>$bigimg</image>";

if ($bigimg1 != "")  {$ttext .= "<image tag='3dplan'>$bigimg1</image>";}

$ttext .= "
</room>
</rooms>
</korp>
</korps>
<images>";
// добавляем фото отделки
for ($r = 1; $r < 25; $r++) { 
if ($endfot[$r] != "")  {$ttext .= "
<image tag='renovation'>http://www.newzelenograd.ru$endfot[$r]</image>";}

}



// добавляем фото стройки
if ($endfotorama[1] != "")  {$ttext .= "
<image tag='dynamics' month='$nummess[$tmess]' year='$tyear'>http://www.newzelenograd.ru$endfotorama[1]</image>";}
if ($endfotorama[2] != "")  {$ttext .= "
<image tag='dynamics' month='$nummess[$tmess]' year='$tyear'>http://www.newzelenograd.ru$endfotorama[2]</image>";}
if ($endfotorama[3] != "")  {$ttext .= "
<image tag='dynamics' month='$nummess[$tmess]' year='$tyear'>http://www.newzelenograd.ru$endfotorama[3]</image>";}
$ttext .= "
</images>
</offer>";

}  // конец цикла


$ttext.= "
</offers>";
// сохраняем всю вкуснятину в XML
$fp = fopen("test.xml","w"); //w+ это режим записи файла 
$test = fwrite($fp, $ttext); // Запись в файл
fclose($fp);
echo "<script type=\"text/javascript\">
document.getElementById('p1').innerHTML='Загрузка ... завершена.';
</script>";

echo "<a href=\"test.xml\" target=\"_blank\">Ссылка на XML</a>";


}  // окончание нажатия кнопки


?>

</body>    
    
</html>