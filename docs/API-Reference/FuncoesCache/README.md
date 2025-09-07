## Cache functions - CacheerPHP

CacheerPHP offers a robust set of functions for managing caching in your PHP application. Below is the detailed documentation for each method available:

---

> **Note:** Each method can be called statically using `Cacheer::method()` or dynamically via an instance `$cache->method()`.

## Basic Cache Operations

### `getCache()` - Retrieves data from the cache

```php

/**
* Gets an item from the cache. If the item doesn't exist or is expired, returns null.
* @param string $cacheKey Unique item key
* @param string $namespace Namespace for organization
* @param string|int $ttl Lifetime in seconds (default: 3600)
* @return CacheDataFormatter|mixed Returns data in special format or raw value
*/
$Cacheer->getCache(string $cacheKey, string $namespace, string|int $ttl = 3600);
```

### `getMany()` - Retrieves multiple items from the cache

```php
/**
* Gets multiple items from the cache by their keys.
* @param array $cacheKeys Array of item keys
* @param string $namespace Namespace for organization
* @param string|int $ttl Lifetime in seconds (default: 3600)
* @return CacheDataFormatter Returns a formatter with the retrieved items
*/
$Cacheer->getMany(array $cacheKeys, string $namespace, string|int $ttl = 3600);
```

### `getAll()` - Retrieves all items in a namespace

```php
/**
* Gets all items in a specific namespace.
* @param string $namespace Namespace for organization
* @return CacheDataFormatter Returns a formatter with all items in the namespace
*/
$Cacheer->getAll(string $namespace);
```

### `putCache()` - Stores data in the cache

```php

/**
* Stores an item in the cache with a specific TTL.
* @param string $cacheKey Unique item key
* @param mixed $cacheData Data to be stored (serializable)
* @param string|int $ttl Lifetime in seconds (default: 3600)
* @return void
*/
$Cacheer->putCache(string $cacheKey, mixed $cacheData, string|int $ttl = 3600);
```

### `putMany()` - Mass operations

```php

/**
* Stores multiple cache items at once with shared TTL.
* @param array $items Associative array [key => value]
* @param string $namespace Common namespace for all items
* @param int $batchSize Number of operations per time
* @return void
*/
$Cacheer->putMany(array $items, string $namespace, int $batchSize = 100);
```

### `appendCache()` - Adding to existing cache

```php
/**
* Adds data to an existing cache item (useful for arrays or strings).
* @param string $cacheKey Existing item key
* @param mixed $cacheData Data to be added
* @param string $namespace Item namespace
* @return void
*/
$Cacheer->appendCache(string $cacheKey, mixed $cacheData, string $namespace);
```

### `has()` - Checks if a key exists in the cache and is still valid (has not expired).

```php
/**
* Checks whether a particular cache key exists, and whether it is still valid.
* @param string $cacheKey
* @param string $namespace
* @return void
*/
$Cacheer->has(string $cacheKey, string $namespace);
```

### `renewCache()` - Renew cache TTL


```php
/**
* Updates the lifetime of an existing item without modifying its data.
* @param string $cacheKey Item key
* @param string|int $ttl New TTL in seconds (default: 3600)
* @param string $namespace Item namespace
* @return mixed Returns the item data or false if it fails
*/
$Cacheer->renewCache(string $cacheKey, string|int $ttl = 3600, string $namespace);
```

### `increment()` - Numeric increment

```php
/**
* Increments a numeric value in the cache.
* @param string $cacheKey Item key
* @param int $amount Value to increment (default: 1)
* @param string $namespace Item namespace
* @return bool True if successful
*/
$Cacheer->increment(string $cacheKey, int $amount, string $namespace);
```

### `decrement()` - Numerical decrement

```php
/**
* Decrements a numeric value in the cache.
* @param string $cacheKey Item key
* @param int $amount Value to decrement (default: 1)
* @param string $namespace Item namespace
* @return bool True if successful
*/
$Cacheer->decrement(string $cacheKey, int $amount, string $namespace);
```

### `forever()` - Permanent storage

