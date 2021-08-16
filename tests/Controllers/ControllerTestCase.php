<?php

namespace Tests\Controllers;

use App\Exceptions\Handler;
use Illuminate\ {
    Contracts\Debug\ExceptionHandler,
    Database\ConnectionInterface,
    Support\Facades\DB
};
use Laravel\Lumen\Testing\TestCase;

class ControllerTestCase extends TestCase
{
//     use \Laravel\Lumen\Testing\DatabaseMigrations; //comment me after link to db made (migration and sqlite_sequence table exist, or its actually simpler to just copy a database.sqlite from template :D), if not this will always reset db state
    
    /**
     *
     * @var ConnectionInterface
     */
    protected $connection;
    
    protected function setUp(): void
    {
        parent::setUp();
//        $this->disableExceptionHandling();
        $this->connection = DB::connection();
        $this->connection->statement('set global max_connections = 800;');//sometime bulk test cause 'to many connection' error - mysql env
        $this->connection->statement('SET FOREIGN_KEY_CHECKS=0;');//to enable table truncate without hassle - mysql env
    }

    public function createApplication()
    {
        return require __DIR__ . '/../../bootstrap/app.php';
    }
    
    protected function disableExceptionHandling()
    {
        $this->oldExceptionHandler = $this->app->make(ExceptionHandler::class);
        $this->app->instance(ExceptionHandler::class, new class extends Handler {
            public function __construct() {}
            public function report(\Throwable $e) {}
            public function render($request, \Throwable $e) {
                throw $e;
            }
        });
    }
    
    protected function currentTimeString()
    {
        return (new \DateTime())->format('Y-m-d H:i:s');
    }
    
    function arrayPreserveJsOrder(array $data) {
        return array_map(
            function($key, $value) {
                if (is_array($value)) {
                    $value = $this->arrayPreserveJsOrder($value);
                }
                return array($key, $value);
            },
            array_keys($data),
            array_values($data)
        );
    }
    
}
