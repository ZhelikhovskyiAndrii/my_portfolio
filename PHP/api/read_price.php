<?php
require_once '/home/bitrix/www/scripts/api/omega/spout/spout-master/src/Spout/Autoloader/autoload.php';
require_once __DIR__.'/db_conn.php';
//require_once 'vendor/autoload.php';

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

$vowels = "'";
$vowels1 = ">";

$sheetIndex = 0; // выбранный индекс листа
$currentSheetIndex = 0; // текущий индекс листа

$filePath =__DIR__.'/ExcelPriceForeignCarNewWarehouseApi.xlsx';
$reader = ReaderEntityFactory::createXLSXReader();
$reader->open($filePath);
//$reader->setCurrentSheet(1); // выбираем второй лист (индекс начинается с 0)

$link = new mysqli($host, $user, $password, $database); //соединение с базой
$link->set_charset("utf8"); // Выбираем кодировку UTF-8

if (!$link) {
            die("Connection failed: " . mysqli_connect_error(). "<br>");
            } echo "Connected successfully", "\n"; 

// Очищаем таблицу
echo 'Очищаю таблицу', "\n";
echo date(DATE_RFC822), "\n";

$sql='TRUNCATE TABLE omega_price';
if (mysqli_query($link, $sql)) {
      echo "Records deleted successfully", "\n";
    } else {
      die ("Error deleting record: " . mysqli_error($link));
    }

foreach ($reader->getSheetIterator() as $sheet) {
  if ($sheet->getIndex() === 1) { // index is 0-based
  echo  $sheetName . "\n";

    foreach ($sheet->getRowIterator() as $row) {
        $value = $row->toArray();
        //$name = $value[0];
           
        $brand = str_replace($vowels, "_", $value[0]); //brand
        $cod_omega = str_replace($vowels, "_", $value[1]); //cod_omega
        $article = str_replace($vowels, "_", $value[2]); //article
        $name = str_replace($vowels, "_", $value[3]); //найти и заменить кавычи в наименовании
        $price = str_replace($vowels, "_", $value[6]); //price
        $productid = str_replace($vowels, "_", $value[10]); //productId
        $remainder_zp = str_replace($vowels1, "", $value[11]); //остатки в запорожье
        
        if ($brand=='Elring' /*and $remainder_zp>0*/) {    
      /*   echo $brand . "\n";
          echo $cod_omega . "\n";
          echo $article . "\n";
          echo $name . "\n";
          echo $price . "\n";
          echo $productid . "\n";
          echo $remainder_zp . "\n";
          echo "-------------------" . "\n";
  */
          $sql = "INSERT INTO `omega_price` (`brand`,`cod_omega`,`article`,`name_omega`,`price`,`productid`,`remainder_zp`) VALUES ('$brand','$cod_omega','$article','$name','$price','$productid','$remainder_zp')";  
      
          if ($link->query($sql) === TRUE) {
            $total_chunks++;
              //echo "New record created successfully", "\n";
          } else {
              echo "Error: " . $sql . "\n" . $link->error;
          } 
        }

    }
    break; // no need to read more sheets
  }
}
echo "Total chunks uploaded: " . $total_chunks . "\n";
echo date(DATE_RFC822). "\n";
//print_r ($id);
// Close file and database connection
$reader->close();
$link->close();
