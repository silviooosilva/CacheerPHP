# Referência da API

Resumo das principais APIs e links para os tópicos detalhados.

- Configuração: `setConfig()` → `../../API-Reference/setConfig.md`
- Seleção de driver: `setDriver()` → `../../API-Reference/setDriver.md`
- Opções fluentes: `OptionBuilder` → `../../API-Reference/optionBuilder.md`
- Auxiliar de tempo: `TimeBuilder` → `../../API-Reference/OptionBuilder/TimeBuilder.md`
- Compressão e criptografia → `../../API-Reference/compression_encryption.md`
- Funções de cache (get/put/has/flush/etc.) → `../../API-Reference/FuncoesCache/README.md`

Notas
- `expirationTime` funciona como TTL padrão quando você não informa na `putCache()` (ou usa o padrão 3600). TTLs explícitos diferentes de 3600 prevalecem.
- `flushAfter` habilita uma verificação de limpeza automática ao inicializar o store; se o intervalo tiver passado, o store chama `flushCache()`.

Se preferir uma visão em página única, consulte `../../api-reference.md` (índice legado).
