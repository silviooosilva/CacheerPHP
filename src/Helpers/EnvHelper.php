<?php

namespace Silviooosilva\CacheerPhp\Helpers;

use Composer\InstalledVersions;

/**
 * Class EnvHelper
 * @author Sílvio Silva <https://github.com/silviooosilva>
 * @package Silviooosilva\CacheerPhp
 */
class EnvHelper {

    /**
     * @return string
     */
    public static function getRootPath()
    {
        // Tenta obter a raiz do projeto via Composer, se disponível
        if (class_exists(InstalledVersions::class)) {
            $rootPackage = InstalledVersions::getRootPackage();
            if (!empty($rootPackage['install_path'])) {
                return rtrim($rootPackage['install_path'], DIRECTORY_SEPARATOR);
            }
        }

        // Fallback: sobe os diretórios a partir do __DIR__ procurando o .env.example
        $baseDir = __DIR__;
        while (!file_exists($baseDir . DIRECTORY_SEPARATOR . '.env.example') && $baseDir !== dirname($baseDir)) {
            $baseDir = dirname($baseDir);
        }

        return rtrim($baseDir, DIRECTORY_SEPARATOR);
    }

    /**
    * @return void
    */
    public static function copyEnv()
    {
        $rootDir = self::getRootPath();
        $envFile = $rootDir . '/.env';
        $envExampleFile = $rootDir . '/.env.example';

        if (!file_exists($envFile) && file_exists($envExampleFile)) {
            if (copy($envExampleFile, $envFile)) {
                echo ".env file created successfully from .env.example.\n";
            } else {
                echo "Failed to create .env file from .env.example.\n";
            }
        }
    }

}
