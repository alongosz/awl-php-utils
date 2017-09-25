<?php


namespace Awl\Helper\Database;

use Doctrine\DBAL\Connection;
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
     * @param string $table db table name
     * @param array $predicates
     */
    public function display($table, array $predicates = [])
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
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if (empty($data)) {
            echo "$table is empty", PHP_EOL;
            return;
        }

        $table = new Table(
            new ConsoleOutput()
        );
        $table
            ->setHeaders(array_keys($data[0]))
            ->setRows($data)
        ;
        $table->render();
    }
}
