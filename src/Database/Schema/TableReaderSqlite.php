<?php
declare(strict_types=1);

namespace App\Database\Schema;

use App\Database\Connection\Connection;

class TablesReaderSqlite implements TablesReaderInterface
{
    public function __construct(
        private readonly Connection $connection
    ){}

    public function getTableNames(): array
    {
        $statement = $this->connection->prepare(
            'SELECT name FROM sqlite_master WHERE type="table" AND name NOT LIKE "sqlite_%"'
        );

        $statement->execute([]);
        $fetchedData = $statement->fetchAll();

        return array_map(fn($item) => $item[0], $fetchedData);
    }
}