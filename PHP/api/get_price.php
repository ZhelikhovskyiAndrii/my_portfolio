<?php
# Документация: https://public.omega.page/swagger/ui/index
# Ключ ZTsppsfTTsZDGQkHDKJYLmQNblT4NqNK
#curl -H "Content-Type: application/json" -X POST "https://public.omega.page/public/api/v1.0/price/downloadPrice" -d " { "Key": "ZTsppsfTTsZDGQkHDKJYLmQNblT4NqNK", "Id": 0, }"
#Получить список прайсов:
#curl -H "Content-Type: application/json" -X POST "https://public.omega.page/public/api/v1.0/price/getPrices" -d " { "Key": "ZTsppsfTTsZDGQkHDKJYLmQNblT4NqNK", }"
/*
curl -X POST -H "Key: RQzSCdY99JgV0kMtSYx0KTcE9bdWmhai" -H "Content-Type: application/json" -d '{"Key":"RQzSCdY99JgV0kMtSYx0KTcE9bdWmhai"}' https://public.omega.page/public/api/v1.0/profile/account
curl -X POST -H "Content-Type: application/json" -d '{"Key":"RQzSCdY99JgV0kMtSYx0KTcE9bdWmhai"}' https://public.omega.page/public/api/v1.0/profile/account

/************************  !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!***********************************
curl -X POST -H "Content-Type: application/json" -d '{ "Key": "ZTsppsfTTsZDGQkHDKJYLmQNblT4NqNK"}' https://public.omega.page/public/api/v1.0/price/getPrices
/************************  !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!***********************************
Использую прайс для сайта  {"Name":"Прайс для інтернет-магазинів за складами, формується ~60 с (Новий)","Id":24,"Status":"Default"},
можно и этот  {"Name":"Прайс для інтернет-магазинів, формується ~60 с (Новий)","Id":19,"Status":"Default"},

Возвращает описание товара:
curl -X POST -H "Content-Type: application/json" -d '{ "Key": "ZTsppsfTTsZDGQkHDKJYLmQNblT4NqNK", "ProductIdList": [-121114] }' https://public.omega.page/public/api/v1.0/product/details

curl -X POST -H "Content-Type: application/json"  -d '{ "Key": "ZTsppsfTTsZDGQkHDKJYLmQNblT4NqNK", "ProductId": -121114, "Number": 1 }' https://public.omega.page/public/api/v1.0/product/image

*/

function form_price()
{
    //Ссылка для запуска формирования прайса, время ожидания 60-90 сек
    $url = 'https://public.omega.page/public/api/v1.0/price/enqueuePrice';
    // API ключ
    $api_key = 'ZTsppsfTTsZDGQkHDKJYLmQNblT4NqNK';
    // Параметры запроса
    $params = array(
    'Key' => 'ZTsppsfTTsZDGQkHDKJYLmQNblT4NqNK',
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
    $url = 'https://public.omega.page/public/api/v1.0/price/downloadPrice';
    // API ключ

    $api_key = 'ZTsppsfTTsZDGQkHDKJYLmQNblT4NqNK';
    // Параметры запроса
    $params = array(
    'Key' => 'ZTsppsfTTsZDGQkHDKJYLmQNblT4NqNK',
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
