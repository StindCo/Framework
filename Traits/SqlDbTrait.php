<?php

namespace StindCo\stinder\Traits;

trait SqlDbTrait
{
    public function get_all($table, $sqlcondition = null)
    {
        $table = $this->connection->quote($table);
        if ($sqlcondition == null) {
            $req = $this->connection->query('SELECT
                                                *
                                            FROM ' . $table);
            $result = $req->fetchAll(\PDO::FETCH_ASSOC);
            if (count($result) == 0) {
                return 0;
            }
            return $result;
        }
    }

    public function query($sql)
    {
        $req = $this->connection->query($sql);
        if ($req == false) {
            return false;
        }
        $result = $req->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    public function queryOne($sql)
    {
        $req = $this->connection->query($sql);
        if ($req == false) {
            return false;
        }
        $result = $req->fetch(\PDO::FETCH_ASSOC);
        return $result;
    }

    public function exec($sql)
    {
        $req = $this->connection->exec($sql);

        return $req;
    }

    public function get_limit($table, $limit, $sqlcondition = null, $orderBy = false)
    {
        if (\is_array($table)) {
            $req = $this->connection->query('SELECT * FROM ' . $table[0]['table'] . ' AS a, ' . $table[1]['table'] . ' AS r
                                            WHERE a.' . $table[0]['champ'] . ' == r.' . $table[1]['champ'] . '
                                            ORDER BY id DESC
                                            LIMIT ' . $limit);
            $result = $req->fetchAll(\PDO::FETCH_ASSOC);
            if (count($result) == 0) {
                return false;
            }
        } else {
            $limit = $this->connection->quote($limit);
            $table = $this->connection->quote($table);
            if (!\is_null($sqlcondition)) {
                if ($orderBy) {
                    $req = $this->connection->query('SELECT * FROM ' . $table . ' ' . $sqlcondition . '
                                                LIMIT ' . $limit);
                } else {
                    $req = $this->connection->query('SELECT * FROM ' . $table . ' ' . $sqlcondition . '
                                                ORDER BY id DESC
                                                LIMIT ' . $limit);
                }
            } else {
                $req = $this->connection->query('SELECT * FROM ' . $table . ' ORDER BY id DESC LIMIT ' . $limit);
            }
            $result = $req->fetchAll(\PDO::FETCH_ASSOC);
            if (count($result) == 0) {
                return false;
            }
        }

        return $result;
    }
    public function get_f_t($table, $from, $to = null, $limit = null)
    {
        $table = $this->connection->quote($table);
        $to = $this->connection->quote($to);
        $from = $this->connection->quote($from);
        if (\is_null($to)) {
            $req = $this->connection->query('SELECT * FROM ' . $table . 'WHERE id > ' . $from . ' OR id < ' . $to . ' ORDER BY id DESC');
        } else {
            $req = $this->connection->query('SELECT * FROM ' . $table . 'WHERE id >= ' . $from . ' AND id <= ' . $to . ' ORDER BY id DESC');
        }
        if (!is_null($limit)) {
            $req = $this->connection->query('SELECT * FROM ' . $table . 'WHERE id >= ' . $from . ' AND id <= ' . $to . ' ORDER BY id DESC LIMIT ' . $limit);
        }
        $result = $req->fetchAll(\PDO::FETCH_ASSOC);
        if (count($result) == 0) {
            return false;
        }
        return $result;
    }
    public function get_one($table, $id, $sqlcondition = null)
    {
        $table = $this->connection->quote($table);
        $id = $this->connection->quote($id);
        if ($sqlcondition == null) {
            $req = $this->connection->query("SELECT * FROM $table WHERE id == $id");
            $result = $req->fetch(\PDO::FETCH_ASSOC);
            if (\is_bool($result)) {
                return false;
            }
            if (count($result) == 0) {
                return false;
            }
            return $result;
        } else {
            $req = $this->connection->query("SELECT * FROM $table $sqlcondition");
            $result = $req->fetch(\PDO::FETCH_ASSOC);
            if (\is_bool($result)) {
                return false;
            }
            if (count($result) == 0) {
                return false;
            }
            return $result;
        }
    }
    public function in_table($table, $condition)
    {
        $table = $this->connection->quote($table);
        $req = $this->connection->query("SELECT COUNT(*) as nbr FROM $table WHERE $condition");
        $result = $req->fetch(\PDO::FETCH_ASSOC);
        if (\intval($result['nbr']) == 0) {
            return false;
        }
        return true;
    }
    public function get_last($table, $sqlcondition = null)
    {
        $table = $this->connection->quote($table);
        if ($sqlcondition == null) {
            $req = $this->connection->query("SELECT * FROM $table ORDER BY id DESC");
            $result = $req->fetch(\PDO::FETCH_ASSOC);
            if (count($result) == 0) {
                return false;
            }
            return $result;
        }
    }
    public function get_first($table, $sqlcondition = null)
    {
        $table = $this->connection->quote($table);
        if ($sqlcondition == null) {
            $req = $this->connection->query("SELECT * FROM $table ORDER BY id");
            $result = $req->fetch(\PDO::FETCH_ASSOC);
            if (count($result) == 0) {
                return false;
            }
            return $result;
        }
    }
    public function insert_one(string $table, array $champs, array $valeurs)
    {
        $v = '?';
        for ($i = 1; $i < count($champs); $i++) {
            $v .= ',?';
        }
        $champs = \join(' , ', $champs);
        $tt = "INSERT INTO $table ($champs) VALUES ($v)";
        $req = $this->connection->prepare($tt);
        $d = $req->execute($valeurs);
        if ($d != true) {
            return false;
        }
        return true;
    }

    public function delete_one($table, $id, $sqlcondition = null)
    {
        $table = $this->connection->quote($table);
        $id = $this->connection->quote($id);
        if ($sqlcondition == null) {
            $req = $this->connection->query("DELETE FROM $table WHERE id == $id");
            if (\is_bool($req)) {
                return false;
            }
        }
        return true;
    }
    public function update_one(string $table, int $id, array $champs, array $valeurs, $sqlcondition = null)
    {
        $table = $this->connection->quote($table);
        $id = $this->connection->quote($id);
        $v = '?';
        $champs = \join(' = ?, ', $champs) . ' = ?';
        if ($sqlcondition == null) {
            $tt = "UPDATE $table SET $champs WHERE id = $id";
            $req = $this->connection->prepare($tt);
            $req->execute($valeurs);
            if (is_bool($req)) {
                return false;
            }
            return true;
        }
    }
}
