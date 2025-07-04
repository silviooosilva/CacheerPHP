## API Reference

The **OptionBuilder** allows you to define different parameters for configuring CacheerPHP, giving it more security, robustness and speed of execution, as well as excluding possible errors, such as typos, for example.

Also check out the **TimeBuilder**: [TimeBuilder - Introduction](./OptionBuilder/TimeBuilder.md)

Currently, it is only compatible with **FileCacheStore**, as this is the driver that requires a set of configurations in advance for it to work.

Here are some examples:

[FileCacheStore - Example01](../example01.md)

[FileCacheStore - Example02](../example02.md)

You've seen that parameters are very susceptible to typing errors, right?
The **OptionBuilder** arises from the need to eliminate these possible errors.

#### `OptionBuilder()`

The **OptionBuilder** has specific methods for configuring each type of cache driver supported.
Each one initializes the configuration for a given driver and returns an instance of the corresponding builder.

`forFile()`

```php
<?php
$Options = OptionBuilder::forFile();
```
This method initializes FileCacheStore, allowing you to configure the cache directory, expiration time and periodic clearing of the cache.

Methods available after `forFile()`

```sh
dir(string $path) → Defines the directory where the cache files will be stored.
expirationTime(string $time) → Sets the expiration time of the files in the cache.
flushAfter(string $interval) → Sets a time to automatically flush the files from the cache.
build() → Finalizes the configuration and returns an array of options ready for use.
```

**Example of use**

```php
<?php
require_once __DIR__ . "/../vendor/autoload.php"; 

$Options = OptionBuilder::forFile()
    ->dir(__DIR__ . "/cache")
    ->expirationTime("2 hours")
    ->flushAfter("1 day")
    ->build();

$Cacheer = new Cacheer($Options);
$Cacheer->setDriver()->useFileDriver(); //File Driver
```

#### Coming soon

```php
OptionBuilder::forRedis();
OptionBuilder::forDatabase();
```

The **OptionBuilder** simplifies the configuration of the **CacheerPHP** by eliminating typing errors and making the process more intuitive.
Now all you have to do is choose the method corresponding to the desired driver and set the necessary parameters to ensure efficient and optimized caching. 🚀