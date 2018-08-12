<?php

namespace suffi\Simple\Components\DB;

use suffi\Simple\Core\Exceptions\ConfigException;

/**
 * Class DB
 * @package suffi\Simple\Components\DB
 */
class OracleDB extends DB
{
    protected $db_link;
    public static $counter = 0;

    private $user;
    private $password;
    private $base;


    /**
     * Конструктор
     *
     * @param string $user Пользователь
     * @param string $password Пароль
     * @param string $base База данных
     * @param string $encoding Кодировка, значение по-умолчанию 'AL32UTF8'
     *
     * @throws ConfigException
     */
    public function __construct(string $user, string $password, string $base, $encoding = 'AL32UTF8')
    {
        $this->db_link = oci_connect($user, $password, $base, $encoding);

        if (!$this->db_link) {
            throw new ConfigException('Не удалось подключиться к БД!');
        }

        $this->user = $user;
        $this->password = $password;
        $this->base = $base;
    }

    /**
     * Деструктор
     */
    public function __destruct()
    {
        if (function_exists('oci_close')) {
            @oci_close($this->db_link);
        }
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getBase(): string
    {
        return $this->base;
    }

    /**
     * {@inheritdoc}
     */
    public function query($query, $params = [], $options = [])
    {
        self::$counter++;

        $parse = oci_parse($this->db_link, $query);

        if (!empty($params)) {
            foreach ($params as $key => $value) {
                oci_bind_by_name($parse, $key, $params[$key], -1);
            }
        }

        $execute = oci_execute($parse, OCI_DEFAULT);
        if ($execute) {
            $data = [];
            $mode = OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS;

            while ($row = oci_fetch_array($parse, $mode)) {
                $data[] = $row;
            }
            oci_free_statement($parse);
            return $data;
        } else {
            oci_rollback($this->db_link);
            $tt = oci_error($parse);
            oci_free_statement($parse);
            return $tt['message'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function execute($query, $params = [], $options = [])
    {
        self::$counter++;

        $parse = oci_parse($this->db_link, $query);

        if (!empty($params)) {
            foreach ($params as $key => $value) {
                @oci_bind_by_name($parse, $key, $params[$key], -1);
            }
        }

        $execute = @oci_execute($parse, OCI_DEFAULT);
        if ($execute) {
            oci_commit($this->db_link);
            oci_free_statement($parse);
            return true;
        } else {
            oci_rollback($this->db_link);
            $tt = oci_error($parse);
            oci_free_statement($parse);
            return $tt['message'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function executeWithResult($query, $label, $params = [], $options = [])
    {
        self::$counter++;

        $parse = oci_parse($this->db_link, $query);

        if (!empty($params)) {
            foreach ($params as $key => $value) {
                @oci_bind_by_name($parse, $key, $params[$key], -1);
            }
        }

        oci_bind_by_name($parse, $label, $var, (64 * 1024) - 1);
        $execute = @oci_execute($parse, OCI_DEFAULT);

        if ($execute) {
            oci_commit($this->db_link);
            oci_free_statement($parse);
            return $var;
        } else {
            oci_rollback($this->db_link);
            $tt = oci_error($parse);
            oci_free_statement($parse);
            return $tt['message'];
        }
    }

    /**
     * Выполнение запроса, параметры типа CLOB
     * @param string $query Текст SQL запроса
     * @param array $params Параметры запроса, ключ => значение
     * @param array $options Массив дополнительных настроек
     * @return mixed Возвращает true или текст ошибки
     */
    public function executeClob($query, $params = [], $options = [])
    {
        self::$counter++;

        $parse = oci_parse($this->db_link, $query);

        $clobs = [];

        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $clobs[$key] = oci_new_descriptor($this->db_link, OCI_D_LOB);
                @oci_bind_by_name($parse, $key, $clobs[$key], -1, OCI_B_CLOB);
                $clobs[$key]->writetemporary($value);
            }
        }

        $execute = @oci_execute($parse, OCI_DEFAULT);
        if ($execute) {
            oci_commit($this->db_link);
            foreach ($clobs as $k => $val) {
                $val->free();
            }
            oci_free_statement($parse);
            return true;
        } else {
            oci_rollback($this->db_link);
            foreach ($clobs as $k => $val) {
                $val->free();
            }
            $tt = oci_error($parse);
            oci_free_statement($parse);
            return $tt['message'];
        }
    }
}
