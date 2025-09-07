# Configuration

CacheerPHP loads configuration from environment variables using [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv). Copy the `.env.example` file to `.env` and adjust the values for your environment.

```sh
cp .env.example .env
```

## Database settings

| Variable       | Description                                               | Default     |
| -------------- | --------------------------------------------------------- | ----------- |
| `DB_CONNECTION`| Database driver (`mysql`, `pgsql`, `sqlite`)              | `sqlite`    |
| `DB_HOST`      | Database host                                             | `localhost` |
| `DB_PORT`      | Database port                                             | `3306`      |
| `DB_DATABASE`  | Database name                                             | `cacheer_db`|
| `DB_USERNAME`  | Database user                                             | `root`      |
| `DB_PASSWORD`  | Database password                                         | *(empty)*   |
| `CACHEER_TABLE`| Cache table name for database driver                      | `cacheer_table` |

## Redis settings

| Variable          | Description                                | Default     |
| ----------------- | ------------------------------------------ | ----------- |
| `REDIS_CLIENT`    | Redis client library (e.g. `predis`)       | *(empty)*   |
| `REDIS_HOST`      | Redis server host                          | `localhost` |
| `REDIS_PASSWORD`  | Redis password                             | *(empty)*   |
| `REDIS_PORT`      | Redis port                                 | `6379`      |
| `REDIS_NAMESPACE` | Optional namespace prefix for cache keys   | *(empty)*   |

These variables are read during bootstrapping in `src/Boot/Configs.php`, enabling CacheerPHP to connect to your preferred backends.
