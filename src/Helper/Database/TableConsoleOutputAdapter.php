<?php

declare(strict_types=1);

namespace Awl\Helper\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

class TableConsoleOutputAdapter
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param array $predicates
     */
    public function display(string $table, array $predicates = []): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->select('*')
            ->from($table)
            ->where('1=1')
        ;

        $i = 0;
        foreach ($predicates as $predicate => $value) {
            $query->andWhere($predicate);
            $query->setParameter($i++, $value);
        }

        $stmt = $query->execute();
        $data = $stmt->fetchAll(FetchMode::ASSOCIATIVE);
        if (empty($data)) {
            echo "$table is empty", PHP_EOL;
            return;
        }

        $this->displayTable($data);
    }

    /**
     * Renders the given rows to STDOUT. Input array is expected to be zero-based,
     * multidimensional with header names as associative keys in each row.
     */
    public function displayTable(array $rows): void
    {
        $table = new Table(new ConsoleOutput());
        $table
            ->setHeaders(array_keys($rows[0]))
            ->setRows($rows)
        ;
        $table->render();
    }
}
