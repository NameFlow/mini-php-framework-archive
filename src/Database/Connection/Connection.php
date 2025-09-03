<?php
declare(strict_types=1);

namespace App\Database\Connection;


use PDO;

final class Connection
{
    public readonly PDO $pdo;

    public function __construct(string $dbPath)
    {
        $this->pdo = new PDO('sqlite:' . $dbPath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function prepare(string $sql): Statement
    {
        return new Statement($this->pdo->prepare($sql));
    }
}