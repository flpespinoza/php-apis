<?php

include_once('telegram_bot.php');
$token = '1028870597:AAFW0zL8IrVDBSDiEKESe__Op5rOClEKF7Q';
$times = 0;

$bot = new TelegramBot($token);

while($times < 15)
{
    $msg = $bot->getUpdates();
    file_put_contents('updates.json', "Vuelta {$times}: \r\n" . json_encode($msg, JSON_PRETTY_PRINT) . "\r\n", FILE_APPEND);
    
    $last_msg = $msg[count($msg) - 1]['update_id'];
    $bot->setLastUpdate($last_msg + 1);

    $chat_id = $msg[count($msg) - 1]['message']['chat']['id'];
    $msg_txt = $msg[count($msg) - 1]['message']['text'];

    switch($msg_txt)
    {
        case '/venta':
            $bot->sendMessage($chat_id, 'Solicitud de venta');
            break;
        case '/compra':
            $bot->sendMessage($chat_id, 'Solicitud compra');
            break;
        default:
            $bot->sendMessage($chat_id, 'Se recibio el texto ' . $msg_txt);
            break;
    }
   
    $times++;
    sleep(5);
}

?>
