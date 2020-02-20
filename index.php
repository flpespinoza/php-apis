<?php

include_once('telegram_bot.php');
//1087997685:AAEcieYMMY7v7ZQbwAOEe4rMZwba2MadpTw
//1028870597:AAFW0zL8IrVDBSDiEKESe__Op5rOClEKF7Q
$token = '1028870597:AAFW0zL8IrVDBSDiEKESe__Op5rOClEKF7Q';
$times = 0;

$bot = new TelegramBot($token);

while($times < 50)
{
    $msg = $bot->getUpdates();
    $msg = $msg[count($msg) - 1];
    
    $last_msg = $msg['update_id'];
    $bot->setLastUpdate($last_msg + 1);

    $msg_type = $bot->getMsgType($msg);
    $uid = $msg['message']['from']['id'];
    $chat_id = $msg['message']['chat']['id'];
    
    if($msg_type == 'register')
    {    
        $phone = $msg['message']['contact']['phone_number'];
        $bot->setUserPhone($uid, $phone);
        $bot->sendMessage($chat_id, "Se ha registrado el numero {$phone}");
    }
    else
    {
        $msg_txt = $msg['message']['text'];
        if($msg_txt == '/start' || $msg_txt == '/restart')
        {
           $bot->sendMessage($chat_id, "Bienvenido, para comenzar envia el mensaje /registrar"); 
        }
        else if($msg_txt == '/registrar')
        {
            $bot->requestUserData($chat_id);
        }
        else
        {
            $phone = $bot->getUserPhone($uid);
            if(!$phone)
            {
                $bot->requestUserData($chat_id);
            }
            else
            {
                $bot->sendMessage($chat_id, "Respuesta a mensaje: {$msg_txt}, telefono de usuario {$phone}");
            }
        }
    }
       
    $times++;
    sleep(1);
}

?>
