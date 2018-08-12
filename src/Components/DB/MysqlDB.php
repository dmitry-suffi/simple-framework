<?php

namespace suffi\Simple\Components\DB;

/**
 * Class MysqlDB
 * @package suffi\Simple\Components\DB
 */
class MysqlDB extends DB
{

    protected $db_link;

    /**
     * MysqlDB constructor.
     * @param $dsn 'mysql:dbname=testdb;host=127.0.0.1';
     * @param string $user
     * @param string $password
     * @throws \Exception
     */
    public function __construct($dsn, string $user, string $password)
    {
        $this->db_link = new \PDO($dsn, $user, $password);

        if ($this->db_link) {
            return true;
        } else {
            throw new \Exception('Не удалось подключиться к БД!');
        }
    }

    /**
     * Деструктор
     */
    public function __destruct()
    {
        $this->db_link = null;
    }


    /**
     * {@inheritdoc}
     */
    public function query($query, $params = [], $options = [])
    {
        $query = $this->db_link->prepare($query);
        foreach ($params as $key => $value) {
            $query->bindParam($key, $value);
        }
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * {@inheritdoc}
     */
    public function execute($query, $params = [], $options = [])
    {
        $query = $this->db_link->prepare($query);
        foreach ($params as $key => $value) {
            $query->bindParam($key, $value);
        }
        return $query->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function executeWithResult($query, $label, $params = [], $options = [])
    {
        //@todo
        return $this->query($query, $params, $options);
    }
}
