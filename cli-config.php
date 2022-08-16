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
    BASE_PATH . "/src/SharedContext/Infrastructure/Persistence/Doctrine/Mapping/ForTableCreation",
    BASE_PATH . "/src/SharedContext/Infrastructure/Persistence/Doctrine/Mapping/ValueObject",
);

$generateProxyPath = [
    BASE_PATH . "/resources/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/Query/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/SharedContext/Infrastructure/Persistence/Doctrine/Mapping/ValueObject",
    BASE_PATH . "/src/Bara/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/Firm/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/Client/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/User/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/Personnel/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/Participant/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/Notification/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/SharedContext/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/Team/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/ActivityCreator/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/ActivityInvitee/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/Payment/Infrastructure/Persistence/Doctrine/Mapping",
];

$doctrineConfig = Setup::createXMLMetadataConfiguration($generateDbPath, $isDevMode);
//$doctrineConfig = Setup::createXMLMetadataConfiguration($generateProxyPath, false);
$doctrineConfig->setProxyDir(dirname(__DIR__) . DIRECTORY_SEPARATOR . "mcp_proxy");

 $conn = array(
    'driver' => 'pdo_mysql',
    'user' => 'root',
    'password' => 'APURac421199df74e5371524',
    'dbname' => 'bara_mcp'
 );
//$conn = array(
//    'driver' => 'pdo_sqlite',
//    'path' => BASE_PATH . "/tests/database.sqlite",
//);

$entityManager = EntityManager::create($conn, $doctrineConfig);
return ConsoleRunner::createHelperSet($entityManager);
