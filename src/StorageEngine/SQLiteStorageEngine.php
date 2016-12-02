<?php
/**
 * @copyright Hayden Pierce (hayden@haydenpierce.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Backup\StorageEngine;

use Backup\Exception\InvalidConfigStateException;
use Backup\Exception\SQLException;
use Backup\User\User;

class SQLiteStorageEngine implements StorageEngineInterface
{
    /** @var \SQLite3 */
    protected $sqlite;

    public static function getName()
    {
        return "SQLite";
    }

    public static function initFromConfig(Array $config)
    {
        if(!isset($config['StorageEngine']['SQLite']['file'])){
            throw new InvalidConfigStateException('Could not read file option in Config:StorageEngine:SQLite:file');
        } else {
            return new static($config['StorageEngine']['SQLite']['file']);
        }
    }

    public function __construct(String $dbPath)
    {
        $this->sqlite = new \SQLite3($dbPath);
        
        if(!$this->schemaExists()){
            $this->buildSchema();
        }
    }


    public function persistUser(String $userAlias, User $user)
    {
        $query = "INSERT INTO users (alias, params) VALUES (:alias, :params)";

        $stmt = $this->sqlite->prepare($query);
        $stmt->bindParam(':alias', $userAlias);
        $stmt->bindParam(':params', $this->serialize($user));
        $stmt->execute();

        return $this->checkQuerySucceeded($query);
    }

    public function retrieveUser(String $userAlias)
    {
        $query = "SELECT params FROM users WHERE alias = :alias";

        $stmt = $this->sqlite->prepare($query);
        $stmt->bindParam(':alias', $userAlias);
        $result = $stmt->execute();

        $this->checkQuerySucceeded($query);
        
        $row = $result->fetchArray(SQLITE3_ASSOC);
        return isset($row['params']) ? $this->unserialize($row['params']) : null;
    }

    public function listUsers()
    {
        $query = "SELECT * FROM users";

        $result = $this->sqlite->query($query);

        if('not an error' !== $error = $this->sqlite->lastErrorMsg()){
            throw new SQLException($error);
        }

        $users = [];
        while($row = $result->fetchArray(SQLITE3_ASSOC)){
            $row['user'] = $this->unserialize($row['params']);
            unset($row['params']);
            unset($row['id']);
            $users[] = $row;
        }

        return $users;
    }

    private function schemaExists()
    {
        $result = $this->sqlite->query("PRAGMA table_info(users)");
        //Providing ANYTHING means that the table exists.
        return (bool) $result->fetchArray(SQLITE3_ASSOC);
    }

    private function buildSchema()
    {
        $query = "
          CREATE TABLE users (
            id INT PRIMARY KEY,
            alias TEXT NOT NULL UNIQUE,
            params TEXT
          )
        ";

        //We silence this (possible) error because we will check it ourselves - a failing query here is fatal,
        //yet this would only raise a warning level error.
        $result = @$this->sqlite->query($query);
        
        return $this->checkQuerySucceeded($query);
    }

    private function checkQuerySucceeded(String $query)
    {
        if($this->sqlite->lastErrorCode()){
            $msg = $this->sqlite->lastErrorMsg();
            throw new SQLException(sprintf('Creating table failed when running the query: %s with error message %s',
                $query, $msg));
        } else {
            return true;
        }
    }

    private function serialize($var)
    {
        $string = serialize($var);
        return str_replace("\0", '\0', $string);
    }

    private function unserialize($string)
    {
        $string = str_replace('\0', "\0", $string);
        return unserialize($string);
    }
}