<?php
/*
Скрипт будет корректировать данные в таблице "Продажи за последний год"
 * будет удалять данные старше года и ежедневно догрудать данные по продажам за этот год 
 *  */
require_once 'db_conn.php';
require_once __DIR__.'/../phpexcel/Classes/PHPExcel.php';
$inputFileName =__DIR__.'/../../../upload/reports/sales_purchase (XLSX).xlsx'; // подключаем данные с продажами за этот год
$inputFileType = 'Excel2007';
// Проверяем наличие файла, если еть, то загружаем
$vowels = "'";
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
            
  // Удаляем данные старше года от сегодняшнего дня          
    $sql ="DELETE FROM `sales_purchase_last_year` WHERE `date_sales` < DATE_SUB(NOW(),INTERVAL 1 YEAR)";
    
   // $result = mysqli_query($link, $sql) or die("Ошибка " . mysqli_error($link)); 
   // mysqli_close($link);
    
    if ($link->query($sql) === TRUE) {
        echo "Records deleted successfully1", "\n";
    } else {
        //echo "Error: " . $sql . "\n" . $link->error;
        die ("Error deleting record: " . mysqli_error($link));
    } 
    
    // удаляем данные за этот год
   $sql ="DELETE FROM `sales_purchase_last_year` WHERE YEAR(date_sales) = YEAR(NOW())";
    if ($link->query($sql) === TRUE) {
        echo "Records deleted successfully2", "\n";
    } else {
        die ("Error deleting record: " . mysqli_error($link));
    } 
    
//вставляем обновленные данные за текущий год.    
    
            
foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) // цикл обходит страницы файла
{
  $highestRow = $worksheet->getHighestRow(); // получаем количество строк
  $highestColumn = $worksheet->getHighestColumn(); // а так можно получить количество колонок

  for ($row = 7; $row <= $highestRow; ++ $row) // обходим все строки начиная со 2 (пропускаем шапку)
  {
    $date_sales = $worksheet->getCellByColumnAndRow(0, $row); //date
    $date_sales=date( "Y-m-d", strtotime($date_sales));
    $cell2 = $worksheet->getCellByColumnAndRow(3, $row); //cod_1c
    $cell3 = $worksheet->getCellByColumnAndRow(4, $row); //article
    $cell4 = $worksheet->getCellByColumnAndRow(6, $row); //brend
    $cell4 = str_replace($vowels, "_", $cell4);
    $cell5 = $worksheet->getCellByColumnAndRow(7, $row); //name
    $cell5 = str_replace($vowels, "_", $cell5); //найти и заменить кавычи в наименовании
    $cell6 = $worksheet->getCellByColumnAndRow(8, $row); //Cod_group_1C
    $cell7 = $worksheet->getCellByColumnAndRow(9, $row); //group1
    $cell8 = $worksheet->getCellByColumnAndRow(10, $row); //manager
    $cell9 = $worksheet->getCellByColumnAndRow(11, $row); //provider
    $cell10 = $worksheet->getCellByColumnAndRow(12, $row); //customer_source
    $cell11 = $worksheet->getCellByColumnAndRow(13, $row); //quantity
    $cell12 = $worksheet->getCellByColumnAndRow(14, $row); //sale
    
    
   $sql = "INSERT INTO `sales_purchase_last_year` (`date_sales`,`cod_1C`,`article`,`brand`,`name`,`Cod_group_1C`,`group1`,`manager`,`provider`,`customer_source`,`quantity_year`,`sale_year`) VALUES
('$date_sales','$cell2','$cell3','$cell4','$cell5','$cell6','$cell7','$cell8','$cell9','$cell10','$cell11','$cell12')";  

   if ($link->query($sql) === TRUE) {
        //echo "New record created successfully", "\n";
    } else {
        echo "Error: " . $sql . "\n" . $link->error;
    } 
  }
}
$link->close();// закрываем соединение с базой
//unlink("../upload/sale/sales.xlsx"); //удаляем файл после загрузки
 