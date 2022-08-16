<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Doctrine\ORM\EntityManager, Doctrine\ORM\Tools\Setup;
define('BASE_PATH', dirname(__FILE__, 3));

class DoctrineEntityManagerProvider extends ServiceProvider
{

    const PATH = [ 
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
//        BASE_PATH . "/src/ActivityCreator/Infrastructure/Persistence/Doctrine/Mapping",
        BASE_PATH . "/src/ActivityInvitee/Infrastructure/Persistence/Doctrine/Mapping",
    ];

    public function register()
    {
        $this->app->singleton(EntityManager::class,
            function ($app) {
                $connection = (env('DOCTRINE_DB_CONNECTION') == "pdo_sqlite")?
                    [
                        'driver' => env('DOCTRINE_DB_CONNECTION'),
                        'path' => env('DB_DATABASE')
                    ]: 
                    [
                        'driver' => env('DOCTRINE_DB_CONNECTION'),
                        'user' => env('DB_USERNAME'),
                        'password' => env('DB_PASSWORD'),
                        'dbname' => env('DB_DATABASE')
                    ];

/**
 * uncomment line 48-49 and comment line 50 if redis connection still failed
 */
//$cache = new \Doctrine\Common\Cache\ArrayCache();
//$config = Setup::createXMLMetadataConfiguration(self::PATH, env('DOCTRINE_IS_DEV_MODE'), null, $cache);
                $config = Setup::createXMLMetadataConfiguration(self::PATH, env('DOCTRINE_IS_DEV_MODE'));
                $config->setProxyDir(dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . "mcp_proxy");
                return EntityManager::create($connection, $config);
            });
    }
}
