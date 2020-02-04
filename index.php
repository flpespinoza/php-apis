<?php
$token = "1037265066:AAEbkhRtehrvTogzZY87ksYl0JB3ElPZEMc";
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

echo $result;
?>