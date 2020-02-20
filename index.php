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

    $chat_id = $msg[count($msg) - 1]['message']['chat']['id'];
    $msg_txt = $msg[count($msg) - 1]['message']['text'];
    
    if($msg_txt == '/start')
    {
        $bot->sendChatAction($chat_id);
        //sleep(2);

        $markup = array
        (
            'keyboard' => array
            (
                array
                (
                    array
                    (
                        'text' => 'Registrar', 
                        'request_contact' => true
                    )
                )
            ),
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        );
        $res = $bot->sendMessage($chat_id, "Registra tu numero presionando el boton", $markup);
        var_dump($res);
    }
    
    $times++;
    sleep(1);
}

?>
