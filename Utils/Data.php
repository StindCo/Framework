<?php

namespace Framework\Utils;

use ArrayAccess;
use Framework\Interfaces\Entity;

class Data implements Entity, ArrayAccess
{
  public function __construct(array $data)
  {
    $this->get_serialization($data);
  }
  public function offsetSet($offset, $value)
  {
    $this->$offset = $value;
  }
  public function offsetGet($offset)
  {
    return $this->$offset;
  }
  public function offsetExists($offset)
  {
    return isset($this->$offset);
  }

  public function offsetUnset($offset)
  {
    unset($this->$offset);
  }
  /**
   * permet de datatifier une array
   *
   * @param array $data
   * @return Data
   */
  public function get_serialization(array $data): Data
  {
    foreach ($data as $key => $value) {
      $this->$key = $value;
    }
    return $this;
  }
  protected function get_serialization_value(array $data): Data
  {
    foreach ($data as $key => $value) {
      $this->$value = null;
    }
    return $this;
  }
  public function __get($name)
  {
    return null;
  }
}
