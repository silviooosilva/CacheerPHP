# CacheerPHP

[![Maintainer](https://img.shields.io/badge/maintainer-@silviooosilva-blue.svg?style=for-the-badge&color=blue)](https://github.com/silviooosilva)
![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/silviooosilva/cacheer-php/PHP?style=for-the-badge&color=blue)
[![Latest Version](https://img.shields.io/github/release/silviooosilva/CacheerPHP.svg?style=for-the-badge&color=blue)](https://github.com/silviooosilva/CacheerPHP/releases)
[![Quality Score](https://img.shields.io/scrutinizer/g/silviooosilva/CacheerPHP.svg?style=for-the-badge&color=blue)](https://scrutinizer-ci.com/g/silviooosilva/CacheerPHP)
![Packagist Downloads](https://img.shields.io/packagist/dt/silviooosilva/cacheer-php?style=for-the-badge&color=blue)

CacheerPHP é um pacote minimalista para caching em PHP. Agora, na versão **3.0.0**, você tem ainda mais flexibilidade, suporte a múltiplos backends (arquivos, banco de dados e Redis), além de novas funcionalidades para monitoramento, compressão, Criptografia(Em Breve) e um design de API mais robusto.

---

## Funcionalidades

- **Armazenamento e Recuperação de Cache:** Suporte a armazenamento em arquivos, bancos de dados (MySQL, PostgreSQL, SQLite) e Redis.
- **Expiração Personalizável:** Defina o TTL (Time To Live) do cache com precisão.
- **Limpeza e Flush de Cache:** Suporte para limpeza manual e automática (via `flushAfter`).
- **Suporte a Namespaces:** Organize suas entradas de cache por categorias.
- **Saída de Dados Personalizada:** Opções para retornar os dados em `JSON`, `Array`, `String` ou `Objeto`.
- **Compressão e Criptografia(Em Breve):** Reduza o espaço de armazenamento e aumente a segurança dos dados cacheados.
- **Cache Statistics and Monitoring:** Acompanhe estatísticas de acertos, falhas e tempos médios de leitura/escrita(Em Breve).
- **Logging Avançado:** Monitoramento detalhado do funcionamento do sistema de cache.

---

## Instalação

O CacheerPHP 3.0.0 está disponível via Composer. Adicione a seguinte linha no seu arquivo **composer.json**:

```sh
  "silviooosilva/cacheer-php": "^3.0"
```

Ou rode o comando:

```sh
composer require silviooosilva/cacheer-php
```

## AVISO IMPORTANTE!!!

Não se esqueça de configurar as suas variáveis de ambiente, presentes no arquivo .env.example.
Relembrar que devem ser configurados no arquivo .env, e não no .env.example.
Para tal, faça o seguinte na sua linha de comandos:

```sh
cp .env.example .env 
```


## Documentação

1.  [Armazenar e Recuperar Dados em Cache](docs/example01.md)
2.  [Expiração de cache personalizável](docs/example02.md)
3.  [Limpeza e flush de cache](docs/example03.md)
4.  [Suporte a namespaces para organização de cache](docs/example04.md)
5.  [Limpeza automática do diretório de cache `flushAfter`](docs/example09.md)
6.  [Cache de Resposta de API](docs/example05.md)
7.  [Saída de Dados Personalizada (`JSON`)](docs/example06.md)
8.  [Saída de Dados Personalizada (`Array`)](docs/example07.md)
9.  [Saída de Dados Personalizada (`String`)](docs/example08.md)
10. [Guia de Upgrade para Versão 2.0.0](docs/guia2.0.0.md)
11. [API Reference](docs/api-reference.md)

Tem ainda disponível diversos exemplos práticos na pasta **Examples**, na raíz do projeto.

## Compatibilidade

- PHP: 8.0 ou superior.
- Drivers de Banco de Dados: MySQL, PostgreSQL, SQLite.
- Redis

### Testes

Para rodar os testes, vá para a raíz do projeto e digite o comando:

```sh
vendor/bin/phpunit
```
