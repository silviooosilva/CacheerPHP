## Example 10 — Tagging and tag invalidation

This example shows how to associate keys to tags and invalidate them in bulk across all drivers. You can use plain keys (no namespace) or namespaced keys in the form `namespace:key`.

### File driver

```php
<?php
require __DIR__ . '/../vendor/autoload.php';

use Silviooosilva\CacheerPhp\Cacheer;

$cacheDir = __DIR__ . '/../.cache';

$cache = new Cacheer([
    'drive'    => 'file',
    'cacheDir' => $cacheDir,
]);

// Store values
$cache->putCache('user:1', ['id' => 1, 'name' => 'Ana']);
$cache->putCache('user:2', ['id' => 2, 'name' => 'Bruno']);

// Tag without explicit namespace
$cache->tag('users', 'user:1', 'user:2');

// Namespaced keys
$cache->putCache('profile', ['bio' => 'Hi'], 'nsA');
$cache->putCache('settings', ['lang' => 'pt'], 'nsA');
$cache->tag('ns-group', 'nsA:profile', 'nsA:settings');

// Invalidate by tag
$cache->flushTag('users');     // removes user:1 and user:2
$cache->flushTag('ns-group');  // removes nsA:profile and nsA:settings

```

Notes:
- Tag index is saved in `cacheDir/_tags/{tag}.json` and removed on `flushTag`.

### Redis driver

```php
<?php
require __DIR__ . '/../vendor/autoload.php';

use Silviooosilva\CacheerPhp\Cacheer;

$cache = new Cacheer();
$cache->setDriver()->useRedisDriver();

$cache->putCache('order:10', ['id' => 10]);
$cache->putCache('order:11', ['id' => 11]);
$cache->tag('orders', 'order:10', 'order:11');

// namespaced
$cache->putCache('summary', ['c' => 2], 'nsB');
$cache->tag('reports', 'nsB:summary');

$cache->flushTag('orders');  // clears order:10, order:11
$cache->flushTag('reports'); // clears nsB:summary
```

Notes:
- Uses a Redis Set `tag:{tag}` to index members; the set is deleted on `flushTag`.

### Database driver (MySQL/SQLite/PgSQL)

```php
<?php
require __DIR__ . '/../vendor/autoload.php';

use Silviooosilva\CacheerPhp\Cacheer;
use Silviooosilva\CacheerPhp\Core\Connect;

$cache = new Cacheer();
$cache->setConfig()->setDatabaseConnection(Connect::getConnection());
$cache->setDriver()->useDatabaseDriver();

$cache->putCache('p:1', ['id' => 1]);
$cache->putCache('p:2', ['id' => 2]);
$cache->tag('products', 'p:1', 'p:2');

// namespaced
$cache->putCache('view', ['ct' => 5], 'nsC');
$cache->tag('analytics', 'nsC:view');

$cache->flushTag('products');
$cache->flushTag('analytics');
```

Notes:
- Index is stored in the same table using reserved namespace `__tags__` and key `tag:{tag}`. It is removed on `flushTag`.

### Array driver (in‑memory)

```php
<?php
require __DIR__ . '/../vendor/autoload.php';

use Silviooosilva\CacheerPhp\Cacheer;

$cache = new Cacheer();
$cache->setDriver()->useArrayDriver();

$cache->putCache('x', 1);
$cache->putCache('y', 2);
$cache->tag('simple', 'x', 'y');
$cache->flushTag('simple');
```

Notes:
- Tag index lives in memory and is reset on `flushCache()`.

