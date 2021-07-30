<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Notification\Application\Service\SendImmediateMail;
use Notification\Domain\SharedModel\Mail\Recipient;
use Notification\Infrastructure\MailManager\SwiftMailSender;

define('BASE_PATH', dirname(__FILE__, 2));

require_once BASE_PATH . "/vendor/autoload.php";

sleep(30);

$isDevMode = false;//generate proxy manually if entity not found
//$generateDbPath = array(
//    BASE_PATH . "/resources/Infrastructure/Persistence/Doctrine/Mapping",
//    BASE_PATH . "/src/Query/Infrastructure/Persistence/Doctrine/Mapping",
//    BASE_PATH . "/src/SharedContext/Infrastructure/Persistence/Doctrine/Mapping/ValueObject",
//);

$generateProxyPath = [
    BASE_PATH . "/resources/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/Query/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/SharedContext/Infrastructure/Persistence/Doctrine/Mapping/ValueObject",
//    BASE_PATH . "/src/Bara/Infrastructure/Persistence/Doctrine/Mapping",
//    BASE_PATH . "/src/Firm/Infrastructure/Persistence/Doctrine/Mapping",
//    BASE_PATH . "/src/Client/Infrastructure/Persistence/Doctrine/Mapping",
//    BASE_PATH . "/src/User/Infrastructure/Persistence/Doctrine/Mapping",
//    BASE_PATH . "/src/Personnel/Infrastructure/Persistence/Doctrine/Mapping",
//    BASE_PATH . "/src/Participant/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/Notification/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/SharedContext/Infrastructure/Persistence/Doctrine/Mapping",
//    BASE_PATH . "/src/Team/Infrastructure/Persistence/Doctrine/Mapping",
//    BASE_PATH . "/src/ActivityCreator/Infrastructure/Persistence/Doctrine/Mapping",
//    BASE_PATH . "/src/ActivityInvitee/Infrastructure/Persistence/Doctrine/Mapping",
];

//$doctrineConfig = Setup::createXMLMetadataConfiguration($generateDbPath, $isDevMode);
$doctrineConfig = Setup::createXMLMetadataConfiguration($generateProxyPath, true);
//$doctrineConfig->setProxyDir(dirname(__DIR__) . DIRECTORY_SEPARATOR . "mcp_proxy");

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

$recipientRepository = $entityManager->getRepository(Recipient::class);
$transport = new Swift_SmtpTransport('mail.innov.id', 465, 'ssl');
$transport->setUsername("no-reply@innov.id");
$transport->setPassword('pr4jaB1@bandung');
$vendor = new Swift_Mailer($transport);
$mailSender = new SwiftMailSender($vendor);
(new SendImmediateMail($recipientRepository, $mailSender))->execute();
echo "apur is great";


