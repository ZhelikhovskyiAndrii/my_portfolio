<?php
# Документация: https://public.omega.page/swagger/ui/index

function form_price()
{
    //Ссылка для запуска формирования прайса, время ожидания 60-90 сек
    $url = 'https://xxxxx';
    // API ключ
    $api_key = 'xxxx';
    // Параметры запроса
    $params = array(
    'Key' => 'xxxxx',
    'Id' => '24'
    );

    $ch = curl_init();

    // Установка URL-адреса для загрузки файла
    curl_setopt($ch, CURLOPT_URL, $url);

    // Установка CURLOPT_RETURNTRANSFER в true
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Установка заголовков авторизации и Content-Type
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer ' . $api_key,
    'Content-Type: application/json'
    ));

    // Установка метода запроса на "POST"
    curl_setopt($ch, CURLOPT_POST, true);

    // Установка данных запроса
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));

    // Выполнение запроса и получение ответа

    $response = curl_exec($ch);
    // Закрытие сеанса cURL

    curl_close($ch);
    // Проверка на наличие ошибок
    
    if(curl_errno($ch)){
        return 'Ошибка выполнения запроса: ' . curl_error($ch);
    } else {
        return 'Запрос выполнен, ожидаем формирование прайса 180 сек. ';
    }
}    

function get_price()
{
    // URL для загрузки файла
    $url = 'https://xxxx';
    // API ключ

    $api_key = 'xxxxxx';
    // Параметры запроса
    $params = array(
    'Key' => 'xxxxxx',
    'Id' => '24'
    );

    // Создание сеанса cURL
    $ch = curl_init();

    // Установка URL-адреса для загрузки файла
    curl_setopt($ch, CURLOPT_URL, $url);

    // Установка CURLOPT_RETURNTRANSFER в true
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Установка заголовков авторизации и Content-Type
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer ' . $api_key,
    'Content-Type: application/json'
    ));

    // Установка метода запроса на "POST"
    curl_setopt($ch, CURLOPT_POST, true);

    // Установка данных запроса
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));

    // Выполнение запроса и получение ответа
    $response = curl_exec($ch);

    // Закрытие сеанса cURL
    curl_close($ch);

    // Проверка на наличие ошибок
    if(curl_errno($ch)){
        return 'Ошибка выполнения запроса: ' . curl_error($ch);
    } else {
        // Сохранение файла на локальный диск
        file_put_contents('price.zip', $response);
        return ' Файл загружен ';
    }

}

function unzip_file( $file_path, $dest ){
	$zip = new ZipArchive;
	if( ! is_dir($dest) ) return 'Нет папки, куда распаковывать...';
	// открываем архив
	if( true === $zip->open($file_path) ) {
		 $zip->extractTo( $dest );
		 $zip->close();
		 return true;
	}
	else
		return 'Произошла ошибка при распаковке архива';
}

//Запускаем формирование прайса
echo form_price();
//Ожидаем пока прайс сформируется
sleep (180);
//скачиваем прайс
get_price();

$zipfile = 'price.zip'; // путь до файла архива
$pathdir = '/home/bitrix/www/scripts/api/omega/'; // путь к папке, в которую будет распакован архив
$done = unzip_file( $zipfile, $pathdir );
if( is_string($done) ){
	echo 'Ошибка: '. $done;
}
