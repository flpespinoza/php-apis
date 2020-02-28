<?php

include_once('telegram_bot_v3.php');
//1087997685:AAEcieYMMY7v7ZQbwAOEe4rMZwba2MadpTw
//1028870597:AAFW0zL8IrVDBSDiEKESe__Op5rOClEKF7Q
$token = '1087997685:AAEcieYMMY7v7ZQbwAOEe4rMZwba2MadpTw';
$times = 0;

try
{
    $bot = new TelegramBot($token);
}
catch(Exception $e)
{
    throw new Exception($e->getMessage());
}

//$listaUsuarios = array('1018428163' => '3315745279', '1018428161' => '3315745278', '1018428162' => '3315745277' );
$adminBot = "1018428160";

while($times < 60)
{
    $bot->getMessage();
    $mensaje = $bot->getCurrentMessage();
    if(count($mensaje))
    {
        $txt = $mensaje['text'];
        if($phone = $bot->getUserPhone())
        {
            $bot->sendMessageToChat($adminBot, $txt);
            $bot->sendMessage("Respuesta a mensaje: {$txt}");
        }
        elseif(isset($mensaje['contact']))
        {
            $phone = substr($mensaje['contact']['phone_number'], 2);
            $bot->setUserPhone($phone);
            $bot->sendMessage("Usuario registrado correctamente");
        }
        else
        {
            $bot->requestUserData();
        }
    }
    $times++;
    sleep(1);
}

?>
