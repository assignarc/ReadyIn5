<?php

namespace RI5\Services;

use RI5\Exception\DatabaseException;
use RI5\Services\Traits\LoggerAwareTrait;
use PDO;
use PDOException;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

class ConnectionService {

    use LoggerAwareTrait;

    private $connection;
    private $DATABASE_URL = "";

    public function __construct() {
        
       //"mysql:host=" . $this->DB_HOST_NAME . ";dbname=" . $this->DB_NAME; 
        $this->DATABASE_URL = getenv('DATABASE_URL');
    }

    public function getConnection() : ?PDO {
        try {
            if ($this->connection instanceof PDO) {
                return $this->connection;
            }
            else{
                //$this->DB_JDBC_URL,$this->DB_USERNAME,$this->DB_PASSWRD);    // Creating PDO instance
                $this->connection = new PDO($this->DATABASE_URL);
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $this->connection;
            }
        } catch (PDOException $e) {
            $this->logError("Connection failed: " . $e->getMessage());
            throw new DatabaseException("Database connection error.",9600,[$this->DATABASE_URL],$e);
        }
    }

    public function __destruct() {
        $this->connection=null;
    }
}

