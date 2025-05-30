<?php

namespace Silviooosilva\CacheerPhp\Repositories;

use PDO;
use Silviooosilva\CacheerPhp\Core\Connect;

/**
 * Class CacheDatabaseRepository
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class CacheDatabaseRepository
{

    /** @var PDO */
    private $connection = null;

    public function __construct()
    {
        $this->connection = Connect::getInstance();
    }


    /**
     * @param string $cacheKey
     * @param mixed  $cacheData
     * @param string $namespace
     * @param string|int $ttl
     * @return bool
     */
    public function store(string $cacheKey, mixed $cacheData, string $namespace, string|int $ttl = 3600)
    {
        if (!empty($this->retrieve($cacheKey, $namespace))) {
            return $this->update($cacheKey, $cacheData, $namespace);
        }

        $expirationTime = date('Y-m-d H:i:s', time() + $ttl);
        $createdAt = date('Y-m-d H:i:s');

        $stmt = $this->connection->prepare(
            "INSERT INTO cacheer_table (cacheKey, cacheData, cacheNamespace, expirationTime, created_at) 
            VALUES (:cacheKey, :cacheData, :namespace, :expirationTime, :createdAt)"
        );
        $stmt->bindValue(':cacheKey', $cacheKey);
        $stmt->bindValue(':cacheData', $this->serialize($cacheData));
        $stmt->bindValue(':namespace', $namespace);
        $stmt->bindValue(':expirationTime', $expirationTime);
        $stmt->bindValue(':createdAt', $createdAt);

        return $stmt->execute() && $stmt->rowCount() > 0;
    }

    /**
    * @param string $cacheKey
    * @param string $namespace
    * @return mixed
    */
    public function retrieve(string $cacheKey, string $namespace = '')
    {
        $driver = $this->connection->getAttribute(PDO::ATTR_DRIVER_NAME);
        $nowFunction = $this->getCurrentDateTime($driver);

        $stmt = $this->connection->prepare(
            "SELECT cacheData FROM cacheer_table 
            WHERE cacheKey = :cacheKey AND cacheNamespace = :namespace AND expirationTime > $nowFunction
            LIMIT 1"
        );
        $stmt->bindValue(':cacheKey', $cacheKey);
        $stmt->bindValue(':namespace', $namespace);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return (!empty($data)) ? $this->serialize($data['cacheData'], false) : null;
    }

    /**
     * @return string
     */
    private function getUpdateQueryWithDriver()
    {
        $driver = $this->connection->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver === 'mysql' || $driver === 'mariadb') {
            return "UPDATE cacheer_table SET cacheData = :cacheData, cacheNamespace = :namespace WHERE cacheKey = :cacheKey LIMIT 1";
        }
        return "UPDATE cacheer_table SET cacheData = :cacheData, cacheNamespace = :namespace WHERE cacheKey = :cacheKey";
    }

    /**
     * @return string
     */
    private function getDeleteQueryWithDriver()
    {
        $driver = $this->connection->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver === 'mysql' || $driver === 'mariadb') {
            return "DELETE FROM cacheer_table WHERE cacheKey = :cacheKey AND cacheNamespace = :namespace LIMIT 1";
        }
        return "DELETE FROM cacheer_table WHERE cacheKey = :cacheKey AND cacheNamespace = :namespace";
    }

    /**
     * @param string $cacheKey
     * @param mixed  $cacheData
     * @param string $namespace
     * @return bool
     */
    public function update(string $cacheKey, mixed $cacheData, string $namespace = '')
    {
        $query = $this->getUpdateQueryWithDriver();
        $stmt = $this->connection->prepare($query);
        $stmt->bindValue(':cacheData', $this->serialize($cacheData));
        $stmt->bindValue(':namespace', $namespace);
        $stmt->bindValue(':cacheKey', $cacheKey);
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
        $query = $this->getDeleteQueryWithDriver();
        $stmt = $this->connection->prepare($query);
        $stmt->bindValue(':cacheKey', $cacheKey);
        $stmt->bindValue(':namespace', $namespace);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * @return string
     */
    private function getRenewExpirationQueryWithDriver(): string
    {
        $driver = $this->connection->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver === 'sqlite') {
            return "UPDATE cacheer_table
                    SET expirationTime = DATETIME(expirationTime, '+' || :ttl || ' seconds')
                    WHERE cacheKey = :cacheKey AND cacheNamespace = :namespace AND expirationTime > :currentTime";
        }
        return "UPDATE cacheer_table
                SET expirationTime = DATE_ADD(expirationTime, INTERVAL :ttl SECOND)
                WHERE cacheKey = :cacheKey AND cacheNamespace = :namespace AND expirationTime > :currentTime";
    }

    /**
     * @param string $cacheKey
     * @param string $namespace
     * @param string $currentTime
     * @return bool
     */
    private function hasValidCache(string $cacheKey, string $namespace, string $currentTime): bool
    {
        $stmt = $this->connection->prepare(
            "SELECT 1 FROM cacheer_table 
            WHERE cacheKey = :cacheKey AND cacheNamespace = :namespace AND expirationTime > :currentTime
            LIMIT 1"
        );
        $stmt->bindValue(':cacheKey', $cacheKey);
        $stmt->bindValue(':namespace', $namespace);
        $stmt->bindValue(':currentTime', $currentTime);
        $stmt->execute();
        return $stmt->fetchColumn() !== false;
    }

    /**
    * @param string $cacheKey
    * @param string|int $ttl
    * @param string $namespace
    * @return bool
    */
    public function renew(string $cacheKey, string|int $ttl, string $namespace = '')
    {
        $currentTime = date('Y-m-d H:i:s');
        if (!$this->hasValidCache($cacheKey, $namespace, $currentTime)) {
            return false;
        }

        $query = $this->getRenewExpirationQueryWithDriver();
        $stmt = $this->connection->prepare($query);
        $stmt->bindValue(':ttl', (int) $ttl, PDO::PARAM_INT);
        $stmt->bindValue(':cacheKey', $cacheKey);
        $stmt->bindValue(':namespace', $namespace);
        $stmt->bindValue(':currentTime', $currentTime);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * @return bool
     */
    public function flush()
    {
        return $this->connection->exec("DELETE FROM cacheer_table") !== false;
    }

    /**
     * @param mixed $data
     * @return string
     */
    private function serialize(mixed $data, bool $serialize = true)
    {
        return $serialize ? serialize($data) : unserialize($data);
    }

    /**
    * @param string $driver
    * @return string
    */
    private function getCurrentDateTime(string $driver)
    {
        return ($driver === 'sqlite') ? "DATETIME('now', 'localtime')" : "NOW()";
    }
}
