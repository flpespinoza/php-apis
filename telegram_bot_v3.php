<?php

class TelegramBot 
{
    private $endPoint,
            $initTs,
            $curlObj, 
            $currentMsg,
            $currentMsgId,
            $currentChat, 
            $currentUser, 
            $usersList,
            $lastUpdate,
            $messageType;
            
    
    public function __construct($token)
    {
        $this->endPoint = "https://api.telegram.org/bot{$token}/";
        $this->initTs = time();
        $this->initCurl();
        try
        {
            $this->initBot();
        }
        catch(Exception $e)
        {
            throw new Exception($e->getMessage());
        }
    }

    /*
    * Iniciar objecto curl
    */
    private function initCurl()
    {
        $this->curlObj = curl_init();
        curl_setopt_array($this->curlObj, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_FORBID_REUSE   => true,
            CURLOPT_HEADER         => false,
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_HTTPHEADER     => ["Connection: Keep-Alive", "Keep-Alive: 120"]
        ));
    }

    /**
     * Realiza las peticiones a la API de Telegram 
     */
    private function doRequest($methodName, $args = array())
    {
        curl_setopt_array($this->curlObj, [
            CURLOPT_URL        => $this->endPoint . $methodName,
            CURLOPT_POSTFIELDS => empty($args) ? null : $args,
        ]);

        $result_curl = curl_exec($this->curlObj);
        if($result_curl === false)
        {
            $resp = array(
                "ok" => false,
                "error_code" => curl_errno($this->curl),
                "error_descripcion" => curl_error($this->curl),
                "curl_error" => true
            );
            return json_decode(json_encode($resp), true);
        }

        $resp = json_decode($result_curl, true);
        if($resp === null )
        {
            $arr = [
                "ok"          => false,
                "error_code"  => json_last_error(),
                "description" => json_last_error_msg(),
                "json_error"  => true
            ];
            $resp = json_decode(json_encode($arr), true);
        }

        return $resp;
    }

    /*
    * Iniciar el bot de telegram, validar credenciales y obtener el ultimo mensaje recibido
    */
    private function initBot()
    {
        $me = $this->getMe();
        if($me['ok'] != true)
        {
            throw new Exception($me['description']);
        }

        $this->initUpdate();
    }

    private function getMe()
    {
        return $this->doRequest('getMe');
    }

    /**
    * Obtener Ãºltimo mensaje recibido por el bot al iniciar  
    */
    private function initUpdate()
    {
        $messages = $this->getUpdates();
        if(count($messages))
        {
            $lastUpdate = $messages[count($messages) - 1]['update_id'];
            $this->setLastUpdate($lastUpdate + 1);
        }
    }

    /*
    * Obtener mensajes recibidos por el bot, si el valor lastUpdate no tiene valor se obtiene un array con los Ãºltimos mensajes, en caso contrario
    * se obtiene un mensaje por llamado al metodo
    */
    public function getUpdates()
    {
       $args = ($this->lastUpdate) ? array('offset' => $this->lastUpdate, 'limit' => 1) : array();           
       $resp = $this->doRequest('getUpdates', $args);

       if($resp['ok'] == true)
       {
           return $resp['result'];
       }

       return array();
    }

    public function setLastUpdate($update)
    {
        $this->lastUpdate = $update;
    }

    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /*
    * Enviar un mensaje al usuario
    */
    public function sendMessage($message, $reply = null, $markup = null)
    {
        $args = array('chat_id' => $this->currentChat, 'text' => $message);
        if($reply)
        {
            $args['reply_to_message_id'] = $this->currentMsgId;
        }

        if($markup)
        {
            $args['reply_markup'] = json_encode($markup);
        }
        return $this->doRequest('sendMessage', $args);
    }
    
    public function sendMessageToChat($chatId, $msg)
    {
      $args = array('chat_id' => $chatId, 'text' => $msg);
      return $this->doRequest('sendMessage', $args);
    }

    /**
    * Obtiene el mensaje mas reciente recibido por el bot
    */
    public function getMessage()
    {
        $lastMsg = $this->getUpdates();
        if(count($lastMsg))
        {
            $lastMsg = $lastMsg[count($lastMsg) - 1];
            $this->currentChat = $lastMsg['message']['chat']['id'];
            $this->currentMsg = $lastMsg['message'];
            $this->currentMsgId = $lastMsg['message']['message_id'];
            $this->currentUser = $lastMsg['message']['from']['id'];
            $this->messageType = (isset($lastMsg['message']['contact'])) ? 'register' : 'text';
            $this->setLastUpdate($lastMsg['update_id'] + 1);
        }
        else
        {
            $this->currentMsg = array();
        }
    }

    public function getCurrentMessage()
    {
        return $this->currentMsg;
    }

    /*
    * Envia una accion en el chat para que el usuario sepa que el bot esta ejecutando una accion
    */
    public function sendChatAction($chatId = null)
    {
        $args = array('chat_id' => ($chatId !== NULL) ? $chatId : $this->currentChat, 'action' => 'typing');
        return $this->doRequest('sendChatAction', $args);
    }

    public function requestUserData($msgTxt = "Bienvenido, para registrar tu telefono presiona el boton de Registrar")
    {
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

        return $this->sendMessage($msgTxt, false, $markup);
    }

    public function getUserPhone()
    {
        return (isset($this->usersList[$this->currentUser])) ? $this->usersList[$this->currentUser] : false;
    }

    public function setUserPhone($phoneNumber)
    {
        $this->usersList[$this->currentUser] = $phoneNumber;
    }

    public function getMessageType()
    {
        return $this->messageType;
    }
    
    public function getCurrentChat()
    {
    	return $this->currentChat;
    }
    
    public function getIdByPhone($phoneNumber)
    {
    	return array_search($phoneNumber, $this->usersList);
    }

    public function forwardMessage($chatId, $fromChatId, $messageId)
    {
        $args = array('chat_id' => $chatId, 'from_chat_id' => $fromChatId, 'message_id' => $messageId);
        return $this->doRequest('forwardMessage', $args);
    }
}