## API Reference

O **OptionBuilder** lhe permite definir diferentes parametros para configuração do CacheerPHP, dando-lhe mais segurança, robustez e velocidade de execução, excluindo ainda, possíveis erros, como os de digitação, por exemplo.

Atualmente, é apenas compatível com o **FileCacheStore**, visto que é o driver que requer um conjunto de configurações antecipadas para o seu funcionamento.

Confira alguns exemplos:

[FileCacheStore - Exemplo01](../example01.md)

[FileCacheStore - Exemplo02](../example02.md)

Conseguiu constatar que os parametros são muito suscetíveis a erros de escrita, certo?
O **OptionBuilder** surge na necessidade de eliminar estes possíveis erros.

#### `OptionBuilder()`

O **OptionBuilder** possui métodos específicos para configurar cada tipo de driver de cache suportado.
Cada um deles inicializa a configuração para um determinado driver e retorna uma instância do builder correspondente.

`forFile()`

```php
<?php
$Options = OptionBuilder::forFile();
```
Este método inicializa o FileCacheStore, permitindo configurar diretório de cache, tempo de expiração e limpeza periódica do cache.

Métodos disponíveis após `forFile()`

```
dir(string $path) → Define o diretório onde os arquivos de cache serão armazenados.
expirationTime(string $time) → Define o tempo de expiração dos arquivos no cache.
flushAfter(string $interval) → Define um tempo para limpar automaticamente os arquivos do cache.
build() → Finaliza a configuração e retorna um array de opções prontas para uso.
```

**Exemplo de uso**

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

#### Em breve

```php
OptionBuilder::forRedis();
OptionBuilder::forDatabase();
```

O **OptionBuilder** simplifica a configuração do **CacheerPHP** eliminando erros de digitação e tornando o processo mais intuitivo.
Agora, basta escolher o método correspondente ao driver desejado e definir os parâmetros necessários para garantir um cache eficiente e otimizado. 🚀