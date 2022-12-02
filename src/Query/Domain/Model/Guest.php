<?php

namespace Query\Domain\Model;

use Query\Domain\Task\Guest\GuestTask;

class Guest
{

    public function __construct()
    {
        
    }
    
    public function executeTask(GuestTask $task, $payload): void
    {
        $task->execute($payload);
    }

}
