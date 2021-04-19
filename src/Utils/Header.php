<?php

namespace StindCo\stinder\Utils;

class Header
{
    private array $heads = [];
    public function set(String $name, $key)
    {
        $this->heads[$name] = $key;
        return $this;
    }
    public function getAll()
    {
        return $this->heads;
        
    }
    public function compile() {
        $text = "";
        foreach ($this->heads as $key => $value) {
            header("{$key} : {$value}");
        }
        return $text;
    }
}
