## API Reference

## **Classes Principais**

```bash
Silviooosilva\CacheerPhp\Cacheer
```

The package's main class, used for all caching operations.


## **Methods**

### 1. **Configuration**

#### `setConfig()`
Starts a customized configuration for CacheerPHP.

[API Reference - setConfig()](API-Reference/setConfig.md)

### 2. **Drivers**
It allows you to define the different backends available for use.

[API Reference - setDriver()](API-Reference/setDriver.md)

### 3. **OptionBuilder**
The **OptionBuilder** simplifies configuration via fluent builders per driver:
- `OptionBuilder::forFile()` → File options (`dir`, `expirationTime`, `flushAfter`)
- `OptionBuilder::forRedis()` → Redis options (`setNamespace`, default `expirationTime`, `flushAfter` auto-flush)
- `OptionBuilder::forDatabase()` → Database options (`table`, default `expirationTime`, `flushAfter` auto-flush)

Notes:
- `expirationTime` acts as default TTL when you omit a TTL in `putCache()` (or pass 3600). Explicit TTL values other than 3600 override the default.
- `flushAfter` triggers an auto-flush check when the store initializes; if the interval has elapsed, the store calls `flushCache()`.

[API Reference - OptionBuilder](API-Reference/optionBuilder.md)
### 4. **Compression & Encryption**
Built-in methods to reduce storage space and secure cached data.

[API Reference - Compression & Encryption](API-Reference/compression_encryption.md)

### 5. **Tagging**
Group keys under tags and invalidate them efficiently across drivers.

[API Reference - Cache Functions (tag/flushTag)](API-Reference/FuncoesCache/README.md)

See a complete usage example in: docs/example10.md
