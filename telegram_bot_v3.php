<?php

class TelegramBot 
{
  private $endPoint,
          $initTs;
  
  public function __construct($token)
  {
    $this->endPoint = "https://api.telegram.org/bot{$token}/";
  }
}