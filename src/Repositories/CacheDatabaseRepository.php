<?php

namespace Silviooosilva\CacheerPhp\Repositories;

use PDO;
use Silviooosilva\CacheerPhp\Core\Connect;

class CacheDatabaseRepository
{
    /**
     * Summary of connection
     */
    private $connection = null;

    public function __construct()
    {
        $this->connection = Connect::getInstance();
    }


    /**
     * @param string $cacheKey
     * @param mixed $cacheData
     * @param string $namespace
     * @param int | string $ttl
     * @return bool
     */
    public function store(string $cacheKey, mixed $cacheData, string $namespace, int | string $ttl = 3600)
    {
        $expirationTime = date('Y-m-d H:i:s', time() + $ttl);
        $createdAt = date('Y-m-d H:i:s');

        $stmt = $this->connection->prepare(
                "INSERT INTO cacheer_table (cacheKey, cacheData, cacheNamespace, expirationTime, created_at) VALUES (?, ?, ?, ?, ?)"
            );

        $stmt->bindValue(1, $cacheKey);
        $stmt->bindValue(2, $this->serialize($cacheData));
        $stmt->bindValue(3, $namespace);
        $stmt->bindValue(4, $expirationTime);
        $stmt->bindValue(5, $createdAt);

        return $stmt->execute() && $stmt->rowCount() > 0;
    }

    /**
     * @param string $cacheKey
     * @param string $namespace
     * @return mixed
     */
    public function retrieve(string $cacheKey, string $namespace = '')
    {
        $stmt = $this->connection->prepare(
            "SELECT cacheData FROM cacheer_table WHERE cacheKey = ? AND cacheNamespace = ? AND expirationTime > NOW()"
        );
        $stmt->bindValue(1, $cacheKey);
        $stmt->bindValue(2, $namespace);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return (!empty($data)) ? $this->serialize($data['cacheData'], false) : null;
    }

    /**
     * @param string $cacheKey
     * @param mixed $cacheData
     * @param string $namespace
     * @return bool
     */
    public function update(string $cacheKey, mixed $cacheData, string $namespace = '')
    {
        $stmt = $this->connection->prepare(
            "UPDATE cacheer_table SET cacheData = ?, cacheNamespace = ? WHERE cacheKey = ?"
        );
        $stmt->bindValue(1, $this->serialize($cacheData));
        $stmt->bindValue(2, $namespace);
        $stmt->bindValue(3, $cacheKey);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * @param string $cacheKey
     * @param string $namespace
     * @return bool
     */
    public function clear(string $cacheKey, string $namespace = '')
    {
        $stmt = $this->connection->prepare(
            "DELETE FROM cacheer_table WHERE cacheKey = ? AND cacheNamespace = ?"
        );
        $stmt->bindValue(1, $cacheKey);
        $stmt->bindValue(2, $namespace);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
    * @param string $cacheKey
    * @param int|string $ttl
    * @param string $namespace
    * @return bool
    */
    public function renew(string $cacheKey, int|string $ttl, string $namespace = '')
    {
        $currentTime = date('Y-m-d H:i:s');

        $actualExpirationTime = $this->connection->prepare(
            "SELECT expirationTime FROM cacheer_table 
            WHERE cacheKey = ? AND cacheNamespace = ? AND expirationTime > ?"
        );
        $actualExpirationTime->bindValue(1, $cacheKey);
        $actualExpirationTime->bindValue(2, $namespace);
        $actualExpirationTime->bindValue(3, $currentTime);
        $actualExpirationTime->execute();

        if ($actualExpirationTime->rowCount() > 0) {
           
            $stmt = $this->connection->prepare(
                "UPDATE cacheer_table 
                SET expirationTime = DATE_ADD(expirationTime, INTERVAL ? SECOND)
                WHERE cacheKey = ? AND cacheNamespace = ? AND expirationTime > ?"
            );
            $stmt->bindValue(1, (int) $ttl, PDO::PARAM_INT);
            $stmt->bindValue(2, $cacheKey);
            $stmt->bindValue(3, $namespace);
            $stmt->bindValue(4, $currentTime);
            $stmt->execute();

            return $stmt->rowCount() > 0;
        }

        return false;
    }



    /**
     * @return bool
     */
    public function flush()
    {
        return $this->connection->exec("DELETE FROM cacheer_table") !== false;
    }

    /**
     * Serializa os dados de cache para armazenamento.
     *
     * @param mixed $data
     * @return string
     */
    private function serialize(mixed $data, bool $serialize = true)
    {
        return $serialize ? serialize($data) : unserialize($data);
    }
}
