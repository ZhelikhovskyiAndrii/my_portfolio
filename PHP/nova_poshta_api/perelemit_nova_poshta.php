<?php
// Скрипт позволяет по api получить суммы отправок посылок за текущий квартал и отправить сообщение в телеграмм бот
// Документация https://developers.novaposhta.ua/view/model/a90d323c-8512-11ec-8ced-005056b2dbe1/method/a9d22b34-8512-11ec-8ced-005056b2dbe1
// document https://www.youtube.com/watch?v=nKsJQCYHOpI&list=PLhgRAQ8BwWFbvbcVab_KKMVYpeI__qKQK&index=2
require_once 'send_to_bot.php';
require '../../../../vendor/autoload.php';
use GuzzleHttp\Client;

// Установите API-ключ, полученный от Новой Почты
$apiKey = '****************';

// Определение периода квартала (например, 1 квартал 2023 года)
// Получение текущей даты
$currentDate = date('Y-m-d');

// Определение текущего месяца
$currentMonth = date('n'); // Число месяца без ведущего нуля

// Определение номера квартала
$quarter = ceil($currentMonth / 3);

// Определение даты начала квартала
$startQuarter = str_replace('-','.',date('d-m-Y', mktime(0, 0, 0, ($quarter - 1) * 3 + 1, 1)));
// Определение даты конца квартала
$endQuarter = str_replace('-','.',date('d-m-Y', strtotime("$startQuarter +3 months -1 day")));


function get_sum ($api_key, $start_period, $end_period) {
// Параметры запроса к API для получения списка накладных за квартал
$apiKey = $api_key;
$startQuarter = $start_period;
$endQuarter = $end_period;
// URL для API сервера Новой Почты
$url = 'https://api.novaposhta.ua/v2.0/json/';
// Создание объекта Guzzle HTTP клиента
$client = new \GuzzleHttp\Client();

$params = [
    'apiKey' => $apiKey,
    'modelName' => 'InternetDocument',
    'calledMethod' => 'getDocumentList',
    'methodProperties' => [
        'DateTimeFrom' => $startQuarter,
        'DateTimeTo' => $endQuarter,
        'GetFullList'=> '1',
    ],
];

try {
    // Выполнение POST-запроса к API
    $response = $client->post($url, [
        'json' => $params,
    ]);

    // Получение данных из ответа
    $data = $response->getBody()->getContents();
    $result = json_decode($data, true);
    $price_sum=0;
    // Обработка полученных данных
    if ($result['success']) {
        $waybills = $result['data'];
        // Обработка полученных накладных
        foreach ($waybills as $waybill) {
            $numberTTN = $waybill['IntDocNumber'];
            $date = $waybill['DateTime'];
            $price = $waybill['BackwardDeliveryMoney'];
            // Дальнейшая обработка данных...
          //  echo $price.' - '.$numberTTN.' - '.$date, "\n";
            $price_sum=$price_sum+$price;
        }
          //echo $price_sum;
    } else {
        echo 'Ошибка при получении данных: ' . $result['errors'][0];
    }

} catch (\GuzzleHttp\Exception\RequestException $e) {
    echo 'Ошибка при выполнении запроса: ' . $e->getMessage();
    }
return $price_sum;

}

$message = 'Рух коштів по акаунтам Нової пошти за '."\n".
           'період з '.$startQuarter.' по '.$endQuarter.". \n".
           'Ліміт 500 000 грн. в квартал на акаунт'.". \n";
$message .= 'По акаунту solokros : '.get_sum ($apiKey, $startQuarter, $endQuarter)." грн. \n";

//Отправляем сообщение в бот
send_bot ($message);
