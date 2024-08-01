# Cacheer-PHP


[![Maintainer](https://img.shields.io/badge/maintainer-@silviooosilva-blue.svg?style=for-the-badge&color=blue)](https://github.com/silviooosilva)
![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/silviooosilva/cacheer-php/PHP?style=for-the-badge&color=blue)
[![Latest Version](https://img.shields.io/github/release/silviooosilva/CacheerPHP.svg?style=for-the-badge&color=blue)](https://github.com/silviooosilva/CacheerPHP/releases)
[![Quality Score](https://img.shields.io/scrutinizer/g/silviooosilva/CacheerPHP.svg?style=for-the-badge&color=blue)](https://scrutinizer-ci.com/g/silviooosilva/CacheerPHP)
![Packagist Downloads](https://img.shields.io/packagist/dt/silviooosilva/cacheer-php?style=for-the-badge&color=blue)



Cacheer-PHP é um pacote minimalista para caching em PHP, oferecendo uma interface simples para armazenar e recuperar dados em cache utilizando arquivos.

## Funcionalidades

- Armazenamento e recuperação de cache em arquivos.
- Expiração de cache personalizável.
- Limpeza e flush de cache.
- Suporte a namespaces para organização de cache.
- Limpeza automática do diretório de cache (`flushAfter`).
- Saída de Dados Personalizada (`JSON, Array, Strings, Objetos`)

## Instalação

1. Clone o repositório ou faça o download dos arquivos:

   ```sh
   git clone https://github.com/silviooosilva/CacheerPHP.git
   ```

2. Inclua o autoload do Composer no seu projeto

   ```php
   require_once __DIR__ . '/vendor/autoload.php';
   ```

3. Instale as dependências via Composer:

   ```sh
   composer install ou composer update
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

### Testes

Para rodar os testes, vá para a raíz do projeto e digite o comando:

```sh
vendor/bin/phpunit
```
