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
                    $req = $bot->requestUserData();
                }
                else
                {
                    $bot->sendMessage('Tu usuario ya esta registrado con numero: ' . $bot->getUserPhone());
                } 
           }
           else
           {
                $bot->sendMessage("Respuesta a mensaje: {$currentMsg['text']}", true);
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


function obtener_venta_mensaje($e_MSG)
{
	$m_RESPUESTA = array();
	switch($e_MSG)
	{
		case (preg_match('#^ *[V]([., ]+T)?[., ]+(10|20|30|50|100|150|200|300|500|1000)[., ]+([0-9]{10})[., ]+([0-9]{4}) *#', $e_MSG, $m_MATCHES) ? true : false):
			$m_RESPUESTA = array('VENTA' => true, 'OPERADOR' => 'TELCEL', 'MONTO' => $m_MATCHES[2], 'DESTINO' => $m_MATCHES[3], 'NIP' => $m_MATCHES[4]);
			break; 
		case (preg_match('#^ *[V][., ]+TA[., ]+(10|20|30|50)[., ]+([0-9]{10})[., ]+([0-9]{4}) *#', $e_MSG, $m_MATCHES) ? true : false):
			$m_RESPUESTA = array('VENTA' => true, 'OPERADOR' => 'TELCEL_ALO', 'MONTO' => $m_MATCHES[1], 'DESTINO' => $m_MATCHES[2], 'NIP' => $m_MATCHES[3]);
			break;
		case (preg_match('#^ *[V][., ]+TPA[., ]+(30|50|100|150|200|300|500)[., ]+([0-9]{10})[., ]+([0-9]{4}) *#', $e_MSG, $m_MATCHES) ? true : false):
			$m_RESPUESTA = array('VENTA' => true, 'OPERADOR' => 'TELCEL_PA', 'MONTO' => $m_MATCHES[1], 'DESTINO' => $m_MATCHES[2], 'NIP' => $m_MATCHES[3]);
			break;
		case (preg_match('#^ *[V][., ]+INT[., ]+(5|20|50|100|150|200|300|500)[., ]+([0-9]{10})[., ]+([0-9]{4}) *#', $e_MSG, $m_MATCHES) ? true : false):
			$m_RESPUESTA = array('VENTA' => true, 'OPERADOR' => 'TELCEL_INT', 'MONTO' => $m_MATCHES[1], 'DESTINO' => $m_MATCHES[2], 'NIP' => $m_MATCHES[3]);
			break;
		case (preg_match('#^ *[V][., ]+M[., ]+(10|20|30|50|60|70|80|100|120|150|200|250|300|400|500)[., ]+([0-9]{10})[., ]+([0-9]{4}) *#', $e_MSG, $m_MATCHES) ? true : false):
			$m_RESPUESTA = array('VENTA' => true, 'OPERADOR' => 'MOVISTAR', 'MONTO' => $m_MATCHES[1], 'DESTINO' => $m_MATCHES[2], 'NIP' => $m_MATCHES[3]);
			break;
		case (preg_match('#^ *[V][., ]+I[., ]+(10|20|30|50|100|150|200|300|500|1000)[., ]+([0-9]{10})[., ]+([0-9]{4}) *#', $e_MSG, $m_MATCHES) ? true : false):
			$m_RESPUESTA = array('VENTA' => true, 'OPERADOR' => 'IUSACELL', 'MONTO' => $m_MATCHES[1], 'DESTINO' => $m_MATCHES[2], 'NIP' => $m_MATCHES[3]);
			break;
		case (preg_match('#^ *[V][., ]+U[., ]+(10|20|30|50|100|150|200|300|500|1000)[., ]+([0-9]{10})[., ]+([0-9]{4}) *#', $e_MSG, $m_MATCHES) ? true : false):
			$m_RESPUESTA = array('VENTA' => true, 'OPERADOR' => 'UNEFON', 'MONTO' => $m_MATCHES[1], 'DESTINO' => $m_MATCHES[2], 'NIP' => $m_MATCHES[3]);
			break;
		case (preg_match('#^ *[V][., ]+N[., ]+(30|50|100|200|500)[., ]+([0-9]{10})[., ]+([0-9]{4}) *#', $e_MSG, $m_MATCHES) ? true : false):
			$m_RESPUESTA = array('VENTA' => true, 'OPERADOR' => 'NEXTEL', 'MONTO' => $m_MATCHES[1], 'DESTINO' => $m_MATCHES[2], 'NIP' => $m_MATCHES[3]);
			break;
		case (preg_match('#^ *[V][., ]+V[., ]+(20|30|40|50|100|150|200|300|500)[., ]+([0-9]{10})[., ]+([0-9]{4}) *#', $e_MSG, $m_MATCHES) ? true : false):
			$m_RESPUESTA = array('VENTA' => true, 'OPERADOR' => 'VIRGIN', 'MONTO' => $m_MATCHES[1], 'DESTINO' => $m_MATCHES[2], 'NIP' => $m_MATCHES[3]);
			break;
		case (preg_match('#^ *[V][., ]+C[., ]+(20|30|50|100|200|500)[., ]+([0-9]{10})[., ]+([0-9]{4}) *#', $e_MSG, $m_MATCHES) ? true : false):
			$m_RESPUESTA = array('VENTA' => true, 'OPERADOR' => 'CIERTO', 'MONTO' => $m_MATCHES[1], 'DESTINO' => $m_MATCHES[2], 'NIP' => $m_MATCHES[3]);
			break;
		case (preg_match('#^ *[V][., ]+MT[., ]+(10|20|30|60|100|120|150|200|300|500)[., ]+([0-9]{10})[., ]+([0-9]{4}) *#', $e_MSG, $m_MATCHES) ? true : false):
			$m_RESPUESTA = array('VENTA' => true, 'OPERADOR' => 'MAZTIEMPO', 'MONTO' => $m_MATCHES[1], 'DESTINO' => $m_MATCHES[2], 'NIP' => $m_MATCHES[3]);
			break;
		case (preg_match('#^ *[V][., ]+TU[., ]+(10|20|30|60|100|120|150|200|300|500)[., ]+([0-9]{10})[., ]+([0-9]{4}) *#', $e_MSG, $m_MATCHES) ? true : false):
			$m_RESPUESTA = array('VENTA' => true, 'OPERADOR' => 'TUENTI', 'MONTO' => $m_MATCHES[1], 'DESTINO' => $m_MATCHES[2], 'NIP' => $m_MATCHES[3]);
			break;
		default:
			$m_RESPUESTA = array('VENTA' => false);
			break;
			
	}
	
	return $m_RESPUESTA;
	
}



?>
