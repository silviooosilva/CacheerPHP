# Cacheer-PHP


[![Maintainer](https://img.shields.io/badge/maintainer-@silviooosilva-blue.svg?style=for-the-badge&color=blue)](https://github.com/silviooosilva)
![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/silviooosilva/cacheer-php/PHP?style=for-the-badge&color=blue)
[![Latest Version](https://img.shields.io/github/release/silviooosilva/CacheerPHP.svg?style=for-the-badge&color=blue)](https://github.com/silviooosilva/CacheerPHP/releases)
[![Quality Score](https://img.shields.io/scrutinizer/g/silviooosilva/CacheerPHP.svg?style=for-the-badge&color=blue)](https://scrutinizer-ci.com/g/silviooosilva/CacheerPHP)
![Packagist Downloads](https://img.shields.io/packagist/dt/silviooosilva/cacheer-php?style=for-the-badge&color=blue)



Cacheer-PHP é um pacote minimalista para caching em PHP, oferecendo uma interface simples para armazenar e recuperar dados em cache utilizando arquivos e banco de dados.

## Funcionalidades

- Armazenamento e recuperação de cache em arquivos e banco de dados.
- Expiração de cache personalizável.
- Limpeza e flush de cache.
- Suporte a namespaces para organização de cache.
- Limpeza automática do diretório de cache (`flushAfter`).
- Saída de Dados Personalizada (`JSON, Array, Strings, Objetos`)

## Instalação

O CacheerPHP está disponível via Composer:

```sh
  "silviooosilva/cacheer-php": "^2.0"
```

Ou rode o comando:

```sh
composer require silviooosilva/cacheer-php
```

## Documentação

1. [Armazenar e Recuperar Dados em Cache](docs/example01.md)
2. [Expiração de cache personalizável](docs/example02.md)
3. [Limpeza e flush de cache](docs/example03.md)
4. [Suporte a namespaces para organização de cache](docs/example04.md)
5. [Limpeza automática do diretório de cache `flushAfter`](docs/example09.md)
6. [Cache de Resposta de API](docs/example05.md)
7. [Saída de Dados Personalizada (`JSON`)](docs/example06.md)
8. [Saída de Dados Personalizada (`Array`)](docs/example07.md)
9. [Saída de Dados Personalizada (`String`)](docs/example08.md)

# Lançamento da Versão 2.0.0 do CacheerPHP

Estamos entusiasmados em anunciar o lançamento da versão **2.0.0** do **CacheerPHP**! Esta versão traz uma série de novos recursos e melhorias que ampliam a flexibilidade e o poder de escolha para desenvolvedores que buscam gerenciar cache de forma eficiente.

## Principais Novidades da Versão 2.0.0

- **Suporte a Banco de Dados**: Agora o CacheerPHP suporta armazenamento de cache em **banco de dados** com opções para `MySQL`, `SQLite` e `PostgreSQL`. Isso permite maior flexibilidade, escalabilidade e performance em diversos cenários de uso.
- **Melhorias de Performance**: Otimizações adicionais para a recuperação e inserção de cache, garantindo maior eficiência, especialmente em sistemas com alto volume de dados.
- **Novos Recursos**: Agora é possível monitorar o funcionamento do sistema de cache com o novo recurso de logs. Erros, avisos, informações e mensagens de debug são registrados e armazenados, proporcionando uma visão clara do desempenho do sistema e facilitando a identificação e solução de eventuais problemas.

## Benefícios da Atualização

Com a **versão 2.0.0**, você ganha:

- **Flexibilidade** para escolher a melhor solução de armazenamento de cache para sua aplicação.
- **Melhor performance**, com aprimoramentos no processo de recuperação e armazenamento de dados em cache.

---

# Guia de Atualização para o CacheerPHP 2.0.0

Para garantir uma transição suave para a versão 2.0.0, siga este manual de atualização detalhado.

## Requisitos do Sistema

- **PHP** versão 8.0 ou superior.
- **Banco de Dados (opcional)**: MySQL, PostgreSQL, ou SQLite (para uso do driver de cache baseado em banco de dados).

## Passo a Passo de Atualização

### 1. Backup dos Dados de Cache Atuais

Antes de iniciar a atualização, é recomendável fazer backup de quaisquer dados de cache relevantes. Se estiver utilizando cache baseado em arquivos, salve o diretório de cache.

### 2. Atualize o Pacote via Composer

Execute o comando abaixo para atualizar para a versão mais recente do CacheerPHP:

```bash
composer require silviooosilva/cacheer-php:^2.0.0
```

### 3. Configuração
Após a atualização, siga as orientações abaixo para configurar a nova versão.

**Manter Cache Baseado em Arquivos**.

Se você já utiliza cache por arquivos e deseja continuar com essa configuração, nenhuma ação adicional é necessária.

**Migrar para Cache Baseado em Banco de Dados**

#### 1) Configurar Dados de Conexão

- Edite o arquivo de configuração do CacheerPHP, localizado na pasta ```Boot/config.php```, e insira os dados do seu banco.

#### 2) Habilitar o Driver de Banco de Dados
- Exemplo de uso no código: 

```php
<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Silviooosilva\CacheerPhp\Cacheer;

$Cacheer = new Cacheer();
$Cacheer->setConfig()->setDatabaseConnection('mysql');
$Cacheer->setDriver()->useDatabaseDriver();

```

#### 3) Configurar Timezone 

- Para evitar problemas com expiração de cache, configure o fuso horário:

```php
$Cacheer->setConfig()->setTimeZone('Africa/Luanda');
```
**NB.: Certifique-se de que o timezone fornecido é válido**
- https://www.php.net/manual/en/timezones.php 

#### 4) Sistema de Logs

- Configure o caminho para salvar os logs:

```php
$Cacheer->setConfig()->setLoggerPath('/path/CacheerPHP.log');
```
## API Reference

## **Classes Principais**

### `Silviooosilva\CacheerPhp\Cacheer`

A classe principal do pacote, usada para todas as operações de cache.


## **Métodos**

### 1. **Configuração**

#### `setConfig()`
Inicia uma configuração personalizada para o CacheerPHP.

```php
$Cacheer->setConfig();
```
```php
$Cacheer->setConfig()->setDatabaseConnection(string $driver)
```
Configura o banco de dados para armazenamento do cache.

- Parâmetros:
```
$driver: Driver do banco de dados. Valores possíveis: 'mysql', 'pgsql', 'sqlite'.
```

**Exemplo:**

```
$Cacheer->setConfig()->setDatabaseConnection('mysql');
```
Timezone
---

```
$Cacheer->setConfig()->setTimeZone(string $timezone);
```

Define o fuso horário para operações de cache.
- Parâmetros

```
$timezone: Fuso horário no formato PHP (exemplo: 'UTC', 'Africa/Luanda').
```

**Exemplo:**

```
$Cacheer->setConfig()->setTimeZone('UTC');
```

Logger
---

```
$Cacheer->setConfig()->setLoggerPath(string $path);
```
Define o caminho onde os logs serão armazenados.

- Parâmetros

```
$path: Caminho completo para o arquivo de logs.
```

**Exemplo:**

```
$Cacheer->setConfig()->setLoggerPath('/path/to/logs/CacheerPHP.log');
```

### 2. **Drivers**

```
useFileDriver()
```
Define o driver de cache como baseado em arquivos.

```
$Cacheer->setDriver()->useFileDriver();
```

Define o driver de cache como baseado em banco de dados.

```
useDatabaseDriver()
```

```
$Cacheer->setDriver()->useDatabaseDriver();
```

## Compatibilidade

- PHP: 8.0 ou superior.
- Drivers de Banco de Dados: MySQL, PostgreSQL, SQLite.

Aproveite os recursos avançados da versão 2.0.0 do CacheerPHP para gerenciar o cache da sua aplicação com eficiência!

### Testes

Para rodar os testes, vá para a raíz do projeto e digite o comando:

```sh
vendor/bin/phpunit
```
