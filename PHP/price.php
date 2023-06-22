<?php
/*
 * Данный скрипт загружает остатки по складам
 * раз в месяц (последнее число месяца) для отслеживания тенденции изменения остатков компании
 */
require_once __DIR__.'/../db_conn.php';
require_once __DIR__.'/../../phpexcel/Classes/PHPExcel.php';
$inputFileName =__DIR__.'/../../../../upload/reports/price_ftp (XLSX).xlsx';
$inputFileType = 'Excel2007';
$vowels = "'"; // переменная используется для хранения спец символов.
// Проверяем наличие файла, если еть, то загружаем
try {
    $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($inputFileName);
} catch (Exception $e) {
    die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME)
        . '": ' . $e->getMessage());
}
$link = new mysqli($host, $user, $password, $database); //соединение с базой
$link->set_charset("utf8"); // Выбираем кодировку UTF-8

if (!$link) {
            die("Connection failed: " . mysqli_connect_error(). "<br>");
            } echo "Connected successfully", "\n"; 

$sql='TRUNCATE TABLE price';
if (mysqli_query($link, $sql)) {
      echo "Records deleted successfully", "\n";
   } else {
      die ("Error deleting record: " . mysqli_error($link));
   }
            
            
foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) // цикл обходит страницы файла
{
  $highestRow = $worksheet->getHighestRow(); // получаем количество строк
  $highestColumn = $worksheet->getHighestColumn(); // а так можно получить количество колонок

  for ($row = 3; $row <= $highestRow; ++ $row) // обходим все строки начиная со 2 (пропускаем шапку)
  {
    $date_reports=date( "Y-m-d", strtotime($worksheet->getCellByColumnAndRow(0, $row)));//date
    $cod_1c = $worksheet->getCellByColumnAndRow(1, $row); //cod_1c
    $article = $worksheet->getCellByColumnAndRow(2, $row); //article
    $brand = str_replace($vowels, "_", $worksheet->getCellByColumnAndRow(3, $row));//brend
    $group1 = $worksheet->getCellByColumnAndRow(4, $row); //group1
    $name = str_replace($vowels, "_", $worksheet->getCellByColumnAndRow(5, $row)); //name и найти и заменить кавычи в наименовании
    $purchasing = $worksheet->getCellByColumnAndRow(6, $row); //цена закупки
    $retail = $worksheet->getCellByColumnAndRow(8, $row); //цена продажи
    
        
    $sql = "INSERT INTO `price` (`date_reports`,`cod_1c`,`article`,`brand`,`group1`,`name`,`purchasing`,`retail`) VALUES
('$date_reports','$cod_1c','$article','$brand','$group1','$name','$purchasing','$retail')";  

   if ($link->query($sql) === TRUE) {
        //echo "New record created successfully", "\n";
    } else {
        echo "Error: " . $sql . "\n" . $link->error;
    } 
  }
}

$link->close();// закрываем соединение с базой
//unlink("../upload/sale/sales.xlsx"); //удаляем файл после загрузки
 