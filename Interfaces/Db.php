<?php

namespace Framework\Interfaces;

interface Db
{
    public function get_connection(String $dns = null, string $username = null, string $password = null);
}
