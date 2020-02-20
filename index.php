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
    
    $last_msg = $msg[count($msg) - 1]['update_id'];
    $bot->setLastUpdate($last_msg + 1);

    $msg_type = $bot->getMsgType($msg[count($msg) - 1]);
    $chat_id = $msg[count($msg) - 1]['message']['chat']['id'];
    $msg_txt = $msg[count($msg) - 1]['message']['text'];

    if($msg_type == 'register')
    {
        $uid = $msg[count($msg) - 1]['message']['contact']['user_id'];
        $phone = $msg[count($msg) - 1]['message']['contact']['phone_number'];
        $bot->setUserPhone($uid, $phone);
        $bot->sendMessage($chat_id, "Se ha registrado el numero {$phone}");
    }
    else
    {
        if($msg_txt == '/start')
        {
            
        }
    }
       
    $times++;
    sleep(1);
}

?>
