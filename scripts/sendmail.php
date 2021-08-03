<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Notification\Application\Service\SendMail;
use Notification\Domain\SharedModel\Mail\Recipient;
use Notification\Infrastructure\MailManager\SwiftMailSender;

define('BASE_PATH', dirname(__FILE__, 2));

require_once BASE_PATH . "/vendor/autoload.php";

sleep(10);

$isDevMode = false;//generate proxy manually if entity not found

$generateProxyPath = [
    BASE_PATH . "/resources/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/Query/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/SharedContext/Infrastructure/Persistence/Doctrine/Mapping/ValueObject",
    BASE_PATH . "/src/Notification/Infrastructure/Persistence/Doctrine/Mapping",
    BASE_PATH . "/src/SharedContext/Infrastructure/Persistence/Doctrine/Mapping",
];

$doctrineConfig = Setup::createXMLMetadataConfiguration($generateProxyPath, true);

 $conn = array(
    'driver' => 'pdo_mysql',
    'user' => 'root',
    'password' => '',
    'dbname' => 'bara_mcp'
 );

$entityManager = EntityManager::create($conn, $doctrineConfig);

$recipientRepository = $entityManager->getRepository(Recipient::class);
$transport = new Swift_SmtpTransport('mail.innov.id', 465, 'ssl');
$transport->setUsername("no-reply@innov.id");
$transport->setPassword('pr4jaB1@bandung');
$vendor = new Swift_Mailer($transport);
$mailSender = new SwiftMailSender($vendor);
(new SendMail($recipientRepository, $mailSender))->execute();


