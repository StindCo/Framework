<?php

namespace Framework\Utils;

use Exception;

class Response
{
    /**
     * [$vue description]
     * @var [type]
     */
    public $vue;
    public $controller;
    public $header;
    /**
     * tableau des variables
     * @var array
     */
    public $vars = [];
    /**
     * @param String $prefix le prefix de la vue
     * @param String $layout le layout
     */
    public function __construct(Request $request, String $prefix, ?String $layout = null)
    {
        $this->request = $request;
        $this->prefixView = $prefix;
        $this->layout = $layout;

        $this->header = new Header();
    }
    /**
     * [set_var description]
     * @param [type] $nom    [description]
     * @param [type] $valeur [description]
     */
    public function set_var(String $nom, $valeur = null)
    {
        $this->vars[$nom] = $valeur;
    }

    public function set_vars(array $variables)
    {
        foreach ($variables as $key => $value) {
            $this->vars[$key] = $value;
        }
    }

    public function writeHead(int $code)
    {
        if($code == 404)
            header("HTTP/1.0 404 Not Found");
        return $this; 
    }
    /**
     * [render description]
     * @param  [type] $vue [description]
     * @return [type]      [description]
     */
    public function renderView($vue = null, $has_layout = null, $vars = null)
    {
        if (!is_null($vars)) $this->set_vars($vars);
        if ($this->request->env == 'rest') {
            echo \json_encode($this->vars);
        } else {
            \extract($this->vars);
            $file = './src/Views/' . $this->prefixView . DIRECTORY_SEPARATOR . $vue . '.php';
            if ($has_layout == 0) {
                if (\is_file($file)) {
                    $this->header->compile();
                    header('Content-Type: text/html; charset=UTF-8');
                    require $file;
                } else {
                    throw new Exception("Désolé, Cette vue n'existe pas", 1);
                }
            } else {
                \ob_start();
                if (\is_file($file)) {
                    require $file;
                } else {
                    require './src/Views/' . 'error.php';
                }
                $content = \ob_get_clean();
                if (isset($this->layout)) {
                    $this->header->compile();
                    header('Content-Type: text/html; charset=UTF-8');
                    require './src/Views/layouts/' . $this->layout . '.php';
                } else {
                }
            }
        }
    }
    /**
     * Undocumented function
     *
     * @param string $text
     * @param [type] $has_layout
     * @return void
     */
    public function send(string $text, $has_layout = null)
    {
        if ($this->request->env == 'rest') {
            echo \json_encode($this->vars);
        } else {
            if ($has_layout == 0) {

                    header('Content-Type: text/html; charset=UTF-8');
                    $this->header->compile();
                    echo $text;
            } else {
                \ob_start();
                echo $text;
                $content =  \ob_get_clean();
                if (isset($this->layout)) {
                    header('Content-Type: text/html; charset=UTF-8');
                    require './src/Views/layouts/' . $this->layout . '.php';
                } else {
                }
            }
        }
    }
}
