<?php

namespace TezlikPlaneacion\Dao;

use TezlikPlaneacion\Constants\Constants;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PDO;
use PDOException;
use Symfony\Component\Dotenv\Dotenv;

/**
 * Class Connection
 * @package TezlikPlaneacion\Dao
 * @author Teenus <Teenus-SAS>
 */
class Connection
{
    protected $dbh;
    private static $_instance;
    private $logger;

    /**
     * Connection constructor.
     */
    public function __construct()
    {
        $this->logger = new Logger(self::class);
        $this->logger->pushHandler(new RotatingFileHandler(Constants::LOGS_PATH . 'querys.log', 20, Logger::DEBUG));
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__ . '/../environment.env');
        try {
            $host = $_ENV["DB_HOST"];
            $dbname = $_ENV["DB_NAME"];
            $dbport = $_ENV["DB_PORT"];
            $dsn = "mysql:host=$host;port=$dbport;dbname=$dbname;charset=utf8";
            $this->dbh = new PDO($dsn, $_ENV["DB_USER"], $_ENV["DB_PASS"]);
            $this->dbh->exec('SET NAMES utf8');
            $this->logger->info("Connection SuccesFully DB", array("pdo" => $this->dbh));
        } catch (PDOException $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /*
	Get an instance of the Database
	@return Instance
	*/
    public static function getInstance()
    {
        if (!self::$_instance) { // If no instance then make one
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    // Magic method clone is empty to prevent duplication of connection
    private function __clone()
    {
    }

    // Get mysqli connection
    public function getConnection()
    {
        return $this->dbh;
    }
}
