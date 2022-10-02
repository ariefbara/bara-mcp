<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByCoordinator;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Program\RegistrantRepository;

class ViewRegistrantDetail implements ProgramTaskExecutableByCoordinator
{

    /**
     * 
     * @var RegistrantRepository
     */
    protected $registrantRepository;

    public function __construct(RegistrantRepository $registrantRepository)
    {
        $this->registrantRepository = $registrantRepository;
    }

    /**
     * 
     * @param string $programId
     * @param CommonViewDetailPayload $payload
     * @return void
     */
    public function execute(string $programId, $payload): void
    {
        $payload->result = $this->registrantRepository->aRegistrantInProgram($programId, $payload->getId());
    }

}
