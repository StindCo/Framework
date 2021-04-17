<?php

namespace Framework\Plugins;

class SessionManager
{
    public function __construct()
    {
        session_start();
    }
    public function set($key, $value): self
    {
        $_SESSION[$key] = $value;
        return $this;
    }
    public function initialize_connection() {
        $_SESSION['connected'] = false;
    }
    public function get($key)
    {
        return $_SESSION[$key];
    }
    public function set_connected()
    {
        $_SESSION['connected'] = true;
        return $this;
    }
    public function set_disconnected(): self
    {
        $_SESSION['connnected'] = false;
        session_destroy();
        return $this;
    }
    public function is_connected()
    {
        return $_SESSION['connected'];
    }
}
