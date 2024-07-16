# Cacheer-PHP

Cacheer-PHP é um pacote minimalista para caching em PHP, oferecendo uma interface simples para armazenar e recuperar dados em cache utilizando arquivos.

## Funcionalidades

- Armazenamento e recuperação de cache em arquivos.
- Expiração de cache personalizável.
- Limpeza e flush de cache.
- Suporte a namespaces para organização de cache.

## Instalação

1. Clone o repositório ou faça o download dos arquivos:

    ```sh
    git clone https://github.com/silviooosilva/CacheerPHP.git
    ```

2. Inclua o autoload do Composer no seu projeto


## Uso

<p>Exemplo 01 </p>
```sh
<b>Cache de Dados Simples</b>
```

<p>Exemplo 02 </p>
```sh
<b>Cache com Tempo de Expiração Personalizada</b>
```

<p>Exemplo 03 </p>
```sh
<b>Limpeza e Flush do Cache</b>
```

<p>Exemplo 04 </p>
```sh
<b>Uso de Namespaces</b>
```

<p>Exemplo 05 </p>
```sh
<b>Cache de Resposta de API</b>
```

### Configuração

Para utilizar o Cacheer-PHP, você precisa configurar o diretório de cache:

```sh
$options = [
    'cacheDir' => __DIR__ . '/cache'
];
```
Opcionalmente, você pode configurar o tempo de expiração do cache:

```sh
$options = [
    'cacheDir' => __DIR__ . '/cache',
    'expirationTime' => '1 hour(s)' // String
];
```

Pode configurar o tempo de expiração do cache em: 
```php
<p>Minutos -> minute(s)</p>
<p>Horas -> hour(s)</p>
<p>Segundos -> second(s)</p>

```