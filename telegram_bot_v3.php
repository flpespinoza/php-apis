<?php

class TelegramBot 
{
  private $endPoint, $curlObj, $lastUpdate, $chats, $currentChat;
  public function __construct($token)
  {
    $this->endPoint = "https://api.telegram.org/bot{$token}/";
    $this->initCurl();
    //Validar bot
    try
    {
      $this->auth();
    }
    catch(Exception $e)
    {
      throw new Exception($e->getMessage());
    }

    //Obtener ultimo mensaje
    $this->initUpdate();
  }

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

  private function auth()
  {
    $myInfo = $this->doRequest('getMe');
    if(!$myInfo['ok'])
    {
      throw new Exception($myInfo['description']);
    }
  }

  private function initUpdate()
  {
    $updates = $this->getUpdates();
    if(count($updates))
    {
      $lastUpdate = $updates[count($updates) - 1]['update_id'];
      $this->setLastUpdate($lastUpdate + 1);
    }
  }

  private function setLastUpdate($update)
  {
    $this->lastUpdate = $update;
  }

  private function getUpdates($args = array())
  {
    $updates = $this->doRequest('getUpdates', $args);
    if($updates['ok'])
    {
      return $updates['result'];
    }

    return array();
  }

  /**
  * Mensajes
  */
  public function getLastMessage()
  {
    $args = array('limit' => 1, 'offset' => $this->lastUpdate);
    $lastMsg = $this->getUpdates($args);
    if(count($lastMsg))
    {
      $lastMsg = $lastMsg[count($lastMsg) - 1];
      $this->currentChat = $lastMsg['message']['chat']['id'];
      $this->setLastUpdate($lastMsg['update_id'] + 1);
    }
  }

  public function sendMessage($args)
  {
    $args['chat_id'] = $this->currentChat;
    return $this->doRequest('sendMessage', $args);
  }

  public function requestUserData()
  {
    $msgTxt = "Presiona el boton Registrar para guadar tus datos";
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
    $args = array('text' => $msgTxt, 'reply_markup' => json_encode($markup)); 
    return $this->sendMessage($args);
  }


  /**
  * Request
  */
  private function doRequest($methodName, $args)
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
        "error_code" => curl_errno($this->curlObj),
        "error_descripcion" => curl_error($this->curlObj),
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


}