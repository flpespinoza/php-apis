<?php

include_once('telegram_bot_v2.php');
//1087997685:AAEcieYMMY7v7ZQbwAOEe4rMZwba2MadpTw
//1028870597:AAFW0zL8IrVDBSDiEKESe__Op5rOClEKF7Q
$token = '1028870597:AAFW0zL8IrVDBSDiEKESe__Op5rOClEKF7Q';
$times = 0;

$bot = new TelegramBot($token);
$listaUsuarios = array('1018428163' => '3315745279', '1018428161' => '3315745278', '1018428162' => '3315745277' );
while($times < 60)
{
    $bot->getMessage();
    $lastMsg = $bot->getCurrentMessage();
    if(!empty($lastMsg))
    {
       if($phone = $bot->getUserPhone())
       {
           $msgTxt = $lastMsg['text'];
           $fechaMsg = date('Y-m-d H:i:s', $lastMsg['message']['date']);
           $bot->sendChatAction();
           if(obtener_venta_mensaje($msgTxt))
           {   
               $bot->sendMessage("Tu venta: {$msgTxt} ha sido solicitada", true);
           }
           else
           {
               $bot->sendMessage("Formato de mensaje incorrecto");
           }
       }
       else
       {
           if(array_key_exists($bot->getCurrentChat(), $listaUsuarios))
           {
              $phone = $listaUsuarios[$bot->getCurrentChat()];
              $idTelegram = $bot->getCurrentChat();
              $bot->setUserPhone($phone);
              $bot->sendMessage("El usuario telegram {$idTelegram} se ha registrado con telefono {$phone} desde array"); 
           }
           elseif(isset($lastMsg['contact']))
           {
               $phoneUser  = $lastMsg['contact']['phone_number'];
               $idTelegram = $bot->getCurrentChat();
               
               $bot->setUserPhone($phoneUser);
               $bot->sendMessage("El usuario telegram {$idTelegram} se ha registrado con telefono {$phoneUser}");
           }
           else
           {
                $bot->requestUserData();
           }
       } 
    }

    $times++;
    sleep(1);
}

function obtener_venta_mensaje($e_MSG)
{
	switch($e_MSG)
	{
		case (preg_match('#^ *[V]([., ]+T)?[., ]+(10|20|30|50|100|150|200|300|500|1000)[., ]+([0-9]{10})[., ]+([0-9]{4}) *#', $e_MSG, $m_MATCHES) ? true : false): 
		case (preg_match('#^ *[V][., ]+TA[., ]+(10|20|30|50)[., ]+([0-9]{10})[., ]+([0-9]{4}) *#', $e_MSG, $m_MATCHES) ? true : false):
		case (preg_match('#^ *[V][., ]+TPA[., ]+(30|50|100|150|200|300|500)[., ]+([0-9]{10})[., ]+([0-9]{4}) *#', $e_MSG, $m_MATCHES) ? true : false):
		case (preg_match('#^ *[V][., ]+INT[., ]+(5|20|50|100|150|200|300|500)[., ]+([0-9]{10})[., ]+([0-9]{4}) *#', $e_MSG, $m_MATCHES) ? true : false):
		case (preg_match('#^ *[V][., ]+M[., ]+(10|20|30|50|60|70|80|100|120|150|200|250|300|400|500)[., ]+([0-9]{10})[., ]+([0-9]{4}) *#', $e_MSG, $m_MATCHES) ? true : false):
		case (preg_match('#^ *[V][., ]+I[., ]+(10|20|30|50|100|150|200|300|500|1000)[., ]+([0-9]{10})[., ]+([0-9]{4}) *#', $e_MSG, $m_MATCHES) ? true : false):
		case (preg_match('#^ *[V][., ]+U[., ]+(10|20|30|50|100|150|200|300|500|1000)[., ]+([0-9]{10})[., ]+([0-9]{4}) *#', $e_MSG, $m_MATCHES) ? true : false):
		case (preg_match('#^ *[V][., ]+N[., ]+(30|50|100|200|500)[., ]+([0-9]{10})[., ]+([0-9]{4}) *#', $e_MSG, $m_MATCHES) ? true : false):
		case (preg_match('#^ *[V][., ]+V[., ]+(20|30|40|50|100|150|200|300|500)[., ]+([0-9]{10})[., ]+([0-9]{4}) *#', $e_MSG, $m_MATCHES) ? true : false):
		case (preg_match('#^ *[V][., ]+C[., ]+(20|30|50|100|200|500)[., ]+([0-9]{10})[., ]+([0-9]{4}) *#', $e_MSG, $m_MATCHES) ? true : false):
		case (preg_match('#^ *[V][., ]+MT[., ]+(10|20|30|60|100|120|150|200|300|500)[., ]+([0-9]{10})[., ]+([0-9]{4}) *#', $e_MSG, $m_MATCHES) ? true : false):
		case (preg_match('#^ *[V][., ]+TU[., ]+(10|20|30|60|100|120|150|200|300|500)[., ]+([0-9]{10})[., ]+([0-9]{4}) *#', $e_MSG, $m_MATCHES) ? true : false):
            $venta = true;
            break;
		default:
			$venta = false;
			break;
			
	}
	
	return $venta;
	
}



?>
