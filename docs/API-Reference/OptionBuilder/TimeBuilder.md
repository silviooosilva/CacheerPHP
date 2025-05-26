## API Reference

TimeBuilder provides a fluid and chainable way of defining time periods in a more intuitive way and without typing errors. 

It allows expirationTime and flushAfter values to be passed directly as integers or defined using chained methods such as day(1), week(2), etc.

#### Simple use

```php
OptionBuilder::forFile()
    ->expirationTime('1 day')
    ->build();
```
Or use TimeBuilder's chained approach:

```php
OptionBuilder::forFile()
    ->expirationTime()->day(1)
    ->build();
```

#### Available methods

Each method allows you to set a specific time interval.

| Method        | Description                      | Example       |
|--------------|--------------------------------|--------------|
| `second($value)` | Define o tempo em segundos  | `->second(30)` |
| `minute($value)` | Define o tempo em minutos   | `->minute(15)` |
| `hour($value)`   | Define o tempo em horas     | `->hour(3)`    |
| `day($value)`    | Define o tempo em dias      | `->day(7)`     |
| `week($value)`   | Define o tempo em semanas   | `->week(2)`    |
| `month($value)`  | Define o tempo em meses     | `->month(1)`   |
| `year($value)`   | Define o tempo em anos      | `->year(1)`    |

#### Full Example

```php
$Options = OptionBuilder::forFile()
    ->dir(__DIR__ . '/cache')
    ->expirationTime()->week(1)
    ->flushAfter()->minute(30)
    ->build();

var_dump($Options);
```

**Expected Output**

```php
[
    "cacheDir" => "/path/to/cache",
    "expirationTime" => "1 week",
    "flushAfter" => "30 minutes"
]
```

Now you can set expiration and flush times without having to remember exact strings. 🚀