```php
/**
* Stores an item in the cache with no expiration time.
* @param string $cacheKey Unique key
* @param mixed $cacheData Data to be stored
* @return void
*/
$Cacheer->forever(string $cacheKey, mixed $cacheData);
```

### `remember()` - Standard “Get or Calculate”

```php
/**
* Gets the item from the cache or executes the closure and stores the result.
* @param string $cacheKey Item key
* @param int|string $ttl Lifetime in seconds
* @param Closure $callback Function that returns the data if the cache does not exist
* @return mixed
*/
$Cacheer->remember(string $cacheKey, int|string $ttl, Closure $callback);
```

### `rememberForever()` - Standard “Get or Calculate” forever 

```php
/**
* Similar to remember, but stores the result without expiration.
* @param string $cacheKey Item key
* @param int|string $ttl Lifetime in seconds
* @param Closure $callback Function that returns the data if the cache does not exist
* @return mixed
*/
$Cacheer->rememberForever(string $cacheKey, int|string $ttl, Closure $callback);
```

### `getAndForget()` - Retrieve and remove


```php
/**
* Gets an item from the cache and immediately removes it.
* @param string $cacheKey Item key
* @param string $namespace Item namespace
* @return mixed Item data or null if it doesn't exist
*/
$Cacheer->getAndForget(string $cacheKey, string $namespace);
```

### `add()` - Conditional addition

```php
/**
* Adds an item to the cache only if the key does not exist.
* @param string $cacheKey Item key
* @param mixed $cacheData Data to be stored
* @param string $namespace Item namespace
* @param int|string $ttl Lifetime in seconds
* @return bool True if the item was added, false if it already existed
*/
$Cacheer->add(string $cacheKey, mixed $cacheData, string $namespace, int|string $ttl);
```

### `clearCache()` - Selective cleaning


```php
/**
* Removes a specific item from the cache.
* @param string $cacheKey Item key
* @param string $namespace Item namespace
* @return void
*/
$Cacheer->clearCache(string $cacheKey, string $namespace);
```

### `flushCache()` - Total cleaning

```php
/**
* Removes all items from the cache (complete cleaning).
* @return void
*/
$Cacheer->flushCache();
```

### `tag()` e `flushTag()` - Agrupar e invalidar por tag

```php
/**
 * Associa uma ou mais chaves a uma tag.
 * Aceita tanto "key" quanto "namespace:key".
 * Retorna true em caso de sucesso.
 */
$Cacheer->tag(string $tag, string ...$keys): bool;

/**
 * Remove todos os itens associados a uma tag.
 * Retorna true em caso de sucesso.
 */
$Cacheer->flushTag(string $tag): bool;

// Exemplos básicos
Cacheer::putCache('user:1', ['id' => 1]);
Cacheer::putCache('user:2', ['id' => 2]);

// Sem namespace explícito
Cacheer::tag('users', 'user:1', 'user:2');
Cacheer::flushTag('users'); // invalida 'user:1' e 'user:2'

// Com namespace explícito
Cacheer::putCache('profile', ['id' => 10], 'nsA');
Cacheer::putCache('settings', ['id' => 10], 'nsA');
Cacheer::tag('grpA', 'nsA:profile', 'nsA:settings');
Cacheer::flushTag('grpA'); // invalida nsA:profile e nsA:settings
```

Notas de implementação por driver (resumo):
- File: índice persistido em `cacheDir/_tags/{tag}.json`.
- Redis: índice em Set `tag:{tag}`.
- Database: índice no namespace reservado `__tags__` com chave `tag:{tag}`.
- Array: índice em memória, reiniciado em `flushCache()`.
### `useCompression()` - Enable or disable compression

```php
$Cacheer->useCompression();
$Cacheer->useCompression(false);
```

### `useEncryption()` - Enable AES encryption

```php
$Cacheer->useEncryption('secret-key');
```
---

Each of the functions below now returns a boolean indicating the success of the operation. If you prefer, you can still check the status separately:

```php
$Cacheer->isSuccess(); // Returns true or false
$Cacheer->getMessage(); // Returns a message
```
