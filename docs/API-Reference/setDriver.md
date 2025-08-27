## API Reference

#### 2. **Drivers**

```php
<?php

require_once __DIR__ . "/../vendor/autoload.php"; 

$Cacheer = new Cacheer();
$Cacheer->setDriver();
```

```php
Cacheer::setDriver();
```

> **Note:** The driver methods can also be called statically, e.g. `Cacheer::setDriver()->useFileDriver();`

Defines the cache driver as file-based:
```php
<?php

require_once __DIR__ . "/../vendor/autoload.php"; 

$Cacheer = new Cacheer();
$Cacheer->setDriver()->useFileDriver();
```

```php
Cacheer::setDriver()->useFileDriver();
```

Defines the cache driver as database-based:
```php
<?php

require_once __DIR__ . "/../vendor/autoload.php"; 

$Cacheer = new Cacheer();
$Cacheer->setDriver()->useDatabaseDriver();
```

```php
Cacheer::setDriver()->useDatabaseDriver();
```

Sets the cache driver to be based on Redis:
```php
<?php

require_once __DIR__ . "/../vendor/autoload.php"; 

$Cacheer = new Cacheer();
$Cacheer->setDriver()->useRedisDriver();
```

```php
Cacheer::setDriver()->useRedisDriver();
```

Sets the cache driver to be based on Arrays (Memory):
```php
<?php

require_once __DIR__ . "/../vendor/autoload.php"; 

$Cacheer = new Cacheer();
$Cacheer->setDriver()->useArrayDriver();
```

```php
Cacheer::setDriver()->useArrayDriver();
```