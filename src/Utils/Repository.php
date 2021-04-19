<?php

namespace StindCo\stinder\Utils;

use ReflectionClass;

class Repository extends EntityManager
{
    protected $classe;
    public function __construct(String $classe)
    {
        parent::__construct();

        $this->classe = $classe;
    }
    public function joinFindOne($id, ...$classes)
    {
        $data = [$this->findOne($id)];
        if (is_bool($data[0])) return false;
        for ($i = 0; $i < count($classes); $i++) {
            $reflect = new ReflectionClass($classes[$i][0]);
            $name = $reflect->getShortName();
            $table = $reflect->getDefaultProperties()['table'];
            if ($this->is_table($table) == false)
                throw new \Exception("La table {$table} n'existe pas dans la base de donnée", 1);
            $infos[$i] = [$table, ...$classes];
        }
        for ($a = 0; $a < count($data); $a++) {
            for ($k = 0; $k < count($infos); $k++) {
                $name = $infos[$k][0];
                $tables = $infos[$k][1][1];
                if ($infos[$k][1][2] == 0) $conditions = ['id' => $data[$a]->$tables];
                else $conditions = [$table => $data[$a]->id];
                $data[$a]->$name = (new self($infos[$k][1][0]))->findAllBy($conditions);
            }
        }
        return $data;
    }
    /**
     * Cette fonction permet de faire automatiquement des jointures
     * 
     * ...classes === [classname, champs_de_jointures, 0 ou 1 == type de jointures],[] ...
     *
     * @param array $condition
     * @param [type] ...$classes
     * @return void
     */
    public function joinFindAllBy(array $condition,...$classes)
    {
        
        $data = $this->findAllBy($condition);
        if (is_bool($data)) return false;
        for ($i = 0; $i < count($classes); $i++) {
            $reflect = new ReflectionClass($classes[$i][0]);
            $name = $reflect->getShortName();
            $table = $reflect->getDefaultProperties()['table'];
            if ($this->is_table($table) == false)
                throw new \Exception("La table {$table} n'existe pas dans la base de donnée", 1);
            $infos[$i] = [$table, ...$classes];
        }
        
        for ($a = 0; $a < count($data); $a++) {
            for ($k = 0; $k < count($infos); $k++) {
                $name = $infos[$k][0];
                $tables = $infos[$k][1][1];
                if ($infos[$k][1][2] == 0) $conditions = ['id' => $data[$a]->$tables];
                else $conditions = [$table => $data[$a]->id];
                $data[$a]->$name = (new self($infos[$k][1][0]))->findAllBy($conditions);
            }
        }
        return $data;
    }
    public function joinFindAll(...$classes)
    {
        $data = $this->findAll();
        if (is_bool($data)) return false;
        for ($i = 0; $i < count($classes); $i++) {
            $reflect = new ReflectionClass($classes[$i][0]);
            $name = $reflect->getShortName();
            $table = $reflect->getDefaultProperties()['table'];
            if ($this->is_table($table) == false)
                throw new \Exception("La table {$table} n'existe pas dans la base de donnée", 1);
            $infos[$i] = [$table, ...$classes];
        }
        for ($a = 0; $a < count($data); $a++) {
            for ($k = 0; $k < count($infos); $k++) {
                $name = $infos[$k][0];
                $tables = $infos[$k][1][1];
                if ($infos[$k][1][2] == 0) $conditions = ['id' => $data[$a]->$tables];
                else $conditions = [$table => $data[$a]->id];
                $data[$a]->$name = (new self($infos[$k][1][0]))->findAllBy($conditions);
                var_dump($data[$a]->$name);
            }
        }
        return $data;
    }
    public function findAll()
    {
        if ($this->is_a_table()) {
            $data = $this->db->get_all($this->table);
            return $this->zip_class_array($data);
        } else return false;
    }
    public function findAllBy(array $condition)
    {
        if ($this->is_a_table()) {
            $condition = $this->condition_parser($condition);
            $sqlText = "SELECT * FROM {$this->table} WHERE {$condition}";
            $status = $this->db->query($sqlText);
            if (is_bool($status)) return false;
            if (!isset($status['id'])) {
                return $this->zip_class_array($status);
            }
            return (new $this->classe())->get_serialization($status);
        } else return false;
    }
    public function findOne($id)
    {
        return $this->find($this->classe, $id);
    }
    public function findOneBy(array $condition)
    {
        if ($this->is_a_table()) {
            $condition = $this->condition_parser($condition);
            $sqlText = "SELECT * FROM {$this->table} WHERE {$condition}";
            $status = $this->db->queryOne($sqlText);
            if (is_bool($status)) return false;
            if (is_array($status[0])) {
                return $this->zip_class_array($status);
            }
            return (new $this->classe())->get_serialization($status);
        } else return false;
    }
    private function condition_parser(array $condition): string
    {
        $valeurs = array_values($condition);
        $champs = array_keys($condition);
        $text = "";
        for ($i = 0; $i < count($condition); $i++) {
            if ($i == 0) {
                $glueT = '';
            } else {
                $glueT = "AND ";
            }
            $text = $text . ' ' . $glueT . " " . $champs[$i] . " == '" . $valeurs[$i] . "'";
        }
        return $text;
    }
    private function zip_class_array(array $data)
    {
        for ($i = 0; $i < count($data); $i++) {
            $datas[$i] = (new $this->classe)->get_serialization($data[$i]);
        }
        return $datas;
    }
    private function is_a_table()
    {
        $reflect = new ReflectionClass($this->classe);
        $this->table = $reflect->getDefaultProperties()['table'];
        return $this->is_table($this->table);
    }
}
