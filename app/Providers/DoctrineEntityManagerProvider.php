<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Doctrine\ORM\EntityManager, Doctrine\ORM\Tools\Setup;
define('BASE_PATH', dirname(__FILE__, 3));

class DoctrineEntityManagerProvider extends ServiceProvider
{

    const PATH = [ 
        BASE_PATH . "/resources/Infrastructure/Persistence/Doctrine/Mapping",
        BASE_PATH . "/src/Bara/Infrastructure/Persistence/Doctrine/Mapping",
        BASE_PATH . "/src/Firm/Infrastructure/Persistence/Doctrine/Mapping",
        BASE_PATH . "/src/Client/Infrastructure/Persistence/Doctrine/Mapping",
        BASE_PATH . "/src/Consultant/Infrastructure/Persistence/Doctrine/Mapping",
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

                $config = Setup::createXMLMetadataConfiguration(self::PATH, env('DOCTRINE_IS_DEV_MODE'));

                return EntityManager::create($connection, $config);
            });
    }
}
