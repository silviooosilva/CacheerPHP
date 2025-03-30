## API Reference

#### 2. **Drivers**

```php
<?php

require_once __DIR__ . "/../vendor/autoload.php"; 

$Cacheer = new Cacheer();
$Cacheer->setDriver();
```

Define o driver de cache como baseado em arquivos:
```php
<?php

require_once __DIR__ . "/../vendor/autoload.php"; 

$Cacheer = new Cacheer();
$Cacheer->setDriver()->useFileDriver();
```

Define o driver de cache como baseado em banco de dados:
```php
<?php

require_once __DIR__ . "/../vendor/autoload.php"; 

$Cacheer = new Cacheer();
$Cacheer->setDriver()->useDatabaseDriver();
```

Define o driver de cache como baseado no Redis:
```php
<?php

require_once __DIR__ . "/../vendor/autoload.php"; 

$Cacheer = new Cacheer();
$Cacheer->setDriver()->useRedisDriver();
```