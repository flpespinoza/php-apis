<?php

class TelegramBotV2
{
    private $endpoint, $curl, $last_update, $init_ts, $users;

    public function __construct($token)
    {
        $this->init_ts = time();
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

        try
        {
            $this->init();
        }
        catch(Exception $e)
        {
            throw new Exception($e->getMessage());
        }
    }

    private function init()
    {
        $me = $this->getMe();
        if($me['ok'] != true)
        {
            throw new Exception($me['description']);
        }

        $this->setInitUpdate();
    }

    private function request($method, $args = array())
    {
        curl_setopt_array($this->curl, [
            CURLOPT_URL        => $this->endpoint . $method,
            CURLOPT_POSTFIELDS => empty($args) ? null : $args,
        ]);

        $result_curl = curl_exec($this->curl);
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

    private function setInitUpdate()
    {
        $msgs = $this->getUpdates();
        if(count($msgs))
        {
            $update = $msgs[count($msgs) - 1]['update_id'];
            $this->setLastUpdate($update + 1);
        }
    }

    public function setLastUpdate($update)
    {
        $this->last_update = $update;
    }

    public function getLastUpdate()
    {
        return $this->last_update;
    }

    public function getUpdates()
    {
       $args = ($this->last_update) ? array('offset' => $this->last_update, 'limit' => 1) : array();           
       $resp = $this->request('getUpdates', $args);

       if($resp['ok'] == true)
       {
           return $resp['result'];
       }

       return array();
    }

    public function sendMessage($chatId, $message, $markup = null)
    {
        $args = array('chat_id' => $chatId, 'text' => $message);
        if($markup)
        {
            $args['reply_markup'] = json_encode($markup);
        }
        return $this->request('sendMessage', $args);
    }

    public function deleteMessage($chatId, $msgId)
    {
        $args = array('chat_id' => $chatId, 'message_id' => $msgId);
        return $this->request('deleteMessage', $args);
    }

    public function getMe()
    {
        return $this->request('getMe');
    }

    public function sendChatAction($chat_id)
    {
        $args = array('chat_id' => $chat_id, 'action' => 'typing');
        return $this->request('sendChatAction', $args);
    }

    public function getUserPhone($user_id)
    {
        return (isset($this->users[$user_id])) ? $this->users[$user_id] : false;
    }

    public function setUserPhone($user_id, $phone)
    {
        $this->users[$user_id] = $phone;
    }
    
    public function requestUserData($chat_id)
    {
        $msg = "Para registrar tu teléfono presiona el boton de Registrar";
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

        $this->sendMessage($chat_id, $msg, $markup);
    }

    public function getMsgType($msg)
    {
       return (isset($msg['message']['contact'])) ? 'register' : 'text';
    }
}
