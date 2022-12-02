<?php

namespace Query\Application\Service\Guest;

use Query\Domain\Task\Guest\GuestTask;

class ExecuteTask
{

    /**
     * 
     * @var GuestRepository
     */
    protected $guestRepository;

    public function __construct(GuestRepository $guestRepository)
    {
        $this->guestRepository = $guestRepository;
    }
    
    public function execute(GuestTask $task, $payload): void
    {
        $this->guestRepository->get()->executeTask($task, $payload);
    }

}
