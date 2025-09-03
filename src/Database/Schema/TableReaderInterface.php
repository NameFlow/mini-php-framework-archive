<?php
declare(strict_types=1);

namespace App\Database\Schema;

interface TablesReaderInterface
{
    public function getTableNames(): array; 
}