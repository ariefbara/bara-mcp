<?php

namespace Query\Domain\Task\Guest;

use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\ProgramRepository;

class ViewPublishedProgramDetail implements GuestTask
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
     * @param CommonViewDetailPayload $payload
     * @return void
     */
    public function execute($payload): void
    {
        $payload->result = $this->programRepository->aPublishedProgram($id);
    }

}
