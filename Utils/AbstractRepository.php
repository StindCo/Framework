<?php

namespace StindPattern\Utils;

use StindPattern\DB;
use StindPattern\Utils\Data;

abstract class abstractRepository
{

  public function __construct()
  {
  }
  public function datatify(array $data)
  {
    foreach ($data as $key => $value) {
      $this->$key = $value;
    }
  }
}
