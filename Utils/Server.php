<?php

namespace StindCo\stinder\Utils;

class Server
{
    private Data $dataSet;

    public function __construct(Data $server)
    {
        $this->dataSet = $server;
    }

    public function get($key)
    {
        return $this->dataSet[$key];
    }

    public function get_method()
    {
        return $this->dataSet['REQUEST_METHOD'];
    }
    
    public function get_pathinfo() {
        return $this->dataSet['PATH_INFO'];
    }
}
