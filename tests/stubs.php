<?php

function get_instance() {
  $ci = new stdClass();
  $ci->config = new Config_stub();

  return $ci;
}

function log_message($msg) {}

class Config_stub
{

  public function __construct()
  {
    $this->items = array(
      'supported_languages' => array(
        'en' => 'english',
        'fr' => 'french',
        'es' => 'spanish'
      ),
      'sess_expiration' => 7200,
      'language' => 'en'
    );
  }

  public function item($key)
  {
    return $this->items[$key];
  }

  public function set_item($key, $val)
  {
    $this->items[$key] = $val;
  }

}

/* End of file */
