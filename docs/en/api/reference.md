# API Reference

This page summarizes the main APIs and points to detailed topics.

- [Core configuration: `setConfig()`](../../API-Reference/setConfig.md)
- [Driver selection: `setDriver()`](../../API-Reference/setDriver.md)
- [Fluent options: `OptionBuilder`](../../API-Reference/optionBuilder.md)
- [Time helpers: `TimeBuilder`](../../API-Reference/OptionBuilder/TimeBuilder.md)
- [Compression & encryption](../../API-Reference/compression_encryption.md)
- [Cache functions (get/put/has/flush/etc.)](../../API-Reference/FuncoesCache/README.md)

Notes
- `expirationTime` acts as a default TTL when you omit TTL in `putCache()` (or pass the implicit 3600). Explicit TTL values other than 3600 override the default.
- `flushAfter` enables an auto-flush check on store initialization; if the interval has elapsed the store will call `flushCache()`.

If you prefer a single-page overview, see [api-reference.md](../../api-reference.md) (legacy index).
