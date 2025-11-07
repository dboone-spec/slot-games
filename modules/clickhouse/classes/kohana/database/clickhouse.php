<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * Class Database_ClickHouse
 */
class Kohana_Database_ClickHouse extends Database
{
    /**
     * Raw server connection
     * @var Client
     */
    protected $_connection;

    /**
     * Character that is used to quote identifiers
     * @var string
     */
    protected $_identifier = '`';

    /**
     * Result meta data
     * @var array
     */
    protected $meta = [];

    /**
     * Result total rows
     * @var int
     */
    protected $totalRows = 0;

    /**
     * Connect to the database. This is called automatically when the first
     * query is executed.
     *
     *     $db->connect();
     *
     * @throws  Database_Exception
     * @return  void
     */
    public function connect()
    {
        $parts = [
            'scheme' => 'http',
            'path'   => '/',
            'host'   => $this->_config['connection']['host'],
            'port'   => $this->_config['connection']['port'],
            'user'   => $this->_config['connection']['username'],
            'pass'   => $this->_config['connection']['password'],
            'query'  => http_build_query(
                [
                    'database' => $this->_config['connection']['database'],
                ]
            ),
        ];

        try {
            $this->_connection = new Client(['base_uri' => Helper_ClickHouse::buildUrl(null, $parts)]);
        } catch (ClientException $e) {
            throw new Database_Exception($e->getMessage(), null, $e->getCode(), $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function list_tables($like = null)
    {
        if (is_string($like)) {
            // Search for table names
            $result = $this->query(Database::SELECT, 'SHOW TABLES LIKE ' . $this->quote($like), false);
        } else {
            // Find all table names
            $result = $this->query(Database::SELECT, 'SHOW TABLES', false);
        }

        $tables = [];

        foreach ($result as $row) {
            $tables[] = $row;
        }

        return $tables;
    }


    /**
     * ClickHouse has no charsets
     * @link https://clickhouse.yandex/reference_ru.html#Кодировки Кодировки
     */
    public function set_charset($charset)
    {
        // Empty stub
    }

    /**
     * @inheritdoc
     */
    public function query($type, $sql, $as_object = false, array $params = null)
    {
        // Make sure the database is connected
        $this->_connection or $this->connect();

        if (Kohana::$profiling) {
            // Benchmark this query for the current instance
            $benchmark = Profiler::start("Database ({$this->_instance})", $sql);
        }

        try {
            $method = ($type == Database::SELECT) ? 'get' : 'post';
            /** @var \Psr\Http\Message\ResponseInterface $result */
            $result = $this->_connection->$method(null, ['query' => ['query' => $sql . $this->getFormat()]]);
            $result = trim($result->getBody()->getContents());
        } catch (Exception $e) {
            if (isset($benchmark)) {
                // This benchmark is worthless
                Profiler::delete($benchmark);
            }

            // Convert the exception in a database exception
            throw new Database_Exception(
                ':error [ :query ]',
                [
                    ':error' => $e->getMessage(),
                    ':query' => $sql
                ],
                $e->getCode()
            );
        }

        if (isset($benchmark)) {
            Profiler::stop($benchmark);
        }

        // Set the last query
        $this->last_query = $sql;

        if ($type === Database::SELECT) {
            return new Database_Result_Cached($this->formatResult($result,$as_object), $sql, $as_object, $params);
        } else {
            // ClickHouse isn't return affected rows
            return 0;
        }
    }

    /**
     * Format result
     * @param string $data Data
     * @return mixed
     */
    protected function formatResult($data,$as_object=null)
    {
        $data = json_decode($data, true);

        $this->meta      = $data['meta'];
        $this->totalRows = $data['rows'];

        $data = $data['data'];

        if(!empty($as_object)) {
            $newdata=[];
            foreach ($data as $values) {
                $new=new $as_object();
                foreach ($values as $k=>$v) {
                    $new->$k=$v;
                }
                $newdata[] = $new;
            }
            $data=$newdata;
        }

        return $data;
    }

    /**
     * Get output ClickHouse format
     * @return string
     */
    public function getFormat()
    {
        return ' FORMAT JSON';
    }

    /**
     * ClickHouse has no transactions
     * @link https://clickhouse.yandex/reference_ru.html Особенности ClickHouse, которые могут считаться недостатками
     */
    public function begin($mode = null)
    {
        // Empty stub
    }

    /**
     * ClickHouse has no transactions
     * @link https://clickhouse.yandex/reference_ru.html Особенности ClickHouse, которые могут считаться недостатками
     */
    public function commit()
    {
        // Empty stub
    }

    /**
     * ClickHouse has no transactions
     * @link https://clickhouse.yandex/reference_ru.html Особенности ClickHouse, которые могут считаться недостатками
     */
    public function rollback()
    {
        // Empty stub
    }

    /**
     * @inheritdoc
     */
    public function list_columns($table, $like = null, $add_prefix = true)
    {
        $table  = $this->quote($table);
        $query  = 'SELECT `name` FROM `system`.`columns` WHERE `table` = ' . $table;
        $result = $this->query(Database::SELECT, $query)->as_array();
        $result = array_column($result, 'name');
        return array_combine($result, $result);
    }

    /**
     * @inheritdoc
     */
    public function escape($value)
    {
        return "'" . addslashes($value) . "'";
    }
}
