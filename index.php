<?php

include_once('telegram_bot.php');
$token = '1028870597:AAFW0zL8IrVDBSDiEKESe__Op5rOClEKF7Q';
$times = 0;

$bot = new TelegramBot($token);

while($times < 15)
{
    $msg = $bot->getUpdates();
    
    $last_msg = $msg[count($msg) - 1]['update_id'];
    $bot->setLastUpdate($last_msg + 1);

    $chat_id = $msg[count($msg) - 1]['message']['chat']['id'];
    $msg_txt = $msg[count($msg) - 1]['message']['text'];
    
    if(preg_match('#^TELCEL[,. ]+(50|100|150|200|300|500)[., ]+([0-9]{10})[., ]+([0-9]{4}) *#', $msg_txt, $m_MATCHES))
    {
        $bot->sendChatAction($chat_id);
        //sleep(2);
        $monto = $m_MATCHES[1];
        $destino = $m_MATCHES[2];
        $nip = $m_MATCHES[3];

        $bot->sendMessage($chat_id, "Haz solicitado una recarga de \${$monto} al numero {$destino}, nip {$nip}");
    }
    
    $times++;
    sleep(5);
}

?>
