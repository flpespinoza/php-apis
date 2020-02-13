<?php

include_once('telegram_bot.php');
$token = '1028870597:AAFW0zL8IrVDBSDiEKESe__Op5rOClEKF7Q_';

try
{
    $bot = new TelegramBot($token);
    var_dump($bot);
}
catch(Exception $e)
{
    echo $e->getMessage() . "\n";
}


?>
