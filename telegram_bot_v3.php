<?php

class TelegramBot 
{
  private $endPoint,
          $initTs,
          $curlObj,
          $lastUpdate,
          $currentChat,
          $currentMsg;

  public $chats;
  
  public function __construct($token)
  {
    $this->endPoint = "https://api.telegram.org/bot{$token}/";
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

    try
    {
      $this->authBot();
    }
    catch(Exception $e)
    {
      throw new Exception($e->getMessage());
    }
  }

  private function authBot()
  {
    $me = $this->getMe();
    if($me['ok'] != true)
    {
      throw new Exception($me['description']);
    }

    $this->setInitUpdate();
  }

  private function getMe()
  {
    return $this->doRequest('getMe');
  }

  private function setInitUpdate()
  {
    $messages = $this->getUpdates();
    if(count($messages))
    {
      $lastUpdate = $messages[count($messages) - 1]['update_id'];
      $this->setLastUpdate($lastUpdate + 1);
    }
  }

  private function getUpdates()
  {
    $args = ($this->lastUpdate) ? array('offset' => $this->lastUpdate, 'limit' => 1) : array();           
    $resp = $this->doRequest('getUpdates', $args);

    if($resp['ok'] == true)
    {
      return $resp['result'];
    }

    return array();
  }

  private function setLastUpdate($update)
  {
    $this->lastUpdate = $update;
  }

  public function getLastUpdate()
  {
    return $this->lastUpdate;
  }

  public function getMessage()
  {
    $msg = $this->getUpdates();
    if(count($msg))
    {
      return $msg[count($msg) - 1];
    }
    
    return array();
  }

  public function chatExists($chat)
  {
    return array_key_exists($chat, $this->chats);
  } 

  public function requestUserData($msgTxt = "Para registrar tu teléfono presiona el boton de Registrar")
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