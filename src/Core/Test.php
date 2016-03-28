<?php
/**
 * VisualPHPUnit
 *
 * VisualPHPUnit is a visual front-end for PHPUnit.
 *
 * PHP Version 5.6<
 *
 * @author    Johannes Skov Frandsen <localgod@heaven.dk>
 * @copyright 2011-2015 VisualPHPUnit
 * @license   http://opensource.org/licenses/BSD-3-Clause The BSD License
 * @link      https://github.com/VisualPHPUnit/VisualPHPUnit VisualPHPUnit
 */
namespace Visualphpunit\Core;

use \Doctrine\DBAL\Connection;
use \DateTime;

/**
 * Visualphpunit test result
 *
 * @author Johannes Skov Frandsen <localgod@heaven.dk>
 */
class Test
{

    /**
     * Create the table if it dos not exists
     *
     * @param \Doctrine\DBAL\Connection $connection
     *
     * @return boolean
     */
    public static function createTable(Connection $connection)
    {
        $sql = "CREATE TABLE IF NOT EXISTS tests(
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            class TEXT,
            status TEXT,
            executed NUMERIC);";
        $stmt = $connection->prepare($sql);
        return $stmt->execute();
    }

    /**
     * Drop the table if it exists
     *
     * @param \Doctrine\DBAL\Connection $connection
     *
     * @return boolean
     */
    public static function dropTable(Connection $connection)
    {
        $sql = "DROP tests;";
        $stmt = $connection->prepare($sql);
        return $stmt->execute();
    }

    /**
     * Truncate the table
     *
     * @param \Doctrine\DBAL\Connection $connection
     *
     * @return boolean
     */
    public static function truncateTable(Connection $connection)
    {
        $sql = "DELETE FROM tests;";
        $stmt = $connection->prepare($sql);
        return $stmt->execute();
    }

    /**
     * Store a test suite result
     *
     * @param \Doctrine\DBAL\Connection $connection
     * @param mixed[] $result
     *
     * @return boolean
     */
    public static function store(Connection $connection, $result)
    {
        $sql = "INSERT INTO tests (name, class, status, executed) VALUES (?, ?, ?, ?);";
        $date = new DateTime();
        
        foreach ($result['tests'] as $test) {
            $stmt = $connection->prepare($sql);
            $stmt->bindValue(1, $test['name']);
            $stmt->bindValue(2, $test['class']);
            $stmt->bindValue(3, $test['status']);
            $stmt->bindValue(4, $date->format('Y-m-d H:i:s'));
            $stmt->execute();
        }
    }

    /**
     * Get test per day between dates
     *
     * @param \Doctrine\DBAL\Connection $connection
     * @param \DateTime $start
     * @param \DateTime $end
     *
     * @return mixed[]
     */
    public static function getByDay(Connection $connection, DateTime $start, DateTime $end)
    {
        $sql = "
            SELECT
            strftime('%Y-%m-%d  %H:%M:%S', executed) as datetime,
            strftime('%d', executed) as day,
            COUNT() as number,
            status
            FROM tests
            WHERE strftime('%Y-%m-%d', executed) BETWEEN ? AND ?
            AND status = 'passed'
            GROUP BY strftime('%d', executed)
            UNION
            SELECT
            strftime('%Y-%m-%d  %H:%M:%S', executed) as datetime,
            strftime('%d', executed) as day,
            COUNT() as number,
            status
            FROM tests
            WHERE strftime('%Y-%m-%d', executed) BETWEEN ? AND ?
            AND status = 'failed'
            GROUP BY strftime('%d', executed)    
            UNION
            SELECT
            strftime('%Y-%m-%d  %H:%M:%S', executed) as datetime,
            strftime('%d', executed) as day,
            COUNT() as number,
            status
            FROM tests
            WHERE strftime('%Y-%m-%d', executed) BETWEEN ? AND ?
            AND status = 'notImplemented'
            GROUP BY strftime('%d', executed)
            UNION
            SELECT
            strftime('%Y-%m-%d  %H:%M:%S', executed) as datetime,
            strftime('%d', executed) as day,
            COUNT() as number,
            status
            FROM tests
            WHERE strftime('%Y-%m-%d', executed) BETWEEN ? AND ?
            AND status = 'skipped'
            GROUP BY strftime('%d', executed)  
            UNION
            SELECT
            strftime('%Y-%m-%d  %H:%M:%S', executed) as datetime,
            strftime('%d', executed) as day,
            COUNT() as number,
            status
            FROM tests
            WHERE strftime('%Y-%m-%d', executed) BETWEEN ? AND ?
            AND status = 'error'
            GROUP BY strftime('%d', executed)
            ORDER BY status";
        
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(1, $start->format('Y-m-d'));
        $stmt->bindValue(2, $end->format('Y-m-d'));
        $stmt->bindValue(3, $start->format('Y-m-d'));
        $stmt->bindValue(4, $end->format('Y-m-d'));
        $stmt->bindValue(5, $start->format('Y-m-d'));
        $stmt->bindValue(6, $end->format('Y-m-d'));
        $stmt->bindValue(7, $start->format('Y-m-d'));
        $stmt->bindValue(8, $end->format('Y-m-d'));
        $stmt->bindValue(9, $start->format('Y-m-d'));
        $stmt->bindValue(10, $end->format('Y-m-d'));
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
