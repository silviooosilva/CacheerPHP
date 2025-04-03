## API Reference

O TimeBuilder fornece uma forma fluida e encadeável de definir períodos de tempo de maneira mais intuitiva e sem erros de digitação. 

Ele permite que os valores de expirationTime e flushAfter sejam passados diretamente como inteiros ou definidos usando métodos encadeados, como day(1), week(2), etc.

#### Uso Simples

```php
OptionBuilder::forFile()
    ->expirationTime('1 day')
    ->build();
```
Ou utilizar a abordagem encadeada do TimeBuilder:

```php
OptionBuilder::forFile()
    ->expirationTime()->day(1)
    ->build();
```

#### Métodos Disponíveis

Cada método permite definir um intervalo de tempo específico.

| Método        | Descrição                      | Exemplo       |
|--------------|--------------------------------|--------------|
| `second($value)` | Define o tempo em segundos  | `->second(30)` |
| `minute($value)` | Define o tempo em minutos   | `->minute(15)` |
| `hour($value)`   | Define o tempo em horas     | `->hour(3)`    |
| `day($value)`    | Define o tempo em dias      | `->day(7)`     |
| `week($value)`   | Define o tempo em semanas   | `->week(2)`    |
| `month($value)`  | Define o tempo em meses     | `->month(1)`   |
| `year($value)`   | Define o tempo em anos      | `->year(1)`    |

#### Exemplo Completo

```php
$Options = OptionBuilder::forFile()
    ->dir(__DIR__ . '/cache')
    ->expirationTime()->week(1)
    ->flushAfter()->minute(30)
    ->build();

var_dump($Options);
```

**Saída Esperada**

```php
[
    "cacheDir" => "/caminho/para/cache",
    "expirationTime" => "1 week",
    "flushAfter" => "30 minutes"
]
```

Agora, você pode definir tempos de expiração e flush sem precisar lembrar de strings exatas. 🚀