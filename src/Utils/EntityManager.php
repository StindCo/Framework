<?php

namespace StindCo\stinder\Utils;

use StindCo\stinder\Interfaces\Entity;
use StindCo\stinder\Interfaces\EntityManagerInterface;
use ReflectionClass;
use ReflectionObject;

class EntityManager implements EntityManagerInterface
{
    /**
     * Instance de la basse de donnée
     *
     * @var Db $db
     */
    protected $db;
    public array $configs;
    private $folder;

    public function __construct($folder)
    {
        $this->folder = $folder;
        $this->settings();
    }
    public function settings(): EntityManager
    {
        $this->db = Db::get_instance();
        return $this;
    }
    public function getCustomRepository(String $repository)
    {
        $classe = "Repositories\\". ucfirst($repository);
        return new $classe();
    }
    public function getRepository(String $classe)
    {
        return new Repository($classe);
    }

    public function flush()
    {
        if ($handle = opendir($this->folder)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $entities[] = "Entities\\".explode(".", $entry)[0];
                }
            }
            closedir($handle);
        }
        for ($i=0; $i < count($entities); $i++) { 
            $reflect = new ReflectionClass($entities[$i]);
            $table = $reflect->getDefaultProperties()['table'];
            if(!$this->is_table($table)) $this->create_database(new $entities[$i]);
        }
    }


    public function create_database(Entity $entity)
    {
        $reflect = new ReflectionClass($entity);
        $table = $reflect->getDefaultProperties()['table'];
        $text = "CREATE TABLE IF NOT EXISTS '{$table}' (";
        $props = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach ($props as $key => $prop) {
            $champ = $prop->getName();
            $type = ($prop->getType()->getName() == 'string') ? "TEXT" : (($prop->getType()->getName() == 'int')? "INTEGER" : 'blabla');
            $text .= "'{$champ}' {$type} ";
            if($champ == "id") $text .= " NOT NULL PRIMARY KEY AUTOINCREMENT";
            if($key != count($props) - 1) $text .= ' ,'; 
                 
        }
        $text .= ")";
        $status = $this->db->exec($text);
       
        return $status;
    }


    /**
     * Permet d'enregister une entité en base de donnée
     * 
     * @depends self::update
     * @depends self::persist
     * @depends self::is_table
     * @param Entity $entity
     * @return mixed
     */
    public function save(Entity $entity)
    {
        $reflect = new ReflectionClass($entity);
        $table = $reflect->getDefaultProperties()['table'];
        if ($this->is_table($table) == false)
            throw new \Exception("La table {$table} n'existe pas dans la base de donnée", 1);
        else {
            if (($this->db->in_table($table, "id == '{$entity->id}'")) == true) {
                return $this->update($entity);
            } else if ($this->persist($table, $entity) == false) return false;
        }
        return 1;
    }
    /**
     * Permet de mettre à jour une entité en base de donnée
     *
     * @param [type] $entity
     * @param [type] $id
     * @return void
     */
    public function update($entity, $id = null)
    {
        $reflect = new ReflectionClass($entity);
        $table = $reflect->getDefaultProperties()['table'];

        $reflect = new ReflectionObject($entity);
        $props = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);
        $propsNames = array_map(function ($prop) {
            return $prop->getName();
        }, $props);
        $propsValues = array_map(function ($prop) use ($entity) {
            return $prop->getValue($entity);
        }, $props);

        for ($i = 0; $i < count($props); $i++) {
            $propreties[$propsNames[$i]] = $propsValues[$i];
        }
        $id = ($propreties['id'] == null) ? $id : $propreties['id'];
        unset($propreties['id']);
        return $this->sql_update($table, $id, $propreties);
    }
    /**
     * permet de trouver une entité en base de donnée
     *
     * @param String $entity
     * @param [type] $identifiant
     * @return void
     */
    public function find(String $entity, $identifiant)
    {
        $reflect = new ReflectionClass($entity);
        $table = $reflect->getDefaultProperties()['table'];

        if ($this->is_table($table) == false)
            throw new \Exception("La table {$table} n'existe pas dans la base de donnée", 1);
        else {
            if (($data = $this->selectOne($table, $identifiant)) == false) return false;
            $otherClass = (new $entity())->get_serialization($data);
            return $otherClass;
        }
    }
    /**
     * Permet de supprimer une entité en base de donnée
     *
     * @param String $entity
     * @param [type] $identifiant
     * @return void
     */
    public function remove($entity, $identifiant = null)
    {
        $reflect = new ReflectionClass($entity);
        $table = $reflect->getDefaultProperties()['table'];

        if ($this->is_table($table) == false)
            throw new \Exception("La table {$table} n'existe pas dans la base de donnée", 1);
        else {
            if (is_null($identifiant) and is_object($entity)) {
                $identifiant = $entity->id;
            }
            if (($data = $this->removeOne($table, $identifiant)) == false) return false;
            return $data;
        }
    }
    /**
     * Methode Sql pour mettre à jour une entité 
     *
     * @param [type] $table
     * @param [type] $id
     * @param [type] $propreties
     * @return void
     */
    private function sql_update($table, $id, $propreties)
    {
        $champs = implode(" = ?, ", array_keys($propreties)) . " = ?";

        $sqlText = "UPDATE {$table} SET $champs WHERE id == {$id}";
        $req = $this->db->connection->prepare($sqlText);
        $status = $req->execute(array_values($propreties));
        return $status;
    }
    /**
     * methode Sql pour supprimer une entité
     *
     * @param string $table
     * @param [type] $id
     * @return void
     */
    private function removeOne(string $table, $id)
    {
        $status = $this->db->exec("DELETE FROM '{$table}' WHERE id = '{$id}'");
        return $status;
    }
    /**
     * Permet d'avoir un seul truc en base de donnée
     *
     * @param String $table
     * @param [type] $id
     * @return void
     */
    private function selectOne(String $table, $id)
    {
        $status = $this->db->query("SELECT * FROM '{$table}' WHERE id = '{$id}'");
        return $status;
    }
    /**
     * Permet de persister le donnée en base de donnée
     *
     * @param string $table
     * @param Entity $entity
     * @return boolean
     */
    private function persist(string $table, Entity $entity): bool
    {
        $reflect = new ReflectionObject(new $entity);
        $props = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);
        $propsNames = array_map(function ($prop) {
            return $prop->getName();
        }, $props);
        $propsValues = array_map(function ($prop) use ($entity) {
            try {

                $valeur = $prop->getValue($entity);
                                
            } catch (Exception $e) {
                echo "hello world";
            }
            return $valeur;
        }, $props);

        for ($i = 0; $i < count($props); $i++) {
            $propreties[$propsNames[$i]] = $this->db->connection->quote($propsValues[$i]);
        }
        unset($propreties['id']);
        $req = $this->sql_insertFromArray($table, $propreties);
        if (is_bool($req)) return false;
        return true;
    }
    /**
     * Methode Sql pour insérer une entité en base de donnée
     *
     * @param [type] $table
     * @param array $data
     * @return void
     */
    private function sql_insertFromArray($table, array $data)
    {

        $rows = implode(',', array_keys($data));

        $values = implode(',', array_values($data));

        $sqlText = "INSERT INTO {$table} ({$rows}) VALUES ({$values})";
        $req = $this->db->connection->exec($sqlText);
        if ($req == false) return false;
        return $this;
    }
    /**
     * Vérifier l'existence de la table en base de donnée
     *
     * @param String $table
     * @return boolean
     */
    protected function is_table(String $table): bool
    {
        $data = $this->show_table($table);
        if (is_array($data))
            return true;
        return false;
    }
    /**
     * affiche la table
     *
     * @param String $table
     * @return void
     */
    protected function show_table(String $table)
    {
        $data = $this->db->query("SELECT COUNT(*) FROM '{$table}' ");
        return $data;
    }
}
