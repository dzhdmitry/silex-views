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

    /**
     * @return array
     */
    public function getStatistics()
    {
        $statement = $this->connection->prepare("
            SELECT 
                l.cookie as cookie, 
                l.amount as loaded, 
                COALESCE(p25.amount, 0) as progress25, 
                COALESCE(p50.amount, 0) as progress50, 
                COALESCE(p75.amount, 0) as progress75, 
                COALESCE(f.amount, 0) as finished
            FROM (
                SELECT cookie, COUNT(*) AS amount
                FROM views
                WHERE type = :loaded
                GROUP BY cookie
            ) as l

            LEFT JOIN (
                SELECT cookie, COUNT(*) AS amount
                FROM views
                WHERE type = :progress AND payload = 25
                GROUP BY cookie
            ) as p25 ON l.cookie = p25.cookie

            LEFT JOIN (
                SELECT cookie, COUNT(*) AS amount
                FROM views
                WHERE type = :progress AND payload = 50
                GROUP BY cookie
            ) as p50 ON l.cookie = p50.cookie

            LEFT JOIN (
                SELECT cookie, COUNT(*) AS amount
                FROM views
                WHERE type = :progress AND payload = 75
                GROUP BY cookie
            ) as p75 ON l.cookie = p75.cookie

            LEFT JOIN (
                SELECT cookie, COUNT(*) AS amount
                FROM views
                WHERE type = :finished
                GROUP BY cookie
            ) as f ON l.cookie = f.cookie");

        $statement->execute([
            ':loaded' => self::TYPE_LOADED,
            ':progress' => self::TYPE_PROGRESS,
            ':finished' => self::TYPE_FINISHED,
        ]);

        return $statement->fetchAll();
    }
}
