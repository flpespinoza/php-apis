<?php

class TelegramBot 
{
  private $endPoint, $curlObj, $lastUpdate;
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



}