<?php

include_once('telegram_bot_v2.php');
//1087997685:AAEcieYMMY7v7ZQbwAOEe4rMZwba2MadpTw
//1028870597:AAFW0zL8IrVDBSDiEKESe__Op5rOClEKF7Q
$token = '1028870597:AAFW0zL8IrVDBSDiEKESe__Op5rOClEKF7Q';
$times = 0;

$bot = new TelegramBot($token);
while($times < 30)
{
    $bot->getMessage();
    $currentMsg = $bot->getCurrentMessage();

    if(!empty($currentMsg))
    {
       if($bot->getMesageType() == 'text')
       {
           if($currentMsg['text'] == '/register')
           {
                if(!$bot->getUserPhone())
                {
                    $req = $bot->requestUserData();
                }
                else
                {
                    $bot->sendMessage('Tu usuario ya esta registrado con numero: ' . $bot->getUserPhone());
                } 
           }
           else
           {
                $bot->sendMessage("Respuesta a mensaje: {$currentMsg['text']}");
           }       
       }
       else
       {
            $bot->setUserPhone($currentMsg['contact']['phone_number']);                
       } 
    }

    $times++;
    sleep(1);
}

?>
