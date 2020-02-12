<?php

class TelegramBot
{
    private $endpoint, $curl, $last_update, $started;

    public function __construct($token)
    {
        $this->started = time();
        $this->endpoint = "https://api.telegram.org/bot{$token}/";
        $this->curl = curl_init();
        curl_setopt_array($this->curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_FORBID_REUSE   => true,
            CURLOPT_HEADER         => false,
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_CONNECTTIMEOUT => 2,
            CURLOPT_HTTPHEADER     => ["Connection: Keep-Alive", "Keep-Alive: 120"]
        ));
    }

    private function request($method, $args = array())
    {
        curl_setopt_array($this->curl, [
            CURLOPT_URL        => $this->endpoint . $method,
            CURLOPT_POSTFIELDS => empty($args) ? null : $args,
        ]);

        $result = curl_exec($this->curl);
            
        return $result;
    }

    public function getUpdates($offset = '')
    {
       $args = ($offset) ? array('offset' => $offset) : array();           
       return $this->request('getUpdates', $args);
    }

    public function sendMessage($chatId, $message)
    {
        $args = array('chat_id' => $chatId, 'text' => $message);
        return $this->request('sendMessage', $args);
    }

    public function deleteMessage($chatId, $msgId)
    {
        $args = array('chat_id' => $chatId, 'message_id' => $msgId);
        return $this->request('deleteMessage', $args);
    }

    
}
