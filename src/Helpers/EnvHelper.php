<?php

namespace Silviooosilva\CacheerPhp\Helpers;

use Composer\InstalledVersions;

class EnvHelper {

    /**
     * @return string
     * @throws \RuntimeException se o arquivo .env não for encontrado
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

        // Fallback: sobe os diretórios a partir do __DIR__ procurando o .env
        $baseDir = __DIR__;
        while (!file_exists($baseDir . DIRECTORY_SEPARATOR . '.env') && $baseDir !== dirname($baseDir)) {
            $baseDir = dirname($baseDir);
        }

        if (!file_exists($baseDir . DIRECTORY_SEPARATOR . '.env')) {
            throw new \RuntimeException('<CacheerPHP>: Arquivo .env não encontrado na raiz do projeto.');
        }

        return rtrim($baseDir, DIRECTORY_SEPARATOR);
    }
}
