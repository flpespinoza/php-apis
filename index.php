<?php
/*$token = "1028870597:AAFW0zL8IrVDBSDiEKESe__Op5rOClEKF7Q";
$url = "https://api.telegram.org/bot{$token}/sendmessage";
$params = ['chat_id' => 523315745279, 'text' => 'Prueba' ];
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
curl_close($ch);
echo $result;*/

include_once('telegram_bot.php');
$token = '1028870597:AAFW0zL8IrVDBSDiEKESe__Op5rOClEKF7Q';
$bot = new TelegramBot($token);

$up = $bot->getUpdates('261995969');
file_put_contents('updates.json', $up);

//$res = $bot->sendMessage('1018428160', 'prueba4');
//file_put_contents('updates.json', $res);
?>
