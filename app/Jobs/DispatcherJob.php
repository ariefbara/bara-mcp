<?php

namespace App\Jobs;

use Resources\Application\Event\Dispatcher;

class DispatcherJob extends Job
{
    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;
    
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
    
    public function handle()
    {
        $this->dispatcher->execute();
    }

}
