<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Dependency\Firm\ProgramRepository;

class ViewListOfCoordinatedProgram implements PersonnelTask
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
     * @param string $personnelId
     * @param CommonViewListPayload $payload
     * @return void
     */
    public function execute(string $personnelId, $payload): void
    {
        $payload->result = $this->programRepository->listOfCoordinatedProgramByPersonnel($personnelId);
    }

}
