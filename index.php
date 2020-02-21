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
           if($currentMsg['text'] == '/start')
           {
                $bot->sendMessage('Bienvenido, para comenzar enviar el mensaje /register');
           }
           elseif($currentMsg['text'] == '/register')
           {
                if(!$bot->getUserPhone())
                {
                    $bot->requestUserData();
                }
                else
                {
                    $bot->sendMessage('Tu usuario ya esta registrado con numero: ' . $bot->getUserPhone());
                } 
           }
           else
           {
               if(!$bot->getUserPhone())
                {
                    $bot->requestUserData("Tu usuario no esta registrado, para hacerlo envia el mensaje /register");
                }
                else
                {
                    if(obtener_venta_mensaje($currentMsg['text']))
                    {
                        $bot->sendMessage("Tu solicitud de venta esta siendo atendida", true);
                    }
                    else
                    {
                        $bot->sendMessage("Sintaxis de venta incorrecta", true);
                    }
                }      
           }       
       }
       else
       {
            $bot->setUserPhone($currentMsg['contact']['phone_number']);
            $bot->sendMessage("Tu usuario ha sido registrado correctamente");                
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
