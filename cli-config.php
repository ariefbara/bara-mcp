<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

define('BASE_PATH', dirname(__FILE__));

require_once "vendor/autoload.php";

$isDevMode = true;//generate proxy manually if entity not found
$generateDbPath = array(
    BASE_PATH . "/resources/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/Query/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/SharedContext/Infrastructure/Persistence/Doctrine/Mapping/ValueObject",
    
);

$generateProxyPath = [
    BASE_PATH . "/resources/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/Query/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/Bara/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/Firm/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/Client/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/User/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/Personnel/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/Participant/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/Notification/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/SharedContext/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/SharedContext/Infrastructure/Persistence/Doctrine/Mapping/ValueObject",
    BASE_PATH . "/src/Team/Infrastructure/Persistence/Doctrine/Mapping",
];

$doctrineConfig = Setup::createXMLMetadataConfiguration($generateDbPath, $isDevMode);
//$doctrineConfig = Setup::createXMLMetadataConfiguration($generateProxyPath, false);
$doctrineConfig->setProxyDir(__DIR__ . DIRECTORY_SEPARATOR . "proxy");

 $conn = array(
    'driver' => 'pdo_mysql',
    'user' => 'root',
    'password' => '',
    'dbname' => 'bara_mcp'
 );
//$conn = array(
//    'driver' => 'pdo_sqlite',
//    'path' => BASE_PATH . "/tests/database.sqlite",
//);

$entityManager = EntityManager::create($conn, $doctrineConfig);
return ConsoleRunner::createHelperSet($entityManager);
