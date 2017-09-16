<?php

namespace Service;

use Doctrine\DBAL\Connection;

class StatisticsManager
{
    const TYPE_LOADED = 'loaded';
    const TYPE_PROGRESS = 'progress';
    const TYPE_FINISHED = 'finished';

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string $cookie
     * @param string $type
     * @param $payload
     * @return bool
     */
    public function save($cookie, $type, $payload)
    {
        $now = new \DateTime();
        $statement = $this->connection->prepare('INSERT INTO views (cookie, type, payload, timestamp) 
            VALUES (:cookie, :type, :payload, :timestamp)');

        return $statement->execute([
            ':cookie' => $cookie,
            ':type' => $type,
            ':payload' => $payload,
            ':timestamp' => $now->getTimestamp(),
        ]);
    }
}
