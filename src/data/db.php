<?php
// ----------------------------------------------------------------------
// tecla. The tennis club app.
// Copyright (C) 2020 Daniel J. Lauk <daniel.lauk@gmail.com>
//
// tecla is open source under the terms of the MIT license.
// For details see LICENSE.md.
// ----------------------------------------------------------------------

namespace tecla\data;

const WEEKDAYS = array(
    'Sunday',
    'Monday',
    'Tuesday',
    'Wednesday',
    'Thursday',
    'Friday',
    'Saturday',
);

class VersionMismatchError extends \Exception
{}

class AmbiguousQueryError extends \Exception
{}

class DBAccess
{
    private $pdo;
    private $debug = false;
    public function __construct(&$pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * enableDebug activates additional error reporting.
     */
    public function enableDebug()
    {
        $this->debug = true;
    }

    private function stopWithError($errorInfo = null)
    {
        $msg = "Error during DB access";
        if ($this->debug && !is_null($errorInfo)) {
            $msg .= "\n\nDEBUG INFO:\n" . implode("\n", $errorInfo);
        }
        die($msg);
    }

    /**
     * query will execute $sql in a prepared statement and return all rows as an array.
     */
    public function query($sql, $values = null)
    {
        $statement = $this->pdo->prepare($sql);
        if ($statement === false) {
            $this->stopWithError($this->pdo->errorInfo());
        }
        if ($statement->execute($values) !== true) {
            $this->stopWithError($statement->errorInfo());

        }

        $results = $statement->fetchAll();
        if ($results === false) {
            $this->stopWithError($statement->errorInfo());
        }
        return $results;
    }

    /**
     * querySingle will execute $sql in a prepared statement and expects 0 or 1 results.
     * If more than 1 result is returned an AmbiguousQueryError is thrown.
     */
    public function querySingle($sql, $values = null)
    {
        $results = $this->query($sql, $values);
        $count = count($results);
        if ($count === 0) {
            return null;
        }
        if ($count > 1) {
            throw new AmbiguousQueryError("Query returned $count results");
        }
        return $results[0];
    }

    /**
     * execute will execute the SQL in $sql in a prepared statement.
     */
    public function execute($sql, $values = null)
    {
        $statement = $this->pdo->prepare($sql);
        if ($statement === false) {
            $this->stopWithError($statement->errorInfo());
        }
        if ($statement->execute($values) !== true) {
            $this->stopWithError($statement->errorInfo());

        }
    }

    /**
     * insert executes the SQL statement in $sql and returns the Id of the last insert.
     *
     * @return int Id which the last insert genertated.
     */
    public function insert($sql, $values = null)
    {
        $this->execute($sql, $values);
        return $this->pdo->lastInsertId();
    }
}
