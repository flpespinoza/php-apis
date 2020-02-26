<?php

class TelegramBot 
{
  private $endPoint, $curlObj, $lastUpdate, $currentMsg, $chatList;

  public function __construct($token)
  {
    $this->endPoint = "https://api.telegram.org/bot{$token}/";
    $this->initCurl();
    $this->chatList = array();
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

  private function getUpdates($args = array())
  {
    $updates = $this->doRequest('getUpdates', $args);
    if($updates['ok'])
    {
      return $updates['result'];
    }

    return array();
  }

  private function setLastUpdate($update)
  {
    $this->lastUpdate = $update;
  }

  public function auth()
  {
    $myInfo = $this->doRequest('getMe');
    if(!$myInfo['ok'])
    {
      throw new Exception($myInfo['description']);
    }
  }

  public function setInitUpdate()
  {
    $updates = $this->getUpdates();
    if(count($updates))
    {
      $lastUpdate = $updates[count($updates) - 1]['update_id'];
      $this->setLastUpdate($lastUpdate + 1);
    }
  }

  public function getMessages()
  {
    $messages = $this->getUpdates(array('offset' => $this->lastUpdate));
    if(count($messages))
    {
      $lastMsg = end($messages);
      $this->setLastUpdate($lastMsg['update_id'] + 1);
    }

    return $messages;
  }

  public function setCurrentMessage($msg)
  {
    $this->currentMsg = $msg['message'];
    $this->currentChat = $msg['message']['chat']['id'];
    $this->currentMsgId = $msg['message']['message_id'];
  }

  public function getCurrentChat()
  {
    return $this->currentChat;
  }

  public function getTxtMsg()
  {
    return $this->currentMsg['text'];
  }

  public function inChats()
  {
    return array_key_exists($this->currentChat, $this->chatList);
  }

  public function saveChat()
  {
    $chat = array
    (
      'phone' => substr($this->currentMsg['contact']['phone_number'], 2),
      'username' => $this->currentMsg['from']['first_name']
    );

    $this->chatList[$this->currentChat] = $chat;
  }

  public function saveUserPhone($phone)
  {
    $chat = array
    (
      'phone' => $phone,
      'username' => $this->currentMsg['from']['first_name']
    );
    $this->chatList[$this->currentChat] = $chat;
  }

  public function isRegisterMessage()
  {
    return isset($this->currentMsg['contact']);
  }

  public function getChats()
  {
    return $this->chatList;
  }

  public function sendMessage($args)
  {
    $args['chat_id'] = $this->currentChat;
    return $this->doRequest('sendMessage', $args);
  }

  public function sendRequestDataMessage()
  {
    $markup = array(
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

    $args = array('text' => 'Registar usuario', 'reply_markup' => json_encode($markup));
    $this->sendMessage($args);
  }

  public function sendReplyMessage($msg)
  {
    $args = array('text' => $msg, 'reply_to_message_id' => $this->currentMsgId);
    $this->sendMessage($args);
  }

  public function sendSimpleMessage($msg)
  {
    $args = array('text' => $msg);
    $this->sendMessage($args);
  }

  public function getUserPhone()
  {
  	return $this->chatList[$this->currentChat]['phone'];
  }


}