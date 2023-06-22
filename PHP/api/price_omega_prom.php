<?php
/* 
Документация: https://jrklein.com/2021/05/04/format-numbers-and-dates-in-box-spout-excel-spreadsheets/

*/
require_once '/home/bitrix/www/scripts/api/omega/spout/spout-master/src/Spout/Autoloader/autoload.php';    
require_once 'db_conn.php';

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

$link = new mysqli($host, $user, $password, $database); //соединение с базой
$link->set_charset("utf8"); // Выбираем кодировку UTF-8

if (!$link) {
            die("Connection failed: " . mysqli_connect_error(). "<br>");
            } echo "Connected successfully", "\n"; 

$result = mysqli_query($link, 
        "SELECT brand, cod_omega, article, name_omega, price, remainder_zp 
        FROM omega_price") 
        or die('Запрос не выполнен!');

  
  // Создать новый объект Writer для формата XLSX
  $writer = WriterEntityFactory::createXLSXWriter();
  
  // Открыть поток для записи
  //$writer->openToBrowser('export_2.xlsx');
  $writer->openToFile('export3.xlsx');

  // Написать заголовки колонок
  $writer->addRow(WriterEntityFactory::createRowFromArray(array('Код_товара', 'Название_позиции', 'Цена', 'Наличие', 'Уникальный_идентификатор', 'Производитель', 'Название_Характеристики', 'Значение_Характеристики')));
  
  // Написать данные
  if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {

/*------------*/
if ($row['remainder_zp']==NULL or $row['remainder_zp']==0) {
        $row['price']= "";
    }
    else{
        if ($row['price']<800){
            $row['price']=round($row['price']*1.4, 0);
        } else {
            $row['price']=round($row['price']*1.3, 0);      
        }
    }
  if ($row['remainder_zp']==NULL or $row['remainder_zp']==0) {   //Наличие
    $row['remainder_zp']="0";}
    else $row['remainder_zp']= "+";



/*----------------------------------*/
          $writer->addRow(WriterEntityFactory::createRowFromArray(array($row['article'], $row['name_omega'], $row['price'], $row['remainder_zp'], $row[''], $row['brand'], 'Код запчастини', $row['cod_omega'] )));
      }
  } 
  
  // Закрыть поток для записи
  $writer->close();
  $link->close(); // закрываем соединение с базой
