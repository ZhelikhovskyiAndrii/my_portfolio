<?php
function send_bot($message) {
// Токен Telegram бота
$botToken = '*************';
//Бот t.me/cross_corp_bot
// Установите ID чата, куда вы хотите отправить сообщение
$chatId = '*********'; // Это может быть ID вашего личного чата с ботом или ID чата с группой/каналом

// Формируем URL для API запроса
$apiUrl = "https://api.telegram.org/bot$botToken/sendMessage";

// Текст сообщения, которое вы хотите отправить
//$message = "Привет, это сообщение из PHP скрипта для Telegram бота!";

// Формируем параметры для POST-запроса к API
$params = [
    'chat_id' => $chatId,
    'text' => $message,
];

// Инициализируем cURL сессию
$ch = curl_init();

// Устанавливаем опции для cURL сессии
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Выполняем запрос к API Telegram Bot
$response = curl_exec($ch);

// Закрываем cURL сессию
curl_close($ch);

// Печатаем ответ от API (для дебага)
//echo $response;
}
