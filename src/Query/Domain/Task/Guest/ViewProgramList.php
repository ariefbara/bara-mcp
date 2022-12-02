<?php

namespace Query\Domain\Task\Guest;

use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Dependency\Firm\ProgramRepository;

class ViewProgramList implements GuestTask
{

    /**
     * 
     * @var ProgramRepository
     */
    protected $programRepository;

    public function __construct(ProgramRepository $programRepository)
    {
        $this->programRepository = $programRepository;
    }

    /**
     * 
     * @param CommonViewListPayload $payload
     * @return void
     */
    public function execute($payload): void
    {
        $payload->result = $this->programRepository->listOfProgram($payload->getFilter());
    }

}
