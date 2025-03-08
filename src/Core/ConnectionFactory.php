<?php

namespace Silviooosilva\CacheerPhp\Core;

use PDO;
use PDOException;
use Silviooosilva\CacheerPhp\Exceptions\ConnectionException;

/**
 * Class ConnectionFactory
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class ConnectionFactory
{

    /**
      * @param array|null $database
      * @return PDO|null
    */
    public static function createConnection(array $database = null)
    {
        $dbConf = $database ?? CACHEER_DATABASE_CONFIG[Connect::getConnection()];

        if ($dbConf['driver'] === 'sqlite') {
            $dbName = $dbConf['dbname'];
            $dbDsn = $dbConf['driver'] . ':' . $dbName;
        } else {
            $dbName = "{$dbConf['driver']}-{$dbConf['dbname']}@{$dbConf['host']}";
            $dbDsn = "{$dbConf['driver']}:host={$dbConf['host']};dbname={$dbConf['dbname']};port={$dbConf['port']}";
        }

        try {
            $options = $dbConf['options'] ?? [];
            foreach ($options as $key => $value) {
                if (is_string($value) && defined($value)) {
                    $options[$key] = constant($value);
                }
            }
            return new PDO($dbDsn, $dbConf['username'] ?? null, $dbConf['passwd'] ?? null, $options);
        } catch (PDOException $exception) {
            throw ConnectionException::create($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }
}
