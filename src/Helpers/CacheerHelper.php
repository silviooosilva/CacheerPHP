<?php

namespace Silviooosilva\CacheerPhp\Helpers;

use InvalidArgumentException;
use RuntimeException;

class CacheerHelper
{
    /**
     * @param array $item
     * @param callable|null $exceptionFactory
     * @return void
     */
    public static function validateCacheItem(array $item, ?callable $exceptionFactory = null)
    {
        if (!isset($item['cacheKey']) || !isset($item['cacheData'])) {
            if ($exceptionFactory) {
                throw $exceptionFactory("Each item must contain 'cacheKey' and 'cacheData'");
            }
            throw new InvalidArgumentException("Each item must contain 'cacheKey' and 'cacheData'");
        }
    }

    /**
     * @param mixed $cacheData
     * @return array
     */
    public static function mergeCacheData($cacheData)
    {
        if (is_array($cacheData) && is_array(reset($cacheData))) {
            $merged = [];
            foreach ($cacheData as $data) {
                $merged[] = $data;
            }
            return $merged;
        }
        return (array)$cacheData;
    }

    /**
     * @param mixed $currentCacheData
     * @param mixed $cacheData
     * @return array
     */
    public static function arrayIdentifier(mixed $currentCacheData, mixed $cacheData)
    {
        if (is_array($currentCacheData) && is_array($cacheData)) {
            return array_merge($currentCacheData, $cacheData);
        }
        return array_merge((array)$currentCacheData, (array)$cacheData);
    }

    /**
     * Prepara os dados para armazenamento, aplicando compressão e/ou encriptação.
     *
     * @param mixed $data
     * @param bool $compression
     * @param string|null $encryptionKey
     * @return mixed
     */
    public static function prepareForStorage(mixed $data, bool $compression = false, ?string $encryptionKey = null)
    {
        if (!$compression && is_null($encryptionKey)) {
            return $data;
        }

        $payload = serialize($data);

        if ($compression) {
            $payload = gzcompress($payload);
        }

        if (!is_null($encryptionKey)) {
            $iv = substr(hash('sha256', $encryptionKey), 0, 16);
            $encrypted = openssl_encrypt($payload, 'AES-256-CBC', $encryptionKey, 0, $iv);
            if ($encrypted === false) {
                throw new RuntimeException('Failed to encrypt cache data');
            }
            $payload = $encrypted;
        }

        return $payload;
    }

    /**
     * Recupera os dados do armazenamento, aplicando decriptação e/ou descompressão.
     *
     * @param mixed $data
     * @param bool $compression
     * @param string|null $encryptionKey
     * @return mixed
     */
    public static function recoverFromStorage(mixed $data, bool $compression = false, ?string $encryptionKey = null)
    {
        if (!$compression && is_null($encryptionKey)) {
            return $data;
        }

        if (!is_null($encryptionKey)) {
            $iv = substr(hash('sha256', $encryptionKey), 0, 16);
            $decrypted = openssl_decrypt($data, 'AES-256-CBC', $encryptionKey, 0, $iv);
            if ($decrypted === false) {
                throw new RuntimeException('Failed to decrypt cache data');
            }
            $data = $decrypted;
        }

        if ($compression) {
            $data = gzuncompress($data);
        }

        return unserialize($data);
    }
}