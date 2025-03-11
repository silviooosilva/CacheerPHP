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