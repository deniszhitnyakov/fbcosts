<?php
//
// Настройки экспорта статистики в Binom
//
$binom = 0;                 // Отправлять данные в Binom? 1 - да, 0 - нет 
$binom_api_key = '';        // API ключ бинома
$binom_domain = 'mybinom.ru'; // Домен, на котором установлен трекер. Писать без http:// и прочей херни!!!
$binom_subid = '1';
$binom_campaigns = '1';


//
// Настройки экспорта статистики в Keitaro
//
$keitaro = 1;               // Отправлять данные в Keitaro? 1 - да, 0 - нет
$keitaro_api_key = '';      // API ключ Keitaro
$keitaro_domain = 'keitaro.ru';       // Домен, на котором установлен трекер. Писать без http://
$keitaro_subid = '1';
$keitaro_campaigns = '1';

$keitaro_currency = 'RUB';
$keitaro_timezone = 'Europe/Moscow';
?>