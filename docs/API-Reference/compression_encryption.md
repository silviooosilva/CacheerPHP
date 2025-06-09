## API Reference - Compression & Encryption

#### `useCompression()`
Enables or disables data compression before storage. When enabled, data is serialized and compressed using `gzcompress`.

```php
$Cacheer->useCompression();        // enable
$Cacheer->useCompression(false);   // disable
```

#### `useEncryption()`
Activates encryption using AES-256-CBC. Provide a secret key to encrypt and decrypt cached data.

```php
$Cacheer->useEncryption('your-secret-key');
```

You can combine both features for smaller and secure payloads:

```php
$Cacheer->useCompression()->useEncryption('your-secret-key');
```
