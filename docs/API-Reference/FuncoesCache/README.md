## Funções de Cache - CacheerPHP

O CacheerPHP oferece um conjunto robusto de funções para gerenciamento de cache na sua aplicação PHP. Abaixo está a documentação detalhada de cada método disponível:

---

## Operações Básicas de Cache

### `getCache()` - Recupera dados do cache

```php

/**
* Obtém um item do cache. Se o item não existir ou estiver expirado, retorna null.
* @param string $cacheKey Chave única do item
* @param string $namespace Namespace para organização
* @param string|int $ttl Tempo de vida em segundos (padrão: 3600)
* @return CacheDataFormatter|mixed Retorna os dados em formato especial ou valor bruto
*/
$Cacheer->getCache(string $cacheKey, string $namespace, string|int $ttl = 3600);
```

### `putCache()` - Armazena dados no cache

```php

/**
* Armazena um item no cache com TTL específico.
* @param string $cacheKey Chave única do item
* @param mixed $cacheData Dados a serem armazenados (serializáveis)
* @param string|int $ttl Tempo de vida em segundos (padrão: 3600)
* @return void
*/
$Cacheer->putCache(string $cacheKey, mixed $cacheData, string|int $ttl = 3600);
```

### `putMany()` - Operações em Massa

```php

/**
* Armazena múltiplos itens de cache de uma vez com TTL compartilhado.
* @param array $items Array associativo [chave => valor]
* @param string $namespace Namespace comum para todos os itens
* @param int $batchSize número de operações por vez
* @return void
*/
$Cacheer->putMany(array $items, string $namespace, int $batchSize = 100);
```

### `appendCache()` - Acréscimo a cache existente

```php
/**
* Adiciona dados a um item de cache existente (útil para arrays ou strings).
* @param string $cacheKey Chave do item existente
* @param mixed $cacheData Dados a serem acrescentados
* @param string $namespace Namespace do item
* @return void
*/
$Cacheer->appendCache(string $cacheKey, mixed $cacheData, string $namespace);
```

### `has()` - Verifica se uma chave existe no cache e ainda é válida (não expirou).

```php
/**
* Verifica se uma determinada chave de cache existe, e se ainda é válida.
* @param string $cacheKey
* @param string $namespace
* @return void
*/
$Cacheer->has(string $cacheKey, string $namespace);
```

### `renewCache()` - Renova TTL do cache


```php
/**
* Atualiza o tempo de vida de um item existente sem modificar seus dados.
* @param string $cacheKey Chave do item
* @param string|int $ttl Novo TTL em segundos (padrão: 3600)
* @param string $namespace Namespace do item
* @return mixed Retorna os dados do item ou false se falhar
*/
$Cacheer->renewCache(string $cacheKey, string|int $ttl = 3600, string $namespace);
```

### `increment()` - Incremento numérico

```php
/**
* Incrementa um valor numérico no cache.
* @param string $cacheKey Chave do item
* @param int $amount Valor a incrementar (padrão: 1)
* @param string $namespace Namespace do item
* @return bool True se bem-sucedido
*/
$Cacheer->increment(string $cacheKey, int $amount, string $namespace);
```

### `decrement()` - Decremento numérico

```php
/**
* Decrementa um valor numérico no cache.
* @param string $cacheKey Chave do item
* @param int $amount Valor a decrementar (padrão: 1)
* @param string $namespace Namespace do item
* @return bool True se bem-sucedido
*/
$Cacheer->decrement(string $cacheKey, int $amount, string $namespace);
```

### `forever()` - Armazenamento permanente

```php
/**
* Armazena um item no cache sem tempo de expiração.
* @param string $cacheKey Chave única
* @param mixed $cacheData Dados a serem armazenados
* @return void
*/
$Cacheer->forever(string $cacheKey, mixed $cacheData);
```

### `remember()` - Padrão "Obter ou Calcular"

```php
/**
* Obtém o item do cache ou executa a closure e armazena o resultado.
* @param string $cacheKey Chave do item
* @param int|string $ttl Tempo de vida em segundos
* @param Closure $callback Função que retorna os dados se o cache não existir
* @return mixed
*/
$Cacheer->remember(string $cacheKey, int|string $ttl, Closure $callback);
```

### `rememberForever()` - Padrão "Obter ou Calcular" para sempre 

```php
/**
* Semelhante ao remember, mas armazena o resultado sem expiração.
* @param string $cacheKey Chave do item
* @param int|string $ttl Tempo de vida em segundos
* @param Closure $callback Função que retorna os dados se o cache não existir
* @return mixed
*/
$Cacheer->rememberForever(string $cacheKey, int|string $ttl, Closure $callback);
```

### `getAndForget()` - Recupera e remove


```php
/**
* Obtém um item do cache e imediatamente o remove.
* @param string $cacheKey Chave do item
* @param string $namespace Namespace do item
* @return mixed Dados do item ou null se não existir
*/
$Cacheer->getAndForget(string $cacheKey, string $namespace);
```

### `add()` - Adição condicional

```php
/**
* Adiciona um item ao cache apenas se a chave não existir.
* @param string $cacheKey Chave do item
* @param mixed $cacheData Dados a serem armazenados
* @param string $namespace Namespace do item
* @param int|string $ttl Tempo de vida em segundos
* @return bool True se o item foi adicionado, false se já existia
*/
$Cacheer->add(string $cacheKey, mixed $cacheData, string $namespace, int|string $ttl);
```

### `clearCache()` - Limpeza seletiva


```php
/**
* Remove um item específico do cache.
* @param string $cacheKey Chave do item
* @param string $namespace Namespace do item
* @return void
*/
$Cacheer->clearCache(string $cacheKey, string $namespace);
```

### `flushCache()` - Limpeza total

```php
/**
* Remove todos os itens do cache (limpeza completa).
* @return void
*/
$Cacheer->flushCache();
```
---

Cada uma das funções abaixo permite interagir com o cache de formas diferentes. Funções que “retornam void” na verdade definem internamente o status da operação, que pode ser verificado via:

```php
$Cacheer->isSuccess(); // Retorna true ou false
$Cacheer->getMessage(); // Retorna uma mensagem descritiva
```