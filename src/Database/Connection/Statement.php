<?php
declare(strict_types=1);

namespace App\Database\Connection;

use PDOStatement;
use PDO;

final class Statement
{
    public function __construct(
        private readonly PDOStatement $statement
    ){}

    public function execute(array $args = []): bool
    {
        return $this->statement->execute($args);
    }

    public function fetch(FetchAttributeEnum $mode = FetchAttributeEnum::AS_ASSOC): array | false
    {
        $pdoAttribute = $this->getPdoFetchAttribute($mode);
        return $this->statement->fetch($pdoAttribute);
    }

    public function fetchAll(FetchAttributeEnum $mode = FetchAttributeEnum::AS_ASSOC): array
    {
        $pdoAttribute = $this->getPdoFetchAttribute($mode);
        return $this->statement->fetchAll($pdoAttribute);
    }

    private function getPdoFetchAttribute(FetchAttributeEnum $mode): int 
    {
        return match ($mode) {
            FetchAttributeEnum::AS_ASSOC => PDO::FETCH_ASSOC,
            FetchAttributeEnum::AS_BOTH => PDO::FETCH_BOTH,
            FetchAttributeEnum::AS_CLASS => PDO::FETCH_CLASS,
            FetchAttributeEnum::AS_NUM => PDO::FETCH_NUM,
        };
    }

    public function columnCount(): int
    {
        return $this->statement->columnCount();
    }
    
    public function getColumnMeta(int $column): array | false
    {
        return $this->statement->getColumnMeta($column);
    }
}