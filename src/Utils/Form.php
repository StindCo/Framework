<?php

namespace StindCo\stinder\Utils;

class Form
{

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function getData($condition = null)
    {
        if ($this->request->env == "browser") $data = $this->request->POST_DATA;
        else $data = $this->request->PUT_DATA;
        $data = $this->wash($data, $condition);
        return $data;
    }
    private function wash(object $data, $condition)
    {
        if(is_null($condition)) return $data;
        foreach ($condition as $key => $value) {
            $d = explode("&", $value);
            for ($i = 0; $i < count($d); $i++) {
                if ($d[$i] == "required") {
                    $data->$key = $this->required($data->$key);
                    if ($data->$key == false) return [0, $key];
                } elseif ($d[$i] == 'crypted') {
                    $data->$key = $this->crypted($data->$key);
                }
            }
        }
        return [1, $data];
    }
    private function required($text)
    {
        if (!empty($text) and $text != "") return htmlspecialchars(addslashes($text));
        return false;
    }
    private function crypted($text)
    {
        return sha1(md5($text));
    }
}
